<?php

namespace Database\Factories;

use App\Model;
use App\Models\Certification;
use App\Models\EmploymentType;
use App\Models\Youth;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificationFactory extends Factory
{
    protected $model = Certification::class;

    public function definition(): array
    {
        $certificationName = $this->faker->name();
        $instituteName = $this->faker->name();
        $location = $this->faker->streetAddress();
        $locationEn = $this->faker->streetAddress();
        $jobDescription = $this->faker->jobTitle();
        $jobDescriptionEn = $this->faker->jobTitle();
        $start = $this->faker->dateTimeBetween('next Monday', 'next Monday +7 days');
        $youthId = Youth::all()->random()->id;

    	return [
//            'company_name' => ucfirst($company),
//            'company_name_en' => ucfirst($company),
//            'position' => $position,
//            'position_en' => $position,
            'job_description' => $jobDescription,
            'job_description_en' => $jobDescriptionEn,
            'location' => $location,
            'location_en' => $locationEn,
            'start_date' => $this->faker->dateTime(),
            'end_date' => $this->faker->dateTime(),
            'youth_id' => $youthId,
            'employment_type_id' => EmploymentType::all()->random()->id,
    	];
    }
}
