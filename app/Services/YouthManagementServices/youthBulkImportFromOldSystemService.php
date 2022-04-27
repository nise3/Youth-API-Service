<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Youth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class youthBulkImportFromOldSystemService
{
    public function youthBulkImportFromOldSystem(array $data)
    {
        $youthInformation = [];
        $guardianInfo = [];
        $key = 1;
        foreach ($data as $datum) {
            $this->youthBasicInformation($youthInformation, $datum);
            $this->getGuardianInfo($guardianInfo, $datum);
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
            $basicInfo['loc_division_id'] = $data['loc_division_id'];
        }
        if ($data['loc_division_id']) {
            $basicInfo['loc_district_id'] = $data['loc_division_id'];
        }
        if ($data['loc_upazila_id']) {
            $basicInfo['loc_upazila_id'] = $data['loc_upazila_id'];
        }

        if (!empty($data['nid_no'])) {
            $basicInfo['identity_type'] = Youth::NID;
            $basicInfo['identity_number'] = bn2en($data['nid_no']);
        } elseif (!empty($data['birth_registration'])) {
            $basicInfo['identity_type'] = Youth::BIRTH_CARD;
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
        return (bool)Youth::where("username", $username)->count("id");
    }

    public function youthValidation(Request $request, Youth $youth): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();

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
            "email" => [
                Rule::unique('youths', 'email')
                    ->ignore($youth->id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            "mobile" => [
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
                'string'
            ],
            "bio" => "nullable|string",
            "zip_or_postal_code" => [
                "nullable",
                "string",
                "size:4"
            ]
        ];

        return Validator::make($data, $rules);
    }

    private function getGuardianInfo(array &$guardianInfo, array $data)
    {

    }
}
