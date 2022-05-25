<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\LocDistrict;
use App\Models\LocDivision;
use App\Models\User;
use App\Models\Youth;
use App\Models\YouthGuardian;
use App\Models\YouthTemp;
use App\Services\CommonServices\CodeGeneratorService;
use Doctrine\DBAL\Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class YouthBulkImportFromOldSystemService
{
    protected mixed $divisionBbsCodeByOldId = [];
    protected array $divisionIdByBbsCode = [];

    protected mixed $districtBbsCodeByOldId = [];
    protected array $districtIdByBbsCode = [];

    public function __construct()
    {
        $rawData = Storage::get("division-bbs-code.json");
        $this->divisionBbsCodeByOldId = json_decode($rawData, true);

        $rawData = Storage::get("district-bbs-code.json");
        $this->districtBbsCodeByOldId = json_decode($rawData, true);

        $this->divisionIdByBbsCode = LocDivision::pluck('id', DB::raw("CAST(bbs_code AS UNSIGNED) as bbs_code"))->toArray();
        $this->districtIdByBbsCode = LocDistrict::pluck('id', DB::raw("CAST(bbs_code AS UNSIGNED) as bbs_code"))->toArray();

    }

    private function getLocationId(mixed $oldId, int $type): int|null
    {

        if ($type == 1) {
            if (!empty($this->divisionBbsCodeByOldId[$oldId]) && !empty($this->divisionIdByBbsCode[$this->divisionBbsCodeByOldId[$oldId]])) {
                return $this->divisionIdByBbsCode[(int)$this->divisionBbsCodeByOldId[$oldId]];
            }
        } elseif ($type == 2) {
            if (!empty($this->districtBbsCodeByOldId[$oldId]) && !empty($this->districtIdByBbsCode[$this->districtBbsCodeByOldId[$oldId]])) {
                return $this->districtIdByBbsCode[(int)$this->districtBbsCodeByOldId[$oldId]];
            }
        }
        return null;
    }

    /**
     * @throws ValidationException|Throwable
     */
    public function youthBulkImportFromOldSystem(): void
    {
        DB::beginTransaction();
        try {
            $youthOldTable = config("nise3.youth_old_data_imported_table_name");
            $youthOldChunkSize = config("nise3.youth_imported_chunk_size");
            throw_if(empty($youthOldTable), new Exception("Imported Table Name is invalid", Response::HTTP_UNPROCESSABLE_ENTITY));

            DB::table($youthOldTable)->orderBy('id')->chunk($youthOldChunkSize, function ($data) {
                foreach ($data as $datum) {
                    $datum = (array)$datum;
                    $youthInformation = [];
                    $this->youthBasicInformation($youthInformation, $datum);
                    if (!empty($youthInformation)) {
                        Log::channel('youth_bulk_import')->info("Youth Basic info: " . json_encode($youthInformation));
                        $validatedData = $this->youthValidation($youthInformation)->validate();
                        $validatedData['username'] = $validatedData['mobile'];
                        if (!$this->youthExist($validatedData['username'])) {
                            $validatedData['code'] = CodeGeneratorService::getYouthCode();
                            $validatedData['password'] = Youth::YOUTH_DEFAULT_PASSWORD;
                            $idpUserId = $this->idUserCreate($validatedData);
                            if (!empty($idpUserId)) {
                                $validatedData['idp_user_id'] = $idpUserId;
                                $youth = new YouthTemp();
                                $youth->fill($validatedData);
                                $youth->save();
                                $this->storeGuardianInfo($datum, $youth->id);
                                $this->storeAddress($datum, $youth->id);
                            } else {
                                Log::channel('youth_bulk_import')->info("IDP USER IS NOT CREATED FOR USERNAME = " . $validatedData['username']);
                            }
                        } else {
                            // Log::channel('youth_bulk_import')->info("Youth is Exist: " . json_encode($youthInformation));
                        }
                    } else {
                        // Log::channel('youth_bulk_import')->info("Invalid Data Set: " . json_encode($youthInformation));
                    }

                }
            });
            DB::commit();
        } catch (Throwable $exception) {
            Log::channel('youth_bulk_import')->info("Youth Import Exception " . json_encode($exception->getMessage()));
            DB::rollBack();
            throw $exception;
        }

    }

    private function youthBasicInformation(array &$basicInfo, $data): void
    {
        if (!empty(trim($data['first_name'])) && !empty(trim($data['phone']))) {
            $basicInfo['username'] = bn2en(trim($data['phone']));
            $basicInfo['first_name'] = trim($data['first_name']);
            $basicInfo['last_name'] = trim($data['last_name']) ?? "";
            $basicInfo['mobile'] = bn2en(trim($data['phone']));


            if (!empty(trim($data['gender']))) {
                $basicInfo['gender'] = (int)trim($data['gender']);
            }

            if (!empty(trim($data['loc_division_id']))) {
                if (!empty($this->getLocationId(trim($data['loc_division_id']), 1))) {
                    $basicInfo['loc_division_id'] = $this->getLocationId(trim($data['loc_division_id']), 1);
                }

            }
            if (!empty(trim($data['loc_division_id']))) {
                if (!empty($this->getLocationId(trim($data['loc_district_id']), 2))) {
                    $basicInfo['loc_district_id'] = $this->getLocationId(trim($data['loc_district_id']), 2);
                }

            }

            if (!empty(trim($data['nid_no']))) {
                $basicInfo['identity_number_type'] = Youth::NID;
                $basicInfo['identity_number'] = bn2en(trim($data['nid_no']));
            } elseif (!empty(trim($data['birth_registration']))) {
                $basicInfo['identity_number_type'] = Youth::BIRTH_CARD;
                $basicInfo['identity_number'] = bn2en(trim($data['birth_registration']));
            }

            if (!empty(trim($data['biography']))) {
                $basicInfo['bio'] = trim($data['biography']);
            }

            if (!empty(trim($data['postal_code'])) && strlen(trim($data['postal_code'])) == 4) {
                $basicInfo['zip_or_postal_code'] = trim($data['postal_code']);
            }

            if (!empty(trim($data['email'])) && filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
                $basicInfo['email'] = strtolower(trim($data['email']));
            }

            if (!empty(trim($data['dob']))) {
                $basicInfo['date_of_birth'] = bn2en(trim($data['dob']));
            }

        } else {
            Log::channel('youth_bulk_import')->info("Youth required field firstName and Phone number is invalid: " . json_encode($data));
        }
    }

    private function storeGuardianInfo(array $data, int $youthId): void
    {
        $guardianInfo = [];
        if (!empty($data['father_name'])) {
            $guardianInfo[] = [
                "youth_id" => $youthId,
                "name" => $data['father_name'],
                "relationship_type" => YouthGuardian::RELATIONSHIP_TYPE_FATHER
            ];
        }

        if (!empty($data['mother_name'])) {
            $guardianInfo[] = [
                "youth_id" => $youthId,
                "name" => $data['mother_name'],
                "relationship_type" => YouthGuardian::RELATIONSHIP_TYPE_MOTHER
            ];
        }
        DB::table('youth_guardian_temp')->insert($guardianInfo);
    }

    private function storeAddress(array $data, int $youthId): void
    {
        if (!empty($data['present_loc_division_id']) && !empty($data['present_loc_district_id'])) {
            if (!empty($this->getLocationId(trim($data['present_loc_division_id']), 1)) && !empty($this->getLocationId(trim($data['present_loc_district_id']), 2))) {
                $address['loc_division_id'] = $this->getLocationId($data['present_loc_division_id'], 1);
                $address['loc_district_id'] = $this->getLocationId($data['present_loc_district_id'], 2);
            }

            if (!empty($data['present_postal_code']) && strlen($data['present_postal_code']) == 4) {
                $address['zip_or_postal_code'] = $data['present_postal_code'];
            }
            $address['youth_id'] = $youthId;
            DB::table("youth_address_temp")->insert($address);
        }
    }


    public function youthExist(string $username): bool
    {
        return (bool)YouthTemp::where("username", $username)->count("id");
    }

    public function youthValidation(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            "first_name" => "required",
            "last_name" => "nullable",
            "loc_division_id" => [
                "nullable"
            ],
            "loc_district_id" => [
                "nullable"
            ],
            "date_of_birth" => [
                'nullable',
            ],
            "gender" => [
                'nullable',
                Rule::in(BaseModel::GENDERS),
            ],
            "email" => [
                "nullable"
            ],
            "mobile" => [
                "required",
            ],
            'identity_number_type' => [
                'nullable',
                'int',
                Rule::in(Youth::IDENTITY_TYPES)
            ],
            'identity_number' => [
                'nullable'
            ],
            "bio" => "nullable",
            "zip_or_postal_code" => [
                "nullable",
                "size:4"
            ]
        ];

        return Validator::make($data, $rules);
    }

    private function idUserCreate(array $validated): string|null
    {
        $idpUserPayLoad = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? "dummy." . $validated['mobile'] . "@nise.gov.bd",
            'username' => $validated['username'],
            'password' => $validated['password'],
            'user_type' => BaseModel::YOUTH_USER_TYPE,
            'account_disable' => true,
            'account_lock' => true
        ];

        $username = $validated['username'];

        $idpFilteredUser = IdpUser()->setPayload([
            'filter' => "userName eq $username",
        ])->findUsers()->get();

        $idpFilteredUser = $idpFilteredUser['data'];

        if (!empty($idpFilteredUser['totalResults']) && $idpFilteredUser['totalResults'] == 1 && !empty($idpFilteredUser['Resources'][0]['phoneNumbers'][0]['value'])) {
            return $idpFilteredUser['Resources'][0]['id'] ?? null;
        } else {
            $response = app(YouthProfileService::class)->idpUserCreate($idpUserPayLoad);
            return $response['data']['id'] ?? null;
        }

    }
}
