<?php


namespace App\Services\YouthManagementServices;


use App\Models\BaseModel;
use App\Models\LanguagesProficiency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class LanguageService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */

    public function getAllLanguages(array $request, Carbon $startTime): array
    {
        $youthId = $request['youth_id'];
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $languageBuilder */
        $languageBuilder = LanguagesProficiency::select([
            'languages.id',
            'languages.youth_id',
            'languages.language_info_id',
            'language_infos.title as language_title',
            'language_infos.title_en as language_title_en',
            'languages.reading_proficiency_level',
            'languages.writing_proficiency_level',
            'languages.speaking_proficiency_level',
            'languages.understand_proficiency_level',
            'languages.row_status',
            'languages.created_at',
            'languages.updated_at'
        ]);
        $languageBuilder->orderBy('languages.id', $order);

        $languageBuilder->join('language_infos', function ($join) use ($rowStatus) {
            $join->on('languages.language_info_id', '=', 'language_infos.id')
                ->whereNull('language_infos.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('language_infos.row_status', $rowStatus);
            }
        });

        if (is_numeric($youthId)) {
            $languageBuilder->where('languages.youth_id', $youthId);
        }

        if (is_numeric($rowStatus)) {
            $languageBuilder->where('languages.row_status', $rowStatus);
        }

        /** @var Collection $languages */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $languages = $languageBuilder->paginate($pageSize);
            $paginateData = (object)$languages->toArray();
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
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneLanguage(int $id, Carbon $startTime): array
    {
        /** @var Builder $languageBuilder */
        $languageBuilder = LanguagesProficiency::select([
            'languages.id',
            'languages.youth_id',
            'languages.language_info_id',
            'languages.reading_proficiency_level',
            'languages.writing_proficiency_level',
            'languages.speaking_proficiency_level',
            'languages.understand_proficiency_level',
            'languages.row_status',
            'languages.created_at',
            'languages.updated_at'
        ]);
        $languageBuilder->where('languages.id', $id);

        /** @var LanguagesProficiency $language */
        $language = $languageBuilder->first();

        return [
            "data" => $language ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];

    }

    /**
     * @param array $data
     * @return LanguagesProficiency
     */
    public function store(array $data): LanguagesProficiency
    {
        $language = new LanguagesProficiency();
        $language->fill($data);
        $language->save();
        return $language;
    }

    /**
     * @param LanguagesProficiency $language
     * @param array $data
     * @return LanguagesProficiency
     */
    public function update(LanguagesProficiency $language, array $data): LanguagesProficiency
    {
        $language->fill($data);
        $language->save();
        return $language;
    }

    /**
     * @param LanguagesProficiency $language
     * @return bool
     */
    public function destroy(LanguagesProficiency $language): bool
    {
        return $language->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be either 1 or 0'
            ]
        ];
        $rules = [
            'youth_id' => [
                'required',
                'int',
                'exists:youths,id'
            ],
            'language_info_id' => [
                'required',
                'int',
                'exists:language_infos,id'
            ],
            'reading_proficiency_level' => [
                'required',
                'int',
                'min:1',
                'max:2'
            ],
            'writing_proficiency_level' => [
                'required',
                'int',
                'min:1',
                'max:2'
            ],
            'speaking_proficiency_level' => [
                'required',
                'int',
                'min:1',
                'max:2'
            ],
            'understand_proficiency_level' => [
                'required',
                'int',
                'min:1',
                'max:2'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ]
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be either ASC or DESC',
            ],
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];

        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return Validator::make($request->all(), [
            'page' => 'numeric|gt:0',
            'youth_id' => 'required|min:1',
            'page_size' => 'numeric|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "numeric",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}
