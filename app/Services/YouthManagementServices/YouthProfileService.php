<?php


namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\PhysicalDisability;
use App\Models\Skill;
use App\Models\Trainer;
use App\Models\Youth;
use Carbon\Carbon;
use Faker\Provider\Uuid;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Prophecy\Promise\PromiseInterface;
use stdClass;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class YouthProfileService
 * @package App\Services\YouthManagementServices
 */
class YouthProfileService
{
    /**
     * @return Builder|Model|object
     */
    public function getYouthProfile()
    {

        /** @var Builder $youthProfileBuilder */
        $youthProfileBuilder = Youth::select(
            [
                'youths.id as id',
                'youths.first_name',
                'youths.first_name_en',
                'youths.last_name',
                'youths.last_name_en',
                'youths.gender',
                'youths.email',
                'youths.mobile',
                'youths.user_name_type',
                'youths.date_of_birth',
                'youths.physical_disability_status',
                'youths.loc_division_id',
                'loc_divisions.title_en as loc_division_title_en',
                'loc_divisions.title as loc_division_title',
                'youths.loc_district_id',
                'loc_districts.title_en as loc_district_title_en',
                'loc_districts.title as loc_district_title',
                'youths.loc_upazila_id',
                'loc_upazilas.title_en as loc_upazila_title_en',
                'loc_upazilas.title as loc_upazila_title',
                'youths.village_or_area',
                'youths.village_or_area_en',
                'youths.house_n_road',
                'youths.house_n_road_en',
                'youths.zip_or_postal_code',
                'youths.is_freelance_profile',
                'youths.bio',
                'youths.bio_en',
                'youths.photo',
                'youths.cv_path'
            ]
        );

        $youthProfileBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'youths.loc_division_id')
                ->whereNull('loc_divisions.deleted_at')
                ->where('loc_divisions.row_status', BaseModel::ROW_STATUS_ACTIVE);
        });

        $youthProfileBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_districts.id', '=', 'youths.loc_district_id')
                ->whereNull('loc_districts.deleted_at')
                ->where("loc_districts.row_status", BaseModel::ROW_STATUS_ACTIVE);

        });

        $youthProfileBuilder->leftJoin('loc_upazilas', function ($join) {
            $join->on('loc_upazilas.id', '=', 'youths.loc_upazila_id')
                ->whereNull('loc_upazilas.deleted_at')
                ->where("loc_upazilas.row_status", BaseModel::ROW_STATUS_ACTIVE);

        });

        $youthProfileBuilder->with(["physicalDisabilities", "LanguagesProficiencies", "skills", "educations", "jobExperiences", "certifications", "portfolios"]);
        $youthProfileBuilder->where('youths.id', '=', Auth::id());
        return $youthProfileBuilder->first();

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
        if (array_key_exists('physical_disabilities', $data)) {
            $this->assignPhysicalDisabilities($youth, $data['physical_disabilities']);
        }
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
        $this->assignSkills($youth, $data["skills"]);
        if ($data['physical_disability_status'] == BaseModel::FALSE) {
            $this->detachPhysicalDisabilities($youth);
        } else if ($data['physical_disability_status'] == BaseModel::TRUE) {
            $this->assignPhysicalDisabilities($youth, $data['physical_disabilities']);
        }
        return $youth;
    }

    /**
     * @param Youth $youth
     * @param array $skills
     */
    private function assignSkills(Youth $youth, array $skills)
    {
        /** Assign skills to Youth */
        $skillIds = Skill::whereIn("id", $skills)->orderBy('id', 'ASC')->pluck('id')->toArray();
        $youth->skills()->sync($skillIds);

    }

    /**
     * @param Youth $youth
     * @param array $disabilities
     */
    private function assignPhysicalDisabilities(Youth $youth, array $disabilities)
    {
        /** Assign skills to Youth */
        $disabilityIds = PhysicalDisability::whereIn("id", $disabilities)->orderBy('id', 'ASC')->pluck('id')->toArray();
        $youth->physicalDisabilities()->sync($disabilityIds);

    }

    /**
     * @param Youth $youth
     * @param array $disabilities
     */
    private function detachPhysicalDisabilities(Youth $youth)
    {
        $youth->physicalDisabilities()->sync([]);

    }

    /**
     * @param Youth $youth
     * @return bool
     */
    public function setFreelanceStatus(Youth $youth, array $data): bool
    {
        $youth->is_freelance_profile = $data['is_freelance_profile'];
        return $youth->save();
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
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     */
    public function idpUserCreate(array $data)
    {
        $url = clientUrl(BaseModel::IDP_SERVER_CLIENT_URL_TYPE);
        $client = Http::retry(3)
            ->withBasicAuth(BaseModel::IDP_USERNAME, BaseModel::IDP_USER_PASSWORD)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->withOptions(['verify' => false])
            ->post($url, [
                'schemas' => [
                ],
                'name' => [
                    'familyName' => $data['name'],
                    'givenName' => $data['name']
                ],
                'userName' => $data['username'],
                'password' => $data['password'],
                'userType' => $data['user_type'],
                'active' => $data['active'],
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
    public function freelanceStatusValidator(Request $request): Validator
    {
        $customMessage = [
            "freelance_profile_status.in" => [
                "code" => 30000,
                "message" => "The freelance_status is either 0 or 1"
            ]
        ];

        $rules = [
            "is_freelance_profile" => [
                "required",
                Rule::in(BaseModel::FREELANCE_PROFILE_STATUS)
            ]
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $customMessage);
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

    public function youthRegisterOrUpdateValidation(Request $request, int $id = null): Validator
    {
        $data = $request->all();

        $customMessage = [
            "password.regex" => [
                "code" => "",
                "message" => [
                    "Have At least one Uppercase letter",
                    "At least one Lower case letter",
                    "Also,At least one numeric value",
                    "And, At least one special character",
                    "Must be more than 8 characters long"
                ]
            ]
        ];

        if (!empty($data["skills"])) {
            $data["skills"] = is_array($request['skills']) ? $request['skills'] : explode(',', $request['skills']);
        }
        if (!empty($data["physical_disabilities"])) {
            $data["physical_disabilities"] = is_array($request['physical_disabilities']) ? $request['physical_disabilities'] : explode(',', $request['physical_disabilities']);
        }
        $rules = [
            "user_name_type" => [
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
                Rule::in(BaseModel::USER_NAME_TYPE)
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
                Rule::in(BaseModel::GENDERS)
            ],
            "email" => [
                Rule::requiredIf(function () use ($id) {
                    if ($id == null)
                        return true;
                    else if ($id) {
                        $youth = Youth::find($id);
                        return $youth->user_name_type == BaseModel::USER_NAME_TYPE_MOBILE_NUMBER;
                    }
                }),
                "unique:youths,email," . $id,
                "email",

            ],
            "mobile" => [
                Rule::requiredIf(function () use ($id) {
                    if ($id == null)
                        return true;
                    else if ($id) {
                        $youth = Youth::find($id);
                        return $youth->user_name_type == BaseModel::USER_NAME_TYPE_EMAIL;
                    }
                }),
                "max:11",
                "unique:youths,mobile," . $id,
                BaseModel::MOBILE_REGEX
            ],
            "date_of_birth" => [
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
                'date',
                'date_format:Y-m-d'
            ],
            "skills" => [
                "required",
                "array",
                "min:1",
                "max:10"
            ],
            "skills.*" => [
                "required",
                'numeric',
                "distinct",
                "min:1"
            ],
            "physical_disability_status" => [
                "required",
                "int",
                Rule::in(BaseModel::PHYSICAL_DISABILITIES_STATUS)
            ],
            "physical_disabilities" => [
                Rule::requiredIf(function () use ($id, $data) {
                    return $data['physical_disability_status'] == BaseModel::TRUE;
                }),
                "array",
                "min:1"
            ],
            "physical_disabilities.*" => [
                Rule::requiredIf(function () use ($id, $data) {
                    return $data['physical_disability_status'] == BaseModel::TRUE;
                }),
                "int",
                "distinct",
                "min:1",
                "exists:physical_disabilities,id"
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
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
                "exists:loc_divisions,id",
                "int"
            ],
            "loc_district_id" => [
                Rule::requiredIf(function () use ($id) {
                    return $id == null;
                }),
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
            "zip_or_postal_code" => [
                "nullable",
                "string"
            ],
        ];
        return \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessage);
    }


    /**
     * @param string $id
     * @return Youth
     */
    public function getAuthYouth(string $id): ?Youth
    {

        /** @var Youth $youth */
        $youth = Youth::where('idp_user_id', $id)
            ->where("row_status", BaseModel::ROW_STATUS_ACTIVE)
            ->first();
        return $youth;
    }
}
