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
//          YouthSeeder::class
            EmploymentTypeSeeder::class,
            BoardSeeder::class,
            ExaminationSeeder::class,
            GroupSeeder::class,
            GeoLocationDatabaseSeeder::class,
            LanguageInfoSeeder::class,
            PortfolioSeeder::class,
            SkillSeeder::class
        ]);
    }
}
