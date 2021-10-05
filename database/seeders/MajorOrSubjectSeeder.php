<?php

namespace Database\Seeders;

use App\Models\MajorOrSubject;
use Illuminate\Database\Seeder;

class MajorOrSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $list = [
            [
                "title_en" => "Computer Science & Engineering",
                "title_bn" => "Computer Science & Engineering"

            ],
            [
                "title_en" => "Electrical & Electronics Engineering",
                "title_bn" => "Electrical & Electronics Engineering"

            ],
            [
                "title_en" => "Physics",
                "title_bn" => "Physics"

            ],
            [
                "title_en" => "History",
                "title_bn" => "History"

            ],
            [
                "title_en" => "Psychology",
                "title_bn" => "Psychology"

            ],
            [
                "title_en" => "English",
                "title_bn" => "English"

            ],
            [
                "title_en" => "Chemistry",
                "title_bn" => "Chemistry"

            ],
            [
                "title_en" => "Political Science",
                "title_bn" => "Political Science"

            ],
            [
                "title_en" => "Social Welfare",
                "title_bn" => "Social Welfare"

            ],
            [
                "title_en" => "Social Science",
                "title_bn" => "Social Science"

            ],
            [
                "title_en" => "Management",
                "title_bn" => "Management"

            ],
            [
                "title_en" => "Accounting",
                "title_bn" => "Accounting"

            ],
            [
                "title_en" => "Finance",
                "title_bn" => "Finance"

            ],
            [
                "title_en" => "Bachelor of Business Administration",
                "title_bn" => "Bachelor of Business Administration"

            ],
            [
                "title_en" => "Masters of Business Administration",
                "title_bn" => "Masters of Business Administration"

            ],
        ];

        MajorOrSubject::insert($list);
    }
}
