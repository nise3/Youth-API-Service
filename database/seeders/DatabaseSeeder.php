<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            EmploymentTypeSeeder::class,
            BoardSeeder::class,
            ExaminationSeeder::class,
            GroupSeeder::class,
            LanguageInfoSeeder::class,
            LocDivisionsTableSeeder::class,
            LocDistrictsTableSeeder::class
        ]);
    }
}
