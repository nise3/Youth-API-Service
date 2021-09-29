<?php

namespace Database\Seeders;

use App\Models\Examination;
use Illuminate\Database\Seeder;

class ExaminationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $examinations = [
            [
                "title_en" => "JSC/JDC",
                "title_bn" => "জেএসসি/জেডিসি"
            ],
            [
                "title_en" => "SSC/Dakhil/Equivalent",
                "title_bn" => "এসএসসি/দাখিল/সমমান"
            ],
            [
                "title_en" => "HSC/Alim/Equivalent",
                "title_bn" => "এইচএসসি/আলিম/সমমান"
            ],
            [
                "title_en" => "DIBS (Diploma in Business Studies)",
                "title_bn" => "ডিআইবিএস (ডিপ্লোমা ইন বিজনেস স্টাডিজ)"
            ]
        ];

        Examination::insert($examinations);
    }
}
