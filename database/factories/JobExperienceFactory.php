<?php

namespace Database\Factories;

use App\Model;
use App\Models\EmploymentType;
use App\Models\JobExperience;
use App\Models\Youth;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobExperienceFactory extends Factory
{
    protected $model = JobExperience::class;

    public function definition(): array
    {
        $company = $this->faker->company();
        $position = $this->faker->jobTitle();
        $location = $this->faker->streetAddress();
        $locationEn = $this->faker->streetAddress();
        $jobDescription = $this->faker->jobTitle();
        $jobDescriptionEn = $this->faker->jobTitle();
        $youthId = Youth::all()->random()->id;

        return [
            'company_name' => ucfirst($company),
            'company_name_en' => ucfirst($company),
            'position' => $position,
            'position_en' => $position,
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
