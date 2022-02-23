<?php

namespace Database\Seeders;

use App\Models\EduBoard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EduBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('edu_boards')->truncate();

        $boardName = [
            array('id' => '1', 'code' => '', 'title_en' => 'Barisal', 'title' => 'বরিশাল', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '2', 'code' => '', 'title_en' => 'Chittagong', 'title' => 'চট্টগ্রাম', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '3', 'code' => '', 'title_en' => 'Comilla', 'title' => 'কুমিল্লা', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '4', 'code' => '', 'title_en' => 'Dhaka', 'title' => 'ঢাকা', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '5', 'code' => '', 'title_en' => 'Dinajpur', 'title' => 'দিনাজপুর', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '6', 'code' => '', 'title_en' => 'Jessore', 'title' => 'যশোর', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '7', 'code' => '', 'title_en' => 'Mymensingh', 'title' => 'ময়মনসিংহ', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '8', 'code' => '', 'title_en' => 'Rajshahi', 'title' => 'রাজশাহী', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '9', 'code' => '', 'title_en' => 'Sylhet', 'title' => 'সিলেট', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '10', 'code' => '', 'title_en' => 'National University', 'title' => 'National University', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL)
        ];

        DB::table('edu_boards')->insert($boardName);

        Schema::enableForeignKeyConstraints();
    }
}
