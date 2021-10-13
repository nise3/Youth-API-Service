<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\Youth;
use App\Services\YouthManagementServices\YouthAddressService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use stdClass;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\Services\YouthManagementServices\YouthProfileService;


class YouthProfileController extends Controller
{
    /**
     * @var YouthProfileService
     */
    public YouthProfileService $youthProfileService;
    /**
     * @var YouthAddressService
     */
    public YouthAddressService $youthAddressService;
    /**
     * @var Carbon
     */
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
        try {
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
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     * @throws Throwable
     */
    function youthRegistration(Request $request): JsonResponse
    {
        $youth = new Youth();
        $validated = $this->youthProfileService->youthRegisterOrUpdateValidation($request)->validate();

        $validated['username'] = $validated['user_name_type'] == BaseModel::USER_NAME_TYPE_EMAIL ? $validated["email"] : $validated['mobile'];

        DB::beginTransaction();
        try {
            $idpUserPayLoad = [
                'name' => $validated['first_name'] . " " . $validated["last_name"],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'password' => $validated['password'],
                'user_type' => BaseModel::YOUTH_USER_TYPE,
                'active' => BaseModel::ROW_STATUS_PENDING,
            ];
            //dd(isset($validated['loc_upazila_id']));
            $httpClient = $this->youthProfileService->idpUserCreate($idpUserPayLoad);
            if ($httpClient->json("id")) {
                Log::info("Youth create for idp user--->".$httpClient->json("id")."----->email-->".$validated['email']);
                $validated['idp_user_id'] = $httpClient->json("id");
                $validated["verification_code"] = $this->youthProfileService->generateCode();
                $validated['verification_code_sent_at'] = Carbon::now();
                $validated['row_status'] = BaseModel::ROW_STATUS_PENDING;
                $youth = $this->youthProfileService->store($youth, $validated);

                Log::info("Youth user create in db----->email-->".$validated['email']);

                $addressData['youth_id'] = $youth->id;
                $addressData['address_type'] = BaseModel::ADDRESS_TYPE_PRESENT;
                $addressData['loc_division_id'] = $validated['loc_division_id'];
                $addressData['loc_district_id'] = $validated['loc_district_id'];
                $addressData['loc_upazila_id'] = isset($validated['loc_upazila_id']) ? $validated['loc_upazila_id'] : null;
                $addressData['village_or_area'] = isset($validated['village_or_area']) ? $validated['village_or_area'] : null;
                $addressData['village_or_area_en'] = isset($validated['village_or_area_en'])?$validated['village_or_area_en']: null;
                $addressData['zip_or_postal_code'] = isset($validated['zip_or_postal_code'])?$validated['zip_or_postal_code']: null;
                $address = $this->youthAddressService->store($addressData);

                Log::info("Youth user address create for user");

                /** @var  $sendVeryCodePayLoad */
                $sendVeryCodePayLoad["code"] = $validated['verification_code'];
                $payloadField = $validated['user_name_type'] == BaseModel::USER_NAME_TYPE_EMAIL ? "email" : "mobile";
                $sendVeryCodePayLoad[$payloadField] = $validated['user_name_type'] == BaseModel::USER_NAME_TYPE_EMAIL ? $validated["email"] : $validated['mobile'];

                $send_status = $this->youthProfileService->sendVerifyCode($sendVeryCodePayLoad);

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
            } else {
                DB::rollBack();
                $response = [
                    '_response_status' => [
                        "success" => false,
                        "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                        "message" => "Youth registration is not done",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                    ]
                ];
            }

        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function youthProfileInfoUpdate(Request $request): JsonResponse
    {
        $id = Auth::id();
        /** @var Youth $youth */
        $youth = Youth::findOrFail(Auth::id());

        $validated = $this->youthProfileService->youthRegisterOrUpdateValidation($request, $id)->validate();

        try {
            $data = $this->youthProfileService->update($youth, $validated);
            $response = [
                'data' => $data ?: [],
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Youth Profile Successfully updated",
                    "query_time" => $this->startTime->diffForHumans(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function setFreelanceStatus(Request $request): JsonResponse
    {
        $id = Auth::id();

        /** @var Youth $youth */
        $youth = Youth::findOrFail($id);

        $validator = $this->youthProfileService->freelanceStatusValidator($request)->validate();

        try {
            $this->youthProfileService->setFreelanceStatus($youth, $validator);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Successfully set your profile as a freelance profile",
                    "query_time" => $this->startTime->diffForHumans(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws Throwable
     * @throws ValidationException
     */
    public function youthVerification(Request $request): JsonResponse
    {
        $validated = $this->youthProfileService->verifyYouthValidator($request)->validate();
        try {
            $status = $this->youthProfileService->verifyYouth($validated);
            $response = [
                '_response_status' => [
                    "success" => $status,
                    "code" => $status ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                    "message" => $status ? "You have successfully verified." : "Unable to verify",
                    "query_time" => $this->startTime->diffForHumans(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
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

        try {
            $status = $this->youthProfileService->resendCode($validated);
            $response = [
                '_response_status' => [
                    "success" => $status,
                    "code" => $status ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                    "message" => $status ? "Your verification code is successfully sent" : "Unable to send",
                    "query_time" => $this->startTime->diffForHumans(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
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

}
