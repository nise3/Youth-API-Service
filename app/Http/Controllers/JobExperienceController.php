<?php

namespace App\Http\Controllers;

use App\Models\JobExperience;
use App\Services\YouthManagementServices\JobExperienceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class JobExperienceController extends Controller
{
    /**
     * @var JobExperienceService
     */
    public JobExperienceService $jobExperienceService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;


    /**
     * JobExperienceController constructor.
     * @param JobExperienceService $jobExperienceService
     */
    public function __construct(JobExperienceService $jobExperienceService)
    {
        $this->jobExperienceService = $jobExperienceService;
        $this->startTime = Carbon::now();
    }


    /**
     * @param Request $request
     * @return \Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function getList(Request $request)
    {
        $filter = $this->jobExperienceService->filterValidator($request)->validate();
        try {
            $response = $this->jobExperienceService->getAllJobExperiences($filter, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->jobExperienceService->getOneJobExperience($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $this->jobExperienceService->validator($request)->validate();
        try {
            $jobExperience = $this->jobExperienceService->store($validated);
            $response = [
                'data' => $jobExperience,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Job Experience added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $jobExperience = JobExperience::findOrFail($id);
        $validated = $this->jobExperienceService->validator($request, $id)->validate();
        try {
            $jobExperience = $this->jobExperienceService->update($jobExperience, $validated);
            $response = [
                'data' => $jobExperience,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Job Experience updated successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Exception|JsonResponse|Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $jobExperience = JobExperience::findOrFail($id);
        try {
            $this->jobExperienceService->destroy($jobExperience);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Job Experience deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
