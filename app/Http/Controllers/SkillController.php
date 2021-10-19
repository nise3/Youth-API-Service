<?php

namespace App\Http\Controllers;


use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\Services\SkillService;
use Illuminate\Http\Request;

/**
 * Class SkillController
 * @package App\Http\Controllers
 */
class SkillController extends Controller
{
    /**
     * @var SkillService
     */
    public SkillService $skillService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * SkillController constructor.
     * @param SkillService $skillService
     */
    public function __construct(SkillService $skillService)
    {
        $this->skillService = $skillService;
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
        $filter = $this->skillService->filterValidator($request)->validate();
        try {
            $returnedData = $this->skillService->getSkillList($filter, $this->startTime);
            $response = [
                'order' => $returnedData['order'],
                'data' => $returnedData['data'],
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    'query_time' => $returnedData['query_time']
                ]
            ];

            if (isset($returnedData['total_page'])) {
                $response['total'] = $returnedData['total'];
                $response['current_page'] = $returnedData['current_page'];
                $response['total_page'] = $returnedData['total_page'];
                $response['page_size'] = $returnedData['page_size'];
            }

            return Response::json($response, ResponseAlias::HTTP_OK);

        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->skillService->getOneSkill($id, $this->startTime);
            $response['_response_status'] = [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                'query_time' => $response['query_time']
            ];
            return Response::json($response, ResponseAlias::HTTP_OK);
        } catch (Throwable $e) {
            throw $e;
        }

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $validated = $this->skillService->validator($request)->validate();
        try {
            $data = $this->skillService->store($validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Skill has been Added Successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
            return Response::json($response, ResponseAlias::HTTP_CREATED);
        } catch (Throwable $e) {
            throw $e;
        }

    }

    /**
     * Update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $skill = Skill::findOrFail($id);
        $validated = $this->skillService->validator($request, $id)->validate();

        try {
            $data = $this->skillService->update($skill, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Skill updated successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
            return Response::json($response, ResponseAlias::HTTP_CREATED);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $skill = Skill::findOrFail($id);

        try {
            $this->skillService->destroy($skill);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Skill deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
            return Response::json($response, ResponseAlias::HTTP_OK);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        try {
            $response = $this->skillService->getTrashedSkillList($request, $this->startTime);

            return Response::json($response);

        } catch (Throwable $e) {
            throw $e;
        }
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function restore(int $id)
    {
        $skill = Skill::onlyTrashed()->findOrFail($id);
        try {
            $this->skillService->restore($skill);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Skill restored successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
            return Response::json($response, ResponseAlias::HTTP_OK);

        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function forceDelete(int $id): JsonResponse
    {
        $skill = Skill::onlyTrashed()->findOrFail($id);
        try {
            $this->skillService->forceDelete($skill);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Skill permanently deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];

            return Response::json($response, ResponseAlias::HTTP_OK);
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
