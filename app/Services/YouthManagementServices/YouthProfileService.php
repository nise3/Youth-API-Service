<?php


namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Skill;
use App\Models\Trainer;
use App\Models\Youth;
use Carbon\Carbon;
use Faker\Provider\Uuid;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Prophecy\Promise\PromiseInterface;
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
        $firstName = $request->query('first_name');

        $lastName = $request->query('last_name');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $youthProfileBuilder */

        $youthProfileBuilder = Youth::select(
            [
                'youths.id as id',
                'youths.first_name',
                'youths.last_name',
                'youths.gender',
                'youths.skills',
                'youths.email',
                'youths.mobile',
                'youths.date_of_birth',
                'youths.physical_disability_status',
                'youths.physical_disabilities',
                'youths.city_or_town',
                'youths.address',
                'youths.zip_or_postal_code',
                'youths.bio',
                'youths.photo',
                'youths.cv_path',
                'youths.date_of_birth',
                'youths.created_at',
                'youths.updated_at',
            ]
        );

        $youthProfileBuilder->orderBy('youths.id', $order);

        if (!empty($firstName)) {
            $youthProfileBuilder->where('youths.first_name', 'like', '%' . $firstName . '%');
        } elseif (!empty($lastName)) {
            $youthProfileBuilder->where('youths.last_name', 'like', '%' . $lastName . '%');
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
                'youths.first_name',
                'youths.last_name',
                'youths.gender',
                'youths.skills',
                'youths.email',
                'youths.mobile',
                'youths.date_of_birth',
                'youths.physical_disability_status',
                'youths.physical_disabilities',
                'youths.city_or_town',
                'youths.zip_or_postal_code',
                'youths.bio',
                'youths.address',
                'youths.photo',
                'youths.cv_path',
                'youths.date_of_birth',
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
        $this->assignSkills($youth, $data["skills"]);
        return $youth;
    }


    /**
     * @param Youth $youth
     * @param array $skills
     */
    private function assignSkills(Youth $youth, array $skills)
    {
        Log::info("youth" . json_encode($youth));
        /** Assign skills to Youth */
        $skillIds = Skill::whereIn("id", $skills)->orderBy('id', 'ASC')->pluck('id')->toArray();
        $youth->skills()->sync($skillIds);

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
     * @return bool
     */
    public function verifyYouth(array $data): bool
    {
        $email = $data['email'] ?? null;
        $mobile = $data['mobile'] ?? null;
        $code = $data['verification_code'] ?? null;
        $conditionalAttribute = "email";
        $conditionalValue = $email;
        if ($mobile) {
            $conditionalAttribute = "mobile";
            $conditionalValue = $mobile;
        }
        /** @var Youth $youth */
        $youth = Youth::where($conditionalAttribute, $conditionalValue)
            ->where("verification_code", $code)
            ->where("row_status", BaseModel::ROW_STATUS_INACTIVE)
            ->first();

        if ($youth) {
            $youth->row_status = BaseModel::ROW_STATUS_ACTIVE;
            $youth->verification_code_verified_at = Carbon::now();
            $youth->save();
            return true;
        }
        return false;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function sendVerifyCode(array $data): bool
    {
        $email = $data["email"] ?? null;
        $mobile_number = $data["mobile"] ?? null;
        if ($email) {
            return true;
        } elseif ($mobile_number) {
            return true;
        }
        return false;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function resendCode(array $data): bool
    {
        $email = $data["email"] ?? null;
        $mobile = $data["mobile"] ?? null;
        $attributeField = $email ? "email" : "mobile";
        $payLoad[$attributeField] = $email ?: $mobile;

        $code = $this->generateCode();

        /** @var Youth $youth */
        $youth = Youth::where($attributeField, $payLoad[$attributeField])
            ->where("row_status", BaseModel::ROW_STATUS_INACTIVE)
            ->first();

        if ($youth) {
            $youth->verification_code = $code;
            $youth->verification_code_sent_at = Carbon::now();
            $youth->save();
            $payLoad["verification_code"] = $code;
            return $this->sendVerifyCode($payLoad);
        }
        return false;
    }

    /**
     * @return string
     */
    public function generateCode(): string
    {
        return "1234";
    }

    /**
     * @param array $data
     * @return PromiseInterface|\Illuminate\Http\Client\Response
     */
    public function idpUserCreate(array $data)
    {
        $url = clientUrl(BaseModel::IDP_SERVER_CLIENT_URL_TYPE);
        Log::info($url);
        $client = Http::retry(3)->withBasicAuth(BaseModel::IDP_USERNAME, BaseModel::IDP_USER_PASSWORD)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])->withOptions([
                'verify' => false
            ])->post($url, [
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
            ])->throw(function ($response, $e) {
                return $e;
            });

        Log::channel('idp_user')->info('idp_user_payload', $data);
        Log::channel('idp_user')->info('idp_user_info', $client->json());

        return $client;

    }

    /**
     * @param Request $request
     * @return Validator
     */
    public function verifyYouthValidator(Request $request): Validator
    {
        $customMessage = [
            "email.exists" => [
                "code" => 24000,
                "message" => "The email is not exists in the system"
            ],
            "mobile.exists" => [
                "code" => 24000,
                "message" => "The mobile is not exists in the system"
            ],
        ];

        $rules = [
            "email" => [
                Rule::requiredIf(function () use ($request) {
                    return !array_key_exists("mobile", $request->all());
                }),
                "exists:youths,email"
            ],
            "mobile" => [
                Rule::requiredIf(function () use ($request) {
                    return !array_key_exists("email", $request->all());
                }),
                "max:11",
                BaseModel::MOBILE_REGEX,
                "exists:youths,mobile"
            ],
            "verification_code" => [
                "required",
                "digits:4",
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $customMessage);
    }

    public function resendCodeValidator(Request $request): Validator
    {
        $customMessage = [
            "email.exists" => [
                "code" => 24000,
                "message" => "The email is not exists in the system"
            ],
            "mobile.exists" => [
                "code" => 24000,
                "message" => "The mobile is not exists in the system"
            ],
        ];

        $rules = [
            "email" => [
                Rule::requiredIf(function () use ($request) {
                    return !array_key_exists("mobile", $request->all());
                }),
                "exists:youths,email"
            ],
            "mobile" => [
                Rule::requiredIf(function () use ($request) {
                    return !array_key_exists("email", $request->all());
                }),
                "max:11",
                BaseModel::MOBILE_REGEX,
                "exists:youths,mobile"
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $customMessage);
    }

    public function youthRegisterValidation(Request $request, int $id = null): Validator
    {
        $data = $request->all();
        if (!empty($data["skills"])) {
            $data["skills"] = is_array($request['skills']) ? $request['skills'] : explode(',', $request['skills']);
        }
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
            "first_name" => "required|string|min:2|max:191",
            "last_name" => "required|string|min:2|max:191",
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
            "skills" => [
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
                "array"
            ],
            "skills.*" => [
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
                'numeric',
                "distinct",
                "min:1"
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
            "password" => [
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
                "required_with:password_confirmation",
                BaseModel::PASSWORD_REGEX,
                BaseModel::PASSWORD_TYPE,
                BaseModel::PASSWORD_MIN_LENGTH,
                BaseModel::PASSWORD_MAX_LENGTH,
                "confirmed"
            ],
            "password_confirmation" => [
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
                "required_with:password",
                BaseModel::PASSWORD_REGEX,
                BaseModel::PASSWORD_TYPE,
                BaseModel::PASSWORD_MIN_LENGTH,
                BaseModel::PASSWORD_MAX_LENGTH,
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
            "address" => [
                "nullable"
            ],
            "photo" => [
                "nullable"
            ],
            "cv_path" => [
                "nullable"
            ]
        ];
        return \Illuminate\Support\Facades\Validator::make($data, $rules);
    }


    public function getAuthYouth(string $id): \stdClass
    {
        $youth = Youth::where('idp_user_id', $id)->first();

        if (!$youth) {
            return new \stdClass();
        }

        return $youth;
    }
}
