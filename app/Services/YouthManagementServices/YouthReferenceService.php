<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\YouthReference;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 *  YouthReferenceService for YouthReference Model
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
        $youthId = $request['youth_id'] ?? Auth::id();
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var  Builder $referenceBuilder */
        $referenceBuilder = YouthReference::select([
            'youth_references.id',
            'youth_references.youth_id',
            'youth_references.referrer_first_name',
            'youth_references.referrer_first_name_en',
            'youth_references.referrer_last_name',
            'youth_references.referrer_last_name_en',
            'youth_references.referrer_organization_name',
            'youth_references.referrer_organization_name_en',
            'youth_references.referrer_designation',
            'youth_references.referrer_designation_en',
            'youth_references.referrer_address',
            'youth_references.referrer_address_en',
            'youth_references.referrer_email',
            'youth_references.referrer_mobile',
            'youth_references.referrer_relation',
            'youth_references.referrer_relation_en',
            'youth_references.created_at',
            'youth_references.updated_at',
        ]);
        $referenceBuilder->orderBy('youth_references.id', $order);

        if (is_numeric($youthId)) {
            $referenceBuilder->where('youth_references.youth_id', $youthId);
        }

        $response = [];
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
     * @return YouthReference
     */
    public function getOneReference(int $id): YouthReference
    {
        /** @var  Builder|YouthReference $referenceBuilder */
        $referenceBuilder = YouthReference::select([
            'youth_references.id',
            'youth_references.youth_id',
            'youth_references.referrer_first_name',
            'youth_references.referrer_first_name_en',
            'youth_references.referrer_last_name',
            'youth_references.referrer_last_name_en',
            'youth_references.referrer_organization_name',
            'youth_references.referrer_organization_name_en',
            'youth_references.referrer_designation',
            'youth_references.referrer_designation_en',
            'youth_references.referrer_address',
            'youth_references.referrer_address_en',
            'youth_references.referrer_email',
            'youth_references.referrer_mobile',
            'youth_references.referrer_relation',
            'youth_references.referrer_relation_en',
            'youth_references.created_at',
            'youth_references.updated_at',
        ]);

        $referenceBuilder->where('youth_references.id', $id);

        /** @var YouthReference $reference */
        return $referenceBuilder->firstOrFail();
    }


    /**
     * @param array $data
     * @return YouthReference
     * @throws \Throwable
     */
    public function store(array $data): YouthReference
    {
        /** @var YouthReference $youthReference */
        $youthReference = app(YouthReference::class);
        $youthReference->fill($data);
        throw_if(!$youthReference->save(), 'RuntimeException', 'Youth Reference has not been Saved to db.', 500);
        return $youthReference;
    }


    /**
     * @param YouthReference $reference
     * @param array $data
     * @return YouthReference
     * @throws \Throwable
     */
    public function update(YouthReference $reference, array $data): YouthReference
    {
        $reference->fill($data);
        throw_if(!$reference->save(), 'RuntimeException', 'Youth Reference has not been updated to db.', 500);
        return $reference;
    }


    /**
     * @param YouthReference $reference
     * @return bool
     * @throws \Throwable
     */
    public function destroy(YouthReference $reference): bool
    {
        throw_if(!$reference->delete(), 'RuntimeException', 'Youth Reference has not been deleted.', 500);
        return true;
    }

    /**
     * @param YouthReference $reference
     * @return bool
     * @throws \Throwable
     */
    public function restore(YouthReference $reference): bool
    {
        throw_if(!$reference->restore(), 'RuntimeException', 'Youth Reference has not been restored.', 500);
        return true;
    }

    /**
     * @param YouthReference $reference
     * @return bool
     * @throws \Throwable
     */
    public function forceDelete(YouthReference $reference): bool
    {
        throw_if(!$reference->forceDelete(), 'RuntimeException', 'Youth Reference has not been successfully deleted forcefully.', 500);
        return true;
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
            'referrer_first_name' => [
                'required',
                'string',
                'max:300',
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
                'max:300',
                'min:2'
            ],
            'referrer_last_name_en' => [
                'nullable',
                'string',
                'max:150',
                'min:2'
            ],
            'referrer_organization_name' => [
                'required',
                'string',
                'max:600',
                'min:2'
            ],
            'referrer_organization_name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'referrer_designation' => [
                'required',
                'string',
                'max:500',
                'min:2'
            ],
            'referrer_designation_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'referrer_address' => [
                'required',
                'string',
                'max:1200',
                'min:2'
            ],
            'referrer_address_en' => [
                'nullable',
                'string',
                'max:600',
                'min:2'
            ],
            'referrer_email' => [
                'nullable',
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
                'max:600',
                'min:2'
            ],
            'referrer_relation_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
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
            'order.in' =>  'Order must be either ASC or DESC. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
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
