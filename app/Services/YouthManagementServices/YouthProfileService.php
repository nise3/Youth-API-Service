<?php


namespace App\Services\YouthManagementServices;

use App\Facade\ServiceToServiceCall;
use App\Models\AppliedJob;
use App\Models\BaseModel;
use App\Models\PhysicalDisability;
use App\Models\Skill;
use App\Models\Youth;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use JetBrains\PhpStorm\ArrayShape;
use Throwable;


/**
 * Class YouthProfileService
 * @package App\Services\YouthManagementServices
 */
class YouthProfileService
{
    /**
     * @return Youth
     */
    public function getYouthProfile(): Youth
    {
        /** @var Builder|Youth $youthProfileBuilder */
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
                'youths.is_freelance_profile',
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

        $profileInfo = $youthProfileBuilder->firstOrFail();

        /** Calculate profile complete in percentage */
        $totalFields = count(Youth::PROFILE_COMPLETE_FIELDS);
        $filled = 0;
        if ($profileInfo) {
            foreach (Youth::PROFILE_COMPLETE_FIELDS as $field) {
                $value = json_decode(json_encode($profileInfo[$field]));
                if (!empty($value)) {
                    $filled++;
                }
            }
        }
        $completedProfile = floor((100 / $totalFields) * $filled);
        $profileInfo->offsetSet('profile_completed', $completedProfile);

        /** Calculate Total Job Experience */
        $totalJobExperiencesInMonth = 0;
        $totalExperience = [
            "year" => 0,
            "month" => 0
        ];
        if (!empty($profileInfo['youthJobExperiences'])) {
            $jobExperiences = json_decode(json_encode($profileInfo['youthJobExperiences']));
            if (is_array($jobExperiences) && count($jobExperiences) > 0) {
                foreach ($jobExperiences as $key => $value) {
                    if ($value->start_date) {
                        $startDate = Carbon::parse($value->start_date);
                        if ($value->end_date) {
                            $difference = $startDate->diffInMonths($value->end_date);
                        } else {
                            $currentDate = Carbon::now();
                            $difference = $startDate->diffInMonths($currentDate);
                        }
                        $totalJobExperiencesInMonth += $difference;
                    }
                }
            }
        }
        if ($totalJobExperiencesInMonth > 0) {
            $year = floor($totalJobExperiencesInMonth / 12);
            $month = $totalJobExperiencesInMonth % 12;
            $totalExperience["year"] = $year;
            $totalExperience["month"] = $month;
        }
        $profileInfo['total_job_experience'] = $totalExperience;

        return $profileInfo;
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
        if (!empty($data['physical_disabilities'])) {
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
     * @throws Throwable
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
            ->where("row_status", BaseModel::ROW_STATUS_PENDING)
            ->first();

        if ($youth) {
            $youth->row_status = BaseModel::ROW_STATUS_ACTIVE;
            $youth->verification_code_verified_at = Carbon::now();
            $youth->save();

            $idpUserPayload = [
                'id' => $youth->idp_user_id,
                'username' => $youth->username,
                'active' => (string)$youth->row_status
            ];
            $this->idpUserUpdate($idpUserPayload);
            //$this->sendYouthUserInfoByMail($youth);
            return true;
        }
        return false;
    }


    /**
     * Update Idp User
     * @throws Exception
     */
    public function idpUserUpdate(array $idpUserPayload): mixed
    {
        return IdpUser()
            ->setPayload($idpUserPayload)
            ->update()
            ->get();
    }

    /**
     * @param array $data
     * @param string $code
     * @return bool
     * @throws Exception
     */
    public function sendVerifyCode(array $data, string $code): bool
    {
        $email = $data["email"] ?? null;
        $mobile_number = $data["mobile"] ?? null;
        $message = "Welcome to NISE-3. Your OTP code : " . $code;
        if ($email) {
            return true;
        }
        if ($mobile_number) {
            $smsService = new SmsService($mobile_number, $message);
            $smsService->sendSms();
        }
        return false;
    }

    /**
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function resendCode(array $data): bool
    {
        $email = $data["email"] ?? null;
        $mobile = $data["mobile"] ?? null;
        $attributeField = $email ? "email" : "mobile";
        $payLoad[$attributeField] = $email ?: $mobile;

        $code = generateOtp(4);

        /** @var Youth $youth */
        $youth = Youth::where($attributeField, $payLoad[$attributeField])
            ->where("row_status", BaseModel::ROW_STATUS_PENDING)
            ->first();

        if ($youth) {
            $youth->verification_code = $code;
            $youth->verification_code_sent_at = Carbon::now();
            $youth->save();
            $payLoad["verification_code"] = $code;
            return $this->sendVerifyCode($payLoad, $code);
        }
        return false;
    }

