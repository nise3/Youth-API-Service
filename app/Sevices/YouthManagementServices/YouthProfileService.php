<?php

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;


class YouthProfileService
{

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validation(Request $request, int $id = null): Validator
    {
        $rules = [
            "name_en" => "required|string|min:2|max:191",
            "name_bn" => "required|string|min:2|max:191",
            "mobile" => "required|string|min:1|max:20|unique:youths,mobile",
            "email" => "required|email|min:1|max:20|unique:youths,email",
            "father_name_en" => "nullable|string|min:2|max:191",
            "father_name_bn" => "nullable|string|min:2|max:191",
            "mother_name_en" => "nullable|string|min:2|max:191",
            "mother_name_bn" => "nullable|string|min:2|max:191",
            "guardian_name_en" => "nullable|string|min:2|max:191",
            "guardian_name_bn" => "nullable|string|min:2|max:191",
            "relation_with_guardian" => "nullable|string|min:2|max:191",
            "number_of_siblings" => "nullable|int",
            "gender" => "nullable|int",
            "date_of_birth" => "nullable|date",
            "birth_certificate_no" => "nullable|string",
            "nid" => "nullable|string",
            "passport_number" => "nullable|string",
            "nationality" => "nullable|string",
            "religion" => "nullable|int",
            "marital_status" => "nullable|int",
            "current_employment_status" => "nullable|int",
            "main_occupation" => "nullable|string",
            "other_occupation" => "nullable|string",
            "personal_monthly_income" => "nullable|numeric",
            "year_of_experience" => "nullable|int",
            "physical_disabilities_status" => "nullable|int",
            "freedom_fighter_status" => "nullable|int",
            "present_address_division_id" => "nullable|int",
            "present_address_district_id" => "nullable|int",
            "present_address_upazila_id" => "nullable|int",
            "present_house_address" => "nullable|string",
            "permanent_address_division_id" => "nullable|int",
            "permanent_address_district_id" => "nullable|int",
            "permanent_address_upazila_id" => "nullable|int",
            "permanent_house_address" => "nullable|string",
            "is_ethnic_group" => "nullable|int",
            "photo" => "nullable|string",
            "signature" => "nullable|string"
        ];
        if($id){
            $rules["mobile"]= "required|string|min:1|max:20|unique:youths,mobile,".$id;
            $rules["email"]= "required|string|min:1|max:20|unique:youths,email,".$id;
        }
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }
}
