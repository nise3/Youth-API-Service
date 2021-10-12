<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('languages')->truncate();
        $languages = [
            [
                "lang_code" => "bn",
                "title" => "বাংলা",
                "title_en" => "Bengali"
            ],
            [
                "lang_code" => "en",
                "title" => "ইংরেজি",
                "title_en" => "English"
            ],
            [
                "lang_code" => "es",
                "title" => "Spanish",
                "title_en" => "স্পেনীয়"
            ],
            [
                "lang_code" => "ar",
                "title" => "Arabic",
                "title_en" => "আরবি"
            ]
        ];
        Language::insert($languages);
        Schema::enableForeignKeyConstraints();
    }
}
