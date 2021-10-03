<?php

namespace Database\Factories;

use App\Models\Youth;
use Illuminate\Database\Eloquent\Factories\Factory;

class YouthFactory extends Factory
{
    protected $model = Youth::class;

    public function definition(): array
    {
        $name = $this->faker->name();
        $email = $this->faker->safeEmail();

        return [
            'first_name' => ucfirst($name),
            'last_name' => ucfirst($name),
            'username' => $email,
            'user_name_type' => $this->faker->randomElement([1]),
            'gender' => $this->faker->randomElement([1,2]),
            'mobile' => $this->faker->phoneNumber(),
            'email' => $email,
            'loc_division_id' => 1,
            'loc_district_id' => 1,
        ];
    }
}
