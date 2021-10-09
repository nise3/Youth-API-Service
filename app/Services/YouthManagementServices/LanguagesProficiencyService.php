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

class LanguagesProficiencyService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */

    public function getLanguagesProficiencyList(array $request, Carbon $startTime): array
    {
        $youthId = $request['youth_id'];
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $languageProficiencyBuilder */
        $languageProficiencyBuilder = LanguagesProficiency::select([
            'languages_proficiencies.id',
            'languages_proficiencies.youth_id',
            'languages_proficiencies.language_id',
            'languages.title as language_title',
            'languages.title_en as language_title_en',
            'languages.lang_code',
            'languages_proficiencies.reading_proficiency_level',
            'languages_proficiencies.writing_proficiency_level',
            'languages_proficiencies.speaking_proficiency_level',
            'languages_proficiencies.understand_proficiency_level',
            'languages_proficiencies.row_status',
            'languages_proficiencies.created_at',
            'languages_proficiencies.updated_at'
        ]);
        $languageProficiencyBuilder->orderBy('languages_proficiencies.id', $order);

        $languageProficiencyBuilder->join('languages', function ($join) use ($rowStatus) {
            $join->on('languages_proficiencies.language_id', '=', 'languages.id')
                ->whereNull('languages.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('languages.row_status', $rowStatus);
            }
        });

        if (is_numeric($youthId)) {
            $languageProficiencyBuilder->where('languages_proficiencies.youth_id', $youthId);
        }

        if (is_numeric($rowStatus)) {
            $languageProficiencyBuilder->where('languages_proficiencies.row_status', $rowStatus);
        }

        /** @var Collection $languagesProficiencies */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $languagesProficiencies = $languageProficiencyBuilder->paginate($pageSize);
            $paginateData = (object)$languagesProficiencies->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $languagesProficiencies = $languageProficiencyBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $languagesProficiencies->toArray()['data'] ?? $languagesProficiencies->toArray();
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
    public function getOneLanguagesProficiency(int $id, Carbon $startTime): array
    {
        /** @var Builder $languageBuilder */
        $languageProficiencyBuilder = LanguagesProficiency::select([
            'languages_proficiencies.id',
            'languages_proficiencies.youth_id',
            'languages_proficiencies.language_id',
            'languages.title as language_title',
            'languages.title_en as language_title_en',
            'languages.lang_code',
            'languages_proficiencies.reading_proficiency_level',
            'languages_proficiencies.writing_proficiency_level',
            'languages_proficiencies.speaking_proficiency_level',
            'languages_proficiencies.understand_proficiency_level',
            'languages_proficiencies.row_status',
            'languages_proficiencies.created_at',
            'languages_proficiencies.updated_at'
        ]);
        $languageProficiencyBuilder->join('languages', function ($join) {
            $join->on('languages_proficiencies.language_id', '=', 'languages.id')
                ->whereNull('languages.deleted_at');
        });
        $languageProficiencyBuilder->where('languages_proficiencies.id', $id);

        /** @var $languageProficiency $language */
        $languagesProficiency = $languageProficiencyBuilder->first();

        return [
            "data" => $languagesProficiency ?: [],
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
        $languagesProficiency = new LanguagesProficiency();
        $languagesProficiency->fill($data);
        $languagesProficiency->save();
        return $languagesProficiency;
    }

    /**
     * @param LanguagesProficiency $languagesProficiency
     * @param array $data
     * @return LanguagesProficiency
     */
    public function update(LanguagesProficiency $languagesProficiency, array $data): LanguagesProficiency
    {
        $languagesProficiency->fill($data);
        $languagesProficiency->save();
        return $languagesProficiency;
    }

    /**
     * @param LanguagesProficiency $languagesProficiency
     * @return bool
     */
    public function destroy(LanguagesProficiency $languagesProficiency): bool
    {
        return $languagesProficiency->delete();
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
            'language_id' => [
                'required',
                'int',
                'exists:languages,id'
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
