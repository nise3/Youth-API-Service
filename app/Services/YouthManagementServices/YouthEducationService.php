<?php


namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\EducationLevel;
use App\Models\EnrollmentEducation;
use App\Models\ExamDegree;
use App\Models\YouthEducation;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


/**
 * Class YouthProfileService
 * @package App\Services\YouthManagementServices
 */
class YouthEducationService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getEducationList(array $request, Carbon $startTime): array
    {

        $instituteName = $request['institute_name'] ?? "";
        $instituteNameEn = $request['institute_name_en'] ?? "";
        $examinationTitleEn = $request['examination_title_en'] ?? "";
        $examinationTitleBn = $request['examination_title'] ?? "";
        $boardTitleEn = $request['board_title_en'] ?? "";
        $boardTitleBn = $request['board_title'] ?? "";
        $eduGroupTitleEn = $request['edu_group_title_en'] ?? "";
        $eduGroupTitleBn = $request['edu_group_title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder $educationBuilder */
        $educationBuilder = YouthEducation::select([
                'youth_educations.id',
                'youth_educations.youth_id',
                'youth_educations.education_level_id',
                'education_levels.title as education_level_title',
                'education_levels.title_en as education_level_title_en',
                'youth_educations.exam_degree_id',
                'exam_degrees.code as exam_degree_code',
                'exam_degrees.title_en as exam_degree_title_en',
                'exam_degrees.title as exam_degree_title',
                'youth_educations.exam_degree_name',
                'youth_educations.exam_degree_name_en',
                'youth_educations.major_or_concentration',
                'youth_educations.major_or_concentration_en',
                'youth_educations.edu_group_id',
                'edu_groups.code as edu_group_code',
                'edu_groups.title_en as edu_group_title_en',
                'edu_groups.title as edu_group_title',
                'youth_educations.edu_board_id',
                'edu_boards.title_en as board_title_en',
                'edu_boards.title as board_title',
                'youth_educations.institute_name',
                'youth_educations.institute_name_en',
                'youth_educations.is_foreign_institute',
                'youth_educations.foreign_institute_country_id',
                'youth_educations.result',
                'youth_educations.marks_in_percentage',
                'youth_educations.cgpa_scale',
                'youth_educations.cgpa',
                'youth_educations.expected_year_of_passing',
                'youth_educations.year_of_passing',
                'youth_educations.duration',
                'youth_educations.achievements',
                'youth_educations.achievements_en',
                'youth_educations.created_at',
                'youth_educations.updated_at',

            ])->acl();

        $educationBuilder->leftJoin('education_levels', function ($join) use ($rowStatus) {
            $join->on('education_levels.id', '=', 'youth_educations.education_level_id')
                ->whereNull('education_levels.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('education_levels.row_status', $rowStatus);
            }
        });

        $educationBuilder->leftJoin('exam_degrees', function ($join) use ($rowStatus) {
            $join->on('exam_degrees.id', '=', 'youth_educations.exam_degree_id')
                ->whereNull('exam_degrees.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('exam_degrees.row_status', $rowStatus);
            }
        });
        $educationBuilder->leftJoin('edu_boards', function ($join) {
            $join->on('edu_boards.id', '=', 'youth_educations.edu_board_id')
                ->whereNull('edu_boards.deleted_at');
        });

        $educationBuilder->leftJoin('edu_groups', function ($join) {
            $join->on('edu_groups.id', '=', 'youth_educations.edu_group_id')
                ->whereNull('edu_groups.deleted_at');
        });
        $educationBuilder->orderBy('youth_educations.id', $order);

        if (!empty($instituteName)) {
            $educationBuilder->where('youth_educations.institute_name', 'like', '%' . $instituteName . '%');
        }
        if (!empty($instituteNameEn)) {
            $educationBuilder->where('youth_educations.institute_name_en', 'like', '%' . $instituteNameEn . '%');
        }

        if (!empty($examinationTitleEn)) {
            $educationBuilder->where('examinations.title_en', 'like', '%' . $examinationTitleEn . '%');
        }
        if (!empty($examinationTitleBn)) {
            $educationBuilder->where('examinations.title', 'like', '%' . $examinationTitleBn . '%');
        }

        if (!empty($boardTitleEn)) {
            $educationBuilder->where('edu_boards.title_en', 'like', '%' . $boardTitleEn . '%');
        }
        if (!empty($boardTitleBn)) {
            $educationBuilder->where('edu_boards.title', 'like', '%' . $boardTitleBn . '%');
        }

        if (!empty($eduGroupTitleEn)) {
            $educationBuilder->where('edu_groups.title_en', 'like', '%' . $eduGroupTitleEn . '%');
        }
        if (!empty($eduGroupTitleBn)) {
            $educationBuilder->where('edu_groups.title', 'like', '%' . $eduGroupTitleBn . '%');
        }

        /** @var Collection $youth_educations */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $youth_educations = $educationBuilder->paginate($pageSize);
            $paginateData = (object)$youth_educations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $youth_educations = $educationBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $youth_educations->toArray()['data'] ?? $youth_educations->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return YouthEducation
     */
    public function getOneEducation(int $id): YouthEducation
    {
        /** @var YouthEducation|Builder $educationBuilder */
        $educationBuilder = YouthEducation::select([
                'youth_educations.id',
                'youth_educations.youth_id',
                'youth_educations.education_level_id',
                'education_levels.title as education_level_title',
                'education_levels.title_en as education_level_title_en',
                'youth_educations.exam_degree_id',
                'exam_degrees.code as exam_degree_code',
                'exam_degrees.title_en as exam_degree_title_en',
                'exam_degrees.title as exam_degree_title',
                'youth_educations.exam_degree_name',
                'youth_educations.exam_degree_name_en',
                'youth_educations.major_or_concentration',
                'youth_educations.major_or_concentration_en',
                'youth_educations.edu_group_id',
                'edu_groups.code as edu_group_code',
                'edu_groups.title_en as edu_group_title_en',
                'edu_groups.title as edu_group_title',
                'youth_educations.edu_board_id',
                'edu_boards.title_en as board_title_en',
                'edu_boards.title as board_title',
                'youth_educations.institute_name',
                'youth_educations.institute_name_en',
                'youth_educations.is_foreign_institute',
                'youth_educations.foreign_institute_country_id',
                'youth_educations.result',
                'youth_educations.marks_in_percentage',
                'youth_educations.cgpa_scale',
                'youth_educations.cgpa',
                'youth_educations.expected_year_of_passing',
                'youth_educations.year_of_passing',
                'youth_educations.duration',
                'youth_educations.achievements',
                'youth_educations.achievements_en',
                'youth_educations.created_at',
                'youth_educations.updated_at',
            ]);

        $educationBuilder->leftJoin('education_levels', function ($join) {
            $join->on('education_levels.id', '=', 'youth_educations.education_level_id')
                ->whereNull('education_levels.deleted_at');
        });

        $educationBuilder->leftJoin('exam_degrees', function ($join) {
            $join->on('exam_degrees.id', '=', 'youth_educations.exam_degree_id')
                ->whereNull('exam_degrees.deleted_at');
        });
        $educationBuilder->leftJoin('edu_boards', function ($join) {
            $join->on('edu_boards.id', '=', 'youth_educations.edu_board_id')
                ->whereNull('edu_boards.deleted_at');
        }
        );
        $educationBuilder->leftJoin('edu_groups', function ($join) {
            $join->on('edu_groups.id', '=', 'youth_educations.edu_group_id')
                ->whereNull('edu_groups.deleted_at');
        });

        $educationBuilder->where("youth_educations.id", $id);

        return $educationBuilder->firstOrFail();

    }

    /**
     * @param YouthEducation $youthEducation
     * @param array $data
     * @return YouthEducation
     * @throws Throwable
     */
    public function createEducation(YouthEducation $youthEducation, array $data): YouthEducation
    {
        $youthEducation->fill($data);
        throw_if(!$youthEducation->save(), 'RuntimeException', 'Youth Education Info has not been Saved to db.', 500);
        return $youthEducation;
    }

    /**
     * @param YouthEducation $youthEducation
     * @param array $data
     * @return YouthEducation
     * @throws Throwable
     */
    public function update(YouthEducation $youthEducation, array $data): YouthEducation
    {
        $youthEducation->fill($data);
        throw_if(!$youthEducation->save(), 'RuntimeException', 'Youth Education Info has not been deleted.', 500);
        return $youthEducation;
    }

    /**
     * @param YouthEducation $youthEducation
     * @return bool
     * @throws Throwable
     */
    public function destroy(YouthEducation $youthEducation): bool
    {
        throw_if(!$youthEducation->delete(), 'RuntimeException', 'Youth Education Info has not been deleted.', 500);
        return true;
    }

    /**
     * @param YouthEducation $youthEducation
     * @return bool
     * @throws Throwable
     */
    public function restore(YouthEducation $youthEducation): bool
    {
        throw_if(!$youthEducation->restore(), 'RuntimeException', 'Youth Education Info has not been restored.', 500);
        return true;
    }

    /**
     * @param YouthEducation $youthEducation
     * @return bool
     * @throws Throwable
     */
    public function forceDelete(YouthEducation $youthEducation): bool
    {
        throw_if(!$youthEducation->forceDelete(), 'RuntimeException', 'Youth Education Info has not been successfully deleted forcefully.', 500);
        return true;
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     **/
    public function validator(Request $request, int $id = null): Validator
    {
        $request->offsetSet('deleted_at', null);

        $rules = [
            'youth_id' => [
                'required',
                'int',
                'exists:youths,id,deleted_at,NULL',
            ],
            'education_level_id' => [
                'required',
                'min:1',
                'integer',
                'exists:education_levels,id,deleted_at,NULL',
                'unique_with:youth_educations,youth_id,deleted_at,' . $id,
            ],
            "exam_degree_id" => [
                Rule::requiredIf(function () use ($request) {
                    return $this->getRequiredStatus(YouthEducation::DEGREE, $request->education_level_id);
                }),
                'nullable',
                Rule::in(ExamDegree::where("education_level_id", $request->education_level_id)->pluck('id')->toArray()),
                'min:1',
                'unique_with:youth_educations,youth_id,deleted_at,' . $id,
                'exists:exam_degrees,id,deleted_at,NULL,education_level_id,' . $request->education_level_id,
                'integer'

            ],
            "exam_degree_name" => [
                Rule::requiredIf(function () use ($request) {
                    return $this->getRequiredStatus(YouthEducation::EXAM_DEGREE_NAME, $request->education_level_id);
                }),
                'nullable',
                "string"
            ],
            "exam_degree_name_en" => [
                "nullable",
                "string"
            ],
            "major_or_concentration" => [
                Rule::requiredIf(function () use ($request) {
                    return $this->getRequiredStatus(YouthEducation::MAJOR, $request->education_level_id);
                }),
                'nullable',
                "string"
            ],
            "major_or_concentration_en" => [
                "nullable",
                "string"
            ],
            "edu_group_id" => [
                Rule::requiredIf(function () use ($request) {
                    return $this->getRequiredStatus(YouthEducation::EDU_GROUP, $request->education_level_id);
                }),
                'nullable',
                'exists:edu_groups,id,deleted_at,NULL',
                'unique_with:youth_educations,exam_degree_id,youth_id,deleted_at,' . $id,
                "integer"
            ],
            'edu_board_id' => [
                Rule::requiredIf(function () use ($request) {
                    return $this->getRequiredStatus(YouthEducation::BOARD, $request->education_level_id);
                }),
                'nullable',
                'exists:edu_boards,id,deleted_at,NULL',
//                'unique_with:youth_educations,exam_degree_id,' . $id,
                "integer"
            ],
            'institute_name' => [
                'required',
                'string',
                'max:800',
            ],
            'institute_name_en' => [
                'nullable',
                'string',
                'max:400',
            ],
            "is_foreign_institute" => [
                'required',
                'integer',
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
            ],
            "foreign_institute_country_id" => [
                Rule::requiredIf(function () use ($request) {
                    return BaseModel::TRUE == $request->is_foreign_institute;
                }),
                'nullable',
                "integer"
            ],
            "result" => [
                "required",
                "integer",
                Rule::in(array_keys(config("nise3.exam_degree_results")))
            ],
            'marks_in_percentage' => [
                Rule::requiredIf(function () use ($request) {
                    return $this->getRequiredStatus(YouthEducation::MARKS, $request->result);
                }),
                'nullable',
                "numeric"
            ],
            "cgpa_scale" => [
                Rule::requiredIf(function () use ($request) {
                    return $this->getRequiredStatus(YouthEducation::SCALE, $request->result);
                }),
                'nullable',
                Rule::in([YouthEducation::GPA_OUT_OF_FOUR, YouthEducation::GPA_OUT_OF_FIVE]),
                "integer"
            ],
            'cgpa' => [
                Rule::requiredIf(function () use ($request) {
                    return $this->getRequiredStatus(YouthEducation::CGPA, $request->result);
                }),
                'nullable',
                'numeric',
                'max:5'
            ],
            'year_of_passing' => [
                Rule::requiredIf(function () use ($request) {
                    return $this->getRequiredStatus(YouthEducation::YEAR_OF_PASS, $request->result);
                }),
                'nullable',
                'string'
            ],
            "expected_year_of_passing" => [
                Rule::requiredIf(function () use ($request) {
                    return $this->getRequiredStatus(YouthEducation::EXPECTED_YEAR_OF_PASSING, $request->result);
                }),
                'nullable',
                'string'
            ],
            "duration" => [
                "nullable",
                "integer"
            ],
            "achievements" => [
                "nullable",
                "string"
            ],
            "achievements_en" => [
                "nullable",
                "string"
            ]

        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }

    /**
     * @param Request $request
     * @return Validator
     */
    public function filterValidator(Request $request): Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return \Illuminate\Support\Facades\Validator::make($request->all(), [

            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]
        ], $customMessage);
    }

    /**
     * @param string $key
     * @param int $id
     * @return bool
     */
    private function getRequiredStatus(string $key, int $id): bool
    {
        switch ($key) {
            /** Validation Rule Based On YouthEducation Level */
            case YouthEducation::DEGREE:
            {
                return in_array($this->getCodeById(YouthEducation::EDUCATION_LEVEL_TRIGGER, $id), [EducationLevel::EDUCATION_LEVEL_PSC_5_PASS, EducationLevel::EDUCATION_LEVEL_JSC_JDC_8_PASS, EducationLevel::EDUCATION_LEVEL_SECONDARY, EducationLevel::EDUCATION_LEVEL_HIGHER_SECONDARY, EducationLevel::EDUCATION_LEVEL_DIPLOMA, EducationLevel::EDUCATION_LEVEL_BACHELOR, EducationLevel::EDUCATION_LEVEL_MASTERS]);
            }
            case YouthEducation::BOARD:
            {
                return in_array($this->getCodeById(YouthEducation::EDUCATION_LEVEL_TRIGGER, $id), [EducationLevel::EDUCATION_LEVEL_PSC_5_PASS, EducationLevel::EDUCATION_LEVEL_JSC_JDC_8_PASS, EducationLevel::EDUCATION_LEVEL_SECONDARY, EducationLevel::EDUCATION_LEVEL_HIGHER_SECONDARY]);
            }
            case YouthEducation::MAJOR:
            {
                return in_array($this->getCodeById(YouthEducation::EDUCATION_LEVEL_TRIGGER, $id), [EducationLevel::EDUCATION_LEVEL_DIPLOMA, EducationLevel::EDUCATION_LEVEL_BACHELOR, EducationLevel::EDUCATION_LEVEL_MASTERS, EducationLevel::EDUCATION_LEVEL_PHD]);
            }
            case YouthEducation::EXAM_DEGREE_NAME:
            {
                return $this->getCodeById(YouthEducation::EDUCATION_LEVEL_TRIGGER, $id) == EducationLevel::EDUCATION_LEVEL_PHD;
            }
            case YouthEducation::EDU_GROUP:
            {
                return in_array($this->getCodeById(YouthEducation::EDUCATION_LEVEL_TRIGGER, $id), [EducationLevel::EDUCATION_LEVEL_SECONDARY, EducationLevel::EDUCATION_LEVEL_HIGHER_SECONDARY]);
            }
            /** Validation Rule Based On Result Type */
            case YouthEducation::MARKS:
            {
                return in_array($this->getCodeById(YouthEducation::RESULT_TRIGGER, $id), [EducationLevel::RESULT_FIRST_DIVISION, EducationLevel::RESULT_SECOND_DIVISION, EducationLevel::RESULT_THIRD_DIVISION]);
            }
            case YouthEducation::SCALE:
            case YouthEducation::CGPA:
            {
                return $this->getCodeById(YouthEducation::RESULT_TRIGGER, $id) == EducationLevel::RESULT_GRADE;
            }
            case YouthEducation::YEAR_OF_PASS:
            {
                return $this->getCodeById(YouthEducation::RESULT_TRIGGER, $id) !== EducationLevel::RESULT_APPEARED;
            }
            case YouthEducation::EXPECTED_YEAR_OF_PASSING:
            {
                return $this->getCodeById(YouthEducation::RESULT_TRIGGER, $id) == EducationLevel::RESULT_APPEARED;
            }
            default:
            {
                return false;
            }

        }
    }

    /**
     * @param string $modelName
     * @param int $id
     * @return string
     */
    public function getCodeById(string $modelName, int $id): string
    {
        if ($modelName == YouthEducation::EDUCATION_LEVEL_TRIGGER) {
            $educationLevelCode = EducationLevel::where('id', $id)->first();
            $code = $educationLevelCode->code ?? "";
        } else {
            $code = config("nise3.exam_degree_results." . $id . ".code");
        }
        return $code ?? "";
    }
}
