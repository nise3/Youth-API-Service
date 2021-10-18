<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\YouthGuardian;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class YouthGuardianService
 * @package App\Services\YouthManagementServices
 */
class YouthGuardianService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getGuardianList(array $request, Carbon $startTime): array
    {
        $guardianName = $request['name'] ?? "";
        $guardianNameEn = $request['name_en'] ?? "";

        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder $guardianBuilder */
        $guardianBuilder = YouthGuardian::select(
            [
                'youth_guardians.id',
                'youth_guardians.name',
                'youth_guardians.name_en',
                'youth_guardians.nid',
                'youth_guardians.mobile',
                'youth_guardians.date_of_birth',
                'youth_guardians.relationship_type',
                'youth_guardians.relationship_title',
                'youth_guardians.relationship_title_en',
                'youth_guardians.created_at',
                'youth_guardians.updated_at',
            ]
        );

        if (is_int(Auth::id())) {
            $guardianBuilder->where('youth_guardians.youth_id', Auth::id());
        }
        if (!empty($guardianName)) {
            $guardianBuilder->where('youth_guardians.name', 'like', '%' . $guardianName . '%');
        }
        if (!empty($guardianNameEn)) {
            $guardianBuilder->where('youth_guardians.name_en', 'like', '%' . $guardianNameEn . '%');
        }

        /** @var Collection $guardians */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $guardians = $guardianBuilder->paginate($pageSize);
            $paginateData = (object)$guardians->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $guardians = $guardianBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $guardians->toArray()['data'] ?? $guardians->toArray();
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
    public function getOneGuardian(int $id, Carbon $startTime): array
    {
        $guardianBuilder = YouthGuardian::select(
            [
                'youth_guardians.id',
                'youth_guardians.name',
                'youth_guardians.name_en',
                'youth_guardians.nid',
                'youth_guardians.mobile',
                'youth_guardians.date_of_birth',
                'youth_guardians.relationship_type',
                'youth_guardians.relationship_title',
                'youth_guardians.relationship_title_en',
                'youth_guardians.created_at',
                'youth_guardians.updated_at'
            ]
        );

        $guardianBuilder->where('youth_guardians.id', $id);

        /** @var YouthGuardian $guardian */
        $guardian = $guardianBuilder->first();

        return [
            "data" => $guardian ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
    }

    /**
     * @param array $data
     * @return YouthGuardian
     */
    public function createGuardian(array $data): YouthGuardian
    {
        $youthGuardian = new YouthGuardian();
        $youthGuardian->fill($data);
        $youthGuardian->save();
        return $youthGuardian;
    }

    /**
     * @param YouthGuardian $youthGuardian
     * @param array $data
     * @return YouthGuardian
     */
    public function update(YouthGuardian $youthGuardian, array $data): YouthGuardian
    {
        $youthGuardian->fill($data);
        $youthGuardian->save();
        return $youthGuardian;
    }

    /**
     * @param YouthGuardian $youthGuardian
     * @return bool
     */
    public function destroy(YouthGuardian $youthGuardian): bool
    {
        return $youthGuardian->delete();
    }


    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $rules = [
            'youth_id' => [
                'required',
                'exists:youths,id,deleted_at,NULL',
                'int',
            ],
            "mobile" => [
                "nullable",
                "max:11",
                BaseModel::MOBILE_REGEX
            ],
            'name' => [
                'required',
                'string',
                'max:500',
                'min:1'
            ],
            'name_en' => [
                'nullable',
                'string',
                'max:250',
                'min:1'
            ],
            'nid' => [
                'nullable',
                'string',
                'max:30',
            ],
            'date_of_birth' => [
                'nullable',
                'date',

            ],
            'relationship_type' => [
                'required',
                function ($attr, $value, $fail) use ($request, $id) {

                    if ($id == null && $value != YouthGuardian::RELATIONSHIP_TYPE_OTHER) {
                        $guardian = YouthGuardian::where('youth_id', $request['youth_id'])->where($attr, $value)->first();
                        $guardian != null && $fail($attr . " should be unique with this youth id");
                    }
                },
                'int',
            ],
            'relationship_title' => [
                Rule::requiredIf(function () use ($request) {
                    return $request['relationship_type'] == YouthGuardian::RELATIONSHIP_TYPE_OTHER;
                }),
                'string',
                'min:1'
            ],
            'relationship_title_en' => [
                'nullable',
                'string',
                'min:1'
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }

    /**
     * @param Request $request
     * @return Validator
     */
    public function filterValidator(Request $request): Validator
    {
        $customMessage = [
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be within ASC or DESC',
            ]
        ];

        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'page' => 'int|gt:0',
            'pageSize' => 'int|gt:0',
            'name' => 'nullable|string',
            'name_en' => 'nullable|string',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]
        ], $customMessage);
    }
}
