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
                "title_en" => "Bengali",
                "title_bn" => "বাংলা"
            ],
            [
                "code" => "en",
                "title_en" => "English",
                "title_bn" => "ইংরেজি"
            ]
        ];
        LanguageInfo::insert($languages);
    }
}
