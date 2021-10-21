<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class SkillService
 * @package App\Services
 */
class SkillService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getSkillList(array $request, Carbon $startTime): array
    {
        $title = $request['title'] ?? "";
        $titleEn = $request['title_en'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $skillBuilder */
        $skillBuilder = Skill::select(
            [
                'skills.id',
                'skills.title',
                'skills.title_en'
            ]
        );
        $skillBuilder->orderBy('skills.id', $order);

        if (!empty($title)) {
            $skillBuilder->where('skills.title', 'like', '%' . $title . '%');
        }
        if (!empty($titleEn)) {
            $skillBuilder->where('skills.title_en', 'like', '%' . $titleEn . '%');
        }

        /** @var Collection $skills */
        $response = [];
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $skills = $skillBuilder->paginate($pageSize);
            $paginateData = (object)$skills->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $skills = $skillBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $skills->toArray()['data'] ?? $skills->toArray();
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());

        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneSkill(int $id, Carbon $startTime): array
    {
        /** @var Builder $skillBuilder */
        $skillBuilder = Skill::select(
            [
                'skills.id',
                'skills.title',
                'skills.title_en'
            ]
        );

        $skillBuilder->where('skills.id', '=', $id);

        /** @var Skill $skill */
        $skill = $skillBuilder->firstOrFail();

        return [
            "data" => $skill,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];
    }

    /**
     * @param array $data
     * @return Skill
     * @throws Throwable
     */
    public function store(array $data): Skill
    {
        /** @var Skill $skill */
        $skill = app(Skill::class);
        $skill->fill($data);
        throw_if($skill->save(), 'RuntimeException', 'Skill has not been saved to db.', 500);
        return $skill;
    }

    /**
     * @param Skill $skill
     * @param array $data
     * @return Skill
     * @throws Throwable
     */
    public function update(Skill $skill, array $data): Skill
    {
        $skill->fill($data);
        throw_if($skill->save(), 'RuntimeException', 'Skill has not been updated to db.', 500);
        return $skill;
    }

    /**
     * @param Skill $skill
     * @return bool
     * @throws Throwable
     */
    public function destroy(Skill $skill): bool
    {
        throw_if($skill->delete(), 'RuntimeException', 'Skill has not been deleted.', 500);
        return true;
    }

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrashedSkillList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $skillBuilder */
        $skillBuilder = Skill::onlyTrashed()->select(
            [
                'skills.id',
                'skills.title',
                'skills.title_en'
            ]
        );

        $skillBuilder->orderBy('skills.id', $order);

        if (!empty($titleEn)) {
            $skillBuilder->where('skills.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $skillBuilder->where('skills.title', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $skills */
        $response = [];
        if (!is_null($paginate) || !is_null($limit)) {
            $limit = $limit ?: 10;
            $skills = $skillBuilder->paginate($limit);
            $paginateData = (object)$skills->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $skills = $skillBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $skills->toArray()['data'] ?? $skills->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param Skill $skill
     * @return bool
     * @throws Throwable
     */
    public function restore(Skill $skill): bool
    {
        throw_if($skill->restore(), 'RuntimeException', 'Skill has not been restored.', 500);
        return true;
    }

    /**
     * @param Skill $skill
     * @return bool
     * @throws Throwable
     */
    public function forceDelete(Skill $skill): bool
    {
        throw_if($skill->forceDelete(), 'RuntimeException', 'Skill has not been successfully deleted forcefully.', 500);
        return true;
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title_en' => [
                'nullable',
                'string',
                'max:200',
                'min:2',

            ],
            'title' => [
                'required',
                'string',
                'max: 400',
                'min:2'
            ]
        ];
        if ($id) {
            $rules['title_en'][] = Rule::unique('skills', 'title_en')
                ->ignore($id)
                ->where(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->whereNull('deleted_at');
                });
            $rules['title'][] = Rule::unique('skills', 'title')
                ->ignore($id)
                ->where(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->whereNull('deleted_at');
                });
        } else {
            $rules['title_en'][] = Rule::unique('skills', 'title_en')
                ->where(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->whereNull('deleted_at');
                });
            $rules['title'][] = Rule::unique('skills', 'title')
                ->where(function (\Illuminate\Database\Query\Builder $query) {
                    return $query->whereNull('deleted_at');
                });
        }
        return Validator::make($request->all(), $rules);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC. [30000]'
        ];

        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|max:200|min:2',
            'title' => 'nullable|max:400|min:2',
            'page' => 'integer|gt:0',
            'pageSize' => 'integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]
        ], $customMessage);
    }
}
