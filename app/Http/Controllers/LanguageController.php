<?php

namespace App\Http\Controllers;

use App\Services\YouthManagementServices\LanguageService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
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
        $response = $this->languageService->getAllLanguageList($filter, $this->startTime);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
