<?php

namespace App\Http\Controllers;

use App\Services\YouthManagementServices\LanguageService;
use App\Services\YouthManagementServices\YouthGuardianService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
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

    public function getList(Request $request)
    {
        $filter = $this->languageService->filterValidator($request)->validate();
        try {
            $response = $this->languageService->getAllLanguageList($filter, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }
}
