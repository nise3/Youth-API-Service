<?php

namespace Database\Factories;

use App\Models\Portfolio;
use App\Models\Youth;
use Illuminate\Database\Eloquent\Factories\Factory;

class PortfolioFactory extends Factory
{
    protected $model = Portfolio::class;

    public function definition(): array
    {
        $title = $this->faker->jobTitle();
        $description = $this->faker->sentence(100);
        $filePath = $this->faker->filePath();
        $youthId = Youth::all()->random()->id;

    	return [
            'title' => ucfirst($title),
            'title_en' => ucfirst($title),
            'description' => $description,
            'description_en' => $description,
            'file_path' => $filePath,
            'youth_id' => $youthId,
    	];
    }
}
