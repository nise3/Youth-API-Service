<?php


namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Examination;
use App\Models\Education;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
        $examinationTitleBn = $request['examination_title'] ?? "";
        $boardTitleEn = $request['board_title_en'] ?? "";
        $boardTitleBn = $request['board_title'] ?? "";
        $eduGroupTitleEn = $request['edu_group_title_en'] ?? "";
        $eduGroupTitleBn = $request['edu_group_title'] ?? "";

        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder $educationBuilder */
        $educationBuilder = Education::select(
            [
                'educations.id',
                'educations.youth_id',
                'educations.institute_name',
                'educations.institute_name_en',
                'educations.examination_id',
                'examinations.code as examination_code',
                'examinations.title_en as examination_title_en',
                'examinations.title as examination_title',
                'educations.board_id',
                'boards.title_en as board_title_en',
                'boards.title as board_title',
                'educations.edu_group_id',
                'edu_groups.code as edu_group_code',
                'edu_groups.title_en as edu_group_title_en',
                'edu_groups.title as edu_group_title',
                'educations.major_or_subject_id',
                'educations.roll_number',
                'educations.registration_number',
                'educations.result_type',
                'educations.division_type_result',
                'educations.cgpa_gpa_max_value',
                'educations.received_cgpa_gpa',
                'educations.passing_year',
                'educations.row_status',
                'educations.created_at',
                'educations.updated_at',
            ]
        );
        $educationBuilder->join('examinations', function ($join) use ($rowStatus) {
            $join->on('examinations.id', '=', 'educations.examination_id')
                ->whereNull('examinations.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('examinations.row_status', $rowStatus);
            }
        });
        $educationBuilder->join('boards', function ($join) use ($rowStatus) {
            $join->on('boards.id', '=', 'educations.board_id')
                ->whereNull('boards.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('boards.row_status', $rowStatus);
            }
        }
        );
        $educationBuilder->join('edu_groups', function ($join) use ($rowStatus) {
            $join->on('edu_groups.id', '=', 'educations.edu_group_id')
                ->whereNull('edu_groups.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('edu_groups.row_status', $rowStatus);
            }
        });
        $educationBuilder->orderBy('educations.id', $order);

        if (is_numeric($rowStatus)) {
            $educationBuilder->where('educations.row_status', $rowStatus);
        }
        if (is_numeric(Auth::id())) {
            $educationBuilder->where('educations.youth_id', Auth::id());
        }

        if (!empty($instituteName)) {
            $educationBuilder->where('educations.institute_name', 'like', '%' . $instituteName . '%');
        }
        if (!empty($instituteNameEn)) {
            $educationBuilder->where('educations.institute_name_en', 'like', '%' . $instituteNameEn . '%');
        }

        if (!empty($examinationTitleEn)) {
            $educationBuilder->where('examinations.title_en', 'like', '%' . $examinationTitleEn . '%');
        }
        if (!empty($examinationTitleBn)) {
            $educationBuilder->where('examinations.title', 'like', '%' . $examinationTitleBn . '%');
        }

        if (!empty($boardTitleEn)) {
            $educationBuilder->where('boards.title_en', 'like', '%' . $boardTitleEn . '%');
        }
        if (!empty($boardTitleBn)) {
            $educationBuilder->where('boards.title', 'like', '%' . $boardTitleBn . '%');
        }

        if (!empty($eduGroupTitleEn)) {
            $educationBuilder->where('edu_groups.title_en', 'like', '%' . $eduGroupTitleEn . '%');
        }
        if (!empty($eduGroupTitleBn)) {
            $educationBuilder->where('edu_groups.title', 'like', '%' . $eduGroupTitleBn . '%');
        }


        /** @var Collection $educations */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $educations = $educationBuilder->paginate($pageSize);
            $paginateData = (object)$educations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $educations = $educationBuilder->get();
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
        $educationBuilder = Education::select(
            [
                'educations.id',
                'educations.youth_id',
                'educations.institute_name',
                'educations.institute_name_en',
                'educations.examination_id',
                'examinations.code as examination_code',
                'examinations.title_en as examination_title_en',
                'examinations.title as examination_title',
                'educations.board_id',
                'boards.title_en as board_title_en',
                'boards.title as board_title',
                'educations.edu_group_id',
                'edu_groups.code as edu_group_code',
                'edu_groups.title_en as edu_group_title_en',
                'edu_groups.title as edu_group_title',
                'educations.major_or_subject_id',
                'educations.roll_number',
                'educations.registration_number',
                'educations.result_type',
                'educations.division_type_result',
                'educations.cgpa_gpa_max_value',
                'educations.received_cgpa_gpa',
                'educations.passing_year',
                'educations.row_status',
                'educations.created_at',
                'educations.updated_at',
            ]
        );
        $educationBuilder->join('examinations', function ($join) {
            $join->on('examinations.id', '=', 'educations.examination_id')
                ->whereNull('examinations.deleted_at');

        });
        $educationBuilder->join('boards', function ($join) {
            $join->on('boards.id', '=', 'educations.board_id')
                ->whereNull('boards.deleted_at');

        }
        );
        $educationBuilder->join('edu_groups', function ($join) {
            $join->on('edu_groups.id', '=', 'educations.edu_group_id')
                ->whereNull('edu_groups.deleted_at');

        });
        $educationBuilder->where('educations.id', $id);

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
        $titleBn = $request->query('title');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $educationBuilder */
        $educationBuilder = Education::onlyTrashed()->select(
            [
                'skills.id as id',
                'skills.title_en',
                'skills.title',
                'skills.row_status',
                'skills.created_at',
                'skills.updated_at'
            ]
        );

        $educationBuilder->orderBy('skills.id', $order);

        if (!empty($titleEn)) {
            $educationBuilder->where('skills.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $educationBuilder->where('skills.title', 'like', '%' . $titleBn . '%');
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
                'int',
                'exists:youths,id'
            ],
            'examination_id' => [
                'required',
                'integer',
                'exists:examinations,id',
                'min:1',
                'unique_with:educations,youth_id,' . $id
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
            'roll_number' => [
                'required',
                'string',

            ],
            'registration_number' => [
                'required',
                'string',
            ],
            'result_type' => [
                'required',
                'integer',
                'min:1'
            ],
            'passing_year' => [
                'required',
                'integer',
                'min:1971'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        if ($request->examination_id == Education::EXAMINATION_ID_HONOURS or $request->examination_id == Education::EXAMINATION_ID_PMASTERS or $request->examination_id == Education::EXAMINATION_ID_MASTERS) {
            $rules['major_or_subject_id'] = [
                'required',
                'integer',
                'exists:major_or_subjects,id',
            ];
        } else {
            $rules['board_id'] = [
                'required',
                'integer',
                'exists:boards,id',
                'min:1'
            ];
        }

        if ($request->result_type == Education::RESULT_TYPE_DIVISION) {
            $rules['division_type_result'] = [
                'required',
                'numeric',
                'min:1',
                Rule::in(Education::DIVISION_FIRST_CLASS, Education::DIVISION_SECOND_CLASS, Education::DIVISION_THIRD_CLASS, Education::DIVISION_PASS),
            ];
        }
        if ($request->examination_id == Education::EXAMINATION_ID_SSC or $request->examination_id == Education::EXAMINATION_ID_DAKHIL or $request->examination_id == Education::EXAMINATION_ID_HSC or $request->examination_id == Education::EXAMINATION_ID_ALIM) {
            $rules['edu_group_id'] = [
                'required',
                'integer',
                'exists:edu_groups,id',
                'min:1'
            ];
        }

        if ($request->result_type == Education::RESULT_TYPE_GRADE_POINT) {
            $rules['cgpa_gpa_max_value'] = [
                'required',
                'numeric',
                Rule::in([Education::GPA_OUT_OF_FIVE, Education::GPA_OUT_OF_FOUR])
            ];

            $rules['received_cgpa_gpa'] = [
                'required',
                'numeric',
                'lte:cgpa_gpa_max_value'
            ];
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
