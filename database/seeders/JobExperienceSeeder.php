<?php

namespace Database\Seeders;

use App\Models\JobExperience;
use Illuminate\Database\Seeder;

class JobExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JobExperience::factory()->count(20)->create();
    }
}
