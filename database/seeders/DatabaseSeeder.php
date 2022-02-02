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
            SkillSeeder::class,
            EducationLevelSeeder::class,
            ExamDegreeSeeder::class,
            EduGroupSeeder::class,
            EduBoardSeeder::class,
            EmploymentTypeSeeder::class,
            LanguageSeeder::class,
            PhysicalDisabilitySeeder::class,
            AreaOfBusinessSeeder::class,
            AreaOfExperienceSeeder::class,
//            YouthSeeder::class,
//            YouthGuardianTableSeeder::class,
//            YouthLanguageProficiencySeeder::class,
//            PortfolioSeeder::class,
//            JobExperienceSeeder::class,
//            ReferenceSeeder::class,
//            CertificationSeeder::class,
//            YouthLanguageSeeder::class
        ]);
    }
}
