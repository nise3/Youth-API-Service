<?php

namespace Database\Factories;

use App\Models\YouthJobExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

class YouthJobExperienceFactory extends Factory
{
    protected $model = YouthJobExperience::class;

    public function definition(): array
    {
        $company = $this->faker->company();
        $position = $this->faker->jobTitle();
        $location = $this->faker->streetAddress();
        $jobDescription = $this->faker->jobTitle();

        return [
            'company_name' => ucfirst($company),
            'company_name_en' => ucfirst($company),
            'position' => $position,
            'position_en' => $position,
            'job_responsibilities' => $jobDescription,
            'job_responsibilities_en' => $jobDescription,
            'location' => $location,
            'location_en' => $location,
            'start_date' => $this->faker->dateTime(),
            'end_date' => $this->faker->dateTime(),
            'employment_type_id' => 1,
        ];
    }
}
