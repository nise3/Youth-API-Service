<?php

namespace Database\Factories;

use App\Models\YouthGuardian;
use Illuminate\Database\Eloquent\Factories\Factory;

class YouthGuardianFactory extends Factory
{
    protected $model = YouthGuardian::class;

    public function definition(): array
    {
        return [
            'youth_id' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'name' => ucfirst($this->faker->name),
            'name_en' => ucfirst($this->faker->name),
            'mobile' => $this->faker->numerify('017########')

        ];
    }
}
