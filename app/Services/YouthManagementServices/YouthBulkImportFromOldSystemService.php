<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\LocDistrict;
use App\Models\LocDivision;
use App\Models\Youth;
use App\Models\YouthAddress;
use App\Models\YouthGuardian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class YouthBulkImportFromOldSystemService
{
    public function youthBulkImportFromOldSystem(): void
    {
        $json = Storage::get('youth-data.json');
        $data = json_decode($json, true);
        $youthInformation = [];
        foreach ($data as $datum) {
            $this->youthBasicInformation($youthInformation, $datum);
            $validatedData = $this->youthValidation($youthInformation)->validate();
            $validatedData['username'] = $validatedData['mobile'];
            if (!$this->youthExist($validatedData['username'])) {
                $youth = new Youth();
                $youth->setTable("youth_temp");
                $youth->fill($validatedData);
                $youth->save();
                $this->storeGuardianInfo($datum, $youth->id);
                $this->storeAddress($datum, $youth->id);
            } else {
                Log::channel('youth_bulk_import')->info("Youth is Exist: " . json_encode($datum));
            }
        }
    }

    private function youthBasicInformation(array &$basicInfo, $data): void
    {
        $basicInfo['username'] = bn2en($data['phone']);
        $basicInfo['first_name'] = $data['first_name'];
        $basicInfo['last_name'] = $data['last_name'] ?? "";
        $basicInfo['email'] = strtolower($data['email']);
        $basicInfo['mobile'] = bn2en($data['phone']);
        $basicInfo['date_of_birth'] = bn2en($data['dob']);
        $basicInfo['gender'] = $data['gender'];

        if ($data['loc_division_id']) {
            $basicInfo['loc_division_id'] = $this->getLocationId($data['loc_division_id'], 1);
        }
        if ($data['loc_division_id']) {
            $basicInfo['loc_district_id'] = $this->getLocationId($data['loc_district_id'], 2);
        }

        if (!empty($data['nid_no'])) {
            $basicInfo['identity_number_type'] = Youth::NID;
            $basicInfo['identity_number'] = bn2en($data['nid_no']);
        } elseif (!empty($data['birth_registration'])) {
            $basicInfo['identity_number_type'] = Youth::BIRTH_CARD;
            $basicInfo['identity_number'] = bn2en($data['birth_registration']);
        }

        if (!empty($data['biography'])) {
            $basicInfo['bio'] = $data['biography'];
        }

        if (!empty($data['postal_code'])) {
            $basicInfo['zip_or_postal_code'] = $data['postal_code'];
        }

    }

    public function youthExist(string $username): bool
    {
        return (bool)DB::table('youth_temp')->where("username", $username)->count("id");
    }

    public function youthValidation(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            "first_name" => "required|string|min:2|max:500",
            "last_name" => "nullable|string|min:2|max:500",
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
            "email" => [
                "required",
                "email"
            ],
            "mobile" => [
                "required",
            ],
            'identity_number_type' => [
                'nullable',
                'int',
                Rule::in(Youth::IDENTITY_TYPES)
            ],
            'identity_number' => [
                'nullable',
                'string'
            ],
            "bio" => "nullable|string",
            "zip_or_postal_code" => [
                "nullable",
                "size:4"
            ]
        ];

        return Validator::make($data, $rules);
    }

    private function storeGuardianInfo(array $data, int $youthId): void
    {
        $guardianInfo = [];
        if (!empty($data['father_name'])) {
            $guardianInfo[] = [
                "youth_id" => $youthId,
                "name" => $data['father_name'],
                "relationship_type" => YouthGuardian::RELATIONSHIP_TYPE_FATHER
            ];
        }

        if (!empty($data['mother_name'])) {
            $guardianInfo[] = [
                "youth_id" => $youthId,
                "name" => $data['mother_name'],
                "relationship_type" => YouthGuardian::RELATIONSHIP_TYPE_MOTHER
            ];
        }
        DB::table('youth_guardian_temp')->insert($guardianInfo);
    }

    private function storeAddress(array $data, int $youthId)
    {
        if ($data['present_loc_division_id'] && $data['present_loc_district_id']) {
            $address['loc_division_id'] = $this->getLocationId($data['present_loc_division_id'], 1);
            $address['loc_district_id'] = $this->getLocationId($data['present_loc_district_id'], 2);
            $address['zip_or_postal_code'] = $data['present_postal_code'];
            $address['youth_id'] = $youthId;
            DB::table("youth_address_temp")->insert($address);
        }

    }

    private function getLocationId(int $id, int $type): int|null
    {
        $jsonfilePath = [
            1 => Storage::get("division-bbs-code.json"),
            2 => Storage::get("district-bbs-code.json")
        ];

        $locationId = null;
        $bbsCode = json_decode($jsonfilePath[$type], true);
        if ($type == 1 && !empty($bbsCode[$id])) {
            $locationId = LocDivision::where("bbs_code", $bbsCode[$id])->first()->id;
        } elseif ($type == 2 && !empty($bbsCode[$id])) {
            $locationId = LocDistrict::where("bbs_code", $bbsCode[$id])->first()->id;
        }
        return $locationId;
    }
}
