<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('languages')->truncate();
        $languages = [
            array('id' => '1', 'lang_code' => 'bn', 'title' => 'বাংলা', 'title_en' => 'Bengali', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '2', 'lang_code' => 'en', 'title' => 'ইংরেজি', 'title_en' => 'English', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '3', 'lang_code' => 'es', 'title' => ' স্পেনীয়', 'title_en' => 'Spanish', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '4', 'lang_code' => 'ar', 'title' => 'আরবি', 'title_en' => 'Arabic', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL)
        ];
        Language::insert($languages);
        Schema::enableForeignKeyConstraints();
    }
}
