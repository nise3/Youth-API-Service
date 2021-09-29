<?php


namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Youth;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;


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

        if (!empty($paginate)) {
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
            "data" => $data['data'] ?? $data,
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
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
     * @param array $data
     * @return Youth
     */
    public function store(Youth $youth, array $data): Youth
    {
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
     * @param array $data
     * @return PromiseInterface|\Illuminate\Http\Client\Response
     */
    public function idpUserCreate(array $data)
    {
        $client = Http::withBasicAuth(BaseModel::IDP_USERNAME, BaseModel::IDP_USER_PASSWORD)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])->withOptions([
                'verify' => false
            ])->post(BaseModel::IDP_USER_CREATE_ENDPOINT, [
                'schemas' => [
                ],
                'name' => [
                    'familyName' => $data['name'],
                    'givenName' => $data['name']
                ],
                'userName' => $data['username'],
                'password' => $data['password'],
                'emails' => [
                    0 => [
                        'primary' => true,
                        'value' => $data['email'],
                        'type' => 'work',
                    ]
                ],
            ]);

        Log::channel('idp_user')->info('idp_user_payload', $data);
        Log::channel('idp_user')->info('idp_user_info', $client->json());

        return $client;

    }


    /**
     * @param Request $request
     * @return Validator
     */
    public function youthProfileUpdateValidation(Request $request, int $id): Validator
    {
        $rules = [
            "first_name" => "required|string|min:2|max:191",
            "last_name" => "required|string|min:2|max:191",
            "gender" => [
                "required",
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
            "city" => "required|string",
            "zip_or_postal_code" => [
                "required",
                "string"
            ],
            "bio" => [
                "nullable"
            ],
            "photo" => [
                "nullable"
            ],
            "cv_path" => [
                "nullable"
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }

    public function youthRegisterValidation(Request $request): Validator
    {
        $data = $request->all();

        if (!empty($data["physical_disabilities"])) {
            $data["physical_disabilities"] = is_array($request['physical_disabilities']) ? $request['physical_disabilities'] : explode(',', $request['physical_disabilities']);
        }

        $rules = [
            "username" => "required|unique:youths,username",
            "first_name" => "required|string|min:2|max:191",
            "last_name" => "required|string|min:2|max:191",
            "gender" => [
                "required",
                "int",
                Rule::in(BaseModel::GENDER)
            ],
            "email" => "required|email|unique:youths,email",
            "mobile" => [
                "required",
                "max:11",
                BaseModel::MOBILE_REGEX,
                "unique:youths,mobile"
            ],
            "date_of_birth" => "required|date|date_format:Y-m-d",
            "physical_disability_status" => [
                "required",
                "int",
                Rule::in(BaseModel::PHYSICAL_DISABILITIES_STATUS)
            ],
            "physical_disabilities" => [
                "required_if:physical_disability_status:" . BaseModel::TRUE,
                "array",
                "min:1"
            ],
            "physical_disabilities.*" => [
                'required|numeric|distinct|min:1',
                BaseModel::PASSWORD_COMMON_RULES
            ],
            "password" => [
                "required_with:password_confirmation",
                BaseModel::PASSWORD_COMMON_RULES
            ]
        ];
        return \Illuminate\Support\Facades\Validator::make($data, $rules);
    }
}
