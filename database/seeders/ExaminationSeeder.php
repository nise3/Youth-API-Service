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
                "title_en" => "JSC/Equivalent",
                'code' => 'JSC',
                "title_bn" => "জেএসসি/সমমান"
            ],
            [
                "title_en" => "JDC/Equivalent",
                'code' => 'JDC',
                "title_bn" => "জেডিসি/সমমান"
            ],
            [
                "title_en" => "SSC/Equivalent",
                'code' => 'SSC',
                "title_bn" => "এসএসসি/সমমান"
            ],
            [
                "title_en" => "Dakhil/Equivalent",
                'code' => 'Dakhil',
                "title_bn" => "দাখিল/সমমান"
            ],
            [
                "title_en" => "HSC/Equivalent",
                'code' => 'HSC',
                "title_bn" => "এইচএসসি/সমমান"
            ],
            [
                "title_en" => "Alim/Equivalent",
                'code' => 'Alim',
                "title_bn" => "আলিম/সমমান"
            ],
            [
                "title_en" => "DIBS (Diploma in Business Studies)",
                'code' => 'DIBS',
                "title_bn" => "ডিআইবিএস (ডিপ্লোমা ইন বিজনেস স্টাডিজ)"
            ],
            [
                "title_en" => "Degree",
                'code' => 'Degree',
                "title_bn" => "ডিগ্রী"
            ],
            [
                "title_en" => "B.Sc. (Honours)",
                'code' => 'BSc',
                "title_bn" => "বিএসসি (সম্মান)"
            ],
            [
                "title_en" => "M.Sc.",
                'code' => 'Msc',
                "title_bn" => "M.Sc."
            ]
        ];

        Examination::insert($examinations);
    }
}
