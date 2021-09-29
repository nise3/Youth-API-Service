<?php

namespace Database\Seeders;

use App\Models\Board;
use Illuminate\Database\Seeder;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $boardName=[
            [
                "title_en"=>"Barisal",
                "title_bn"=>"বরিশাল"
            ],
            [
                "title_en"=>"Chittagong",
                "title_bn"=>"চট্টগ্রাম"
            ],
            [
                "title_en"=>"Comilla",
                "title_bn"=>"কুমিল্লা"
            ],
            [
                "title_en"=>"Dhaka",
                "title_bn"=>"ঢাকা"
            ],
            [
                "title_en"=>"Dinajpur",
                "title_bn"=>"দিনাজপুর"
            ],
            [
                "title_en"=>"Jessore",
                "title_bn"=>"যশোর"
            ],
            [
                "title_en"=>"Mymensingh",
                "title_bn"=>"ময়মনসিংহ"
            ],
            [
                "title_en"=>"Rajshahi",
                "title_bn"=>"রাজশাহী"
            ],
            [
                "title_en"=>"Sylhet",
                "title_bn"=>"সিলেট"
            ],
        ];

        Board::insert($boardName);
    }
}
