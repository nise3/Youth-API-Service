<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\YouthCertification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class YouthCertificationService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */

    public function getAllCertifications(array $request, Carbon $startTime): array
    {
        $youthId = $request['youth_id'] ?? Auth::id();
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? BaseModel::ROW_ORDER_ASC;

        /** @var Builder $certificationBuilder */
        $certificationBuilder = YouthCertification::select([
            'youth_certifications.id',
            'youth_certifications.youth_id',
            'youth_certifications.certification_name',
            'youth_certifications.certification_name_en',
            'youth_certifications.institute_name',
            'youth_certifications.institute_name_en',
            'youth_certifications.location',
            'youth_certifications.location_en',
            'youth_certifications.start_date',
            'youth_certifications.end_date',
            'youth_certifications.certificate_file_path',
            'youth_certifications.created_at',
            'youth_certifications.updated_at'
        ]);

        if (is_integer($youthId)) {
            $certificationBuilder->where('youth_certifications.youth_id', $youthId);
        }

        $certificationBuilder->orderBy('youth_certifications.id', $order);

        $response = [];
        /** @var Collection $certifications */

        if (is_int($paginate) || is_int($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $certifications = $certificationBuilder->paginate($pageSize);
            $paginateData = (object)$certifications->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $certifications = $certificationBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $certifications->toArray()['data'] ?? $certifications->toArray();
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());

        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneCertification(int $id, Carbon $startTime): array
    {
        /** @var Builder $certificationBuilder */
        $certificationBuilder = YouthCertification::select([
            'youth_certifications.id',
            'youth_certifications.youth_id',
            'youth_certifications.certification_name',
            'youth_certifications.certification_name_en',
            'youth_certifications.institute_name',
            'youth_certifications.institute_name_en',
            'youth_certifications.location',
            'youth_certifications.location_en',
            'youth_certifications.start_date',
            'youth_certifications.end_date',
            'youth_certifications.certificate_file_path',
            'youth_certifications.created_at',
            'youth_certifications.updated_at'
        ]);

        $certificationBuilder->where('youth_certifications.id', $id);

        /** @var YouthCertification $certification */
        $certification = $certificationBuilder->firstOrFail();

        return [
            "data" => $certification,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];
    }

    /**
     * @param array $data
     * @return YouthCertification
     * @throws \Throwable
     */
    public function store(array $data): YouthCertification
    {
        /** @var YouthCertification $certification */
        $certification = app(YouthCertification::class);
        $certification->fill($data);
        throw_if($certification->save(), 'RuntimeException', 'Youth Certification has not been Saved to db.', 500);
        return $certification;
    }

    /**
     * @param YouthCertification $certification
     * @param array $data
     * @return YouthCertification
     * @throws \Throwable
     */
    public function update(YouthCertification $certification, array $data): YouthCertification
    {
        $certification->fill($data);
        throw_if($certification->save(), 'RuntimeException', 'Youth Certification has not been updated to db.', 500);
        return $certification;
    }

    /**
     * @param YouthCertification $certification
     * @return bool
     * @throws \Throwable
     */
    public function destroy(YouthCertification $certification): bool
    {
        throw_if($certification->delete(), 'RuntimeException', 'Youth Certification has not been deleted.', 500);
        return true;
    }

    /**
     * @param YouthCertification $certification
     * @return bool
     * @throws \Throwable
     */
    public function restore(YouthCertification $certification): bool
    {
        throw_if($certification->restore(), 'RuntimeException', 'Youth Certification has not been restored.', 500);
        return true;
    }

    /**
     * @param YouthCertification $certification
     * @return bool
     * @throws \Throwable
     */
    public function forceDelete(YouthCertification $certification): bool
    {
        throw_if($certification->forceDelete(), 'RuntimeException', 'Youth Certification has not been successfully deleted forcefully.', 500);
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

        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return Validator::make($request->all(), [
            'page' => 'integer|gt:0',
            'page_size' => 'integer|gt:0',
            'order' => [
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
        $rules = [
            'youth_id' => [
                'required',
                'int',
                'exists:youths,id,deleted_at,NULL',
            ],
            'certification_name' => [
                'required',
                'string',
                'max:600'
            ],
            'certification_name_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'institute_name' => [
                'required',
                'string',
                'max:600'
            ],
            'institute_name_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'location' => [
                'required',
                'string',
                'max:1000'
            ],
            'location_en' => [
                'nullable',
                'string',
                'max:500'
            ],
            'start_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d'
            ],
            'end_date' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->exists('start_date') && $request->filled('start_date');
                }),
                'date',
                'date_format:Y-m-d',
                'after:start_date',
            ],
            'certificate_file_path' => [
                'nullable',
                'string',
                'max:600'
            ]
        ];
        return Validator::make($request->all(), $rules);
    }

}
