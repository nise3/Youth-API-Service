<?php

namespace Database\Seeders;

use App\Models\Youth;
use Illuminate\Database\Seeder;

class YouthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Youth::factory()->count(1)->create();
    }
}
