<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Services\YouthManagementServices\YouthPortfolioService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * Class YouthPortfolioController
 * @package App\Http\Controllers
 */
class YouthPortfolioController extends Controller
{
    /**
     * @var YouthPortfolioService
     */
    public YouthPortfolioService $portfolioService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;


    /**
     * YouthPortfolioController constructor.
     * @param YouthPortfolioService $portfolioService
     */
    public function __construct(YouthPortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
        $this->startTime = Carbon::now();
    }

    /**
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function getList(Request $request)
    {
        $filter = $this->portfolioService->filterValidator($request)->validate();
        try {
            $response = $this->portfolioService->getAllPortfolios($filter, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->portfolioService->getOnePortfolio($id, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request['youth_id'] = Auth::id();
        $validated = $this->portfolioService->validator($request)->validate();
        try {
            $portfolio = $this->portfolioService->store($validated);
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
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $portfolio = Portfolio::findOrFail($id);
        $request['youth_id'] = Auth::id();
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
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Exception|JsonResponse|Throwable
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
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
