<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EduGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('edu_groups')->truncate();
        $groups = [
            array('id' => '1','title_en' => 'Science','title' => 'বিজ্ঞান','code' => 'Science','deleted_at' => NULL),
            array('id' => '2','title_en' => 'Arts and Humanities','title' => 'মানবিক','code' => 'Humanities','deleted_at' => NULL),
            array('id' => '3','title_en' => 'Commerce or Business Studies','title' => 'ব্যবসায় শিক্ষা','code' => 'Commerce','deleted_at' => NULL)
        ];

        DB::table('edu_groups')->insert($groups);
        Schema::enableForeignKeyConstraints();
    }
}
