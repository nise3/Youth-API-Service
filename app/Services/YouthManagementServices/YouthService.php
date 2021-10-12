<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Board;
use App\Models\EduGroup;
use App\Models\Examination;
use App\Models\MajorOrSubject;
use App\Models\Youth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
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

        $youthBuilder->orderBy('youths.id', $order);
        $youthBuilder->with(["skills", "physicalDisabilities", "jobExperiences", "LanguagesProficiencies", "certifications", "educations", "portfolios", "references"]);

        if (!empty($firstName)) {
            $youthBuilder->where('youths.first_name', 'like', '%' . $firstName . '%');
        }
        if (!empty($lastName)) {
            $youthBuilder->where('youths.last_name', 'like', '%' . $lastName . '%');
        }
        if (is_numeric($rowStatus)) {
            $youthBuilder->where('youths.id', $order);
        }

        /** @var Collection $youths */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
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

        $youthBuilder->where('youths.id', $id);
        $youthBuilder->with(["skills", "physicalDisabilities", "jobExperiences", "LanguagesProficiencies", "certifications", "educations", "portfolios", "references"]);


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

    public function getEducationBasicTablesInfos(): array
    {
        return [
            "examinations" => Examination::all(),
            "edu_groups" => EduGroup::all(),
            "boards" => Board::all(),
            "major_subjects" => MajorOrSubject::all()
        ];
    }

}
