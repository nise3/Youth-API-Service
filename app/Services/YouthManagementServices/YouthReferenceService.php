<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Reference;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class YouthReferenceService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getReferenceList(array $request, Carbon $startTime): array
    {
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
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
            'references.created_at',
            'references.updated_at',
        ]);
        $referenceBuilder->orderBy('references.id', $order);

        if (is_int(Auth::id())) {
            $referenceBuilder->where('references.youth_id', Auth::id());
        }

        /** @var Collection $references */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
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
                'email'
            ],
            'referrer_mobile' => [
                'required',
                'string',
                BaseModel::MOBILE_REGEX
            ],
            'referrer_relation' => [
                'required',
                'string',
            ],
            'referrer_relation_en' => [
                'nullable',
                'string',
            ]
        ];
        return Validator::make($request->all(), $rules);
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

}
