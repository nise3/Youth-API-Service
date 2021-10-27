<?php


namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\YouthPortfolio;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class YouthPortfolioService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */

    public function getAllPortfolios(array $request, Carbon $startTime): array
    {
        $youthId = $request['youth_id'] ?? Auth::id();
        $title = $request['title'] ?? "";
        $titleEn = $request['title_en'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $portfolioBuilder */
        $portfolioBuilder = YouthPortfolio::select([
            'youth_portfolios.id',
            'youth_portfolios.title',
            'youth_portfolios.title_en',
            'youth_portfolios.description',
            'youth_portfolios.description_en',
            'youth_portfolios.file_path',
            'youth_portfolios.youth_id',
            'youth_portfolios.created_at',
            'youth_portfolios.updated_at'
        ]);
        $portfolioBuilder->orderBy('youth_portfolios.id', $order);

        if (is_numeric($youthId)) {
            $portfolioBuilder->where('youth_portfolios.youth_id', $youthId);
        }

        if (!empty($title)) {
            $portfolioBuilder->where('youth_portfolios.title', 'like', '%' . $title . '%');
        }

        if (!empty($titleEn)) {
            $portfolioBuilder->where('youth_portfolios.title_en', 'like', '%' . $titleEn . '%');
        }

        /** @var Collection $portfolios */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $portfolios = $portfolioBuilder->paginate($pageSize);
            $paginateData = (object)$portfolios->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $portfolios = $portfolioBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $portfolios->toArray()['data'] ?? $portfolios->toArray();
        $response['response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];
        return $response;

    }

    /**
     * @param int $id
     * @return YouthPortfolio
     */
    public function getOnePortfolio(int $id): YouthPortfolio
    {
        /** @var Builder|YouthPortfolio $portfolioBuilder */
        $portfolioBuilder = YouthPortfolio::select([
            'youth_portfolios.id',
            'youth_portfolios.title',
            'youth_portfolios.title_en',
            'youth_portfolios.description',
            'youth_portfolios.description_en',
            'youth_portfolios.file_path',
            'youth_portfolios.youth_id',
            'youth_portfolios.created_at',
            'youth_portfolios.updated_at'
        ]);
        $portfolioBuilder->where('youth_portfolios.id', $id);

        /** @var YouthPortfolio $portfolio */
        return $portfolioBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return YouthPortfolio
     * @throws \Throwable
     */
    public function store(array $data): YouthPortfolio
    {
        $portfolio = app(YouthPortfolio::class);
        $portfolio->fill($data);
        throw_if(!$portfolio->save(), 'RuntimeException', 'Youth Portfolio has not been Saved to db.', 500);
        return $portfolio;
    }

    /**
     * @param YouthPortfolio $portfolio
     * @param array $data
     * @return YouthPortfolio
     * @throws \Throwable
     */
    public function update(YouthPortfolio $portfolio, array $data): YouthPortfolio
    {
        $portfolio->fill($data);
        throw_if(!$portfolio->save(), 'RuntimeException', 'Youth Portfolio has not been deleted.', 500);
        return $portfolio;
    }

    /**
     * @param YouthPortfolio $portfolio
     * @return bool
     * @throws \Throwable
     */
    public function destroy(YouthPortfolio $portfolio): bool
    {
        throw_if(!$portfolio->delete(), 'RuntimeException', 'Youth Portfolio has not been deleted.', 500);
        return true;
    }

    /**
     * @param YouthPortfolio $portfolio
     * @return bool
     * @throws \Throwable
     */
    public function restore(YouthPortfolio $portfolio): bool
    {
        throw_if(!$portfolio->restore(), 'RuntimeException', 'Youth Portfolio has not been restored.', 500);
        return true;
    }

    /**
     * @param YouthPortfolio $portfolio
     * @return bool
     * @throws \Throwable
     */
    public function forceDelete(YouthPortfolio $portfolio): bool
    {
        throw_if(!$portfolio->forceDelete(), 'RuntimeException', 'Youth Portfolio has not been successfully deleted forcefully.', 500);
        return true;
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'youth_id' => [
                'required',
                'exists:youths,id,deleted_at,NULL',
                'int',
            ],
            'title' => [
                'required',
                'string',
                'max:400',
                'min:2'
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'description' => [
                'nullable',
                'string',
                'min:2'
            ],
            'description_en' => [
                'nullable',
                'string',
                'min:2'
            ],
            'file_path' => [
                'required',
                'string'
            ]
        ];
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

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'page' => 'nullable|integer|gt:0',
            'title' => 'nullable|max:400|min:2',
            'title_en' => 'nullable|max:300|min:2',
            'page_size' => 'nullable|integer|gt:0',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]
        ], $customMessage);
    }
}
