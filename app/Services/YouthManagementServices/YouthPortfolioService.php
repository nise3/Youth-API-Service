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

        if (is_int(Auth::id())) {
            $portfolioBuilder->where('youth_portfolios.youth_id', Auth::id());
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
     * @param Carbon $startTime
     * @return array
     */
    public function getOnePortfolio(int $id, Carbon $startTime): array
    {
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
        $portfolioBuilder->where('youth_portfolios.id', $id);

        /** @var YouthPortfolio $portfolio */
        $portfolio = $portfolioBuilder->first();

        return [
            "data" => $portfolio ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];

    }

    /**
     * @param array $data
     * @return YouthPortfolio
     */
    public function store(array $data): YouthPortfolio
    {
        $portfolio = new YouthPortfolio();
        $portfolio->fill($data);
        $portfolio->save();
        return $portfolio;
    }

    /**
     * @param YouthPortfolio $portfolio
     * @param array $data
     * @return YouthPortfolio
     */
    public function update(YouthPortfolio $portfolio, array $data): YouthPortfolio
    {
        $portfolio->fill($data);
        $portfolio->save();
        return $portfolio;
    }

    /**
     * @param YouthPortfolio $portfolio
     * @return bool
     */
    public function destroy(YouthPortfolio $portfolio): bool
    {
        return $portfolio->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
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
            'youth_id' => [
                'required',
                'int',
                'exists:youths,id'
            ],
            'file_path' => [
                'nullable',
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
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be within ASC or DESC',
            ]
        ];

        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return Validator::make($request->all(), [
            'page' => 'numeric|gt:0',
            'title' => 'nullable|max:400|min:2',
            'title_en' => 'nullable|max:300|min:2',
            'page_size' => 'int|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]
        ], $customMessage);
    }
}
