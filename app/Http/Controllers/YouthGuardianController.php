<?php

namespace App\Http\Controllers;

use App\Models\YouthGuardian;
use App\Services\YouthManagementServices\YouthGuardianService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class YouthGuardianController extends Controller
{
    public YouthGuardianService $youthGuardianService;
    private Carbon $startTime;


    public function __construct(YouthGuardianService $youthGuardianService)
    {
        $this->youthGuardianService = $youthGuardianService;
        $this->startTime = Carbon::now();
    }


    public function getList(Request $request): JsonResponse
    {

        $filter = $this->youthGuardianService->filterValidator($request)->validate();
        try {
            $response = $this->youthGuardianService->getGuardianList($filter, $this->startTime);

        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return \Exception|JsonResponse|Throwable
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->youthGuardianService->getOneGuardian($id, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Exception|JsonResponse|Throwable
     * @throws ValidationException|Throwable
     */
    function store(Request $request): JsonResponse
    {
        $request['youth_id'] = $request['youth_id'] ?? Auth::id();
        $validated = $this->youthGuardianService->validator($request)->validate();
        try {
            $data = $this->youthGuardianService->createGuardian($validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Guardian added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return \Exception|JsonResponse|Throwable
     * @throws ValidationException|Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {

        $guardian = YouthGuardian::findOrFail($id);
        $request['youth_id'] = Auth::id();
        $validated = $this->youthGuardianService->validator($request, $id)->validate();

        try {
            $data = $this->youthGuardianService->update($guardian, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Guardian updated successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
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
     * @return \Exception|JsonResponse|Throwable
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $guardian = YouthGuardian::findOrFail($id);
        try {
            $this->youthGuardianService->destroy($guardian);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Guardian deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
