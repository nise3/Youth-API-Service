<?php

namespace App\Http\Controllers;

use App\Models\LanguagesProficiency;
use App\Services\YouthManagementServices\LanguagesProficiencyService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class LanguagesProficiencyController extends Controller
{

    public LanguagesProficiencyService $languagesProficiencyService;

    private Carbon $startTime;


    public function __construct(LanguagesProficiencyService $languagesProficiencyService)
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
        try {
            $response = $this->languagesProficiencyService->getLanguagesProficiencyList($filter, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->languagesProficiencyService->getOneLanguagesProficiency($id, $this->startTime);
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
        $validated = $this->languagesProficiencyService->validator($request)->validate();
        try {
            $validated['youth_id'] = $validated['youth_id'] ?? Auth::id();
            $languageProficiency = $this->languagesProficiencyService->store($validated);
            $response = [
                'data' => $languageProficiency,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "LanguagesProficiency added successfully",
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
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request['youth_id'] = Auth::id();
        $languageProficiency = LanguagesProficiency::findOrFail($id);
        $validated = $this->languagesProficiencyService->validator($request, $id)->validate();
        try {
            $language = $this->languagesProficiencyService->update($languageProficiency, $validated);
            $response = [
                'data' => $language,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "LanguagesProficiency updated successfully",
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
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $languageProficiency = LanguagesProficiency::findOrFail($id);
        try {
            $this->languagesProficiencyService->destroy($languageProficiency);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "LanguagesProficiency deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
