<?php

namespace Database\Seeders;

use App\Models\BaseModel;
use App\Models\EducationLevel;
use Illuminate\Database\Seeder;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $educationLevels = [
            [
                "id" => 1,
                "code" => EducationLevel::EDUCATION_LEVEL_PSC_5_PASS,
                "title" => "পি এস সি/৫ম শ্রেনী উত্তীর্ণ",
                "title_en" => "PSC/5 Pass",
            ],
            [
                "id" => 2,
                "code" => EducationLevel::EDUCATION_LEVEL_JSC_JDC_8_PASS,
                "title" => "জে এস সি/ জে ডি সি/৮ম শ্রেনী উত্তীর্ণ",
                "title_en" => "JSC/JDC/8 Pass",
            ],
            [
                "id" => 3,
                "code" => EducationLevel::EDUCATION_LEVEL_SECONDARY,
                "title" => "মাধ্যমিক",
                "title_en" => "Secondary",
            ],
            [
                "id" => 4,
                "code" => EducationLevel::EDUCATION_LEVEL_HIGHER_SECONDARY,
                "title" => "উচ্চ মাধ্যমিক",
                "title_en" => "Higher Secondary",
            ],
            [
                "id" => 5,
                "code" => EducationLevel::EDUCATION_LEVEL_DIPLOMA,
                "title" => "ডিপ্লোমা",
                "title_en" => "Diploma",
            ],
            [
                "id" => 6,
                "code" => EducationLevel::EDUCATION_LEVEL_BACHELOR,
                "title" => "ব্যাচেলর/অনার্স",
                "title_en" => "Bachelor/Honors",
            ],
            [
                "id" => 7,
                "code" => EducationLevel::EDUCATION_LEVEL_MASTERS,
                "title" => "মাস্টার্স",
                "title_en" => "Masters",
            ],
            [
                "id" => 8,
                "code" => EducationLevel::EDUCATION_LEVEL_PHD,
                "title" => "পি এইচ ডি (দর্শনে ডক্টরেট)",
                "title_en" => "PhD (Doctor of Philosophy)",
            ]
        ];
        EducationLevel::insert($educationLevels);
    }
}
