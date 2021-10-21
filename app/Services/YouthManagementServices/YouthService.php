<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\EduBoard;
use App\Models\EducationLevel;
use App\Models\EduGroup;
use App\Models\PhysicalDisability;
use App\Models\Youth;
use App\Models\YouthAddress;
use App\Models\YouthEducation;
use App\Models\YouthGuardian;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class YouthService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getYouthProfileList(array $request, Carbon $startTime): array
    {
        $firstName = $requestp['first_name'] ?? "";
        $lastName = $request['last_name'] ?? "";
        $isFreelanceProfile = $request['is_freelance_profile'] ?? "";
        $divisionId = $request['loc_division_id'] ?? "";
        $districtId = $request['loc_district_id'] ?? "";

        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $youthBuilder */

        $youthBuilder = Youth::select(
            [
                'youths.id',
                'youths.username',
                'youths.user_name_type',
                'youths.first_name',
                'youths.first_name_en',
                'youths.last_name',
                'youths.last_name_en',
                'youths.loc_division_id',
                'loc_divisions.title as division_title',
                'loc_divisions.title_en as division_title_en',
                'youths.loc_district_id',
                'loc_districts.title as district_title',
                'loc_districts.title_en as district_title_en',
                'youths.loc_upazila_id',
                'loc_upazilas.title as upazila_title',
                'loc_upazilas.title_en as upazila_title_en',
                DB::raw('SUBSTR(youths.bio, 0, 160) as bio'),
                DB::raw('SUBSTR(youths.bio_en, 0, 80) as bio_en'),
                'youths.gender',
                'youths.religion',
                'youths.marital_status',
                'youths.nationality',
                'youths.email',
                'youths.mobile',
                'youths.identity_number_type',
                'youths.identity_number',
                'youths.date_of_birth',
                'youths.is_freelance_profile',
                'youths.freedom_fighter_status',
                'youths.physical_disability_status',
                'youths.does_belong_to_ethnic_group',
                'youths.photo',
                'youths.cv_path',
                'youths.row_status',
                'youths.created_at',
                'youths.updated_at',
            ]
        );

        $youthBuilder->orderBy('youths.id', $order);

        $youthBuilder->leftjoin('loc_divisions', function ($join) {
            $join->on('youths.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $youthBuilder->leftjoin('loc_districts', function ($join) {
            $join->on('youths.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });

        $youthBuilder->leftjoin('loc_upazilas', function ($join) {
            $join->on('youths.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $youthBuilder->with(['skills', 'physicalDisabilities']);

        if (!empty($firstName)) {
            $youthBuilder->where('youths.first_name', 'like', '%' . $firstName . '%');
        }

        if (!empty($lastName)) {
            $youthBuilder->where('youths.last_name', 'like', '%' . $lastName . '%');
        }

        if ($isFreelanceProfile !== '' && is_integer($isFreelanceProfile)) {
            $youthBuilder->where('youths.is_freelance_profile', '=', $isFreelanceProfile);
        }

        if (is_integer($rowStatus)) {
            $youthBuilder->where('youths.row_status', $rowStatus);
        }

        if (is_integer($divisionId) && $divisionId) {
            $youthBuilder->where('youths.loc_division_id', $divisionId);
        }

        if (is_integer($districtId) && $districtId) {
            $youthBuilder->where('youths.loc_district_id', $districtId);
        }

        /** @var Collection $youths */

        if (is_integer($paginate) || is_integer($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $youths = $youthBuilder->paginate($pageSize);
            $paginateData = (object)$youths->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $youths = $youthBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $youths->toArray()['data'] ?? $youths->toArray();
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
    public function getOneYouthProfile(int $id, Carbon $startTime): array
    {
        /** @var Builder $youthBuilder */
        $youthBuilder = Youth::select(
            [
                'youths.id',
                'youths.idp_user_id',
                'youths.username',
                'youths.user_name_type',
                'youths.first_name',
                'youths.first_name_en',
                'youths.last_name',
                'youths.last_name_en',
                'youths.is_freelance_profile',
                'youths.loc_division_id',
                'loc_divisions.title as division_title',
                'loc_divisions.title_en as division_title_en',
                'youths.loc_district_id',
                'loc_districts.title as district_title',
                'loc_districts.title_en as district_title_en',
                'youths.loc_upazila_id',
                'loc_upazilas.title as upazila_title',
                'loc_upazilas.title_en as upazila_title_en',
                'youths.gender',
                'youths.religion',
                'youths.marital_status',
                'youths.nationality',
                'youths.email',
                'youths.mobile',
                'youths.identity_number_type',
                'youths.identity_number',
                'youths.date_of_birth',
                'youths.freedom_fighter_status',
                'youths.physical_disability_status',
                'youths.does_belong_to_ethnic_group',
                'youths.bio as bio',
                'youths.bio_en as bio_en',
                'youths.photo',
                'youths.cv_path',
                'youths.signature_image_path',
                'youths.row_status',
                'youths.created_at',
                'youths.updated_at',
            ]
        );

        $youthBuilder->leftjoin('loc_divisions', function ($join) {
            $join->on('youths.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $youthBuilder->leftjoin('loc_districts', function ($join) {
            $join->on('youths.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });

        $youthBuilder->leftjoin('loc_upazilas', function ($join) {
            $join->on('youths.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $youthBuilder->where('youths.id', $id);

        $youthBuilder->with([
            "skills",
            "physicalDisabilities",
            "youthJobExperiences",
            "youthLanguagesProficiencies",
            "youthCertifications",
            "youthEducations",
            "youthPortfolios",
            "youthReferences"
        ]);


        /** @var Youth $youth */
        $youth = $youthBuilder->firstOrFail();

        return [
            "data" => $youth ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ]
        ];

    }

    /**
     * @param array $data
     * @return Youth
     * @throws \Throwable
     */
    public function store(array $data): Youth
    {
        $youth = app(Youth::class);
        $youth->fill($data);
        throw_if(!$youth->save(), 'RuntimeException', 'Youth has not been Saved to db.', 500);
        return $youth;
    }

    /**
     * @param Youth $youth
     * @param array $data
     * @return Youth
     * @throws \Throwable
     */
    public function update(Youth $youth, array $data): Youth
    {
        $youth->fill($data);
        throw_if(!$youth->save(), 'RuntimeException', 'Youth has not been updated to db.', 500);
        return $youth;
    }


    /**
     * @param Youth $youth
     * @return bool
     * @throws \Throwable
     */
    public function destroy(Youth $youth): bool
    {
        throw_if(!$youth->delete(), 'RuntimeException', 'Youth has not been deleted.', 500);
        return true;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be either 1 or 0. [30000]',
            'is_freelance_profile.in' => "'Is Freelance Profile' must be either 1 or 0. [30000]",
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'first_name' => 'nullable|max:300|min:2',
            'first_name_en' => 'nullable|max:150|min:2',
            'last_name' => 'nullable|max:300|min:2',
            'last_name_en' => 'nullable|max:150|min:2',
            'is_freelance_profile' => [
                'nullable',
                'int',
                Rule::in([0, 1])
            ],
            "loc_division_id" => [
                "nullable",
                "int",
                "exists:loc_divisions,id,deleted_at,NULL",
            ],
            "loc_district_id" => [
                "nullable",
                "int",
                "exists:loc_districts,id,deleted_at,NULL",
            ],
            'page' => 'integer|gt:0',
            'pageSize' => 'integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "integer",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }

    /**
     * @return array
     */
    public function getEducationBasicTablesInfos()
    {
        return [
            "edu_groups" => EduGroup::all(),
            "edu_boards" => EduBoard::all(),
            "education_level_with_degrees" => EducationLevel::with('examDegrees')->get(),
            "result" => array_values(config("nise3.exam_degree_results"))
        ];
    }


    public function updateYouthProfileAfterCourseEnroll(Request $request)
    {
        $data = $request->all();
        if (!empty($data["physical_disabilities"])) {
            $data["physical_disabilities"] = isset($data['physical_disabilities']) && is_array($data['physical_disabilities']) ? $data['physical_disabilities'] : explode(',', $data['physical_disabilities']);
        }
       // dd($data);
        if (!empty($data['youth_id'])) {
            $youth = Youth::findOrFail($data['youth_id']);
            $youth->fill($data);
            $youth->save();

            $this->updateYouthAddresses($data, $youth);
            $this->updateYouthGuardian($data, $youth);
            $this->updateYouthEducations($data, $youth);
            $this->updateYouthPhysicalDisabilities($data, $youth);
        }
    }

    private function updateYouthEducations(array $data, Youth $youth)
    {
        if(!empty($data['education_info'])){
            foreach ($data['education_info'] as $eduLabelId=>$values){
                $youthEducation = YouthEducation::where('youth_id',$youth->id)->where('education_level_id',$eduLabelId)->first();
                if(empty($youthEducation)){
                    $youthEducation = app(YouthEducation::class);
                    $values['youth_id'] = $youth->id;
                    $values['education_level_id'] = $eduLabelId;
                }
                $youthEducation->fill($values);
                $youthEducation->save();
            }
        }
    }

    private function updateYouthGuardian(array $data, Youth $youth): void
    {
        if (!empty($data['guardian_info'])) {
            $youthFather = YouthGuardian::where('youth_id', $youth->id)->where('relationship_type', YouthGuardian::RELATIONSHIP_TYPE_FATHER)->first();
            $youthMother = YouthGuardian::where('youth_id', $youth->id)->where('relationship_type', YouthGuardian::RELATIONSHIP_TYPE_MOTHER)->first();
            $guardian = $data['guardian_info'];
            if (empty($youthFather)) {
                $youthFather = app(YouthGuardian::class);
            }
            if (empty($youthMother)) {
                $youthMother = app(YouthGuardian::class);
            }
            $this->saveYouthGuardian($youthFather, $guardian, YouthGuardian::RELATIONSHIP_TYPE_FATHER, $youth->id);
            $this->saveYouthGuardian($youthMother, $guardian, YouthGuardian::RELATIONSHIP_TYPE_MOTHER, $youth->id);

        }
    }

    private function updateYouthAddresses(array $data, Youth $youth): void
    {
        if (!empty($data['address_info'])) {
            if (!empty($data['address_info']['present_address'])) {
                $youthPresentAddress = YouthAddress::where('youth_id', $youth->id)->where('address_type', YouthAddress::ADDRESS_TYPE_PRESENT)->first();
                $addressValues = $data['address_info']['present_address'];
                if (empty($youthPresentAddress)) {
                    $youthPresentAddress = app(YouthAddress::class);
                    $addressValues['youth_id'] = $youth->id;
                    $addressValues['address_type'] = YouthAddress::ADDRESS_TYPE_PRESENT;
                }
                $youthPresentAddress->fill($addressValues);
                $youthPresentAddress->save();

            }
            if (!empty($data['address_info']['is_permanent_address'])) {
                $youthPermanentAddress = YouthAddress::where('youth_id', $youth->id)->where('address_type', YouthAddress::ADDRESS_TYPE_PERMANENT)->first();
                $addressValues = $data['address_info']['permanent_address'];
                if (empty($youthPermanentAddress)) {
                    $youthPermanentAddress = app(YouthAddress::class);
                    $addressValues['youth_id'] = $youth->id;
                    $addressValues['address_type'] = YouthAddress::ADDRESS_TYPE_PERMANENT;
                }
                $youthPermanentAddress->fill($addressValues);
                $youthPermanentAddress->save();
            }
        }
    }

    public function updateYouthPhysicalDisabilities(array $data, Youth $youth){
        if ($data['physical_disability_status'] == BaseModel::FALSE) {
            $this->detachPhysicalDisabilities($youth);
        } else if ($data['physical_disability_status'] == BaseModel::TRUE) {
            $this->assignPhysicalDisabilities($youth, $data['physical_disabilities']);
        }
    }

    /**
     * @param Youth $youth
     * @param array $disabilities
     */
    private function assignPhysicalDisabilities(Youth $youth, array $disabilities)
    {
        /** Assign skills to Youth */
        $disabilityIds = PhysicalDisability::whereIn("id", $disabilities)->orderBy('id', 'ASC')->pluck('id')->toArray();
        $youth->physicalDisabilities()->sync($disabilityIds);

    }

    /**
     * @param Youth $youth
     */
    private function detachPhysicalDisabilities(Youth $youth)
    {
        $youth->physicalDisabilities()->sync([]);

    }

    private function saveYouthGuardian(YouthGuardian $youthGuardian, array $guardian, int $relationshipType, int $youthId)
    {
        $relationshipStr = $relationshipType == YouthGuardian::RELATIONSHIP_TYPE_FATHER ? "father" : "mother";
        $youthGuardian->name = !empty($guardian[$relationshipStr . '_name']) ? $guardian[$relationshipStr . '_name'] : $youthGuardian->name;
        $youthGuardian->name_en = !empty($guardian[$relationshipStr . '_name_en']) ? $guardian[$relationshipStr . '_name_en'] : $youthGuardian->name_en;
        $youthGuardian->nid = !empty($guardian[$relationshipStr . '_nid']) ? $guardian[$relationshipStr . '_nid'] : $youthGuardian->nid;
        $youthGuardian->mobile = !empty($guardian[$relationshipStr . '_mobile']) ? $guardian[$relationshipStr . '_mobile'] : $youthGuardian->mobile;
        $youthGuardian->date_of_birth = !empty($guardian[$relationshipStr . '_date_of_birth']) ? $guardian[$relationshipStr . '_date_of_birth'] : $youthGuardian->date_of_birth;
        $youthGuardian->relationship_type = $relationshipType;
        $youthGuardian->youth_id = $youthId;

        $youthGuardian->save();
    }
}
