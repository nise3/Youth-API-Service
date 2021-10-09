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
                "title" => "Computer Science & Engineering"
            ],
            [
                "title_en" => "Electrical & Electronics Engineering",
                'code' => 'EEE',
                "title" => "Electrical & Electronics Engineering"
            ],
            [
                "title_en" => "Physics",
                'code' => 'PHY',
                "title" => "Physics"
            ],
            [
                "title_en" => "History",
                'code' => 'HIS',
                "title" => "History"
            ],
            [
                "title_en" => "Psychology",
                'code' => 'PSYCH',
                "title" => "Psychology"
            ],
            [
                "title_en" => "English",
                'code' => 'ENG',
                "title" => "English"
            ],
            [
                "title_en" => "Chemistry",
                'code' => 'CHEM',
                "title" => "Chemistry"
            ],
            [
                "title_en" => "Political Science",
                'code' => 'POLSC',
                "title" => "Political Science"
            ],
            [
                "title_en" => "Social Welfare",
                'code' => 'SOWEL',
                "title" => "Social Welfare"
            ],
            [
                "title_en" => "Social Science",
                'code' => 'SOSC',
                "title" => "Social Science"
            ],
            [
                "title_en" => "Management",
                'code' => 'MAN',
                "title" => "Management"
            ],
            [
                "title_en" => "Accounting",
                'code' => 'ACC',
                "title" => "Accounting"
            ],
            [
                "title_en" => "Finance",
                'code' => 'FIN',
                "title" => "Finance"
            ],
            [
                "title_en" => "Bachelor of Business Administration",
                'code' => 'BBA',
                "title" => "Bachelor of Business Administration"
            ],
            [
                "title_en" => "Masters of Business Administration",
                'code' => 'MBA',
                "title" => "Masters of Business Administration"
            ],
        ];

        MajorOrSubject::insert($list);
    }
}
