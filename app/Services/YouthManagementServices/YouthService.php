<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\EduBoard;
use App\Models\EducationLevel;
use App\Models\EduGroup;
use App\Models\PhysicalDisability;
use App\Models\Youth;
use App\Models\YouthAddress;
use App\Models\YouthEducation;
use App\Models\YouthGuardian;
use App\Services\CommonServices\MailService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class YouthService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getYouthProfileList(array $request, Carbon $startTime): array
    {
        $firstName = $request['first_name'] ?? "";
        $lastName = $request['last_name'] ?? "";
        $email = $request['email'] ?? "";
        $searchText = $request['search_text'] ?? "";
        $isFreelanceProfile = $request['is_freelance_profile'] ?? "";
        $locDistrictId = $request['loc_district_id'] ?? "";
        $locUpazilaId = $request['loc_upazila_id'] ?? "";
        $skillIds = $request['skill_ids'] ?? [];
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $youth = Auth::User();

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
                'youths.expected_salary',
                'youths.job_level',
                'youths.loc_division_id',
                'loc_divisions.title as division_title',
                'loc_divisions.title_en as division_title_en',
                'youths.loc_district_id',
                'loc_districts.title as district_title',
                'loc_districts.title_en as district_title_en',
                'youths.loc_upazila_id',
                'loc_upazilas.title as upazila_title',
                'loc_upazilas.title_en as upazila_title_en',
                DB::raw('SUBSTR(youths.bio, 1, 160) as bio'),
                DB::raw('SUBSTR(youths.bio_en, 1, 80) as bio_en'),
                'youths.gender',
                'youths.religion',
                'youths.marital_status',
                'youths.nationality',
                'youths.email',
                'youths.mobile',
                'youths.identity_number_type',
                'youths.identity_number',
                'youths.date_of_birth',
                'youths.is_freelance_profile',
                'youths.freedom_fighter_status',
                'youths.physical_disability_status',
                'youths.does_belong_to_ethnic_group',
                'youths.photo',
                'youths.cv_path',
                'youths.default_cv_template',
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

        $youthBuilder->with(['skills', 'physicalDisabilities']);

        if (!empty($youth) && !empty($youth['id'])) $youthBuilder->where('youths.id', '!=', $youth['id']);

        if (!empty($firstName)) {
            $youthBuilder->where('youths.first_name', 'like', '%' . $firstName . '%');
        }

        if (!empty($lastName)) {
            $youthBuilder->where('youths.last_name', 'like', '%' . $lastName . '%');
        }

        if (!empty($email)) {
            $youthBuilder->where('youths.email', 'like', '%' . $email . '%');
        }

        if (is_numeric($isFreelanceProfile)) {
            $youthBuilder->where('youths.is_freelance_profile', '=', $isFreelanceProfile);
        }

        if (is_numeric($rowStatus)) {
            $youthBuilder->where('youths.row_status', $rowStatus);
        }

        if (is_numeric($locUpazilaId)) {
            $youthBuilder->where('youths.loc_upazila_id', '=', $locUpazilaId);
        } else if (is_numeric($locDistrictId)) {
            $youthBuilder->where('youths.loc_district_id', '=', $locDistrictId);
        }

        if (!empty($searchText) || (is_array($skillIds) && count($skillIds) > 0)) {
            $youthBuilder->leftJoin('youth_skills', 'youth_skills.youth_id', '=', 'youths.id');

            if (!empty($searchText)) {
                $youthBuilder->leftJoin('skills', 'skills.id', '=', 'youth_skills.skill_id');

                $youthBuilder->where(function ($builder) use ($searchText) {
                    $builder->where('youths.first_name', 'like', '%' . $searchText . '%');
                    $builder->orWhere('youths.first_name_en', 'like', '%' . $searchText . '%');
                    $builder->orWhere('youths.last_name', 'like', '%' . $searchText . '%');
                    $builder->orWhere('youths.last_name_en', 'like', '%' . $searchText . '%');
                    $builder->orWhere('loc_divisions.title', 'like', '%' . $searchText . '%');
                    $builder->orWhere('loc_divisions.title_en', 'like', '%' . $searchText . '%');
                    $builder->orWhere('loc_districts.title', 'like', '%' . $searchText . '%');
                    $builder->orWhere('loc_districts.title_en', 'like', '%' . $searchText . '%');
                    $builder->orWhere('loc_upazilas.title', 'like', '%' . $searchText . '%');
                    $builder->orWhere('loc_upazilas.title_en', 'like', '%' . $searchText . '%');

                    /** Search youth by Skill name */
                    $builder->orWhere('skills.title', 'like', '%' . $searchText . '%');
                    $builder->orWhere('skills.title_en', 'like', '%' . $searchText . '%');
                });
            }

            /** Search youth by skill IDs */
            if (is_array($skillIds) && count($skillIds) > 0) {
                $youthBuilder->whereIn('youth_skills.skill_id', $skillIds);
            }
        }
        $youthBuilder->groupBy('youths.id');

        /** @var Collection $youths */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
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
     * @return Youth
     */
    public function getOneYouthProfile(int $id): Youth
    {
        /** @var Youth|Builder $youthBuilder */
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
                'youths.is_freelance_profile',
                'youths.expected_salary',
                'youths.job_level',
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
                'youths.freedom_fighter_status',
                'youths.physical_disability_status',
                'youths.does_belong_to_ethnic_group',
                'youths.bio as bio',
                'youths.bio_en as bio_en',
                'youths.photo',
                'youths.cv_path',
                'youths.signature_image_path',
                'youths.default_cv_template',
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
            "skills",
            "physicalDisabilities",
            "youthJobExperiences",
            "youthAddresses",
            "youthLanguagesProficiencies",
            "youthCertifications",
            "youthEducations",
            "youthPortfolios",
            "youthReferences"
        ]);

        /** @var Youth $youth */
        return $youthBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return Youth
     * @throws Throwable
     */
    public function store(array $data): Youth
    {
        $youth = app(Youth::class);
        $youth->fill($data);
        throw_if(!$youth->save(), 'RuntimeException', 'Youth has not been Saved to db.', 500);
        return $youth;
    }

    /**
     * @param Youth $youth
     * @param array $data
     * @return Youth
     * @throws Throwable
     */
    public function update(Youth $youth, array $data): Youth
    {
        $youth->fill($data);
        throw_if(!$youth->save(), 'RuntimeException', 'Youth has not been updated to db.', 500);
        return $youth;
    }


    /**
     * @param Youth $youth
     * @return bool
     * @throws Throwable
     */
    public function destroy(Youth $youth): bool
    {
        throw_if(!$youth->delete(), 'RuntimeException', 'Youth has not been deleted.', 500);
        return true;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be either 1 or 0. [30000]',
            'is_freelance_profile.in' => "'Is Freelance Profile' must be either 1 or 0. [30000]",
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        $requestData = $request->all();

        if (!empty($requestData['skill_ids'])) {
            $requestData['skill_ids'] = is_array($requestData['skill_ids']) ? $requestData['skill_ids'] : explode(',', $requestData['skill_ids']);
        }

        $rules = [
            'first_name' => 'nullable|string|max:300|min:2',
            'last_name' => 'nullable|string|max:300|min:2',
            'email' => 'nullable|string|min:2',
            'search_text' => 'nullable|string|max:150|min:2',
            'is_freelance_profile' => [
                'nullable',
                'int',
                Rule::in([0, 1])
            ],
            "loc_district_id" => [
                "nullable",
                "int"
            ],
            "loc_upazila_id" => [
                "nullable",
                "int"
            ],
            "skill_ids" => [
                'nullable',
                'array',
                'min:1',
                'max:10'
            ],
            "skill_ids.*" => [
                'required',
                'integer',
                'distinct',
                'min:1'
            ],
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "nullable",
                "integer",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($requestData, $rules, $customMessage);
    }

    /**
     * @return array
     */
    public function getEducationBasicTablesInfos()
    {
        return [
            "edu_groups" => EduGroup::all(),
            "edu_boards" => EduBoard::all(),
            "education_level_with_degrees" => EducationLevel::with('examDegrees')->get(),
            "result" => array_values(config("nise3.exam_degree_results"))
        ];
    }

    /**
     * @throws Throwable
     */
    public function sendMailCourseEnrollmentSuccess(array $mailPayload)
    {
        /** Mail send after user registration */
        $youth = Youth::findOrFail($mailPayload['youth_id']);
        $to = array($youth->email);
        $from = BaseModel::NISE3_FROM_EMAIL;
        $subject = "Course Enrollment Confirmation";
        $message = "Congratulation, You are successfully complete your course enrollment";
        $messageBody = MailService::templateView($message);
        $mailService = new MailService($to, $from, $subject, $messageBody);
        $mailService->sendMail();

    }

    /**
     * @param array $data
     * @return mixed
     */
    public function updateOrCreateYouth(array $data): mixed
    {
        return Youth::updateOrCreate([
            "username" => $data['mobile'],
        ], $data);
    }

    public function updateYouthEducations(array $data, Youth $youth)
    {
        if (!empty($data['education_info'])) {
            foreach ($data['education_info'] as $eduLabelId => $values) {
                $values['youth_id'] = $youth->id;
                $values['education_level_id'] = $eduLabelId;
                Log::info("Youth Education:" . json_encode($values, JSON_PRETTY_PRINT));
                YouthAddress::updateOrCreate([
                    "youth_id" => $youth->id,
                    "education_level_id" => $eduLabelId
                ], $values);
            }
        }
    }

    /**
     * @param array $data
     * @param Youth $youth
     */
    public function updateRplApplicationYouthEducations(array $data, Youth $youth)
    {
        if (!empty($data['education_info'])) {
            foreach ($data['education_info'] as $educationInfo) {
                $youthEducation = YouthEducation::where('youth_id', $youth->id)->where('education_level_id', $educationInfo['education_level_id'])->first();
                if (empty($youthEducation)) {
                    $youthEducation = app(YouthEducation::class);
                    $educationInfo['youth_id'] = $youth->id;
                }
                $youthEducation->fill($educationInfo);
                $youthEducation->save();
            }
        }

        Log::info("education saved");
    }

    /**
     * @param array $data
     * @param Youth $youth
     */
    public function updateYouthGuardian(array $data, Youth $youth): void
    {
        if (!empty($data['guardian_info'])) {
            $youthFather = YouthGuardian::where('youth_id', $youth->id)->where('relationship_type', YouthGuardian::RELATIONSHIP_TYPE_FATHER)->first();
            $youthMother = YouthGuardian::where('youth_id', $youth->id)->where('relationship_type', YouthGuardian::RELATIONSHIP_TYPE_MOTHER)->first();
            $guardian = $data['guardian_info'];
            if (empty($youthFather)) {
                $youthFather = app(YouthGuardian::class);
            }
            if (empty($youthMother)) {
                $youthMother = app(YouthGuardian::class);
            }
            $this->saveYouthGuardian($youthFather, $guardian, YouthGuardian::RELATIONSHIP_TYPE_FATHER, $youth->id);
            $this->saveYouthGuardian($youthMother, $guardian, YouthGuardian::RELATIONSHIP_TYPE_MOTHER, $youth->id);

        }
    }

    /**
     * @param array $data
     * @param Youth $youth
     */
    public function updateRplApplicationYouthGuardian(array $data, Youth $youth): void
    {

        if (!empty($data)) {
            $youthFather = YouthGuardian::where('youth_id', $youth->id)->where('relationship_type', YouthGuardian::RELATIONSHIP_TYPE_FATHER)->first();
            $youthMother = YouthGuardian::where('youth_id', $youth->id)->where('relationship_type', YouthGuardian::RELATIONSHIP_TYPE_MOTHER)->first();

            if (empty($youthFather)) {
                $youthFather = app(YouthGuardian::class);
            }
            if (empty($youthMother)) {
                $youthMother = app(YouthGuardian::class);
            }


            $youthFather->name = $youthFather->name ?? $data['father_name'];
            $youthFather->name_en = !empty($youthFather->name_en) ? $youthFather->name_en : (!empty($data['father_name_en']) ? $data['father_name_en'] : null);
            $youthFather->relationship_type = $youthFather->relationship_type ?? YouthGuardian::RELATIONSHIP_TYPE_FATHER;
            $youthFather->youth_id = $youth->id;
            $youthFather->save();


            $youthMother->name = $youthMother->name ?? $data['mother_name'];
            $youthMother->name_en = !empty($youthFather->name_en) ? $youthFather->name_en : (!empty($data['mother_name_en']) ? $data['mother_name_en'] : null);
            $youthMother->relationship_type = $youthMother->relationship_type ?? YouthGuardian::RELATIONSHIP_TYPE_MOTHER;
            $youthMother->youth_id = $youth->id;
            $youthMother->save();
            Log::info("guardian data saved");

        }
    }

    /**
     * @param array $data
     * @param Youth $youth
     */
    public function storeRplApplicationYouthInfo(array $data, Youth $youth)
    {

        $youth->first_name = !empty($youth->first_name) ? $youth->first_name : $data['first_name'];
        $youth->first_name_en = !empty($youth->first_name_en) ? $youth->first_name_en : (!empty($data['first_name_en']) ? $data['first_name_en'] : null);
        $youth->last_name = !empty($youth->last_name) ? $youth->last_name : $data['last_name'];
        $youth->identity_number_type = !empty($youth->identity_number_type) ? $youth->identity_number_type : $data['identity_number_type'];
        $youth->identity_number = !empty($youth->identity_number) ? $youth->identity_number : $data['identity_number'];
        $youth->photo = !empty($youth->photo) ? $youth->photo : (!empty($data['photo']) ? $data['photo'] : null);
        $youth->nationality = !empty($youth->nationality) ? $youth->nationality : $data['nationality'];
        $youth->religion = !empty($youth->religion) ? $youth->religion : $data['religion'];
        $youth->save();
        Log::info("youth data saved");

    }

    public function updateYouthAddresses(array $data, Youth $youth): void
    {
        if (!empty($data['address_info'])) {
            if (!empty($data['address_info']['present_address'])) {
                $addressValues = $data['address_info']['present_address'];
                if (empty($addressValues)) {
                    $addressValues['youth_id'] = $youth->id;
                    $addressValues['address_type'] = YouthAddress::ADDRESS_TYPE_PRESENT;
                }
                YouthAddress::updateOrCreate([
                    "youth_id" => $youth->id,
                    "address_type" => YouthAddress::ADDRESS_TYPE_PRESENT
                ], $addressValues);
            }
            if (!empty($data['address_info']['present_address'])) {
                $addressValues = $data['address_info']['permanent_address'];
                if (empty($addressValues)) {
                    $addressValues['youth_id'] = $youth->id;
                    $addressValues['address_type'] = YouthAddress::ADDRESS_TYPE_PERMANENT;
                }

                YouthAddress::updateOrCreate([
                    "youth_id" => $youth->id,
                    "address_type" => YouthAddress::ADDRESS_TYPE_PERMANENT
                ], $addressValues);

            }
        }
    }


    /**
     * @param array $data
     * @param Youth $youth
     */
    public function updateRplApplicationYouthAddresses(array $data, Youth $youth): void
    {
        if (!empty($data['present_address'])) {
            $youthPresentAddress = YouthAddress::where('youth_id', $youth->id)->where('address_type', YouthAddress::ADDRESS_TYPE_PRESENT)->first();
            $addressValues = $data['present_address'];
            if (empty($youthPresentAddress)) {
                $youthPresentAddress = app(YouthAddress::class);
                $addressValues['youth_id'] = $youth->id;
                $addressValues['address_type'] = YouthAddress::ADDRESS_TYPE_PRESENT;
            }
            $youthPresentAddress->fill($addressValues);
            $youthPresentAddress->save();

        }
        if (!empty($data['permanent_address'])) {
            $youthPermanentAddress = YouthAddress::where('youth_id', $youth->id)->where('address_type', YouthAddress::ADDRESS_TYPE_PERMANENT)->first();
            $addressValues = $data['permanent_address'];
            if (empty($youthPermanentAddress)) {
                $youthPermanentAddress = app(YouthAddress::class);
                $addressValues['youth_id'] = $youth->id;
                $addressValues['address_type'] = YouthAddress::ADDRESS_TYPE_PERMANENT;
            }
            $youthPermanentAddress->fill($addressValues);
            $youthPermanentAddress->save();
        }
        Log::info("Address saved");
    }


    public function updateYouthPhysicalDisabilities(array $data, Youth $youth)
    {
        Log::info(json_encode($data['physical_disability_status'], JSON_PRETTY_PRINT));
        if ($data['physical_disability_status'] == BaseModel::FALSE) {
            $this->detachPhysicalDisabilities($youth);
        } else if ($data['physical_disability_status'] == BaseModel::TRUE) {
            $this->assignPhysicalDisabilities($youth, $data['physical_disabilities']);
        }
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

    private function saveYouthGuardian(YouthGuardian $youthGuardian, array $guardian, int $relationshipType, int $youthId)
    {
        $relationshipStr = $relationshipType == YouthGuardian::RELATIONSHIP_TYPE_FATHER ? "father" : "mother";
        $youthGuardian->name = !empty($guardian[$relationshipStr . '_name']) ? $guardian[$relationshipStr . '_name'] : $youthGuardian->name;
        $youthGuardian->name_en = !empty($guardian[$relationshipStr . '_name_en']) ? $guardian[$relationshipStr . '_name_en'] : $youthGuardian->name_en;
        $youthGuardian->nid = !empty($guardian[$relationshipStr . '_nid']) ? $guardian[$relationshipStr . '_nid'] : $youthGuardian->nid;
        $youthGuardian->mobile = !empty($guardian[$relationshipStr . '_mobile']) ? $guardian[$relationshipStr . '_mobile'] : $youthGuardian->mobile;
        $youthGuardian->date_of_birth = !empty($guardian[$relationshipStr . '_date_of_birth']) ? $guardian[$relationshipStr . '_date_of_birth'] : $youthGuardian->date_of_birth;
        $youthGuardian->relationship_type = $relationshipType;
        $youthGuardian->youth_id = $youthId;
        Log::info("Youth Education:" . json_encode($youthGuardian, JSON_PRETTY_PRINT));
        $youthGuardian->save();
    }

    public function youthUpdateValidationForCourseEnrollmentBulkImport(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
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
            "last_name" => "required|string|min:2|max:500",
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
                'date_format:Y-m-d',
                function ($attr, $value, $failed) {
                    if (Carbon::parse($value)->greaterThan(Carbon::now()->subYear(5))) {
                        $failed('Age should be greater than 5 years.');
                    }
                }
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
                Rule::unique('youths', 'email')
                    ->ignore($id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            "mobile" => [
                Rule::unique('youths', 'mobile')
                    ->ignore($id)
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
                'nullable'
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

    public function rollbackYouthById(Youth $youth): void
    {
        YouthAddress::where("youth_id", $youth->id)->delete();
        YouthGuardian::where("youth_id", $youth->id)->delete();
        YouthEducation::where("youth_id", $youth->id)->delete();
        app(YouthService::class)->updateYouthPhysicalDisabilities([], $youth);
        app(YouthProfileService::class)->idpUserDelete($youth->idp_user_id);
        $youth->delete();
    }

    public function getPassword(): string
    {
        return Youth::YOUTH_DEFAULT_PASSWORD;
    }
}
