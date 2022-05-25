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

class YouthBulkImportForCourseEnrollmentService
{
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

    public function updateYouthEducations(array $data, Youth $youth): void
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
    public function updateYouthGuardian(array $data, Youth $youth): void
    {
        if (!empty($data['guardian_info'])) {
            $guardianInfo = [];
            $tempRelationshipData = -1;
            foreach ($data['guardian_info'] as $key => $value) {
                $explode = explode("_", $key);
                if (sizeof($explode) > 0 && in_array($explode[0], array_keys(YouthGuardian::RELATIONSHIP_TYPE))) {
                    $relationshipType = YouthGuardian::RELATIONSHIP_TYPE[$explode[0]];
                    $replacePrefix = $explode[0] . "_";
                    $attribute = str_replace($replacePrefix, "", $key);
                    $guardianInfo[$attribute] = $value;
                    if ($tempRelationshipData != $relationshipType) {
                        $tempRelationshipData = $relationshipType;
                        Log::info("guradian" . json_encode([
                                $relationshipType,
                                $guardianInfo
                            ], JSON_PRETTY_PRINT));

                        YouthGuardian::updateOrCreate(
                            [
                                "relationship_type" => $relationshipType,
                                "youth_id" => $youth->id
                            ],
                            $guardianInfo
                        );

                        $guardianInfo = [];
                    }
                }
            }
        }
    }


    public function sendMailCourseEnrollmentSuccess(array $mailPayload): void
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

    public function updateYouthAddresses(array $data, Youth $youth): void
    {
        if (!empty($data['address_info'])) {
            if (!empty($data['address_info']['present_address'])) {
                $addressValues = $data['address_info']['present_address'];
                $addressValues['youth_id'] = $youth->id;
                $addressValues['address_type'] = YouthAddress::ADDRESS_TYPE_PRESENT;
                YouthAddress::updateOrCreate([
                    "youth_id" => $youth->id,
                    "address_type" => YouthAddress::ADDRESS_TYPE_PRESENT
                ], $addressValues);
            }
            if (!empty($data['address_info']['permanent_address'])) {

                $addressValues['youth_id'] = $youth->id;
                $addressValues['address_type'] = YouthAddress::ADDRESS_TYPE_PERMANENT;
                YouthAddress::updateOrCreate([
                    "youth_id" => $youth->id,
                    "address_type" => YouthAddress::ADDRESS_TYPE_PERMANENT
                ], $addressValues);

            }
        }
    }

    public function rollbackYouthById(string $username): void
    {
        /** @var Youth $youth */
        $youth = Youth::where("username", $username)->first();
        if ($youth) {
            YouthAddress::where("youth_id", $youth->id)->delete();
            YouthGuardian::where("youth_id", $youth->id)->delete();
            YouthEducation::where("youth_id", $youth->id)->delete();
            $data['physical_disability_status'] = BaseModel::FALSE;
            $this->updateYouthPhysicalDisabilities($data, $youth);
            app(YouthProfileService::class)->idpUserDelete($youth->idp_user_id);
            Youth::where("id", $youth->id)->delete();
            Log::info("youth-rollback" . json_encode([$youth], JSON_PRETTY_PRINT));
        }
    }

    public function updateYouthPhysicalDisabilities(array $data, Youth $youth)
    {
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

    public function getPassword(): string
    {
        return Youth::YOUTH_DEFAULT_PASSWORD;
    }
}
