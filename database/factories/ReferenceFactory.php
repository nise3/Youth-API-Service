<?php

namespace Database\Factories;

use App\Models\Reference;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReferenceFactory extends Factory
{

    protected $model = Reference::class;

    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $address = $this->faker->address();
        return [
            'referrer_first_name' => $firstName,
            'referrer_first_name_en' => $firstName,
            'referrer_last_name' => $lastName,
            'referrer_last_name_en' => $lastName,
            'referrer_organization_name' => $this->faker->company(),
            'referrer_organization_name_en' => $this->faker->company(),
            'referrer_designation' => $this->faker->jobTitle(),
            'referrer_designation_en' => $this->faker->jobTitle(),
            'referrer_address' => $address,
            'referrer_address_en' => $address,
            'referrer_email' => $this->faker->safeEmail(),
            'referrer_mobile' => $this->faker->phoneNumber(),
            'referrer_relation' => $this->faker->word(),
            'referrer_relation_en' => $this->faker->word(),
        ];
    }
}
