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
                'code' => 'CSE',
                "title_bn" => "Computer Science & Engineering"
            ],
            [
                "title_en" => "Electrical & Electronics Engineering",
                'code' => 'EEE',
                "title_bn" => "Electrical & Electronics Engineering"
            ],
            [
                "title_en" => "Physics",
                'code' => 'PHY',
                "title_bn" => "Physics"
            ],
            [
                "title_en" => "History",
                'code' => 'HIS',
                "title_bn" => "History"
            ],
            [
                "title_en" => "Psychology",
                'code' => 'PSYCH',
                "title_bn" => "Psychology"
            ],
            [
                "title_en" => "English",
                'code' => 'ENG',
                "title_bn" => "English"
            ],
            [
                "title_en" => "Chemistry",
                'code' => 'CHEM',
                "title_bn" => "Chemistry"
            ],
            [
                "title_en" => "Political Science",
                'code' => 'POLSC',
                "title_bn" => "Political Science"
            ],
            [
                "title_en" => "Social Welfare",
                'code' => 'SOWEL',
                "title_bn" => "Social Welfare"
            ],
            [
                "title_en" => "Social Science",
                'code' => 'SOSC',
                "title_bn" => "Social Science"
            ],
            [
                "title_en" => "Management",
                'code' => 'MAN',
                "title_bn" => "Management"
            ],
            [
                "title_en" => "Accounting",
                'code' => 'ACC',
                "title_bn" => "Accounting"
            ],
            [
                "title_en" => "Finance",
                'code' => 'FIN',
                "title_bn" => "Finance"
            ],
            [
                "title_en" => "Bachelor of Business Administration",
                'code' => 'BBA',
                "title_bn" => "Bachelor of Business Administration"
            ],
            [
                "title_en" => "Masters of Business Administration",
                'code' => 'MBA',
                "title_bn" => "Masters of Business Administration"
            ],
        ];

        MajorOrSubject::insert($list);
    }
}
