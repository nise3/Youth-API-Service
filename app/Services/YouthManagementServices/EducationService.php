<?php


namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Examination;
use App\Models\Education;
use App\Models\Trainer;
use App\Models\Youth;
use Carbon\Carbon;
use Faker\Provider\Uuid;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Prophecy\Promise\PromiseInterface;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class YouthProfileService
 * @package App\Services\YouthManagementServices
 */
class EducationService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getEducationList(array $request, Carbon $startTime): array
    {
        $instituteName = $request['institute_name'] ?? "";
        $instituteNameEn = $request['institute_name_en'] ?? "";
        $examinationTitleEn = $request['examination_title_en'] ?? "";
        $examinationTitleBn = $request['examination_title_bn'] ?? "";
        $boardTitleEn = $request['board_title_en'] ?? "";
        $boardTitleBn = $request['board_title_bn'] ?? "";

        $groupTitleEn = $request['group_title_en'] ?? "";
        $groupTitleBn = $request['group_title_bn'] ?? "";

        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder $educationBuilders */
        $educationBuilders = Education::select(
            [
                'education.id',
                'education.youth_id',
                'education.examination_id',
                'examinations.title_en as examination_title_en',
                'examinations.title_bn as examination_title_bn',
                'education.institute_name',
                'education.institute_name_en',
                'education.board_id',
                'boards.title_en as board_title_en',
                'boards.title_bn as board_title_bn',
                'education.group_id',
                'groups.title_en as group_title_en',
                'groups.title_bn as group_title_bn',
                'education.result_type',
                'education.result',
                'education.cgpa',
                'education.passing_year',
                'education.row_status',
                'education.created_at',
                'education.updated_at',
            ]
        );
        $educationBuilders->join('youths', 'youths.id', '=', 'education.youth_id');
        $educationBuilders->join('examinations', 'examinations.id', '=', 'education.examination_id');
        $educationBuilders->join('boards', 'boards.id', '=', 'education.board_id');
        $educationBuilders->join('groups', 'groups.id', '=', 'education.group_id');
        $educationBuilders->orderBy('education.id', $order);

        if (is_numeric($rowStatus)) {
            $educationBuilders->where('education.row_status', $rowStatus);
        }
        if (!empty($instituteName)) {
            $educationBuilders->where('education.institute_name', 'like', '%' . $instituteName . '%');
        }
        if (!empty($instituteNameEn)) {
            $educationBuilders->where('education.institute_name_en', 'like', '%' . $instituteNameEn . '%');
        }

        if (!empty($examinationTitleEn)) {
            $educationBuilders->where('examinations.title_en', 'like', '%' . $examinationTitleEn . '%');
        }
        if (!empty($examinationTitleBn)) {
            $educationBuilders->where('examinations.title_bn', 'like', '%' . $examinationTitleBn . '%');
        }

        if (!empty($boardTitleEn)) {
            $educationBuilders->where('boards.title_en', 'like', '%' . $boardTitleEn . '%');
        }
        if (!empty($boardTitleBn)) {
            $educationBuilders->where('boards.title_bn', 'like', '%' . $boardTitleBn . '%');
        }

        if (!empty($groupTitleEn)) {
            $educationBuilders->where('groups.title_en', 'like', '%' . $groupTitleEn . '%');
        }
        if (!empty($groupTitleBn)) {
            $educationBuilders->where('groups.title_bn', 'like', '%' . $groupTitleBn . '%');
        }


        /** @var Collection $educations */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $educations = $educationBuilders->paginate($pageSize);
            $paginateData = (object)$educations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $educations = $educationBuilders->get();
        }

        $response['order'] = $order;
        $response['data'] = $educations->toArray()['data'] ?? $educations->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneEducation(int $id, Carbon $startTime): array
    {
        /** @var Builder $educationBuilder */
        $educationBuilder = Education::select(
            [
                'education.id',
                'education.youth_id',
                'education.examination_id',
                'examinations.title_en as examination_title_en',
                'examinations.title_bn as examination_title_bn',
                'education.institute_name',
                'education.institute_name_en',
                'education.board_id',
                'boards.title_en as board_title_en',
                'boards.title_bn as board_title_bn',
                'education.group_id',
                'groups.title_en as group_title_en',
                'groups.title_bn as group_title_bn',
                'education.result_type',
                'education.result',
                'education.cgpa',
                'education.passing_year',
                'education.row_status',
                'education.created_at',
                'education.updated_at',
            ]
        );
        $educationBuilder->join('youths', 'youths.id', '=', 'education.youth_id');
        $educationBuilder->join('examinations', 'examinations.id', '=', 'education.examination_id');
        $educationBuilder->join('boards', 'boards.id', '=', 'education.board_id');
        $educationBuilder->join('groups', 'groups.id', '=', 'education.group_id');

        /** @var Education $education */
        $education = $educationBuilder->first();

        return [
            "data" => $education ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
    }

    /**
     * @param array $data
     * @return Education
     */
    public function createEducation(array $data): Education
    {
        $youthEducation = new Education();
        $youthEducation->fill($data);
        $youthEducation->save();
        return $youthEducation;
    }

    /**
     * @param Education $youthEducation
     * @param array $data
     * @return Education
     */
    public function update(Education $youthEducation, array $data): Education
    {
        $youthEducation->fill($data);
        $youthEducation->save();
        return $youthEducation;
    }

    /**
     * @param Education $youthEducation
     * @return bool
     */
    public function destroy(Education $youthEducation): bool
    {
        return $youthEducation->delete();
    }

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrashedYouthEducationList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $educationBuilder */
        $educationBuilder = Education::onlyTrashed()->select(
            [
                'skills.id as id',
                'skills.title_en',
                'skills.title_bn',
                'skills.description',
                'skills.description_en',
                'skills.row_status',
                'skills.created_at',
                'skills.updated_at',
                'skills.created_by',
                'skills.updated_by',
            ]
        );

        $educationBuilder->orderBy('skills.id', $order);

        if (!empty($titleEn)) {
            $educationBuilder->where('skills.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $educationBuilder->where('skills.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $youthEducations */

        if (!is_null($paginate) || !is_null($limit)) {
            $limit = $limit ?: 10;
            $youthEducations = $educationBuilder->paginate($limit);
            $paginateData = (object)$youthEducations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $youthEducations = $educationBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $youthEducations->toArray()['data'] ?? $youthEducations->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param Education $youthEducation
     * @return bool
     */
    public function restore(Education $youthEducation): bool
    {
        return $youthEducation->restore();
    }

    /**
     * @param Education $youthEducation
     * @return bool
     */
    public function forceDelete(Education $youthEducation): bool
    {
        return $youthEducation->forceDelete();
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ],
            'examination_id.unique' => [
                'message' => Examination::findOrFail($request->examination_id)->title_en . " examination already added your education list"
            ]
        ];
        $rules = [
            'youth_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'examination_id' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('education')->where(function ($query) use ($request) {
                    return $query->where('youth_id', $request->youth_id);
                }),
            ],
            'institute_name' => [
                'required',
                'string',
                'max:400',
            ],
            'institute_name_en' => [
                'nullable',
                'string',
                'max:400',
            ],
            'board_id' => [
                'required',
                'integer',
                'min:1'
            ],
            'group_id' => [
                'required',
                'integer',
                'min:1'
            ],
            'result_type' => [
                'required',
                'integer',
                'min:1'
            ],
            'passing_year' => [
                'required',
                'string',
                'max:4',
                'min:4',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];

        if ($request->result_type == Education::RESULT_TYPE_DIVISION) {
            $rules['result'] = [
                'required',
                'integer',
                'min:1',
                Rule::in(Education::DIVISION_FIRST_CLASS, Education::DIVISION_SECOND_CLASS, Education::DIVISION_THIRD_CLASS, Education::DIVISION_PASS),
            ];
        } else {
            $rules['result'] = [
                'required',
                'integer',
                'min:1',
                Rule::in(Education::GPA_OUT_OF_FIVE, Education::GPA_OUT_OF_FOUR),
            ];
        }

        if ($request->result_type == Education::RESULT_TYPE_GPA) {
            if ($request->result == Education::GPA_OUT_OF_FOUR) {
                $rules['cgpa'] = [
                    'required',
                    'numeric',
                    'between:1.00,4.00'
                ];
            } else {
                $rules['cgpa'] = [
                    'required',
                    'numeric',
                    'between:1.00,5.00'
                ];
            }
        }
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @return Validator
     */
    public function filterValidator(Request $request): Validator
    {
        $customMessage = [
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be within ASC or DESC',
            ],
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];

        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'institute_name' => [
                'nullable',
                'string',
                'max:400',
            ],
            'institute_name_en' => [
                'nullable',
                'string',
                'max:400',
            ],
            'examination_title_en' => [
                'nullable',
                'string',
                'max:255',
            ],
            'examination_title_bn' => [
                'nullable',
                'string',
                'max:400',
            ],
            'board_title_en' => [
                'nullable',
                'string',
                'max:255',
            ],
            'board_title_bn' => [
                'nullable',
                'string',
                'max:400',
            ],
            'group_title_en' => [
                'nullable',
                'string',
                'max:255',
            ],
            'group_title_bn' => [
                'nullable',
                'string',
                'max:400',
            ],
            'page' => 'numeric|gt:0',
            'pageSize' => 'numeric|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "numeric",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}
