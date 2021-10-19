<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\EduBoard;
use App\Models\EducationLevel;
use App\Models\EduGroup;
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

        /** @var Builder $youthBuilder */

        $youthBuilder = Youth::select(
            [
                'youths.id',
                'youths.username',
                'youths.user_name_type',
                'youths.first_name',
                'youths.first_name_en',
                'youths.last_name',
                'youths.last_name_en',
                'youths.loc_division_id',
                'loc_divisions.title as division_title',
                'loc_divisions.title_en as division_title_en',
                'youths.loc_district_id',
                'loc_districts.title as district_title',
                'loc_districts.title_en as district_title_en',
                'youths.loc_upazila_id',
                'loc_upazilas.title as upazila_title',
                'loc_upazilas.title_en as upazila_title_en',
                'youths.gender',
                'youths.religion',
                'youths.marital_status',
                'youths.nationality',
                'youths.email',
                'youths.mobile',
                'youths.identity_number_type',
                'youths.identity_number',
                'youths.date_of_birth',
                'youths.freedom_fighter_status',
                'youths.physical_disability_status',
                'youths.does_belong_to_ethnic_group',
                'youths.photo',
                'youths.cv_path',
                'youths.row_status',
                'youths.created_at',
                'youths.updated_at',
            ]
        );

        $youthBuilder->orderBy('youths.id', $order);

        $youthBuilder->leftjoin('loc_divisions', function ($join) {
            $join->on('youths.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $youthBuilder->leftjoin('loc_districts', function ($join) {
            $join->on('youths.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });

        $youthBuilder->leftjoin('loc_upazilas', function ($join) {
            $join->on('youths.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });
        $youthBuilder->with('skills');
        if (!empty($firstName)) {
            $youthBuilder->where('youths.first_name', 'like', '%' . $firstName . '%');
        }

        if (!empty($lastName)) {
            $youthBuilder->where('youths.last_name', 'like', '%' . $lastName . '%');
        }

        if (is_integer($rowStatus)) {
            $youthBuilder->where('youths.row_status', $rowStatus);
        }


        /** @var Collection $youths */

        if (is_integer($paginate) || is_integer($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $youths = $youthBuilder->paginate($pageSize);
            $paginateData = (object)$youths->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $youths = $youthBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $youths->toArray()['data'] ?? $youths->toArray();
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
        /** @var Builder $youthBuilder */
        $youthBuilder = Youth::select(
            [
                'youths.id',
                'youths.idp_user_id',
                'youths.username',
                'youths.user_name_type',
                'youths.first_name',
                'youths.first_name_en',
                'youths.last_name',
                'youths.last_name_en',
                'youths.loc_division_id',
                'loc_divisions.title as division_title',
                'loc_divisions.title_en as division_title_en',
                'youths.loc_district_id',
                'loc_districts.title as district_title',
                'loc_districts.title_en as district_title_en',
                'youths.loc_upazila_id',
                'loc_upazilas.title as upazila_title',
                'loc_upazilas.title_en as upazila_title_en',
                'youths.gender',
                'youths.religion',
                'youths.marital_status',
                'youths.nationality',
                'youths.email',
                'youths.mobile',
                'youths.identity_number_type',
                'youths.identity_number',
                'youths.date_of_birth',
                'youths.freedom_fighter_status',
                'youths.physical_disability_status',
                'youths.does_belong_to_ethnic_group',
                'youths.bio',
                'youths.bio_en',
                'youths.photo',
                'youths.cv_path',
                'youths.signature_image_path',
                'youths.row_status',
                'youths.created_at',
                'youths.updated_at',
            ]
        );

        $youthBuilder->leftjoin('loc_divisions', function ($join) {
            $join->on('youths.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $youthBuilder->leftjoin('loc_districts', function ($join) {
            $join->on('youths.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });

        $youthBuilder->leftjoin('loc_upazilas', function ($join) {
            $join->on('youths.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $youthBuilder->where('youths.id', $id);

        $youthBuilder->with([
            "skills", "physicalDisabilities",
            "youthJobExperiences", "youthLanguagesProficiencies",
            "youthCertifications", "youthEducations", "youthPortfolios",
            "youthReferences"
        ]);


        /** @var Youth $youth */
        $youth = $youthBuilder->first();

        return [
            "data" => $youth ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ]
        ];

    }

    /**
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
            'first_name' => 'nullable|max:300|min:2',
            'first_name_en' => 'nullable|max:150|min:2',
            'last_name' => 'nullable|max:300|min:2',
            'last_name_en' => 'nullable|max:150|min:2',
            'page' => 'integer|gt:0',
            'pageSize' => 'integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "integer",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }

    /**
     * @return array
     */
    public function getEducationBasicTablesInfos(): array
    {
        return [
            "edu_groups" => EduGroup::all(),
            "edu_boards" => EduBoard::all(),
            "education_level_with_degrees" => EducationLevel::with('examDegrees')->get(),
            "result" => config("nise3.exam_degree_results")
        ];
    }

}
