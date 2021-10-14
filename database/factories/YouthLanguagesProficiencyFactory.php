<?php

namespace Database\Factories;

use App\Models\YouthLanguagesProficiency;
use Illuminate\Database\Eloquent\Factories\Factory;

class YouthLanguagesProficiencyFactory extends Factory
{
    protected $model = YouthLanguagesProficiency::class;

    public function definition(): array
    {
        return [
            'language_id' => $this->faker->numberBetween(1, 3),
            'reading_proficiency_level' => $this->faker->randomElement([1, 2]),
            'writing_proficiency_level' => $this->faker->randomElement([1, 2]),
            'speaking_proficiency_level' => $this->faker->randomElement([1, 2]),
            'understand_proficiency_level' => $this->faker->randomElement([1, 2])
        ];
    }
}
