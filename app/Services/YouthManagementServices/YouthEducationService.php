<?php


namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Examination;
use App\Models\YouthEducation;
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
class YouthEducationService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getYouthEducationList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $instituteName = $request['institute_name'] ?? "";
        $instituteNameEN = $request['institute_name_en'] ?? "";
        $titleBn = $request['title_bn'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder $youthEducationBuilder */
        $youthEducationBuilder = YouthEducation::select(
            [
                'education.id',
                'education.youth_id',
                'youths.username',


                /*'education.title',
                'education.title_en',
                'education.description',
                'education.description_en',
                'education.row_status',
                'education.created_at',
                'education.updated_at',
                'education.created_by',
                'education.updated_by',*/
            ]
        );
        $youthEducationBuilder->join('youths','youths.id','=','education.youth_id');
        $youthEducationBuilder->orderBy('education.id', $order);

        if (is_numeric($rowStatus)) {
            $youthEducationBuilder->where('education.row_status', $rowStatus);
        }

        if (!empty($instituteName)) {
            $youthEducationBuilder->where('youths.username', 'like', '%' . $instituteName . '%');
        }
        if (!empty($instituteNameEn)) {
            $youthEducationBuilder->where('education.institute_name_en', 'like', '%' . $instituteName . '%');
        }

        /** @var Collection $youthEducations */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $youthEducations = $youthEducationBuilder->paginate($pageSize);
            $paginateData = (object)$youthEducations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $youthEducations = $youthEducationBuilder->get();
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
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneYouthEducation(int $id, Carbon $startTime): array
    {
        /** @var Builder $youthEducationBuilder */
        $youthEducationBuilder = YouthEducation::select(
            [
                'skills.id',
                'skills.title',
                'skills.title_en',
                'skills.description',
                'skills.description_en',
                'skills.row_status',
                'skills.created_at',
                'skills.updated_at',
                'skills.created_by',
                'skills.updated_by',
            ]
        );

        $youthEducationBuilder->where('skills.id', '=', $id);

        /** @var YouthEducation $youthEducation */
        $youthEducation = $youthEducationBuilder->first();

        return [
            "data" => $youthEducation ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
    }

    /**
     * @param array $data
     * @return YouthEducation
     */
    public function createYouthEducation(array $data): YouthEducation
    {
        $youthEducation = new YouthEducation();
        $youthEducation->fill($data);
        $youthEducation->save();
        return $youthEducation;
    }

    /**
     * @param YouthEducation $youthEducation
     * @param array $data
     * @return YouthEducation
     */
    public function update(YouthEducation $youthEducation, array $data): YouthEducation
    {
        $youthEducation->fill($data);
        $youthEducation->save();
        return $youthEducation;
    }

    /**
     * @param YouthEducation $youthEducation
     * @return bool
     */
    public function destroy(YouthEducation $youthEducation): bool
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

        /** @var Builder $youthEducationBuilder */
        $youthEducationBuilder = YouthEducation::onlyTrashed()->select(
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

        $youthEducationBuilder->orderBy('skills.id', $order);

        if (!empty($titleEn)) {
            $youthEducationBuilder->where('skills.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $youthEducationBuilder->where('skills.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $youthEducations */

        if (!is_null($paginate) || !is_null($limit)) {
            $limit = $limit ?: 10;
            $youthEducations = $youthEducationBuilder->paginate($limit);
            $paginateData = (object)$youthEducations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $youthEducations = $youthEducationBuilder->get();
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
     * @param YouthEducation $youthEducation
     * @return bool
     */
    public function restore(YouthEducation $youthEducation): bool
    {
        return $youthEducation->restore();
    }

    /**
     * @param YouthEducation $youthEducation
     * @return bool
     */
    public function forceDelete(YouthEducation $youthEducation): bool
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
                'message' => Examination::findOrFail($request->examination_id)->title_en ." examination already added your profile"
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
            'result' => [
                'required',
                'integer',
                'min:1'
            ],
            'cgpa' => [
                'nullable',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];

        if($request->result == YouthEducation::GPA_OUT_OF_FOUR || $request->result == YouthEducation::GPA_OUT_OF_FIVE){
            if($request->result == YouthEducation::GPA_OUT_OF_FOUR){
                $rules['cgpa'] = [
                    'nullable',
                    'numeric',
                    'between:1.00,4.00'
                ];
            }else{
                $rules['cgpa'] = [
                    'nullable',
                    'numeric',
                    'between:1.00,5.00'
                ];
            }
        }else{
            $rules['cgpa'] = [
                'nullable',
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
            'institute_name' => [
                'required',
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
