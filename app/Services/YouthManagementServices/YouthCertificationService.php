<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Certification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class YouthCertificationService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */

    public function getAllCertifications(array $request, Carbon $startTime): array
    {
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? BaseModel::ROW_ORDER_ASC;

        /** @var Builder $certificationBuilder */
        $certificationBuilder = Certification::select([
            'certifications.id',
            'certifications.youth_id',
            'certifications.certification_name',
            'certifications.certification_name_en',
            'certifications.institute_name',
            'certifications.institute_name_en',
            'certifications.location',
            'certifications.location_en',
            'certifications.start_date',
            'certifications.end_date',
            'certifications.certificate_file_path',
            'certifications.created_at',
            'certifications.updated_at'
        ]);

        if (is_int(Auth::id())) {
            $certificationBuilder->where('certifications.youth_id', Auth::id());
        }

        $certificationBuilder->orderBy('certifications.id', $order);


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
        $certificationBuilder = Certification::select([
            'certifications.id',
            'certifications.youth_id',
            'certifications.certification_name',
            'certifications.certification_name_en',
            'certifications.institute_name',
            'certifications.institute_name_en',
            'certifications.location',
            'certifications.location_en',
            'certifications.start_date',
            'certifications.end_date',
            'certifications.certificate_file_path',
            'certifications.created_at',
            'certifications.updated_at'
        ]);
        $certificationBuilder->where('certifications.id', $id);

        /** @var Certification $certification */
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
    public function store(array $data): Certification
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
            ]
        ];

        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return Validator::make($request->all(), [
            'page' => 'int|gt:0',
            'page_size' => 'int|gt:0',
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
                'nullable'
            ],
            'end_date' => [
                'date',
                'nullable',
                'after:start_date',
                Rule::requiredIf(function () use ($request) {
                    return (!empty($request->start_date));
                })
            ],
            'youth_id' => [
                'required',
                'min:1',
                'exists:youths,id',
                'int'
            ],
            'certificate_file_path' => [
                'nullable',
                'string',
                'max:500',
                'required'
            ]
        ];
        return Validator::make($request->all(), $rules);
    }

}
