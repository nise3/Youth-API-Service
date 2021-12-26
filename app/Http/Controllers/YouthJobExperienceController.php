<?php

namespace App\Http\Controllers;

use App\Models\YouthJobExperience;
use App\Services\YouthManagementServices\YouthJobExperienceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class YouthJobExperienceController extends Controller
{
    /**
     * @var YouthJobExperienceService
     */
    public YouthJobExperienceService $jobExperienceService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * YouthJobExperienceController constructor.
     * @param YouthJobExperienceService $jobExperienceService
     */
    public function __construct(YouthJobExperienceService $jobExperienceService)
    {
        $this->jobExperienceService = $jobExperienceService;
        $this->startTime = Carbon::now();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', YouthJobExperience::class);

        $filter = $this->jobExperienceService->filterValidator($request)->validate();
        $response = $this->jobExperienceService->getAllJobExperiences($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param int|null $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id = null): JsonResponse
    {
        $jobExperience = $this->jobExperienceService->getOneJobExperience($id);
        $this->authorize('view', $jobExperience);

        $response = [
            "data" => $jobExperience,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $jobExperience = app(YouthJobExperience::class);

        $this->authorize('create', YouthJobExperience::class);

        $validated = $this->jobExperienceService->validator($request)->validate();
        $jobExperience = $this->jobExperienceService->store($jobExperience, $validated);
        $response = [
            'data' => $jobExperience,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Job Experience added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $jobExperience = YouthJobExperience::findOrFail($id);
        $this->authorize('update', $jobExperience);

        $validated = $this->jobExperienceService->validator($request, $id)->validate();
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
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $jobExperience = YouthJobExperience::findOrFail($id);
        $this->authorize('delete', $jobExperience);

        $this->jobExperienceService->destroy($jobExperience);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Job Experience deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
