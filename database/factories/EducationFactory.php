<?php

namespace Database\Factories;

use App\Models\Education;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationFactory extends Factory
{
    protected $model = Education::class;

    public function definition(): array
    {
        $company = $this->faker->company();

        return [
            'institute_name' => ucfirst($company),
            'institute_name_en' => ucfirst($company),
            'examination_id' => $this->faker->randomElement([1, 3, 5]),
            'board_id' => $this->faker->numberBetween(1, 8),
            'edu_group_id' => $this->faker->numberBetween(1, 3),
            'registration_number' => $this->faker->numerify('############'),
            'roll_number' => $this->faker->numerify('############'),
            'result_type' => 2,
            'cgpa_gpa_max_value' => 5,
            'received_cgpa_gpa' => $this->faker->randomFloat(2, 2.0, 5.0),
            'passing_year' => $this->faker->dateTimeBetween('-35 years', '-15 years', null)
        ];
    }
}
