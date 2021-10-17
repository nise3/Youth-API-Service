<?php


namespace App\Services\YouthManagementServices;


use App\Models\BaseModel;
use App\Models\YouthJobExperience;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class YouthJobExperienceService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */

    public function getAllJobExperiences(array $request, Carbon $startTime): array
    {
        $companyNameEn = $request['company_name_en'] ?? "";
        $companyNameBn = $request['company_name_bn'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $jobExperienceBuilder */
        $jobExperienceBuilder = YouthJobExperience::select([
            'job_experiences.id',
            'job_experiences.company_name',
            'job_experiences.company_name_en',
            'job_experiences.position',
            'job_experiences.position_en',
            'job_experiences.youth_id',
            'job_experiences.location',
            'job_experiences.location_en',
            'job_experiences.job_responsibilities',
            'job_experiences.job_responsibilities_en',
            'job_experiences.start_date',
            'job_experiences.end_date',
            'job_experiences.is_currently_work',
            'job_experiences.row_status',
            'job_experiences.created_at',
            'job_experiences.updated_at'
        ]);

        if (is_numeric(Auth::id())) {
            $jobExperienceBuilder->where('job_experiences.youth_id', Auth::id());
        }

        $jobExperienceBuilder->orderBy('job_experiences.id', $order);

        if (is_numeric($rowStatus)) {
            $jobExperienceBuilder->where('job_experiences.row_status', $rowStatus);
        }
        if (!empty($companyNameEn)) {
            $jobExperienceBuilder->where('job_experiences.company_name_en', 'like', '%' . $companyNameEn . '%');
        }
        if (!empty($companyNameBn)) {
            $jobExperienceBuilder->where('job_experiences.company_name_bn', 'like', '%' . $companyNameBn . '%');
        }

        /** @var Collection $jobExperiences */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $jobExperiences = $jobExperienceBuilder->paginate($pageSize);
            $paginateData = (object)$jobExperiences->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $jobExperiences = $jobExperienceBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $jobExperiences->toArray()['data'] ?? $jobExperiences->toArray();
        $response['response_status'] = [
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
    public function getOneJobExperience(int $id, Carbon $startTime): array
    {
        /** @var Builder $jobExperienceBuilder */
        $jobExperienceBuilder = YouthJobExperience::select([
            'job_experiences.id',
            'job_experiences.company_name',
            'job_experiences.company_name_en',
            'job_experiences.position',
            'job_experiences.position_en',
            'job_experiences.youth_id',
            'job_experiences.location',
            'job_experiences.location_en',
            'job_experiences.job_responsibilities',
            'job_experiences.job_responsibilities_en',
            'job_experiences.start_date',
            'job_experiences.end_date',
            'job_experiences.employment_type_id',
            'job_experiences.is_currently_work',
            'job_experiences.row_status',
            'job_experiences.created_at',
            'job_experiences.updated_at'
        ]);
        $jobExperienceBuilder->where('job_experiences.id', $id);

        /** @var YouthJobExperience $jobExperience */
        $jobExperience = $jobExperienceBuilder->first();

        return [
            "data" => $jobExperience ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];

    }

    /**
     * @param array $data
     * @return YouthJobExperience
     */
    public function store(YouthJobExperience $jobExperience, array $data): YouthJobExperience
    {
        $jobExperience->fill($data);
        $jobExperience->save();
        return $jobExperience;
    }

    /**
     * @param YouthJobExperience $jobExperience
     * @param array $data
     * @return YouthJobExperience
     */
    public function update(YouthJobExperience $jobExperience, array $data): YouthJobExperience
    {
        $jobExperience->fill($data);
        $jobExperience->save();
        return $jobExperience;
    }

    /**
     * @param YouthJobExperience $jobExperience
     * @return bool
     */
    public function destroy(YouthJobExperience $jobExperience): bool
    {
        return $jobExperience->delete();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be either ASC or DESC',
            ],
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];

        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return Validator::make($request->all(), [
            'page' => 'numeric|gt:0',
            'company_name' => 'nullable|max:300|min:2',
            'company_name_en' => 'nullable|max:300|min:2',
            'location' => 'nullable|max:300|min:2',
            'location_en' => 'nullable|max:300|min:2',
            'position' => 'nullable|max:300|min:2',
            'position_en' => 'nullable|max:300|min:2',
            'page_size' => 'numeric|gt:0',
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

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be either 1 or 0'
            ]
        ];
        $rules = [
            'employment_type_id' => [
                'required',
                'int',
                'min:1'
            ],
            'company_name' => [
                'required',
                'string',
                'max:300'
            ],
            'company_name_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'position' => [
                'required',
                'string',
                'max:150'
            ],
            'position_en' => [
                'nullable',
                'string',
                'max:150'
            ],
            'location' => [
                'required',
                'string',
                'max:300'
            ],
            'location_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'job_responsibilities' => [
                'nullable',
                'string',
            ],
            'job_responsibilities_en' => [
                'nullable',
                'string',
            ],
            'start_date' => [
                'date',
                'required',
            ],
            'end_date' => [
                'date',
                'nullable',
                'after:start_date'
            ],
            'is_currently_work' => [
                'numeric',
                Rule::in([BaseModel::CURRENTLY_NOT_WORKING, BaseModel::CURRENTLY_WORKING])
            ]
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }
}
