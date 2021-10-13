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
        $educationLevels=[
            [
                "id"=>1,
                "code"=>BaseModel::PSC_5_PASS,
                "title"=>"PSC/5 Pass",
                "title_en"=>"PSC/5 Pass",
            ],
            [
                "id"=>2,
                "code"=>BaseModel::JSC_JDC_8_PASS,
                "title"=>"JSC/JDC/8 Pass",
                "title_en"=>"JSC/JDC/8 Pass",
            ],
            [
                "id"=>3,
                "code"=>BaseModel::SECONDARY,
                "title"=>"Secondary",
                "title_en"=>"Secondary",
            ],
            [
                "id"=>4,
                "code"=>BaseModel::HIGHER_SECONDARY,
                "title"=>"Higher Secondary",
                "title_en"=>"Higher Secondary",
            ],
            [
                "id"=>5,
                "code"=>BaseModel::DIPLOMA,
                "title"=>"Diploma",
                "title_en"=>"Diploma",
            ],
            [
                "id"=>6,
                "code"=>BaseModel::BACHELOR,
                "title"=>"Bachelor/Honors",
                "title_en"=>"Bachelor/Honors",
            ],
            [
                "id"=>7,
                "code"=>BaseModel::MASTERS,
                "title"=>"Masters",
                "title_en"=>"Masters",
            ],
            [
                "id"=>8,
                "code"=>BaseModel::PHD,
                "title"=>"PhD (Doctor of Philosophy)",
                "title_en"=>"PhD (Doctor of Philosophy)",
            ]
        ];
        EducationLevel::insert($educationLevels);
    }
}
