<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Language;
use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LanguageService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllLanguageList(array $request, Carbon $startTime): array
    {
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";

        $languageBuilder = Language::select(
        [
            'languages.id',
            'languages.lang_code',
            'languages.title',
            'languages.title_en',
            'languages.row_status',
            'languages.created_at',
            'languages.updated_at',
            'languages.deleted_at'
        ]
    );
        $languageBuilder->orderBy('languages.id', $order);

        $response = [];
        if (is_numeric($paginate) || is_numeric($pageSize)) {
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
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());

        return $response;
    }

    /**
     * @param Request $request
     * @return Validator
     */
    public function filterValidator(Request $request): Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC.[30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'page' => 'nullable|int|gt:0',
            'page_size' => 'nullable|int|gt:0',
            'name' => 'nullable|string',
            'name_en' => 'nullable|string',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]
        ], $customMessage);
    }
}