    /**
     * Idp User Create
     * @param array $idpUserPayload
     * @return mixed
     * @throws Exception
     */
    public function idpUserCreate(array $idpUserPayload): mixed
    {
        Log::info("IDP_Payload is bellow");
        Log::info(json_encode($idpUserPayload));

        /** response from idp server after user creation */
        $response = IdpUser()->setPayload($idpUserPayload)->create()->get();
        Log::channel('idp_user')->info('idp_user_payload', $idpUserPayload);
        Log::channel('idp_user')->info('idp_user_info', $response);

        return $response;
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

    /**
     * @param Request $request
     * @return Validator
     */
    public function resendCodeValidator(Request $request): Validator
    {
        $customMessage = [
            "email.exists" => "The email does not exist in the system. [24000]",
            "mobile.exists" => "The mobile does not exist in the system. [24000]"
        ];

        $rules = [
            "email" => [
                Rule::requiredIf(function () use ($request) {
                    return !$request->exists('mobile');
                }),
                'nullable',
                "exists:youths,email,deleted_at,NULL"
            ],
            "mobile" => [
                Rule::requiredIf(function () use ($request) {
                    return !$request->exists('email');
                }),
                'nullable',
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
            $data["skills"] = is_array($data['skills']) ? $data['skills'] : explode(',', $data['skills']);
        }

        if (!empty($data["physical_disabilities"])) {
            $data["physical_disabilities"] = is_array($data['physical_disabilities']) ? $data['physical_disabilities'] : explode(',', $data['physical_disabilities']);
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
                'nullable',
                "array",
                "min:1"
            ];
            $rules['physical_disabilities.*'] = [
                Rule::requiredIf(function () use ($data) {
                    return $data['physical_disability_status'] == BaseModel::TRUE;
                }),
                'nullable',
                "exists:physical_disabilities,id,deleted_at,NULL",
                "int",
                "distinct",
                "min:1",
            ];
        }

        return \Illuminate\Support\Facades\Validator::make($data, $rules);
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function youthRegisterValidation(Request $request, int $id = null): Validator
    {
        $data = $request->all();

        if (!empty($data["skills"])) {
            $data["skills"] = isset($data['skills']) && is_array($data['skills']) ? $data['skills'] : explode(',', $data['skills']);
        }

        if (!empty($data["physical_disabilities"])) {
            $data["physical_disabilities"] = isset($data['physical_disabilities']) && is_array($data['physical_disabilities']) ? $data['physical_disabilities'] : explode(',', $data['physical_disabilities']);
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
                'integer',
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
                'nullable',
                "array",
                "min:1"
            ];
            $rules['physical_disabilities.*'] = [
                Rule::requiredIf(function () use ($data) {
                    return $data['physical_disability_status'] == BaseModel::TRUE;
                }),
                'nullable',
                "int",
                "distinct",
                "min:1",
                "exists:physical_disabilities,id,deleted_at,NULL",
            ];
        }

        return \Illuminate\Support\Facades\Validator::make($data, $rules);
    }

    /**
     * @param string $id
     * @return  Youth|null
     */
    public function getAuthYouth(string $id): Youth|null
    {
        return Youth::where('idp_user_id', $id)
            ->where("row_status", BaseModel::ROW_STATUS_ACTIVE)
            ->first();
    }

    /**
     * @param array $data
     * @return array
     * @throws RequestException
     */
    public function getYouthEnrollCourses(array $data): array
    {
        $url = clientUrl(BaseModel::INSTITUTE_URL_CLIENT_TYPE) . 'youth-enroll-courses';
        $queryStr = http_build_query($data);
        $urlWithQueryStr = $url . '?' . $queryStr;

        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
            'timeout' => config("nise3.http_timeout")
        ])
            ->get($urlWithQueryStr)
            ->throw(function ($response, $e) use ($url) {
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . json_encode($response));
                return $e;
            })
            ->json();
    }

    /**
     * @param Request $request
     * @return Validator
     */
    public function youthEnrollCoursesFilterValidator(Request $request): Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be between 0 to 3. [30000]'
        ];

        $requestData = $request->all();

        $rules = [
            'course_id' => 'nullable|int|gt:0',
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "int",
                Rule::in(Youth::ROW_STATUSES),
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($requestData, $rules, $customMessage);
    }

    /**
     * @param array $data
     * @return array
     */
    public function applyToJob(array $data): array
    {
        $jobId = $data['job_id'];
        $youthId = intval($data['youth_id']);
        return AppliedJob::updateOrCreate(
            [
                'job_id' => $jobId,
                'youth_id' => $youthId,
            ],
            [
                'job_id' => $jobId,
                'youth_id' => $youthId,
            ]
        )->toArray();
    }

    /**
     * @param Request $request
     * @return Validator
     */
    public function youthApplyToJobFilterValidator(Request $request): Validator
    {
        $requestData = $request->all();
        $jobId = $requestData['job_id'];
        $jobData = ServiceToServiceCall::getJobInfo($jobId);
        $matchingCriteria = $jobData['matching_criteria'];
        $youthData = $this->getYouthProfile()->toArray();

        $requestData["age_valid"] = 1;
        $requestData["experience_valid"] = 1;
        $requestData["gender_valid"] = 1;
        $requestData["location_valid"] = 1;

        if ($matchingCriteria["is_age_enabled"] == 1 && $matchingCriteria["is_age_mandatory"] == 1) {
            $ageMin = intval($matchingCriteria["candidate_requirement"]["age_minimum"]);
            $ageMax = intval($matchingCriteria["candidate_requirement"]["age_maximum"]);
            $dbDate = Carbon::parse($youthData["date_of_birth"]);
            $age = Carbon::now()->diffInYears($dbDate);
            $requestData["age_valid"] = $age >= $ageMin && $age <= $ageMax ? 1 : 0;
        }

        if ($matchingCriteria["is_total_year_of_experience_enabled"] == 1 && $matchingCriteria["is_total_year_of_experience_mandatory"] == 1) {
            $expMin = intval($matchingCriteria["candidate_requirement"]["minimum_year_of_experience"]);
            $expMax = intval($matchingCriteria["candidate_requirement"]["maximum_year_of_experience"]);
            $exp = intval($youthData["total_job_experience"]["year"]);
            $requestData["experience_valid"] = $exp >= $expMin && $exp <= $expMax ? 1 : 0;
        }

        if ($matchingCriteria["is_gender_enabled"] == 1 && $matchingCriteria["is_gender_mandatory"] == 1) {
            $gender = intval($youthData["gender"]);
            $genderMatch = false;
            foreach ($matchingCriteria["genders"] as $genderItem) {
                $genderMatch = ($genderMatch || (intval($genderItem['gender_id']) == $gender));
            }
            $requestData["gender_valid"] = $genderMatch ? 1 : 0;
        }

        if ($matchingCriteria["is_job_location_enabled"] == 1 && $matchingCriteria["is_job_location_mandatory"] == 1) {
            $location = [
                "division" => $youthData["loc_division_id"],
                "district" => $youthData["loc_district_id"],
                "upazila" => $youthData["loc_upazila_id"],
            ];
            $locationMatch = false;
            foreach ($matchingCriteria["job_locations"] as $jobLoc) {
                $division = $jobLoc["loc_division_id"];
                $district = $jobLoc["loc_district_id"];
                $upazila = $jobLoc["loc_upazila_id"];
                // TODO: match these with youth data when available
                // $union = $jobLoc["loc_union_id"];
                // $cityCorporation = $jobLoc["loc_city_corporation_id"];
                // $cityCorporationWard = $jobLoc["loc_city_corporation_ward_id"];
                // $area = $jobLoc["loc_area_id"];
                $locationMatch = $locationMatch || (
                        $division == $location["division"] ||
                        $district == $location["district"] ||
                        $upazila == $location["upazila"]
                    );
            }
            $requestData["location_valid"] = $locationMatch ? 1 : 0;
        }

        $rules = [
            'job_id' => 'nullable|string',
            "age_valid" => [
                'required',
                'integer',
                Rule::in([1])
            ],
            "experience_valid" => [
                'required',
                'integer',
                Rule::in([1])
            ],
            "gender_valid" => [
                'required',
                'integer',
                Rule::in([1])
            ],
            "location_valid" => [
                'required',
                'integer',
                Rule::in([1])
            ],
        ];

        $customMessage = [
            'age_valid.in' => 'Age must be valid. [30000]',
            'experience_valid.in' => 'Experience must be valid. [30000]',
            'gender_valid.in' => 'Gender must be valid. [30000]',
            'location_valid.in' => 'Location must be valid. [30000]'
        ];

        return \Illuminate\Support\Facades\Validator::make($requestData, $rules, $customMessage);
    }

    /**
     * @param $youthId
     * @return array
     * @throws RequestException
     */
    public function getYouthFeedStatisticsData($youthId): array
    {
        $url = clientUrl(BaseModel::INSTITUTE_URL_CLIENT_TYPE) . 'youth-feed-statistics/' . $youthId;
        $skillIds = DB::table('youth_skills')->where('youth_id', $youthId)->pluck('skill_id')->toArray();
        $skillIds = implode(",", $skillIds);
        $urlWithSkillIds = $url . '?' . "skill_ids=" . $skillIds;
        Log::info($urlWithSkillIds);
        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
            'timeout' => config("nise3.http_timeout")
        ])
            ->get($urlWithSkillIds)
            ->throw(function ($response, $e) use ($url) {
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . json_encode($response));
                return $e;
            })
            ->json();
    }

    /**
     * @param Youth $youth
     * @throws Throwable
     */
    private function sendYouthUserInfoByMail(Youth $youth)
    {
        $mailService = new MailService();
        $mailService->setTo([
            $youth->email
        ]);
        $from = $mailPayload['from'] ?? BaseModel::NISE3_FROM_EMAIL;
        $subject = $mailPayload['subject'] ?? "Youth Registration";
        $mailService->setForm($from);
        $mailService->setSubject($subject);
        $mailService->setMessageBody($youth->toArray());
        $instituteRegistrationTemplate = $mailPayload['template'] ?? 'mail.youth-create-default-template';
        $mailService->setTemplate($instituteRegistrationTemplate);
        $mailService->sendMail();
    }
}
