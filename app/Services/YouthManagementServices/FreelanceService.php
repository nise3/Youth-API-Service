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

class FreelanceService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllFreelancerList(array $request, Carbon $startTime): array
    {
        $skills = $request['skills'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? BaseModel::ROW_ORDER_ASC;

        /** @var Builder $freelancersBuilder */
        $freelancersBuilder = Youth::select(
            [
                'youths.id as id',
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
                'loc_divisions.title_en as loc_division_title_en',
                'loc_divisions.title_bn as loc_division_title_bn',
                'youths.loc_district_id',
                'loc_districts.title_en as loc_district_title_en',
                'loc_districts.title_bn as loc_district_title_bn',
                'youths.loc_upazila_id',
                'loc_upazilas.title_en as loc_upazila_title_en',
                'loc_upazilas.title_bn as loc_upazila_title_bn',
                'youths.village_or_area',
                'youths.village_or_area_en',
                'youths.house_n_road',
                'youths.house_n_road_en',
                'youths.zip_or_postal_code',
                'youths.bio',
                'youths.bio_en',
                'youths.photo',
                'youths.cv_path'
            ]
        );

        $freelancersBuilder->where('youths.is_freelance_profile', BaseModel::FREELANCE_PROFILE_YES);

        if (count($skills)>0) {
            $users = Youth::whereHas('skills', function($query) {
                $query->whereIn('id', [1, 2, 3]);
            })
                ->get();
            dd(Youth::find(1)->with('skills'));
        }


        $freelancersBuilder->orderBy('youths.id', $order);

        $freelancersBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'youths.loc_division_id')
                ->whereNull('loc_divisions.deleted_at')
                ->where('loc_divisions.row_status', BaseModel::ROW_STATUS_ACTIVE);
        });

        $freelancersBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_districts.id', '=', 'youths.loc_district_id')
                ->whereNull('loc_districts.deleted_at')
                ->where("loc_districts.row_status", BaseModel::ROW_STATUS_ACTIVE);

        });

        $freelancersBuilder->leftJoin('loc_upazilas', function ($join) {
            $join->on('loc_upazilas.id', '=', 'youths.loc_upazila_id')
                ->whereNull('loc_upazilas.deleted_at')
                ->where("loc_upazilas.row_status", BaseModel::ROW_STATUS_ACTIVE);

        });

        $freelancersBuilder->with(["skills"]);

        /** @var Collection $freelancers */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $freelancers = $freelancersBuilder->paginate($pageSize);
            $paginateData = (object)$freelancers->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $freelancers = $freelancersBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $freelancers->toArray()['data'] ?? $freelancers->toArray();
        $response['response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];
        return $response;
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

        if (!empty($request["skills"])) {
            $request["skills"] = is_array($request['skills']) ? $request['skills'] : explode(',', $request['skills']);
        }

        return Validator::make($request->all(), [
            'page' => 'int|gt:0',
            'page_size' => 'int|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "int",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            "skills" => [
                "nullable",
                "array",
                "min:1"
            ],
            "skills.*" => [
                "required",
                "int",
                "distinct",
                "min:1",
                "exists:skills,id"
            ],
        ], $customMessage);
    }

}
