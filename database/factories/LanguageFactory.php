<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\LanguageInfo;
use App\Models\Youth;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition(): array
    {
        $youthId = Youth::all()->random()->id;
        $languageInfoId = LanguageInfo::all()->random()->id;
        return [
            'youth_id' => $youthId,
            'language_id' => $languageInfoId,
            'reading_proficiency_level' => $this->faker->randomElement([1,2]),
            'writing_proficiency_level' => $this->faker->randomElement([1,2]),
            'speaking_proficiency_level' => $this->faker->randomElement([1,2]),
            'understand_proficiency_level' => $this->faker->randomElement([1,2])
        ];
    }
}
