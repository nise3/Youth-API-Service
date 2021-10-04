<?php

namespace App\Http\Controllers;

use App\Models\Youth;
use Faker\Provider\Uuid;
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


class YouthController extends Controller
{


    public YouthProfileService $youthProfileService;
    private Carbon $startTime;


    public function __construct(YouthProfileService $youthProfileService)
    {
        $this->youthProfileService = $youthProfileService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->youthProfileService->getYouthProfileList($request, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->youthProfileService->getOneYouthProfile($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    function store(Request $request)
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
                $validated["verification_code"] = "1234";
                $validated['verification_code_sent_at'] = Carbon::now();
                $youth = $this->youthProfileService->store($youth, $validated);
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
            return $e;
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
    public function youthVerification(Request $request):JsonResponse
    {
        $validated=$this->youthProfileService->verifyYouthValidator($request)->validate();
        try {
            $this->youthProfileService->verifyYouth($validated);
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

}
