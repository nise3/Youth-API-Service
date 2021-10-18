<?php


namespace App\Services\YouthManagementServices;


use App\Models\BaseModel;
use App\Models\YouthAddress;
use App\Models\YouthLanguagesProficiency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class YouthAddressService
 * @package App\Services\YouthManagementServices
 */
class YouthAddressService
{

    public function getAddressList(array $request, Carbon $startTime)
    {
        $youthId = $request['youth_id'] ?? Auth::id();
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
        ]);

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

        if (is_integer($youthId)) {
            $youthAddressBuilder->where('youth_addresses.youth_id', $youthId);
        }
        /** @var Collection $youthAddresses */

        if (is_integer($paginate) || is_integer($pageSize)) {
            $pageSize = $pageSize ?: 10;
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
    public function getOneYouthAddress(int $id, Carbon $startTime): array
    {
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

        $youthAddress = $youthAddressBuilder->first();

        return [
            "data" => $youthAddress ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
    }

    /**
     * @param array $data
     * @return YouthAddress
     */
    public function store(array $data): YouthAddress
    {
        $address = app(YouthAddress::class);
        $address->fill($data);
        $address->save();
        return $address;
    }

    /**
     * @param YouthAddress $youthAddress
     * @param array $data
     * @return YouthAddress
     */
    public function update(YouthAddress $youthAddress, array $data): YouthAddress
    {
        $youthAddress->fill($data);
        $youthAddress->save();
        return $youthAddress;
    }

    /**
     * @param YouthAddress $youthAddress
     * @return bool
     */
    public function destroy(YouthAddress $youthAddress): bool
    {
        return $youthAddress->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [];

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
            'loc_division_id' => [
                'required',
                'integer',
            ],
            'loc_district_id' => [
                'required',
                'integer',
            ],
            'loc_upazila_id' => [
                'nullable',
                'integer',
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
                "message" => 'Order must be either ASC or DESC',
            ]
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
}
