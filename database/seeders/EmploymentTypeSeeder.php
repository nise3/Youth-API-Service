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
                "code" => 'FULL',
                "title_en" => "Full-Time",
                "title" => "ফুল টাইম",
                "description" => "As set by IRS standards , full-time employees at organizations are typically working 130 hours or more in a calendar month, which breaks down to 30 – 40 hour work weeks"
            ],
            [
                "code" => 'PART',
                "title_en" => "Part-Time",
                "title" => "খন্ডকালীন",
                "description" => "Employees working less than 30 hours in a work week for an employer are typically classified as part-time employees."
            ],
            [
                "code" => 'APPREN',
                "title_en" => "Apprentices and Trainees",
                "title" => "শিক্ষানবিশ এবং প্রশিক্ষণার্থী",
                "description" => "Apprentices and trainees may be suitable for your business. They are working towards a nationally recognised qualification and must be formally registered, usually through a contract between a registered training provider, the employee and you."
            ],
            [
                "code" => 'TEMP',
                "title_en" => "Temporary",
                "title" => "সাময়িক",
                "description" => "Temporary employees are typically hired by employers for a specific length of time or a specific project that has a defined end date"
            ],
            [
                "code" => 'SEAS',
                "title_en" => "Seasonal",
                "title" => "মৌসুমী",
                "description" => "Seasonal employees are typically hired by employers during peak seasons for certain industries."
            ],
            [
                "code" => 'LEAS',
                "title_en" => "Leased",
                "title" => "ইজারা ভিত্তিক",
                "description" => "Leased employees are hired through staffing agencies and are on the employer’s payroll."
            ]


        ];

        EmploymentType::insert($employmentTypes);
    }
}
