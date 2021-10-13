<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Education;
use App\Models\JobExperience;
use App\Models\LanguagesProficiency;
use App\Models\Portfolio;
use App\Models\Reference;
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
                JobExperience::factory()->count(1), 'jobExperiences'
            )
            ->has(
                LanguagesProficiency::factory()->count(2), 'LanguagesProficiencies'
            )
            ->has(
                Portfolio::factory()->count(5), 'portfolios'
            )
            ->has(
                Certification::factory()->count(2), 'certifications'
            )
            ->has(
                Reference::factory()->count(2), 'references'
            )
            ->create();
    }
}
