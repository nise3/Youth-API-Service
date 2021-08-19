<?php

namespace Database\Factories;

use App\Models\Youth;
use Illuminate\Database\Eloquent\Factories\Factory;

class YouthFactory extends Factory
{
    protected $model = Youth::class;

    public function definition(): array
    {
        $name = $this->faker->name();
        $father_name = $this->faker->name();
        $mother_name = $this->faker->name('female');
        $guardian_name = $this->faker->name();

        return [
            'name_en' => ucfirst($name),
            'name_bn' => ucfirst($name),
            'mobile' => $this->faker->phoneNumber(),
            'email' =>$this->faker->safeEmail(),
            "father_name_en" =>ucfirst($father_name),
            "father_name_bn" =>ucfirst($father_name),
            "mother_name_en"=>ucfirst($mother_name),
            "mother_name_bn"=>ucfirst($mother_name),
            "guardian_name_en"=>ucfirst($guardian_name),
            "guardian_name_bn"=>ucfirst($guardian_name),
            "relation_with_guardian" =>"guardian",
            "number_of_siblings"=>$this->faker->randomDigit(),
            "gender"=>$this->faker->randomElement([1,2,3]),
            "date_of_birth"=>$this->faker->date(),
            "birth_certificate_no"=> $this->faker->shuffleString("123456789"),
            "nid"=>$this->faker->shuffleString("123456789"),
            "passport_number"=>$this->faker->shuffleString("123456789"),
            "nationality"=>"Bangladeshi",
            "religion"=>$this->faker->randomElement([1,2,3,4,5]),
            "marital_status"=>$this->faker->boolean(),
            "current_employment_status"=>$this->faker->boolean(),
            "main_occupation"=>$this->faker->randomElement(['Student','Engineer','Banker','Business']),
            "other_occupation"=>$this->faker->randomElement(['Student','Engineer','Banker','Business']),
            "personal_monthly_income"=>$this->faker->randomElement(['10000','20000','30000','40000']),
            "year_of_experience"=>$this->faker->randomDigit(),
            "physical_disabilities_status"=>$this->faker->boolean(),
            "freedom_fighter_status"=>$this->faker->boolean(),
            "present_address_division_id"=>$this->faker->randomDigit(),
            "present_address_district_id"=>$this->faker->randomDigit(),
            "present_address_upazila_id"=>$this->faker->randomDigit(),
            "present_house_address"=> $this->faker->address(),
            "permanent_address_division_id"=>$this->faker->randomDigit(),
            "permanent_address_district_id"=>$this->faker->randomDigit(),
            "permanent_address_upazila_id"=>$this->faker->randomDigit(),
            "permanent_house_address"=>$this->faker->address(),
            "is_ethnic_group"=>$this->faker->boolean(),
            "photo"=>$this->faker->sentence(),
            "signature"=>$this->faker->name()

        ];
    }
}
