<?php

namespace App\Http\Controllers;

use App\Services\YouthManagementServices\LanguageService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class LanguageController extends Controller
{

    public LanguageService $languageService;
    private Carbon $startTime;


    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
        $this->startTime = Carbon::now();
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->languageService->filterValidator($request)->validate();
        try {
            $returnedData = $this->languageService->getAllLanguageList($filter, $this->startTime);
            $response = [
                'order' => $returnedData['order'],
                'data' => $returnedData['data'],
                '_response_status' => [
                    "success" => true,
                    "code" => \Symfony\Component\HttpFoundation\Response::HTTP_OK,
                    'query_time' => $returnedData['query_time']
                ]
            ];

            if (isset($returnedData['total_page'])) {
                $response['total'] = $returnedData['total'];
                $response['current_page'] = $returnedData['current_page'];
                $response['total_page'] = $returnedData['total_page'];
                $response['page_size'] = $returnedData['page_size'];
            }
            return Response::json($response, \Symfony\Component\HttpFoundation\Response::HTTP_OK);
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
