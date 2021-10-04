<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class YouthLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::factory()->count(20)->create();
    }
}
