<?php

namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\YouthLanguagesProficiency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class YouthLanguagesProficiencyService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */

    public function getLanguagesProficiencyList(array $request, Carbon $startTime): array
    {
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $languageProficiencyBuilder */
        $languageProficiencyBuilder = YouthLanguagesProficiency::select([

            'youth_languages_proficiencies.id',
            'youth_languages_proficiencies.youth_id',
            'youth_languages_proficiencies.language_id',
            'languages.title as language_title',
            'languages.title_en as language_title_en',
            'languages.lang_code',
            'youth_languages_proficiencies.reading_proficiency_level',
            'youth_languages_proficiencies.writing_proficiency_level',
            'youth_languages_proficiencies.speaking_proficiency_level',
            'youth_languages_proficiencies.understand_proficiency_level',
            'youth_languages_proficiencies.created_at',
            'youth_languages_proficiencies.updated_at'

        ])->acl();

        $languageProficiencyBuilder->orderBy('youth_languages_proficiencies.id', $order);

        $languageProficiencyBuilder->join('languages', function ($join) {
            $join->on('youth_languages_proficiencies.language_id', '=', 'languages.id')
                ->whereNull('languages.deleted_at');
        });

        /** @var Collection $languagesProficiencies */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
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
     * @return YouthLanguagesProficiency
     */
    public function getOneLanguagesProficiency(int $id): YouthLanguagesProficiency
    {
        /** @var Builder|YouthLanguagesProficiency $languageBuilder */
        $languageProficiencyBuilder = YouthLanguagesProficiency::select([
            'youth_languages_proficiencies.id',
            'youth_languages_proficiencies.youth_id',
            'youth_languages_proficiencies.language_id',
            'languages.title as language_title',
            'languages.title_en as language_title_en',
            'languages.lang_code',
            'youth_languages_proficiencies.reading_proficiency_level',
            'youth_languages_proficiencies.writing_proficiency_level',
            'youth_languages_proficiencies.speaking_proficiency_level',
            'youth_languages_proficiencies.understand_proficiency_level',
            'youth_languages_proficiencies.created_at',
            'youth_languages_proficiencies.updated_at'
        ]);

        $languageProficiencyBuilder->join('languages', function ($join) {
            $join->on('youth_languages_proficiencies.language_id', '=', 'languages.id')
                ->whereNull('languages.deleted_at');
        });

        $languageProficiencyBuilder->where('youth_languages_proficiencies.id', $id);

        /** @var $languageProficiency $language */
        return $languageProficiencyBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return YouthLanguagesProficiency
     * @throws \Throwable
     */
    public function store(array $data): YouthLanguagesProficiency
    {
        $languagesProficiency = app(YouthLanguagesProficiency::class);
        $languagesProficiency->fill($data);
        throw_if(!$languagesProficiency->save(), 'RuntimeException', 'Youth Language Proficiency has not been Saved to db.', 500);
        return $languagesProficiency;
    }

    /**
     * @param YouthLanguagesProficiency $languagesProficiency
     * @param array $data
     * @return YouthLanguagesProficiency
     * @throws \Throwable
     */
    public function update(YouthLanguagesProficiency $languagesProficiency, array $data): YouthLanguagesProficiency
    {
        $languagesProficiency->fill($data);
        throw_if(!$languagesProficiency->save(), 'RuntimeException', 'Youth Language Proficiency has not been updated to db.', 500);
        return $languagesProficiency;
    }

    /**
     * @param YouthLanguagesProficiency $languagesProficiency
     * @return bool
     * @throws \Throwable
     */
    public function destroy(YouthLanguagesProficiency $languagesProficiency): bool
    {
        throw_if(!$languagesProficiency->delete(), 'RuntimeException', 'Youth Language Proficiency has not been deleted.', 500);
        return true;
    }

    /**
     * @param YouthLanguagesProficiency $languagesProficiency
     * @return bool
     * @throws \Throwable
     */
    public function restore(YouthLanguagesProficiency $languagesProficiency): bool
    {
        throw_if(!$languagesProficiency->restore(), 'RuntimeException', 'Youth Language Proficiency has not been restored.', 500);
        return true;
    }

    /**
     * @param YouthLanguagesProficiency $languagesProficiency
     * @return bool
     * @throws \Throwable
     */
    public function forceDelete(YouthLanguagesProficiency $languagesProficiency): bool
    {
        throw_if(!$languagesProficiency->forceDelete(), 'RuntimeException', 'Youth Language Proficiency has not been successfully deleted forcefully.', 500);
        return true;
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $request->offsetSet('deleted_at', null);
        $customMessage = [];
        $rules = [
            'youth_id' => [
                'required',
                'int',
                'exists:youths,id,deleted_at,NULL',
            ],
            'language_id' => [
                'required',
                'int',
                'exists:languages,id,deleted_at,NULL',
                'unique_with:youth_languages_proficiencies,youth_id,deleted_at,' . $id,
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
            'order.in' => 'Order must be either ASC or DESC. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'page' => 'integer|gt:0',
            'page_size' => 'integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]
        ], $customMessage);
    }
}
