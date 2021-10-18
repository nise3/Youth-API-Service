<?php


namespace App\Services\YouthManagementServices;


use App\Models\BaseModel;
use App\Models\Language;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class LanguageService
{
    public function getAllLanguageList(array $request, Carbon $startTime)
    {
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $languageBuilder */
        $languageBuilder = Language::all();

        $languageBuilder->orderBy('languages.id', $order);

        /** @var Collection $languages */

        if (is_integer($paginate) || is_integer($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $languages = $languageBuilder->paginate($pageSize);
            $paginateData = (object)$languageBuilder->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $languages = $languageBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $languages->toArray()['data'] ?? $languages->toArray();
        $response['response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];
        return $response;
    }

    /**
     * @param Request $request
     * @return Validator
     */
    public function filterValidator(Request $request): Validator
    {
        $customMessage = [
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be within ASC or DESC',
            ]
        ];

        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'page' => 'int|gt:0',
            'pageSize' => 'int|gt:0',
            'name' => 'nullable|string',
            'name_en' => 'nullable|string',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]
        ], $customMessage);
    }
}
