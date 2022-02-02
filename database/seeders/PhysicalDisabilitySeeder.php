<?php

namespace Database\Seeders;

use App\Models\PhysicalDisability;
use Illuminate\Database\Seeder;

class PhysicalDisabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PhysicalDisability::insert([
            [
                "title" => "চাক্ষুষ অক্ষমতা",
                "title_en" => "Visual Disabilities",
            ],
            [
                "title" => "শ্রবণ প্রতিবন্ধী",
                "title_en" => "Hearing Disabilities",
            ],
            [
                "title" => "মানসিক স্বাস্থ্য অক্ষমতা",
                "title_en" => "Mental Health Disabilities",
            ],
            [
                "title" => "বুদ্ধিবৃত্তিক অক্ষমতা",
                "title_en" => "Intellectual Disabilities",
            ],
            [
                "title" => "সামাজিক প্রতিবন্ধী",
                "title_en" => "Social Disabilities",
            ]
        ]);
    }
}
