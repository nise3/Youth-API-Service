<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class EduGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = [
            [
                "title_en" => "Science",
                "title_bn" => "বিজ্ঞান"

            ],
            [
                "title_en" => "Arts and Humanities",
                "title_bn" => "মানবিক"

            ],
            [
                "title_en" => "Commerce or Business Studies",
                "title_bn" => "ব্যবসায় শিক্ষা"

            ]
        ];

        Group::insert($groups);
    }
}
