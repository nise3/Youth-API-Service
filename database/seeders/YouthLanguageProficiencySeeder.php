<?php

namespace Database\Seeders;

use App\Models\YouthLanguagesProficiency;
use Illuminate\Database\Seeder;

class YouthLanguageProficiencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        YouthLanguagesProficiency::factory()->count(20)->create();
    }
}
