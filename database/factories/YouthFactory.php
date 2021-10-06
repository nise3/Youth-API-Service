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
        $idp_user_id = "327ec391-a2b9-44c6-a271-3a9b98e71ee5";
        return [
            "idp_user_id" => $idp_user_id,
            'first_name' => ucfirst($name),
            'last_name' => ucfirst($name),
            'username' => $email,
            'user_name_type' => $this->faker->randomElement([1]),
            'gender' => $this->faker->randomElement([1, 2]),
            'mobile' => $this->faker->numerify('017########'),
            'email' => $email,
            'loc_division_id' => 1,
            'loc_district_id' => 1,
            'row_status' => 1
        ];
    }
}
