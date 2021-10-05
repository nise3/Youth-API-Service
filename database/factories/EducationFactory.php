<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\Education;
use App\Models\EmploymentType;
use App\Models\Examination;
use App\Models\Group;
use App\Models\Youth;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class EducationFactory extends Factory
{
    protected $model = Education::class;

    public function definition(): array
    {
        $company = $this->faker->company();
        $examinationId = Examination::all()->random()->id;
        $boardId = Board::all()->random()->id;
        $groupId = Group::all()->random()->id;
        $youthId = Youth::all()->random()->id;

        return [
            'institute_name' => ucfirst($company),
            'institute_name_en' => ucfirst($company),
            'examination_id' => $examinationId,
            'board_id' => $boardId,
            'group_id' => $groupId,
            'result_type' => 2,
            'division_type_result' => $location,
            'cgpa_gpa_max_value' => $locationEn,
            'received_cgpa_gpa' => $this->faker->dateTime(),
            'passing_year' => $this->faker->dateTime(),
            'youth_id' => $youthId
        ];
    }
    public function applyResult()
    {
        return $this->state(function (array $attributes) {
            $upazilla = DB::table('loc_upazilas')
                ->select('id')
                ->where('loc_district_id', $attributes['district_id'])
                ->inRandomOrder()
                ->first();
            return [
                'upazila_id' => $upazilla->id,
            ];
        });
    }
}
