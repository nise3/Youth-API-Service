<?php

namespace Database\Seeders;

use App\Models\PhysicalDisability;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PhysicalDisabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        PhysicalDisability::query()->truncate();

        PhysicalDisability::insert([
            array('id' => '1', 'code' => 'Visual_Dis', 'title' => 'চাক্ষুষ অক্ষমতা', 'title_en' => 'Visual Disabilities', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '2', 'code' => 'Hearing_Dis', 'title' => 'শ্রবণ প্রতিবন্ধী', 'title_en' => 'Hearing Disabilities', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '3', 'code' => 'Mental_H_Dis', 'title' => 'মানসিক স্বাস্থ্য অক্ষমতা', 'title_en' => 'Mental Health Disabilities', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '4', 'code' => 'Intellectual_Dis', 'title' => 'বুদ্ধিবৃত্তিক অক্ষমতা', 'title_en' => 'Intellectual Disabilities', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '5', 'code' => 'Physical_Dis', 'title' => 'শারীরিক অক্ষমতা', 'title_en' => 'Physical Disabilities', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '6', 'code' => 'Speech_Dis', 'title' => 'বাক প্রতিবন্ধিতা', 'title_en' => 'Speech Disabilities', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '7', 'code' => 'Deaf_Blindness_Dis', 'title' => 'শ্রবণ-দৃষ্টিপ্রতিবন্ধিতা', 'title_en' => 'Deaf Blindness Disabilities', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '8', 'code' => 'Cerebral_Palsy_Dis', 'title' => 'সেরিব্রাল পালসি', 'title_en' => 'Cerebral Palsy ', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '9', 'code' => 'Down_Syndrome_Dis', 'title' => 'ডাউন সিনড্রোম', 'title_en' => 'Down Syndrome', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '10', 'code' => 'Autism_Or_Autism_Spectrum_Dis', 'title' => 'অটিজম বা অটিজমস্পেকট্রাম ডিজঅর্ডারস', 'title_en' => 'Autism Or Autism Spectrum Disorders', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '11', 'code' => 'Multiple_Dis', 'title' => 'বহুমাত্রিক প্রতিবন্ধিতা', 'title_en' => 'Multiple Disability', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '12', 'code' => 'Other_Dis', 'title' => 'অন্যান্য প্রতিবন্ধিতা', 'title_en' => 'Other Disability', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
