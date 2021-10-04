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
            GeoLocationDatabaseSeeder::class,
            YouthSeeder::class,
            EmploymentTypeSeeder::class,
            BoardSeeder::class,
            ExaminationSeeder::class,
            GroupSeeder::class,
            LanguageInfoSeeder::class,
            PhysicalDisabilitySeeder::class,
            PortfolioSeeder::class,
            JobExperienceSeeder::class,
            SkillSeeder::class,
            ReferenceSeeder::class,
            CertificationSeeder::class,
            YouthLanguageSeeder::class
        ]);
    }
}
