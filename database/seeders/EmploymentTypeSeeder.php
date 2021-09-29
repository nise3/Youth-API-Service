<?php

namespace Database\Seeders;

use App\Models\EmploymentType;
use Illuminate\Database\Seeder;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employmentTypes = [
            [
                "title_en" => "Full-Time",
                "title_bn" => "ফুল টাইম",
                "description" => "As set by IRS standards , full-time employees at organizations are typically working 130 hours or more in a calendar month, which breaks down to 30 – 40 hour work weeks"
            ],
            [
                "title_en" => "Part-Time",
                "title_bn" => "খন্ডকালীন",
                "description" => "Employees working less than 30 hours in a work week for an employer are typically classified as part-time employees."
            ],
            [
                "title_en" => "Apprentices and Trainees",
                "title_bn" => "শিক্ষানবিশ এবং প্রশিক্ষণার্থী",
                "description" => "Apprentices and trainees may be suitable for your business. They are working towards a nationally recognised qualification and must be formally registered, usually through a contract between a registered training provider, the employee and you."
            ],
            [
                "title_en" => "Temporary",
                "title_bn" => "সাময়িক",
                "description" => "Temporary employees are typically hired by employers for a specific length of time or a specific project that has a defined end date"
            ],
            [
                "title_en" => "Seasonal",
                "title_bn" => "মৌসুমী",
                "description" => "Seasonal employees are typically hired by employers during peak seasons for certain industries."
            ],
            [
                "title_en" => "Leased",
                "title_bn" => "ইজারা ভিত্তিক",
                "description" => "Leased employees are hired through staffing agencies and are on the employer’s payroll."
            ]


        ];

        EmploymentType::insert($employmentTypes);
    }
}
