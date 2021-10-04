<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Reference;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class ReferenceService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getReferenceList(array $request, Carbon $startTime): array
    {
        $youthId = $request['youth_id'];
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var  Builder $referenceBuilder */
        $referenceBuilder = Reference::select([
            'references.id',
            'references.youth_id',
            'references.referrer_first_name',
            'references.referrer_first_name_en',
            'references.referrer_last_name',
            'references.referrer_last_name_en',
            'references.referrer_organization_name',
            'references.referrer_organization_name_en',
            'references.referrer_designation',
            'references.referrer_designation_en',
            'references.referrer_address',
            'references.referrer_address_en',
            'references.referrer_email',
            'references.referrer_mobile',
            'references.referrer_relation',
            'references.referrer_relation_en',
            'references.row_status',
            'references.created_at',
            'references.updated_at',
        ]);
        $referenceBuilder->orderBy('reference.id', $order);

        if (is_numeric($youthId)) {
            $referenceBuilder->where('references.youth_id', $youthId);
        }

        if (is_numeric($rowStatus)) {
            $referenceBuilder->where('references.row_status', $rowStatus);
        }

        /** @var Collection $references */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $references = $referenceBuilder->paginate($pageSize);
            $paginateData = (object)$references->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $references = $referenceBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $references->toArray()['data'] ?? $references->toArray();
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
    public function getOneReference(int $id, Carbon $startTime): array
    {
        /** @var  Builder $referenceBuilder */
        $referenceBuilder = Reference::select([
            'references.id',
            'references.youth_id',
            'references.referrer_first_name',
            'references.referrer_first_name_en',
            'references.referrer_last_name',
            'references.referrer_last_name_en',
            'references.referrer_organization_name',
            'references.referrer_organization_name_en',
            'references.referrer_designation',
            'references.referrer_designation_en',
            'references.referrer_address',
            'references.referrer_address_en',
            'references.referrer_email',
            'references.referrer_mobile',
            'references.referrer_relation',
            'references.referrer_relation_en',
            'references.row_status',
            'references.created_at',
            'references.updated_at',
        ]);
        $referenceBuilder->where('references.id', $id);

        /** @var Reference $reference */
        $reference = $referenceBuilder->first();

        return [
            "data" => $reference ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
    }


    /**
     * @param array $data
     * @return Reference
     */
    public function store(array $data): Reference
    {
        $reference = new Reference();
        $reference->fill($data);
        $reference->save();
        return $reference;
    }


    /**
     * @param Reference $reference
     * @param array $data
     * @return Reference
     */
    public function update(Reference $reference, array $data): Reference
    {
        $reference->fill($data);
        $reference->save();
        return $reference;
    }


    /**
     * @param Reference $reference
     * @return bool
     */
    public function destroy(Reference $reference): bool
    {
        return $reference->delete();
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
                'message' => 'Row status must be within 1 or 0'
            ]
        ];
        $rules = [
            'youth_id' => [
                'required',
                'int',
                'exists:youths,id'
            ],
            'referrer_first_name' => [
                'required',
                'string',
                'max:150',
                'min:2'
            ],
            'referrer_first_name_en' => [
                'nullable',
                'string',
                'max:150',
                'min:2'
            ],
            'referrer_last_name' => [
                'required',
                'string',
                'max:150',
                'min:2'
            ],
            'referrer_last_name_en' => [
                'nullable',
                'string',
                'max:150',
                'min:2'
            ],
            'referrer_designation' => [
                'required',
                'string',
                'max:200',
                'min:2'
            ],
            'referrer_designation_en' => [
                'nullable',
                'string',
                'max:200',
                'min:2'
            ],
            'referrer_address' => [
                'required',
                'string',
                'max:600',
                'min:2'
            ],
            'referrer_address_en' => [
                'nullable',
                'string',
                'max:600',
                'min:2'
            ],
            'referrer_email' => [
                'required',
                'string',
            ],
            'referrer_mobile' => [
                'required',
                'string',
            ],
            'referrer_relation' => [
                'required',
                'string',
            ],
            'referrer_relation_en' => [
                'nullable',
                'string',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ]
        ];
        return Validator::make($request->all(), $rules, $customMessage);
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

}
