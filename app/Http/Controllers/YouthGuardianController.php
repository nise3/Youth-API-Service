<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Education;
use App\Models\Skill;
use App\Services\YouthManagementServices\CertificationService;
use App\Services\YouthManagementServices\EducationService;
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
     * @return Exception|JsonResponse|Throwable
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->educationService->getOneEducation($id, $this->startTime);
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
        $request['youth_id'] = $request['youth_id'] ?? Auth::id();
        $validated = $this->educationService->validator($request)->validate();
        try {
            $validated['youth_id'] = $validated['youth_id'] ?? Auth::id();
            $data = $this->educationService->createEducation($validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Education added successfully",
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

        $education = Education::findOrFail($id);
        $request['youth_id'] = Auth::id();
        $validated = $this->educationService->validator($request, $id)->validate();

        try {
            $data = $this->educationService->update($education, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Education updated successfully",
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
        $education = Education::findOrFail($id);

        try {
            $this->educationService->destroy($education);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Education deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
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
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        try {
            $response = $this->educationService->getTrashedYouthEducationList($request, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $skill = Skill::onlyTrashed()->findOrFail($id);
        try {
            $this->educationService->restore($skill);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Skill restored successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function forceDelete(int $id)
    {
        $skill = Skill::onlyTrashed()->findOrFail($id);
        try {
            $this->educationService->forceDelete($skill);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Skill permanently deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
