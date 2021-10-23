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
        $filter = $this->jobExperienceService->filterValidator($request)->validate();
        $response = $this->jobExperienceService->getAllJobExperiences($filter, $this->startTime);
        return Response::json($response);
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
        $response = $this->jobExperienceService->getOneJobExperience($id, $this->startTime);
        return Response::json($response);
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
        /** @var YouthJobExperience  $jobExperience */
        $jobExperience = app(YouthJobExperience::class);
        if (!$request->filled('youth_id')) {
            $youthId = Auth::id();
            $request->offsetSet('youth_id', $youthId);
        }
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
        if (!$request->filled('youth_id')) {
            $youthId = Auth::id();
            $request->offsetSet('youth_id', $youthId);
        }
        $jobExperience = YouthJobExperience::findOrFail($id);
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
