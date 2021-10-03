<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
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
     * PortfolioController constructor.
     * @param JobExperienceService $jobExperienceService
     */
    public function __construct(JobExperienceService $jobExperienceService)
    {
        $this->jobExperienceService = $jobExperienceService;
        $this->startTime = Carbon::now();
    }


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
            $portfolio = $this->jobExperienceService->store($validated);
            $response = [
                'data' => $portfolio,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Portfolio added successfully",
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
        $portfolio = Portfolio::findOrFail($id);
        $validated = $this->portfolioService->validator($request, $id)->validate();
        try {
            $portfolio = $this->portfolioService->update($portfolio, $validated);
            $response = [
                'data' => $portfolio,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Portfolio updated successfully",
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
        $portfolio = Portfolio::findOrFail($id);
        try {
            $this->portfolioService->destroy($portfolio);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Portfolio deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
