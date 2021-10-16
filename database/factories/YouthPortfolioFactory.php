<?php

namespace Database\Factories;

use App\Models\YouthPortfolio;
use Illuminate\Database\Eloquent\Factories\Factory;

class YouthPortfolioFactory extends Factory
{
    protected $model = YouthPortfolio::class;

    public function definition(): array
    {
        $title = $this->faker->jobTitle();
        $description = $this->faker->sentence(100);
        $filePath = $this->faker->filePath();

        return [
            'title' => ucfirst($title),
            'title_en' => ucfirst($title),
            'description' => $description,
            'description_en' => $description,
            'file_path' => $filePath
        ];
    }
}
