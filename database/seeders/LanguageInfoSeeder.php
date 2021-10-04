<?php

namespace Database\Seeders;

use App\Models\LanguageInfo;
use Illuminate\Database\Seeder;

class LanguageInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function run()
    {
        $languages =[
            [
                "code" => "bn",
                "title" => "বাংলা",
                "title_en" => "Bengali"
            ],
            [
                "code" => "en",
                "title" => "ইংরেজি",
                "title_en" => "English"
            ]
        ];
        LanguageInfo::insert($languages);
    }
}
