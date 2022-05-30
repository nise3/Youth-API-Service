<?php

namespace App\Services\YouthManagementServices;


use App\Models\BaseModel;
use App\Models\YouthJobExperience;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $order = $request['order'] ?? "ASC";

        /** @var Builder $jobExperienceBuilder */
        $jobExperienceBuilder = YouthJobExperience::select([
            'youth_job_experiences.id',
            'youth_job_experiences.company_name',
            'youth_job_experiences.company_name_en',
            'youth_job_experiences.position',
            'youth_job_experiences.position_en',
            'youth_job_experiences.youth_id',
            'youth_job_experiences.location',
            'youth_job_experiences.location_en',
            'youth_job_experiences.job_responsibilities',
            'youth_job_experiences.job_responsibilities_en',
            'youth_job_experiences.start_date',
            'youth_job_experiences.end_date',
            'youth_job_experiences.is_currently_working',
            'youth_job_experiences.created_at',
            'youth_job_experiences.updated_at'

        ])->acl();

        $jobExperienceBuilder->orderBy('youth_job_experiences.id', $order);

        if (!empty($companyNameEn)) {
            $jobExperienceBuilder->where('youth_job_experiences.company_name_en', 'like', '%' . $companyNameEn . '%');
        }
        if (!empty($companyNameBn)) {
            $jobExperienceBuilder->where('youth_job_experiences.company_name_bn', 'like', '%' . $companyNameBn . '%');
        }

        $jobExperienceBuilder->with('areaOfExperiences');
        $jobExperienceBuilder->with('areaOfBusinesses');

        /** @var Collection $jobExperiences */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
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
     * @return YouthJobExperience
     */
    public function getOneJobExperience(int $id): YouthJobExperience
    {
        /** @var Builder|YouthJobExperience $jobExperienceBuilder */
        $jobExperienceBuilder = YouthJobExperience::select([
            'youth_job_experiences.id',
            'youth_job_experiences.company_name',
            'youth_job_experiences.company_name_en',
            'youth_job_experiences.position',
            'youth_job_experiences.position_en',
            'youth_job_experiences.youth_id',
            'youth_job_experiences.location',
            'youth_job_experiences.location_en',
            'youth_job_experiences.job_responsibilities',
            'youth_job_experiences.job_responsibilities_en',
            'youth_job_experiences.start_date',
            'youth_job_experiences.end_date',
            'youth_job_experiences.employment_type_id',
            'youth_job_experiences.is_currently_working',
            'youth_job_experiences.created_at',
            'youth_job_experiences.updated_at'
        ]);
        $jobExperienceBuilder->where('youth_job_experiences.id', $id);
        $jobExperienceBuilder->with('areaOfExperiences');
        $jobExperienceBuilder->with('areaOfBusinesses');

        /** @var YouthJobExperience $jobExperience */
        $jobExperience = $jobExperienceBuilder->firstOrFail();

        return $jobExperience;

    }

    /**
     * @param YouthJobExperience $jobExperience
     * @param array $data
     * @return YouthJobExperience
     * @throws \Throwable
     */
    public function store(YouthJobExperience $jobExperience, array $data): YouthJobExperience
    {
        $jobExperience->fill($data);
        throw_if(!$jobExperience->save(), 'RuntimeException', 'Youth Job Experience has not been Saved to db.', 500);
        return $jobExperience;
    }

    /**
     * @param YouthJobExperience $jobExperience
     * @param array $data
     * @return YouthJobExperience
     * @throws \Throwable
     */
    public function update(YouthJobExperience $jobExperience, array $data): YouthJobExperience
    {
        $jobExperience->fill($data);
        throw_if(!$jobExperience->save(), 'RuntimeException', 'Youth Job Experience has not been deleted.', 500);
        return $jobExperience;
    }

    /**
     * @param YouthJobExperience $jobExperience
     * @return bool
     * @throws \Throwable
     */
    public function destroy(YouthJobExperience $jobExperience): bool
    {
        throw_if(!$jobExperience->delete(), 'RuntimeException', 'Youth Job Experience has not been deleted.', 500);
        return true;
    }

    /**
     * @param YouthJobExperience $jobExperience
     * @return bool
     * @throws \Throwable
     */
    public function restore(YouthJobExperience $jobExperience): bool
    {
        throw_if(!$jobExperience->restore(), 'RuntimeException', 'Youth Job Experience has not been restored.', 500);
        return true;
    }

    /**
     * @param YouthJobExperience $jobExperience
     * @return bool
     * @throws \Throwable
     */
    public function forceDelete(YouthJobExperience $jobExperience): bool
    {
        throw_if(!$jobExperience->forceDelete(), 'RuntimeException', 'Youth Job Experience has not been successfully deleted forcefully.', 500);
        return true;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'page' => 'integer|gt:0',
            'company_name' => 'nullable|max:600|min:2',
            'company_name_en' => 'nullable|max:300|min:2',
            'location' => 'nullable|max:600|min:2',
            'location_en' => 'nullable|max:300|min:2',
            'position' => 'nullable|max:300|min:2',
            'position_en' => 'nullable|max:150|min:2',
            'page_size' => 'nullable|integer|gt:0',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]
        ], $customMessage);
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();

        if (!empty($data["area_of_experiences"])) {
            $data["area_of_experiences"] = is_array($data['area_of_experiences']) ? $data['area_of_experiences'] : explode(',', $data['area_of_experiences']);
        }
        if (!empty($data["area_of_businesses"])) {
            $data["area_of_businesses"] = is_array($data['area_of_businesses']) ? $data['area_of_businesses'] : explode(',', $data['area_of_businesses']);
        }
        $customMessage = [
            'is_currently_working.in' => 'Row status must be either 1 or 0. [30000]'
        ];
        $rules = [
            'youth_id' => [
                'required',
                'int',
                'exists:youths,id,deleted_at,NULL',
            ],
            'employment_type_id' => [
                'required',
                'int',
                'min:1',
                'exists:employment_types,id,deleted_at,NULL',
            ],
            'company_name' => [
                'required',
                'string',
                'max:600'
            ],
            'company_name_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'position' => [
                'required',
                'string',
                'max:300'
            ],
            'position_en' => [
                'nullable',
                'string',
                'max:150'
            ],
            'location' => [
                'required',
                'string',
                'max:600'
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
            "area_of_experiences" => [
                "required",
                "array",
            ],
            "area_of_experiences.*" => [
                "required",
                "distinct",
                "integer",
                "exists:area_of_experiences,id,deleted_at,NULL",
            ],
            "area_of_businesses" => [
                "required",
                "array",
            ],
            "area_of_businesses.*" => [
                "integer",
                "distinct",
                "required",
                "exists:area_of_business,id,deleted_at,NULL",
            ],
            'start_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                function ($attr, $value, $failed) {
                    if (Carbon::parse($value)->greaterThan(Carbon::now())) {
                        $failed('Start date must be less then or equal today.[100000]');
                    }
                }
            ],
            'is_currently_working' => [
                'required',
                'integer',
                Rule::in([YouthJobExperience::CURRENTLY_NOT_WORKING, YouthJobExperience::CURRENTLY_WORKING])
            ],
            'end_date' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->filled('is_currently_working') && $request->get('is_currently_working') == BaseModel::FALSE;
                }),
                'nullable',
                'date',
                'date_format:Y-m-d',
                function ($attr, $value, $failed) use ($data) {
                    if (Carbon::parse($value)->lessThan($data['start_date'])) {
                        $failed('End date must be greater than or equal start date.[200000]');
                    }
                },
            ],

        ];
        return Validator::make($data, $rules, $customMessage);
    }

    /**
     * @param YouthJobExperience $jobExperience
     * @param array $areaOfExperience
     */
    public function syncWithAreaOfExperience(YouthJobExperience $jobExperience, array $areaOfExperience)
    {
//        $jobExperience->areaOfExperiences()->syncWithPivotValues($areaOfExperience, ['youth_id' => $jobExperience->youth_id]);
        DB::table('youth_job_experience_area_of_experiences')->where('youth_job_experience_id', $jobExperience->id)->delete();
        foreach ($areaOfExperience as $item) {
            DB::table('youth_job_experience_area_of_experiences')->insert(
                [
                    'youth_id' => $jobExperience->youth_id,
                    'youth_job_experience_id' => $jobExperience->id,
                    'area_of_experience_id' => $item
                ]
            );
        }
    }

    /**
     * @param YouthJobExperience $jobExperience
     * @param array $areaOfBusiness
     */
    public function syncWithAreaOfBusiness(YouthJobExperience $jobExperience, array $areaOfBusiness)
    {
//        $jobExperience->areaOfBusinesses()->syncWithPivotValues($areaOfBusiness, ['youth_id' => $jobExperience->youth_id]);
        DB::table('youth_job_experience_area_of_businesses')->where('youth_job_experience_id', $jobExperience->id)->delete();
        foreach ($areaOfBusiness as $item) {
            DB::table('youth_job_experience_area_of_businesses')->insert(
                [
                    'youth_id' => $jobExperience->youth_id,
                    'youth_job_experience_id' => $jobExperience->id,
                    'area_of_business_id' => $item
                ]
            );
        }
    }
}
