<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Youth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class YouthService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getYouthProfileList(array $request, Carbon $startTime): array
    {
        $firstName = $requestp['first_name'] ?? "";
        $lastName = $request['last_name'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $youthProfileBuilder */

        $youthProfileBuilder = Youth::select(
            [
                'youths.id',
                'youths.idp_user_id',
                'youths.username',
                'youths.user_name_type',
                'youths.first_name',
                'youths.first_name_en',
                'youths.last_name',
                'youths.last_name_en',
                'youths.gender',
                'youths.email',
                'youths.mobile',
                'youths.date_of_birth',
                'youths.physical_disability_status',
                'youths.loc_division_id',
                'youths.loc_district_id',
                'youths.loc_upazila_id',
                'youths.village_or_area',
                'youths.village_or_area_en',
                'youths.house_n_road',
                'youths.house_n_road_en',
                'youths.zip_or_postal_code',
                'youths.bio',
                'youths.bio_en',
                'youths.photo',
                'youths.cv_path',
                'youths.verification_code',
                'youths.verification_code_sent_at',
                'youths.verification_code_verified_at',
                'youths.row_status',
                'youths.created_at',
                'youths.updated_at',
            ]
        );

        $youthProfileBuilder->orderBy('youths.id', $order);

        if (!empty($firstName)) {
            $youthProfileBuilder->where('youths.first_name', 'like', '%' . $firstName . '%');
        }
        if (!empty($lastName)) {
            $youthProfileBuilder->where('youths.last_name', 'like', '%' . $lastName . '%');
        }
        if (is_numeric($rowStatus)) {
            $youthProfileBuilder->where('youths.id', $order);
        }

        /** @var Collection $youthProfiles */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $youthProfiles = $youthProfileBuilder->paginate($pageSize);
            $paginateData = (object)$youthProfiles->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $youthProfiles = $youthProfileBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $youthProfiles->toArray()['data'] ?? $youthProfiles->toArray();
        $response['_response_status'] = [
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
    public function getOneYouthProfile(int $id, Carbon $startTime): array
    {
        /** @var Builder $youthProfileBuilder */
        $youthProfileBuilder = Youth::select(
            [
                'youths.id',
                'youths.idp_user_id',
                'youths.username',
                'youths.user_name_type',
                'youths.first_name',
                'youths.first_name_en',
                'youths.last_name',
                'youths.last_name_en',
                'youths.gender',
                'youths.email',
                'youths.mobile',
                'youths.date_of_birth',
                'youths.physical_disability_status',
                'youths.physical_disabilities',
                'youths.loc_division_id',
                'youths.loc_district_id',
                'youths.loc_upazila_id',
                'youths.village_or_area',
                'youths.village_or_area_en',
                'youths.house_n_road',
                'youths.house_n_road_en',
                'youths.zip_or_postal_code',
                'youths.bio',
                'youths.bio_en',
                'youths.photo',
                'youths.cv_path',
                'youths.verification_code',
                'youths.verification_code_sent_at',
                'youths.verification_code_verified_at',
                'youths.row_status',
                'youths.created_at',
                'youths.updated_at',
            ]
        );
        $youthProfileBuilder->where('youths.id', $id);

        /** @var Youth $youthProfile */
        $youthProfile = $youthProfileBuilder->first();

        return [
            "data" => $youthProfile ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ]
        ];

    }

    /**
     * @param Youth $youth
     * @param array $data
     * @return Youth
     */
    public function store(array $data): Youth
    {
        $youth = new Youth();
        $youth->fill($data);
        $youth->save();
        return $youth;
    }

    /**
     * @param Youth $youth
     * @param array $data
     * @return Youth
     */
    public function update(Youth $youth, array $data): Youth
    {
        $youth->fill($data);
        $youth->save();
        return $youth;
    }


    /**
     * @param Youth $youth
     * @return bool
     */
    public function destroy(Youth $youth): bool
    {
        return $youth->delete();
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
            'first_name' => 'nullable|max:400|min:2',
            'last_name' => 'nullable|max:191|min:2',
            'page' => 'numeric|gt:0',
            '$pageSize' => 'numeric|gt:0',
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

    public function youthRegisterValidation(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();

        if (!empty($data["physical_disabilities"])) {
            $data["physical_disabilities"] = is_array($request['physical_disabilities']) ? $request['physical_disabilities'] : explode(',', $request['physical_disabilities']);
        } else {
            unset($data["physical_disabilities"]);
        }

        $rules = [
            "username" => [
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
                "unique:youths,username"
            ],
            "user_name_type" => [
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
                Rule::in(BaseModel::USER_TYPE)
            ],
            "first_name" => "required|string|min:2|max:500",
            "first_name_en" => "nullable|string|min:2|max:500",
            "last_name" => "required|string|min:2|max:500",
            "last_name_en" => "nullable|string|min:2|max:500",
            "gender" => [
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
                "int",
                Rule::in(BaseModel::GENDER)
            ],
            "email" => "required|email|unique:youths,email," . $id,
            "mobile" => [
                "required",
                "max:11",
                BaseModel::MOBILE_REGEX,
                "unique:youths,mobile," . $id
            ],
            "date_of_birth" => [
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
                'date',
                'date_format:Y-m-d'
            ],
            "physical_disability_status" => [
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
                "int",
                Rule::in(BaseModel::PHYSICAL_DISABILITIES_STATUS)
            ],
            "physical_disabilities" => [
                Rule::requiredIf(function () use ($id, $request) {
                    return ($id == null && $request->physical_disability_status == BaseModel::TRUE);
                }),
                "array",
                "min:1"
            ],
            "physical_disabilities.*" => [
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
                "numeric",
                "distinct",
                "min:1"
            ],
            "loc_division_id" => [
                "required",
                "exists:loc_divisions,id",
                "int"
            ],
            "loc_district_id" => [
                "required",
                "exists:loc_districts,id",
                "int"
            ],
            "loc_upazila_id" => [
                "nullable",
                "numeric",
                "exists:loc_upazilas,id",
            ],
            "village_or_area" => [
                "nullable",
                "string"
            ],
            "village_or_area_en" => [
                "nullable",
                "string"
            ],
            "house_n_road" => [
                "nullable",
                "string"
            ],
            "house_n_road_en" => [
                "nullable",
                "string"
            ],
            "zip_or_postal_code" => [
                Rule::requiredIf(function () use ($id) {
                    return $id != null;
                }),
                "string"
            ],
            "bio" => [
                "nullable"
            ],
            "bio_en" => [
                "nullable"
            ],
            "address" => [
                "nullable"
            ],
            "photo" => [
                "nullable"
            ],
            "cv_path" => [
                "nullable"
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ]
        ];
        return Validator::make($data, $rules);
    }

}
