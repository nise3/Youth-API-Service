<?php

namespace Database\Factories;

use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Factories\Factory;

class PortfolioFactory extends Factory
{
    protected $model = Portfolio::class;

    public function definition(): array
    {
        $title = $this->faker->jobTitle();
        $description = $this->faker->sentence(100);
        $filePath = $this->faker->filePath();
        $youthId = $this->faker->randomElement([1]);

    	return [
            'title' => ucfirst($title),
            'description' => $description,
            'file_path' => $filePath,
            'youth_id' => $youthId,
    	];
    }
}
