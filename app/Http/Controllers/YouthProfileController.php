<?php

namespace App\Http\Controllers;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\Youth;
use App\Models\YouthAddress;
use App\Services\YouthManagementServices\YouthAddressService;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use stdClass;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\Services\YouthManagementServices\YouthProfileService;


class YouthProfileController extends Controller
{

    public YouthProfileService $youthProfileService;

    public YouthAddressService $youthAddressService;

    private Carbon $startTime;

    /**
     * YouthProfileController constructor.
     * @param YouthProfileService $youthProfileService
     * @param YouthAddressService $youthAddressService
     */
    public function __construct(YouthProfileService $youthProfileService, YouthAddressService $youthAddressService)
    {
        $this->youthProfileService = $youthProfileService;
        $this->youthAddressService = $youthAddressService;
        $this->startTime = Carbon::now();
    }

    /**
     * @return JsonResponse
     * @throws Throwable
     */
    public function getYouthProfile(): JsonResponse
    {
        $youth = $this->youthProfileService->getYouthProfile();
        $response = [
            "data" => $youth ?: new stdClass(),
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Youth profile information",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, $response['_response_status']['code']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function youthProfiles(Request $request): JsonResponse
    {
        $requestData = $request->all();
        $youth = $this->youthProfileService->getYouthProfile($requestData['youth_ids']);
        $response = [
            "data" => $youth ?: new stdClass(),
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Youth profile information from list",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, $response['_response_status']['code']);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    function youthRegistration(Request $request): JsonResponse
    {
        $youth = app(Youth::class);
        $validated = $this->youthProfileService->youthRegisterValidation($request)->validate();

        $validated['username'] = $validated['user_name_type'] == BaseModel::USER_NAME_TYPE_EMAIL ? $validated["email"] : $validated['mobile'];
        Log::debug('-- Youth Registration Validation Ok -- ');

        try {
            DB::beginTransaction();
            $idpUserPayLoad = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'mobile' => $validated['mobile'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'password' => $validated['password'],
                'user_type' => BaseModel::YOUTH_USER_TYPE,
                'active' => (string)BaseModel::ROW_STATUS_PENDING,
            ];

            $idpResponse = $this->youthProfileService->idpUserCreate($idpUserPayLoad);

            if (!empty($idpResponse['code']) && $idpResponse['code'] == ResponseAlias::HTTP_CONFLICT) {
                throw new RuntimeException('Idp user already exists', 409);
            }

            if (empty($idpResponse['data']['id'])) {
                $response = [
                    '_response_status' => [
                        "success" => false,
                        "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                        "message" => "Youth registration is not done in idp",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                    ]
                ];
                return Response::json($response, $response['_response_status']['code']);
            }

            Log::info("Youth create for idp user--->" . $idpResponse['data']['id'] . "----->email-->" . $validated['email']);

            $validated['idp_user_id'] = $idpResponse['data']['id'];
            $validated["verification_code"] = generateOtp(4);
            $validated['verification_code_sent_at'] = Carbon::now();
            $validated['row_status'] = BaseModel::ROW_STATUS_PENDING;
            $youth = $this->youthProfileService->store($youth, $validated);

            Log::info("Youth user create in youth db----->email-->" . $validated['email']);

            //TODO:: Need to optimize this code
            $addressData['youth_id'] = $youth->id;
            $addressData['address_type'] = YouthAddress::ADDRESS_TYPE_PRESENT;
            $addressData['loc_division_id'] = $validated['loc_division_id'];
            $addressData['loc_district_id'] = $validated['loc_district_id'];
            $addressData['loc_upazila_id'] = $validated['loc_upazila_id'] ?? null;
            $addressData['village_or_area'] = $validated['village_or_area'] ?? null;
            $addressData['village_or_area_en'] = $validated['village_or_area_en'] ?? null;
            $addressData['house_n_road'] = $validated['house_n_road'] ?? null;
            $addressData['house_n_road_en'] = $validated['house_n_road_en'] ?? null;
            $addressData['zip_or_postal_code'] = $validated['zip_or_postal_code'] ?? null;

            $this->youthAddressService->store($addressData);

            Log::info("Youth address save in db successfully");


            /** @var  $sendVerifyCodePayLoad */
            $sendVerifyCodePayLoad["code"] = $validated['verification_code'];
            $payloadField = $validated['user_name_type'] == BaseModel::USER_NAME_TYPE_EMAIL ? "email" : "mobile";
            $sendVerifyCodePayLoad[$payloadField] = $validated['user_name_type'] == BaseModel::USER_NAME_TYPE_EMAIL ? $validated["email"] : $validated['mobile'];

            $this->youthProfileService->sendVerifyCode($sendVerifyCodePayLoad, $validated['verification_code']);

            Log::info("Sms send successfully after registration");

            $response = [
                'data' => $youth ?? new stdClass(),
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Youth registration successfully done!",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
            DB::commit();
            return Response::json($response, $response['_response_status']['code']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function youthProfileInfoUpdate(Request $request): JsonResponse
    {
        /** @var Youth $youth */
        $youth = Youth::findOrFail(Auth::id());

        $validated = $this->youthProfileService->youthUpdateValidation($request, $youth)->validate();

        $data = $this->youthProfileService->update($youth, $validated);
        if ($data) {
            $idpUserPayload = [
                'id' => $youth->idp_user_id,
                'username' => $youth->username,
                'first_name' => $youth->first_name,
                'last_name' => $youth->last_name,
                'email' => $youth->email,
                'mobile' => $youth->mobile,
                'active' => (string)$youth->row_status
            ];
            $this->youthProfileService->idpUserUpdate($idpUserPayload);
        }
        $response = [
            'data' => $data ?: [],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Youth Profile Successfully Updated",
                "query_time" => $this->startTime->diffForHumans(Carbon::now())
            ]
        ];
        return Response::json($response, $response['_response_status']['code']);
    }

    /**
     * Remove the specified resource from storage
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function setFreelanceStatus(Request $request): JsonResponse
    {
        $id = Auth::id();

        /** @var Youth $youth */
        $youth = Youth::findOrFail($id);

        $validator = $this->youthProfileService->freelanceStatusValidator($request)->validate();

        $this->youthProfileService->setFreelanceStatus($youth, $validator);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Successfully set your profile as a freelance profile",
                "query_time" => $this->startTime->diffForHumans(Carbon::now())
            ]
        ];
        return Response::json($response, $response['_response_status']['code']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function youthVerification(Request $request): JsonResponse
    {
        $validated = $this->youthProfileService->verifyYouthValidator($request)->validate();
        $status = $this->youthProfileService->verifyYouth($validated);

        $response = [
            '_response_status' => [
                "success" => $status,
                "code" => $status ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                "message" => $status ? "You have successfully verified." : "Unable to verify",
                "query_time" => $this->startTime->diffForHumans(Carbon::now())
            ]
        ];

        return Response::json($response, $response['_response_status']['code']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function resendVerificationCode(Request $request): JsonResponse
    {
        $validated = $this->youthProfileService->resendCodeValidator($request)->validate();

        $status = $this->youthProfileService->resendCode($validated);
        $response = [
            '_response_status' => [
                "success" => $status,
                "code" => $status ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                "message" => $status ? "Your verification code is successfully sent" : "Unable to send",
                "query_time" => $this->startTime->diffForHumans(Carbon::now())
            ]
        ];

        return Response::json($response, $response['_response_status']['code']);
    }

    public function getYouthEnrollCourses(Request $request): JsonResponse
    {
        $validated = $this->youthProfileService->youthEnrollCoursesFilterValidator($request)->validate();
        $validated['youth_id'] = Auth::id();
        $data = $this->youthProfileService->getYouthEnrollCourses($validated);

        $response = [
            'data' => $data ? $data['data'] : [],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "My courses fetch successfully",
                "query_time" => $this->startTime->diffForHumans(Carbon::now())
            ]
        ];
        return Response::json($response, $response['_response_status']['code']);
    }

    /**
     * @throws Throwable
     */
    public function youthApplyToJob(Request $request): JsonResponse
    {
        $requestData = $request->all();

        throw_if(!is_numeric($requestData['expected_salary']), ValidationException::withMessages([
            "Expected Salary must be integer.[32000]"
        ]));

        $data = ServiceToServiceCall::youthApplyToJob($requestData);

        $response = [
            'data' => $data ?? [],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Applied to job successfully",
                "query_time" => $this->startTime->diffForHumans(Carbon::now())
            ]
        ];
        return Response::json($response, $response['_response_status']['code']);
    }

    /**
     * @throws RequestException
     */
    public function getYouthFeedStatistics(): JsonResponse
    {
        $youthId = Auth::id();
        $data = $this->youthProfileService->getYouthFeedStatisticsData($youthId);
        $data ['applied_jobs'] = 0;
        $data ['total_jobs'] = 0;
        $data['skill_matching_jobs'] = 0;
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Youth statistics fetch successfully",
                "query_time" => $this->startTime->diffForHumans(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Cv download function
     */
    public function youthCvDownload($id)
    {
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML('
             <h1>' . 'Youth Name' . '</h1>' .
            '<p> service_version: ' . 'service_version' . '</p>' .
            '<p> lumen_version: ' . 'lumen_version' . '</p>' .
            '<p> ID: ' . $id . '</p>' .
            '<p> module_list: ' . 'module_list' . '</p>' .
            '<p> description: <br>' . 'description' . '</p>'
        );
        $mpdf->Output('MyPDF.pdf', 'D');
    }


    public function getAuthYouthInfoByIdpId(Request $request): JsonResponse
    {
        $authYouthInfo = $this->youthProfileService->getAuthYouth($request->idp_user_id ?? null);
        $response = [
            'data' => $authYouthInfo,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Auth Youth Information",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * Update youth career info
     * @throws ValidationException
     */
    public function youthCareerInfoUpdate(Request $request): JsonResponse
    {
        $youthId = Auth::id();
        $youth = Youth::findOrFail($youthId);
        $validated = $this->youthProfileService->youthCareerInfoUpdateValidator($request)->validate();
        $youthCareerInfo = $this->youthProfileService->youthCareerInfoUpdate($youth, $validated);
        $response = [
            'data' => $youthCareerInfo,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Youth career info updated successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
