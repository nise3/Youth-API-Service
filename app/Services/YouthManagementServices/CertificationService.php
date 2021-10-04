<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Certification;
use App\Models\JobExperience;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class CertificationService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */

    public function getAllCertifications(array $request, Carbon $startTime): array
    {
        $youthId = $request['youth_id'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $certificationBuilder */
        $certificationBuilder = JobExperience::select([
            'certification.id',
            'certification.youth_id',
            'certification.certification_name',
            'certification.certification_name_en',
            'certification.institute_name',
            'certification.institute_name_en',
            'certification.location',
            'certification.location_en',
            'certification.start_date',
            'certification.end_date',
            'certification.certificate_file_path',
            'certification.row_status',
            'certification.created_at',
            'certification.updated_at'
        ]);

        if (is_numeric($youthId)) {
            $certificationBuilder->where('certification.youth_id', $youthId);
        }

        $certificationBuilder->orderBy('certification.id', $order);

        if (is_numeric($rowStatus)) {
            $certificationBuilder->where('certification.row_status', $rowStatus);
        }

        /** @var Collection $certifications */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
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
    public function getOneCertification(int $id, Carbon $startTime): array
    {
        /** @var Builder $certificationBuilder */
        $certificationBuilder = JobExperience::select([
            'certification.id',
            'certification.youth_id',
            'certification.certification_name',
            'certification.certification_name_en',
            'certification.institute_name',
            'certification.institute_name_en',
            'certification.location',
            'certification.location_en',
            'certification.start_date',
            'certification.end_date',
            'certification.certificate_file_path',
            'certification.row_status',
            'certification.created_at',
            'certification.updated_at'
        ]);
        $certificationBuilder->where('certification.id', $id);

        /** @var JobExperience $certification */
        $certification = $certificationBuilder->first();

        return [
            "data" => $certification ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
    }

    /**
     * @param array $data
     * @return certification
     */
    public function store(array $data): certification
    {
        $certification = new Certification();
        $certification->fill($data);
        $certification->save();
        return $certification;
    }

    /**
     * @param Certification $certification
     * @param array $data
     * @return Certification
     */
    public function update(Certification $certification, array $data): Certification
    {
        $certification->fill($data);
        $certification->save();
        return $certification;
    }

    /**
     * @param Certification $certification
     * @return bool
     */
    public function destroy(Certification $certification): bool
    {
        return $certification->delete();
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
            'youth_id' => 'required|min:1',
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
            'certification_name' => [
                'required',
                'string',
                'max:300'
            ],
            'certification_name_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'institute_name' => [
                'required',
                'string',
                'max:300'
            ],
            'institute_name_en' => [
                'nullable',
                'string',
                'max:300'
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
            'start_date' => [
                'date',
                'required',
            ],
            'end_date' => [
                'date',
                'nullable',
                'after:start_date'
            ],
            'certificate_file_path' => [
                'string',
                'max:500'
            ]
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }

}
