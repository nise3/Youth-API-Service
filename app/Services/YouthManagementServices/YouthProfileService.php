<?php


namespace App\Services\YouthManagementServices;

use App\Exceptions\HttpErrorException;
use App\Models\AppliedJob;
use App\Models\BaseModel;
use App\Models\PhysicalDisability;
use App\Models\Skill;
use App\Models\Youth;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Throwable;


/**
 * Class YouthProfileService
 * @package App\Services\YouthManagementServices
 */
class YouthProfileService
{
    /**
     * @param array $youth_ids
     * @return Youth | Collection
     * @throws Exception
     */
    public function getYouthProfile(array $youth_ids = []): Youth|Collection
    {
        /** youth_ids only passed for bulk query */
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
                'youths.expected_salary',
                'youths.job_level',
                'youths.loc_division_id',
                'youths.code',
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

        if (count($youth_ids) > 0) {
            $youthProfileBuilder->whereIn('youths.id', $youth_ids);
        } else {
            $youthProfileBuilder->where('youths.id', '=', Auth::id());
        }
        $youthProfileBuilder->with(["physicalDisabilities", "youthLanguagesProficiencies", "skills", "youthEducations", "youthJobExperiences.areaOfExperiences", "youthJobExperiences.areaOfBusinesses", "youthCertifications", "youthPortfolios", "youthAddresses"]);

        /** adding additional profile infos */
        if (count($youth_ids) > 0) {
            $profileInfos = $youthProfileBuilder->get();
            if (empty($profileInfos) || count($profileInfos) != count($youth_ids)) throw new ModelNotFoundException();
            return $profileInfos->map(function ($profileInfo) {
                return $this->additionalProfileInfo($profileInfo);
            });
        } else {
            $profileInfo = $youthProfileBuilder->firstOrFail();
            return $this->additionalProfileInfo($profileInfo);
        }
    }

    public function additionalProfileInfo($profileInfo)
    {
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
     * @param Youth $youth
     * @param array $data
     * @return bool
     */
    public function setDefaultCvTemplateStatus(Youth $youth, array $data): bool
    {
        $youth->default_cv_template = $data['default_cv_template'];
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
                'account_disable' => false,
                'account_lock' => false
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
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function freelanceStatusValidator(Request $request): \Illuminate\Contracts\Validation\Validator
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
        return Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function defaultCvTemplateStatusValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            "default_cv_template.in" => "The youth default cv template must be in " . implode(',', array_keys(config('nise3.youth_cv_template'))) . ". [30000]"
        ];

        $rules = [
            "default_cv_template" => [
                "required",
                Rule::in(array_keys(config('nise3.youth_cv_template')))
            ]
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function verifyYouthValidator(Request $request): \Illuminate\Contracts\Validation\Validator
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

        return Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function resendCodeValidator(Request $request): \Illuminate\Contracts\Validation\Validator
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

        return Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @param Youth $youth
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function youthUpdateValidation(Request $request, Youth $youth): \Illuminate\Contracts\Validation\Validator
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

        return Validator::make($data, $rules);
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function youthRegisterValidation(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
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

        return Validator::make($data, $rules);
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
            ->throw(static function (Response $httpResponse, $httpException) use ($urlWithQueryStr) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $urlWithQueryStr . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function youthEnrollCoursesFilterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
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

        return Validator::make($requestData, $rules, $customMessage);
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
            ->throw(static function (Response $httpResponse, $httpException) use ($urlWithSkillIds) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $urlWithSkillIds . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
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

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function youthCareerInfoUpdateValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'expected_salary' => [
                'integer',
                'required'
            ],
            'job_level' => [
                'integer',
                'required',
                Rule::in(Youth::JOB_LEVELS)
            ]
        ];

        return Validator::make($request->all(), $rules);
    }

    /**
     * @param Youth $youth
     * @param array $careerInfoData
     * @return Youth
     */
    public function youthCareerInfoUpdate(Youth $youth, array $careerInfoData): Youth
    {
        $youth->expected_salary = $careerInfoData['expected_salary'];
        $youth->job_level = $careerInfoData['job_level'];
        $youth->save();
        return $youth;
    }
}
