<?php

namespace Database\Seeders;

use App\Models\YouthCertification;
use App\Models\Education;
use App\Models\YouthJobExperience;
use App\Models\YouthLanguagesProficiency;
use App\Models\YouthPortfolio;
use App\Models\YouthReference;
use App\Models\Skill;
use App\Models\Youth;
use App\Models\YouthEducation;
use Illuminate\Database\Seeder;

class YouthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Youth::factory()->count(20)
            ->has(
                YouthEducation::factory(), 'youthEducations'
            )
            ->has(
                YouthJobExperience::factory()->count(1), 'youthJobExperiences'
            )
            ->has(
                YouthLanguagesProficiency::factory()->count(2), 'youthLanguagesProficiencies'
            )
            ->has(
                YouthPortfolio::factory()->count(5), 'youthPortfolios'
            )
            ->has(
                YouthCertification::factory()->count(2), 'youthCertifications'
            )
            ->has(
                YouthReference::factory()->count(2), 'youthReferences'
            )
            ->create();
    }
}
