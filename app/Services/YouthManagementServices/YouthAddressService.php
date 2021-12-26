<?php


namespace App\Services\YouthManagementServices;


use App\Models\BaseModel;
use App\Models\YouthAddress;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class YouthAddressService
 * @package App\Services\YouthManagementServices
 */
class YouthAddressService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAddressList(array $request, Carbon $startTime): array
    {
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $youthAddressBuilder */
        $youthAddressBuilder = YouthAddress::select([
            'youth_addresses.id',
            'youth_addresses.youth_id',
            'youth_addresses.address_type',
            'youth_addresses.loc_division_id',
            'loc_divisions.title_en as loc_division_title_en',
            'loc_divisions.title as loc_division_title',
            'youth_addresses.loc_district_id',
            'loc_districts.title_en as loc_district_title_en',
            'loc_districts.title as loc_district_title',
            'youth_addresses.loc_upazila_id',
            'loc_upazilas.title_en as loc_upazila_title_en',
            'loc_upazilas.title as loc_upazila_title',
            'youth_addresses.village_or_area',
            'youth_addresses.village_or_area_en',
            'youth_addresses.house_n_road',
            'youth_addresses.house_n_road_en',
            'youth_addresses.zip_or_postal_code',
            'youth_addresses.created_at',
            'youth_addresses.updated_at'

        ])->acl();

        $youthAddressBuilder->orderBy('youth_addresses.id', $order);

        $youthAddressBuilder->leftjoin('loc_divisions', function ($join) {
            $join->on('youth_addresses.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');
        });
        $youthAddressBuilder->leftjoin('loc_districts', function ($join) {
            $join->on('youth_addresses.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });
        $youthAddressBuilder->leftjoin('loc_upazilas', function ($join) {
            $join->on('youth_addresses.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $response = [];
        /** @var Collection $youthAddresses */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $youthAddresses = $youthAddressBuilder->paginate($pageSize);
            $paginateData = (object)$youthAddresses->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $youthAddresses = $youthAddressBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $youthAddresses->toArray()['data'] ?? $youthAddresses->toArray();
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());

        return $response;
    }

    /**
     * @param int $id
     * @return YouthAddress
     */
    public function getOneYouthAddress(int $id): YouthAddress
    {
        /** @var YouthAddress|Builder $youthAddressBuilder */
        $youthAddressBuilder = YouthAddress::select([
            'youth_addresses.id',
            'youth_addresses.youth_id',
            'youth_addresses.address_type',
            'youth_addresses.loc_division_id',
            'loc_divisions.title_en as loc_division_title_en',
            'loc_divisions.title as loc_division_title',
            'youth_addresses.loc_district_id',
            'loc_districts.title_en as loc_district_title_en',
            'loc_districts.title as loc_district_title',
            'youth_addresses.loc_upazila_id',
            'loc_upazilas.title_en as loc_upazila_title_en',
            'loc_upazilas.title as loc_upazila_title',
            'youth_addresses.village_or_area',
            'youth_addresses.village_or_area_en',
            'youth_addresses.house_n_road',
            'youth_addresses.house_n_road_en',
            'youth_addresses.zip_or_postal_code',
            'youth_addresses.created_at',
            'youth_addresses.updated_at'
        ]);

        $youthAddressBuilder->leftjoin('loc_divisions', function ($join) {
            $join->on('youth_addresses.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');
        });
        $youthAddressBuilder->leftjoin('loc_districts', function ($join) {
            $join->on('youth_addresses.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });
        $youthAddressBuilder->leftjoin('loc_upazilas', function ($join) {
            $join->on('youth_addresses.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $youthAddressBuilder->where('youth_addresses.id', $id);

        return $youthAddressBuilder->firstOrFail();

    }

    /**
     * @param array $data
     * @return YouthAddress
     * @throws \Throwable
     */
    public function store(array $data): YouthAddress
    {
        /** @var YouthAddress $address */
        $address = app(YouthAddress::class);
        $address->fill($data);
        throw_if(!$address->save(), 'RuntimeException', 'Youth Address has not been Saved to db.', 500);
        return $address;
    }

    /**
     * @param YouthAddress $youthAddress
     * @param array $data
     * @return YouthAddress
     * @throws \Throwable
     */
    public function update(YouthAddress $youthAddress, array $data): YouthAddress
    {
        $youthAddress->fill($data);
        throw_if(!$youthAddress->save(), 'RuntimeException', 'Youth Address has not been updated to db.', 500);
        return $youthAddress;
    }

    /**
     * @param YouthAddress $youthAddress
     * @return bool
     * @throws \Throwable
     */
    public function destroy(YouthAddress $youthAddress): bool
    {
        throw_if(!$youthAddress->delete(), 'RuntimeException', 'Youth Address has not been deleted.', 500);
        return true;
    }

    /**
     * @param YouthAddress $youthAddress
     * @return bool
     * @throws \Throwable
     */
    public function restore(YouthAddress $youthAddress): bool
    {
        throw_if(!$youthAddress->restore(), 'RuntimeException', 'Youth Address has not been restored.', 500);
        return true;
    }

    /**
     * @param YouthAddress $youthAddress
     * @return bool
     * @throws \Throwable
     */
    public function forceDelete(YouthAddress $youthAddress): bool
    {
        throw_if(!$youthAddress->forceDelete(), 'RuntimeException', 'Youth Address has not been successfully deleted forcefully.', 500);
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
                'exists:youths,id,deleted_at,NULL',
                'int',
            ],
            'address_type' => [
                'required',
                'int',
                Rule::in([YouthAddress::ADDRESS_TYPE_PRESENT, YouthAddress::ADDRESS_TYPE_PERMANENT, YouthAddress::ADDRESS_TYPE_OTHERS])
            ],
            "loc_division_id" => [
                "required",
                "int",
                "exists:loc_divisions,id,deleted_at,NULL",
            ],
            "loc_district_id" => [
                "required",
                "int",
                "exists:loc_districts,id,deleted_at,NULL",
            ],
            "loc_upazila_id" => [
                "nullable",
                "int",
                "exists:loc_upazilas,id,deleted_at,NULL",
            ],
            'village_or_area' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'village_or_area_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'house_n_road' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'house_n_road_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'zip_or_postal_code' => [
                'nullable',
                'string',
                'max:5',
                'min:4'
            ],
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
            'order.in' => 'Order must be either ASC or DESC. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]
        ], $customMessage);
    }
}
