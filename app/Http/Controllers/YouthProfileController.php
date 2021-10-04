<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\Youth;
use Faker\Provider\Uuid;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
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


    public YouthProfileService $youthProfileService;
    private Carbon $startTime;


    public function __construct(YouthProfileService $youthProfileService)
    {
        $this->youthProfileService = $youthProfileService;
        $this->startTime = Carbon::now();
    }

    public function getYouthProfile(int $id): JsonResponse
    {
        try {
            $response = $this->youthProfileService->getOneYouthProfile($id, $this->startTime);
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
    function youthRegister(Request $request): JsonResponse
    {
        $youth = new Youth();

        $validated = $this->youthProfileService->youthRegisterValidation($request)->validate();
        DB::beginTransaction();
        try {
            $idpUserPayLoad = [
                'name' => $validated['first_name'] . " " . $validated["last_name"],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'password' => $validated['password']
            ];
            $httpClient = Uuid::uuid();//$this->youthProfileService->idpUserCreate($idpUserPayLoad);
            if ($httpClient) {
                $validated['idp_user_id'] = $httpClient;
                $validated["verification_code"] = $this->youthProfileService->generateCode();
                $validated['verification_code_sent_at'] = Carbon::now();
                $youth = $this->youthProfileService->store($youth, $validated);

                /** @var  $sendVeryCodePayLoad */
                $sendVeryCodePayLoad["code"] = $validated['verification_code'];
                $payloadField = $validated['user_name_type'] == BaseModel::USER_TYPE_EMAIL ? "email" : "mobile";
                $sendVeryCodePayLoad[$payloadField] = $validated['user_name_type'] == BaseModel::USER_TYPE_EMAIL ? $validated["email"] : $validated['mobile'];

                $send_status=$this->youthProfileService->sendVerifyCode($sendVeryCodePayLoad);

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
     * Update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        /** @var Youth $youth */
        $youth = Youth::findOrFail($id);
        $validated = $this->youthProfileService->youthRegisterValidation($request, $id)->validate();
        Log::info(json_encode($validated));
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
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        /** @var Youth $youth */
        $youth = Youth::findOrFail($id);

        try {
            $this->youthProfileService->destroy($youth);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Skill deleted successfully",
                    "query_time" => $this->startTime->diffForHumans(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
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

}
