<?php

namespace App\Http\Controllers;

use App\Models\YouthPortfolio;
use App\Services\YouthManagementServices\YouthPortfolioService;
use Carbon\Carbon;
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
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', YouthPortfolio::class);

        $filter = $this->portfolioService->filterValidator($request)->validate();
        $response = $this->portfolioService->getAllPortfolios($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $portfolio = $this->portfolioService->getOnePortfolio($id);
        $this->authorize('view', $portfolio);

        $response = [
            "data" => $portfolio,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
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
        $this->authorize('create', YouthPortfolio::class);
        $validated = $this->portfolioService->validator($request)->validate();
        $portfolio = $this->portfolioService->store($validated);
        $response = [
            'data' => $portfolio,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "YouthPortfolio added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $portfolio = YouthPortfolio::findOrFail($id);

        $this->authorize('update', $portfolio);

        $validated = $this->portfolioService->validator($request, $id)->validate();
        $portfolio = $this->portfolioService->update($portfolio, $validated);
        $response = [
            'data' => $portfolio,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "YouthPortfolio updated successfully",
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
        $portfolio = YouthPortfolio::findOrFail($id);
        $this->authorize('delete', $portfolio);
        $this->portfolioService->destroy($portfolio);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "YouthPortfolio deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
