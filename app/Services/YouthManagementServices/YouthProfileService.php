<?php


namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\PhysicalDisability;
use App\Models\Skill;
use App\Models\Youth;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;


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

        $youthProfileBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'youths.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $youthProfileBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_districts.id', '=', 'youths.loc_district_id')
                ->whereNull('loc_districts.deleted_at');

        });

        $youthProfileBuilder->leftJoin('loc_upazilas', function ($join) {
            $join->on('loc_upazilas.id', '=', 'youths.loc_upazila_id')
                ->whereNull('loc_upazilas.deleted_at');

        });

        $youthProfileBuilder->where('youths.id', '=', Auth::id());
        $youthProfileBuilder->with(["physicalDisabilities", "youthLanguagesProficiencies", "skills", "youthEducations", "youthJobExperiences", "youthCertifications", "youthPortfolios", "youthAddresses"]);

        return $youthProfileBuilder->first();

    }

    /**
     * @param Youth $youth
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
     */
    private function detachPhysicalDisabilities(Youth $youth)
    {
        $youth->physicalDisabilities()->sync([]);

    }

    /**
     * @param Youth $youth
     * @param array $data
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
        }
        if ($mobile_number) {
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
     * @return PromiseInterface|Response
     */
    public function idpUserCreate(array $data): PromiseInterface|Response
    {
        $url = clientUrl(BaseModel::IDP_SERVER_CLIENT_URL_TYPE);
        $payload = $this->prepareIdpPayload($data);
        $client = Http::withBasicAuth(BaseModel::IDP_USERNAME, BaseModel::IDP_USER_PASSWORD)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->withOptions([
                'verify' => false
            ])
            ->post($url, $payload);

        Log::channel('idp_user')->info('idp_user_payload', $payload);
        Log::channel('idp_user')->debug($client->json());

        return $client;

    }

    private function prepareIdpPayload($data)
    {
        $userEmailNo = trim($data['username']);
        $cleanUserName = trim($data['username']);
        if (!str_contains($userEmailNo, '@')) {
            $userEmailNo = 'y_' . $data['username'] . '@youth.nise3.com';
        } else {
            $cleanUserName = str_replace('@', '', $cleanUserName);
        }
        return [
            'schemas' => [
                "urn:ietf:params:scim:schemas:core:2.0:User",
                "urn:ietf:params:scim:schemas:extension:enterprise:2.0:User"
            ],
            'name' => [
                'familyName' => $data['name'],
                'givenName' => $data['name']
            ],
            'active' => (string)$data['active'],
            'organization' => $data['name'],
            'userName' => $cleanUserName,
            'password' => $data['password'],
            'userType' => $data['user_type'],
            'country' => 'BD',
            'emails' => [
                0 => $userEmailNo
            ]
        ];
    }

    /**
     * @param Request $request
     * @return Validator
     */
    public function freelanceStatusValidator(Request $request): Validator
    {
        $customMessage = [
            "freelance_profile_status.in" => "The freelance_status is either 0 or 1. [30000]"
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
            "email.exists" => "The email is not exists in the system. [24000]",
            "mobile.exists" => "The mobile is not exists in the system. [24000]"
        ];

        $rules = [
            "email" => [
                Rule::requiredIf(function () use ($request) {
                    return !$request->exists('mobile');
                }),
                "exists:youths,email,deleted_at,NULL"
            ],
            "mobile" => [
                Rule::requiredIf(function () use ($request) {
                    return !$request->exists('email');
                }),
                "exists:youths,mobile,deleted_at,NULL"
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
            "email.exists" => "The email is not exists in the system. [24000]",
            "mobile.exists" => "The mobile is not exists in the system. [24000]"
        ];

        $rules = [
            "email" => [
                Rule::requiredIf(function () use ($request) {
                    return !$request->exists('mobile');
                }),
                "exists:youths,email,deleted_at,NULL"
            ],
            "mobile" => [
                Rule::requiredIf(function () use ($request) {
                    return !$request->exists('email');
                }),
                "exists:youths,mobile,deleted_at,NULL"
            ],
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @param Youth $youth
     * @return Validator
     */
    public function youthUpdateValidation(Request $request, Youth $youth): Validator
    {
        $data = $request->all();

        if (!empty($data["skills"])) {
            $data["skills"] = is_array($request['skills']) ? $request['skills'] : explode(',', $request['skills']);
        }

        if (!empty($data["physical_disabilities"])) {
            $data["physical_disabilities"] = is_array($request['physical_disabilities']) ? $request['physical_disabilities'] : explode(',', $request['physical_disabilities']);
        }

        $rules = [
            "first_name" => "required|string|min:2|max:500",
            "first_name_en" => "nullable|string|min:2|max:250",
            "last_name" => "required|string|min:2|max:500",
            "last_name_en" => "nullable|string|min:2|max:250",
            "loc_division_id" => [
                "required",
                "exists:loc_divisions,id,deleted_at,NULL",
                "int"
            ],
            "loc_district_id" => [
                "required",
                "exists:loc_districts,id,deleted_at,NULL",
                "int"
            ],
            "loc_upazila_id" => [
                "nullable",
                "exists:loc_upazilas,id,deleted_at,NULL",
                "int"
            ],
            "date_of_birth" => [
                'nullable',
                'date',
                'date_format:Y-m-d'
            ],
            "gender" => [
                'nullable',
                Rule::in(BaseModel::GENDERS),
                "int"
            ],
            'religion' => [
                'nullable',
                'int',
                Rule::in(Youth::RELIGIONS)
            ],
            'marital_status' => [
                'nullable',
                'int',
                Rule::in(Youth::MARITAL_STATUSES)
            ],
            'nationality' => [
                'required',
                'int',
            ],
            "email" => [
                Rule::requiredIf(function () use ($youth) {
                    return $youth->user_name_type == BaseModel::USER_NAME_TYPE_MOBILE_NUMBER;
                }),
                Rule::unique('youths', 'email')
                    ->ignore($youth->id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            "mobile" => [
                Rule::requiredIf(function () use ($youth) {
                    return $youth->user_name_type == BaseModel::USER_NAME_TYPE_EMAIL;
                }),
                Rule::unique('youths', 'mobile')
                    ->ignore($youth->id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            'identity_number_type' => [
                'nullable',
                'int',
                Rule::in(Youth::IDENTITY_TYPES)
            ],
            'identity_number' => [
                'nullable',
                'string',
                'min:11',
                'max:50'
            ],
            'freedom_fighter_status' => [
                'required',
                'int',
                Rule::in(Youth::FREEDOM_FIGHTER_STATUSES)
            ],
            "physical_disability_status" => [
                "required",
                "int",
                Rule::in(BaseModel::PHYSICAL_DISABILITIES_STATUSES)
            ],
            'does_belong_to_ethnic_group' => [
                'required',
                'int'
            ],
            "bio" => "nullable|string",
            "bio_en" => "nullable|string",
            "photo" => "nullable|string|max:600",
            "cv_path" => "nullable|string|max:600",
            "signature_image_path" => "nullable|string|max:600",
            "skills" => [
                "required",
                "array",
                "min:1",
                "max:10"
            ],
            "skills.*" => [
                "required",
                'integer',
                "distinct",
                "min:1"
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
                "nullable",
                "string",
                "size:4"
            ]
        ];

        if (isset($request['physical_disability_status']) && $request['physical_disability_status'] == BaseModel::TRUE) {
            $rules['physical_disabilities'] = [
                Rule::requiredIf(function () use ($data) {
                    return $data['physical_disability_status'] == BaseModel::TRUE;
                }),
                "array",
                "min:1"
            ];
            $rules['physical_disabilities.*'] = [
                Rule::requiredIf(function () use ($data) {
                    return $data['physical_disability_status'] == BaseModel::TRUE;
                }),
                "exists:physical_disabilities,id,deleted_at,NULL",
                "int",
                "distinct",
                "min:1",
            ];
        }

        return \Illuminate\Support\Facades\Validator::make($data, $rules);
    }

    public function youthRegisterValidation(Request $request, int $id = null): Validator
    {
        $data = $request->all();

        /*        $customMessage = [
                    "password.regex" => [
                        "message" => [
                            "Have At least one Uppercase letter",
                            "At least one Lower case letter",
                            "Also,At least one numeric value",
                            "And, At least one special character",
                            "Must be more than 8 characters long"
                        ]
                    ]
                ];
        */

        if (!empty($data["skills"])) {
            $data["skills"] = is_array($request['skills']) ? $request['skills'] : explode(',', $request['skills']);
        }

        if (!empty($data["physical_disabilities"])) {
            $data["physical_disabilities"] = is_array($request['physical_disabilities']) ? $request['physical_disabilities'] : explode(',', $request['physical_disabilities']);
        }

        $rules = [
            'user_name_type' => [
                Rule::in(BaseModel::USER_NAME_TYPES)
            ],
            "first_name" => "required|string|min:2|max:500",
            "first_name_en" => "nullable|string|min:2|max:250",
            "last_name" => "required|string|min:2|max:500",
            "last_name_en" => "nullable|string|min:2|max:250",
            "loc_division_id" => [
                "required",
                "int",
                "exists:loc_divisions,id,deleted_at,NULL",
            ],
            "loc_district_id" => [
                "required",
                "int",
                "exists:loc_districts,id,deleted_at,NULL",
            ],
            "loc_upazila_id" => [
                "nullable",
                "int",
                "exists:loc_upazilas,id,deleted_at,NULL",
            ],
            "date_of_birth" => [
                "required",
                'date',
                'date_format:Y-m-d'
            ],
            "gender" => [
                "required",
                Rule::in(BaseModel::GENDERS),
                "int"
            ],
            "email" => [
                Rule::requiredIf(function () use ($data) {
                    return isset($data["user_name_type"]) && $data["user_name_type"] == BaseModel::USER_NAME_TYPE_EMAIL;
                }),
                "email",
                Rule::unique('youths', 'email')
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            "mobile" => [
                Rule::requiredIf(function () use ($data) {
                    return isset($data["user_name_type"]) && $data["user_name_type"] == BaseModel::USER_NAME_TYPE_MOBILE_NUMBER;
                }),
                "max:11",
                BaseModel::MOBILE_REGEX,
                Rule::unique('youths', 'mobile')
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    }),
            ],
            "physical_disability_status" => [
                "required",
                "int",
                Rule::in(BaseModel::PHYSICAL_DISABILITIES_STATUSES)
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
            "password" => [
                "required",
                "confirmed",
                Password::min(BaseModel::PASSWORD_MIN_LENGTH_V1)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
            ],
            "password_confirmation" => [
                "required_with:password"
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
                "nullable",
                "string",
                "size:4"
            ]
        ];

        if (isset($request['physical_disability_status']) && $request['physical_disability_status'] == BaseModel::TRUE) {
            $rules['physical_disabilities'] = [
                Rule::requiredIf(function () use ($data) {
                    return $data['physical_disability_status'] == BaseModel::TRUE;
                }),
                "array",
                "min:1"
            ];
            $rules['physical_disabilities.*'] = [
                Rule::requiredIf(function () use ($data) {
                    return $data['physical_disability_status'] == BaseModel::TRUE;
                }),
                "exists:physical_disabilities,id,deleted_at,NULL",
                "int",
                "distinct",
                "min:1",
            ];
        }

        return \Illuminate\Support\Facades\Validator::make($data, $rules);
    }

    /**
     * @param string $id
     * @return Youth|null
     */
    public function getAuthYouth(string $id): ?Youth
    {
        /** @var Youth $youth */
        return Youth::where('idp_user_id', $id)
            ->where("row_status", BaseModel::ROW_STATUS_ACTIVE)
            ->first();
    }
}
