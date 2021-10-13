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
            EmploymentTypeSeeder::class,
            BoardSeeder::class,
            ExaminationSeeder::class,
            EduGroupSeeder::class,
            LanguageSeeder::class,
            PhysicalDisabilitySeeder::class,
            MajorOrSubjectSeeder::class,
            YouthSeeder::class,
            EducationLevelSeeder::class,
            ExamDegreeSeeder::class,
            GuardianSeeder::class
//            PortfolioSeeder::class,
//            JobExperienceSeeder::class,
//            ReferenceSeeder::class,
//            CertificationSeeder::class,
//            YouthLanguageSeeder::class
        ]);
    }
}
