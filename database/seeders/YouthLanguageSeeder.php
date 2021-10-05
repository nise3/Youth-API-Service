<?php

namespace Database\Seeders;

use App\Models\LanguagesProficiency;
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
        LanguagesProficiency::factory()->count(20)->create();
    }
}
