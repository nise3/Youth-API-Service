<?php


namespace App\Services\YouthManagementServices;


use App\Models\BaseModel;
use App\Models\Education;
use App\Models\Examination;
use App\Models\Youth;
use App\Models\YouthGuardian;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class YouthGuardianService
{

    public function getGuardianList(array $request, Carbon $startTime): array
    {
        $guardianName = $request['name'] ?? "";
        $guardianNameEn = $request['name_en'] ?? "";

        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder $guardianBuilder */
        $guardianBuilder = YouthGuardian::select(
            [
                'youth_guardians.id',
                'youth_guardians.name',
                'youth_guardians.name_en',
                'youth_guardians.nid',
                'youth_guardians.mobile',
                'youth_guardians.date_of_birth',
                'youth_guardians.relationship_type',
                'youth_guardians.relationship_title',
                'youth_guardians.relationship_title_en',
                'youth_guardians.created_at',
                'youth_guardians.updated_at',
            ]
        );

        if (is_int(Auth::id())) {
            $guardianBuilder->where('youth_guardians.youth_id', Auth::id());
        }
        if (!empty($guardianName)) {
            $guardianBuilder->where('youth_guardians.name', 'like', '%' . $guardianName . '%');
        }
        if (!empty($guardianNameEn)) {
            $guardianBuilder->where('youth_guardians.name_en', 'like', '%' . $guardianNameEn . '%');
        }

        /** @var Collection $guardians */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $guardians = $guardianBuilder->paginate($pageSize);
            $paginateData = (object)$guardians->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $guardians = $guardianBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $guardians->toArray()['data'] ?? $guardians->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneEducation(int $id, Carbon $startTime): array
    {
        $educationBuilder = Education::select(
            [
                'educations.id',
                'educations.youth_id',
                'educations.institute_name',
                'educations.institute_name_en',
                'educations.examination_id',
                'examinations.code as examination_code',
                'examinations.title_en as examination_title_en',
                'examinations.title as examination_title',
                'educations.board_id',
                'boards.title_en as board_title_en',
                'boards.title as board_title',
                'educations.edu_group_id',
                'edu_groups.code as edu_group_code',
                'edu_groups.title_en as edu_group_title_en',
                'edu_groups.title as edu_group_title',
                'educations.major_or_subject_id',
                'educations.roll_number',
                'educations.registration_number',
                'educations.result_type',
                'educations.division_type_result',
                'educations.cgpa_gpa_max_value',
                'educations.received_cgpa_gpa',
                'educations.passing_year',
                'educations.row_status',
                'educations.created_at',
                'educations.updated_at',
            ]
        );
        $educationBuilder->join('examinations', function ($join) {
            $join->on('examinations.id', '=', 'educations.examination_id')
                ->whereNull('examinations.deleted_at');

        });
        $educationBuilder->join('boards', function ($join) {
            $join->on('boards.id', '=', 'educations.board_id')
                ->whereNull('boards.deleted_at');

        }
        );
        $educationBuilder->join('edu_groups', function ($join) {
            $join->on('edu_groups.id', '=', 'educations.edu_group_id')
                ->whereNull('edu_groups.deleted_at');

        });
        $educationBuilder->where('educations.id', $id);

        /** @var Education $education */
        $education = $educationBuilder->first();

        return [
            "data" => $education ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
    }

    /**
     * @param array $data
     * @return Education
     */
    public function createEducation(array $data): Education
    {
        $youthEducation = new Education();
        $youthEducation->fill($data);
        $youthEducation->save();
        return $youthEducation;
    }

    /**
     * @param Education $youthEducation
     * @param array $data
     * @return Education
     */
    public function update(Education $youthEducation, array $data): Education
    {
        $youthEducation->fill($data);
        $youthEducation->save();
        return $youthEducation;
    }

    /**
     * @param Education $youthEducation
     * @return bool
     */
    public function destroy(Education $youthEducation): bool
    {
        return $youthEducation->delete();
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $rules = [
            'youth_id' => [
                'required',
                'int',
                'exists:youths,id'
            ],
            "mobile" => [
                "required",
                "max:11",
                BaseModel::MOBILE_REGEX
            ],
            'name' => [
                'required',
                'string',
                'max:500',
                'min:1'
            ],
            'name_en' => [
                'nullable',
                'string',
                'max:250',
                'min:1'
            ],
            'nid' => [
                'nullable',
                'string',
                'max:30',
            ],
            'date_of_birth' => [
                'nullable',
                'date',

            ],
            'relationship_type' => [
                'required',
                Rule::unique(function () use ($request) {
                    if ($request['relationship_title'] != config('nise3.relationship_types')[5]) {
                        YouthGuardian::where('youth_id' , $request['youth_id'])
                        return $request['relationship_title'] == config('nise3.relationship_types')[5];
                    }
                }),
                'int',
            ],
            'relationship_title' => [
                Rule::requiredIf(function () use ($request) {
                    return $request['relationship_title'] == config('nise3.relationship_types')[5];
                }),
                'string',
                'min:1'
            ],
            'relationship_title_en' => [
                'nullable',
                'string',
                'min:1'
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
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be within ASC or DESC',
            ]
        ];

        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'page' => 'numeric|gt:0',
            'pageSize' => 'numeric|gt:0',
            'name' => 'nullable|string',
            'name_en' => 'nullable|string',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]
        ], $customMessage);
    }
}
