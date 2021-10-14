<?php

namespace App\Http\Controllers;

use App\Models\YouthEducation;
use App\Services\YouthManagementServices\YouthEducationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;


class YouthEducationController extends Controller
{


    public YouthEducationService $youthEducationService;
    private Carbon $startTime;


    public function __construct(YouthEducationService $youthEducationService)
    {
        $this->youthEducationService = $youthEducationService;
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

        $filter = $this->youthEducationService->filterValidator($request)->validate();
        try {
            $response = $this->youthEducationService->getEducationList($filter, $this->startTime);
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
            $response = $this->youthEducationService->getOneEducation($id, $this->startTime);
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
    function store(Request $request): JsonResponse
    {
        $youthEducation = new YouthEducation();
        $request['youth_id'] = $request['youth_id'] ?? Auth::id();
        $validated = $this->youthEducationService->validator($request)->validate();
        try {
            $data = $this->youthEducationService->createEducation($youthEducation, $validated);
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "YouthEducation added successfully",
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
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException|Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {

        $education = YouthEducation::findOrFail($id);
        $request['youth_id'] = Auth::id();
        $validated = $this->youthEducationService->validator($request, $id)->validate();

        try {
            $data = $this->youthEducationService->update($education, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "YouthEducation updated successfully",
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
     * @return Exception|JsonResponse|Throwable
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $education = YouthEducation::findOrFail($id);

        try {
            $this->youthEducationService->destroy($education);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "YouthEducation deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
