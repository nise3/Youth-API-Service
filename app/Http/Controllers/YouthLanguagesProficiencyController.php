<?php

namespace App\Http\Controllers;

use App\Models\YouthLanguagesProficiency;
use App\Services\YouthManagementServices\YouthLanguagesProficiencyService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class YouthLanguagesProficiencyController extends Controller
{

    public YouthLanguagesProficiencyService $languagesProficiencyService;

    private Carbon $startTime;


    public function __construct(YouthLanguagesProficiencyService $languagesProficiencyService)
    {
        $this->languagesProficiencyService = $languagesProficiencyService;
        $this->startTime = Carbon::now();
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->languagesProficiencyService->filterValidator($request)->validate();
        $response = $this->languagesProficiencyService->getLanguagesProficiencyList($filter, $this->startTime);
        return Response::json($response);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $response = $this->languagesProficiencyService->getOneLanguagesProficiency($id, $this->startTime);
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
        $request['youth_id'] = Auth::id();
        $validated = $this->languagesProficiencyService->validator($request)->validate();
        $languageProficiency = $this->languagesProficiencyService->store($validated);
        $response = [
            'data' => $languageProficiency,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "YouthLanguagesProficiency added successfully",
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
        $request['youth_id'] = Auth::id();
        $languageProficiency = YouthLanguagesProficiency::findOrFail($id);
        $validated = $this->languagesProficiencyService->validator($request, $id)->validate();
        $language = $this->languagesProficiencyService->update($languageProficiency, $validated);
        $response = [
            'data' => $language,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "YouthLanguagesProficiency updated successfully",
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
        $languageProficiency = YouthLanguagesProficiency::findOrFail($id);
        $this->languagesProficiencyService->destroy($languageProficiency);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "YouthLanguagesProficiency deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
