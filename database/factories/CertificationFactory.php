<?php

namespace Database\Factories;


use App\Models\Certification;
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
        $start = $this->faker->dateTime()->format('Y-m-d');
        $end = $this->faker->dateTimeBetween($start, $start.' +2 days');

    	return [
            'certification_name' => $certificationName,
            'certification_name_en' => $certificationName,
            'institute_name' => $instituteName,
            'institute_name_en' => $instituteName,
            'location' => $location,
            'location_en' => $locationEn,
            'start_date' => $start,
            'end_date' => $end,
            'certificate_file_path' => $certificationName,
    	];
    }
}
