<?php

namespace App\Http\Controllers;

use App\Models\Youth;
use App\Services\YouthManagementServices\YouthService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use stdClass;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;


class  YouthController extends Controller
{
    public YouthService $youthService;
    public Carbon $startTime;


    public function __construct(YouthService $youthService)
    {
        $this->youthService = $youthService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException|Throwable
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->youthService->filterValidator($request)->validate();
        try {
            $response = $this->youthService->getYouthProfileList($filter, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->youthService->getOneYouthProfile($id, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException|Throwable
     */
    function store(Request $request)
    {
//        $youth = new Youth();
        $validated = $this->youthService->youthRegisterValidation($request)->validate();
//
//        DB::beginTransaction();
        try {
//            $idpUserPayLoad = [
//                'name' => $validated['first_name'] . " " . $validated["last_name"],
//                'email' => $validated['email'],
//                'username' => $validated['username'],
//                'password' => $validated['password']
//            ];
//            $httpClient = $this->youthProfileService->idpUserCreate($idpUserPayLoad);
//            if ($httpClient->json("id")) {
//                $validated['idp_user_id'] = $httpClient->json("id");
//                $validated["verification_code"] = "1234";
//                $validated['verification_code_sent_at'] = Carbon::now();
            $youth = $this->youthService->store($validated);
            $response = [
                'data' => $youth ?? new stdClass(),
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Youth registration successfully done!",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
//                DB::commit();
//            } else {
//                DB::rollBack();//              $response = [
//                    '_response_status' => [
//                        "success" => false,
//                        "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
//                        "message" => "Youth registration is not done",
//                        "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
//                    ]
//                ];
//            }
//
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
        $youth = Youth::findOrFail($id);
        $validated = $this->youthService->youthRegisterValidation($request, $id)->validate();

        try {
            $data = $this->youthService->update($youth, $validated);
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
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        /** @var Youth $youth */
        $youth = Youth::findOrFail($id);

        try {
            $this->youthService->destroy($youth);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Youth Profile deleted successfully",
                    "query_time" => $this->startTime->diffForHumans(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
