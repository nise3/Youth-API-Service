<?php


namespace App\Services\YouthManagementServices;

use App\Models\Youth;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


/**
 * Class YouthProfileService
 * @package App\Services\YouthManagementServices
 */
class YouthProfileService
{
    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getYouthProfileList(Request $request, Carbon $startTime): array
    {
        $paginateLink = [];
        $page = [];
        $nameEn = $request->query('name_en');

        $nameBn = $request->query('name_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $youthProfileBuilder */
        $youthProfileBuilder = Youth::select(
            [
                'youths.id as id',
                'youths.name_en',
                'youths.name_bn',
                'youths.mobile',
                'youths.email',
                'youths.father_name_en',
                'youths.father_name_bn',
                'youths.mother_name_en',
                'youths.mother_name_bn',
                'youths.guardian_name_en',
                'youths.guardian_name_bn',
                'youths.relation_with_guardian',
                'youths.number_of_siblings',
                'youths.gender',
                'youths.date_of_birth',
                'youths.birth_certificate_no',
                'youths.nid',
                'youths.passport_number',
                'youths.nationality',
                'youths.religion',
                'youths.marital_status',
                'youths.current_employment_status',
                'youths.main_occupation',
                'youths.other_occupation',
                'youths.personal_monthly_income',
                'youths.year_of_experience',
                'youths.physical_disabilities_status',
                'youths.freedom_fighter_status',
                'youths.present_address_division_id',
                'youths.present_address_district_id',
                'youths.present_address_upazila_id',
                'youths.present_house_address',
                'youths.permanent_address_division_id',
                'youths.permanent_address_district_id',
                'youths.permanent_address_upazila_id',
                'youths.permanent_house_address',
                'youths.is_ethnic_group',
                'youths.photo',
                'youths.signature',
                'youths.created_at',
                'youths.updated_at',
            ]
        );

        $youthProfileBuilder->orderBy('youths.id', $order);

        if (!empty($nameEn)) {
            $youthProfileBuilder->where('youths.name_en', 'like', '%' . $nameEn . '%');
        } elseif (!empty($nameBn)) {
            $youthProfileBuilder->where('youths.name_bn', 'like', '%' . $nameBn . '%');
        }

        /** @var Collection $youthProfiles */

        if ($paginate) {
            $youthProfiles = $youthProfileBuilder->paginate(10);
            $paginateData = (object)$youthProfiles->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $youthProfiles = $youthProfileBuilder->get();
        }

        $data = $youthProfiles->toArray();

        return [
            "data" => $data ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => [
                'paginate' => $paginateLink,
            ],
            "_page" => $page,
            "_order" => $order
        ];
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
                'youths.id as id',
                'youths.name_en',
                'youths.name_bn',
                'youths.mobile',
                'youths.email',
                'youths.father_name_en',
                'youths.father_name_bn',
                'youths.mother_name_en',
                'youths.mother_name_bn',
                'youths.guardian_name_en',
                'youths.guardian_name_bn',
                'youths.relation_with_guardian',
                'youths.number_of_siblings',
                'youths.gender',
                'youths.date_of_birth',
                'youths.birth_certificate_no',
                'youths.nid',
                'youths.passport_number',
                'youths.nationality',
                'youths.religion',
                'youths.marital_status',
                'youths.current_employment_status',
                'youths.main_occupation',
                'youths.other_occupation',
                'youths.personal_monthly_income',
                'youths.year_of_experience',
                'youths.physical_disabilities_status',
                'youths.freedom_fighter_status',
                'youths.present_address_division_id',
                'youths.present_address_district_id',
                'youths.present_address_upazila_id',
                'youths.present_house_address',
                'youths.permanent_address_division_id',
                'youths.permanent_address_district_id',
                'youths.permanent_address_upazila_id',
                'youths.permanent_house_address',
                'youths.is_ethnic_group',
                'youths.photo',
                'youths.signature',
                'youths.created_at',
                'youths.updated_at',
            ]
        );
        $youthProfileBuilder->where('youths.id', '=', $id);

        /** @var Youth $youthProfile */
        $youthProfile = $youthProfileBuilder->first();

        return [
            "data" => $youthProfile ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
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
     * @param int|null $id
     * @return Validator
     */
    public function validation(Request $request, int $id = null): Validator
    {
        $rules = [
            "name_en" => "required|string|min:2|max:191",
            "name_bn" => "required|string|min:2|max:191",
            "mobile" => "required|string|min:1|max:20|unique:youths,mobile",
            "email" => "required|email|min:1|max:20|unique:youths,email",
            "father_name_en" => "nullable|string|min:2|max:191",
            "father_name_bn" => "nullable|string|min:2|max:191",
            "mother_name_en" => "nullable|string|min:2|max:191",
            "mother_name_bn" => "nullable|string|min:2|max:191",
            "guardian_name_en" => "nullable|string|min:2|max:191",
            "guardian_name_bn" => "nullable|string|min:2|max:191",
            "relation_with_guardian" => "nullable|string|min:2|max:191",
            "number_of_siblings" => "nullable|int",
            "gender" => "nullable|int",
            "date_of_birth" => "nullable|date",
            "birth_certificate_no" => "nullable|string",
            "nid" => "nullable|string",
            "passport_number" => "nullable|string",
            "nationality" => "nullable|string",
            "religion" => "nullable|int",
            "marital_status" => "nullable|int",
            "current_employment_status" => "nullable|int",
            "main_occupation" => "nullable|string",
            "other_occupation" => "nullable|string",
            "personal_monthly_income" => "nullable|numeric",
            "year_of_experience" => "nullable|int",
            "physical_disabilities_status" => "nullable|int",
            "freedom_fighter_status" => "nullable|int",
            "present_address_division_id" => "nullable|int",
            "present_address_district_id" => "nullable|int",
            "present_address_upazila_id" => "nullable|int",
            "present_house_address" => "nullable|string",
            "permanent_address_division_id" => "nullable|int",
            "permanent_address_district_id" => "nullable|int",
            "permanent_address_upazila_id" => "nullable|int",
            "permanent_house_address" => "nullable|string",
            "is_ethnic_group" => "nullable|int",
            "photo" => "nullable|string",
            "signature" => "nullable|string"
        ];
        if ($id) {
            $rules["mobile"] = "required|string|min:1|max:20|unique:youths,mobile," . $id;
            $rules["email"] = "required|string|min:1|max:20|unique:youths,email," . $id;
        }
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }
}
