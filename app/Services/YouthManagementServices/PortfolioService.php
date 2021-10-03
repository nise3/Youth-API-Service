<?php


namespace App\Services\YouthManagementServices;

use App\Models\BaseModel;
use App\Models\Portfolio;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class PortfolioService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */

    public function getAllPortfolios(array $request, Carbon $startTime): array
    {

        $title = $request['title'] ?? "";
        $youthId = $request['youth_id'];
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $portfolioBuilder */
        $portfoliosBuilder = Portfolio::select([
            'portfolios.id',
            'portfolios.title',
            'portfolios.description',
            'portfolios.file_path',
            'portfolios.youth_id',
            'portfolios.row_status',
            'portfolios.created_at',
            'portfolios.updated_at'
        ]);

        if ($youthId) {
            $portfoliosBuilder->where('portfolios.youth_id', $youthId);
        }

        $portfoliosBuilder->orderBy('portfolios.id', $order);

        if (is_numeric($rowStatus)) {
            $portfoliosBuilder->where('portfolios.row_status', $rowStatus);
        }
        if (!empty($title)) {
            $portfoliosBuilder->where('portfolios.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $portfolios */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $portfolios = $portfoliosBuilder->paginate($pageSize);
            $paginateData = (object)$portfolios->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $portfolios = $portfoliosBuilder->get();
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
        $portfolioBuilder = Portfolio::select([
            'portfolios.id',
            'portfolios.title',
            'portfolios.description',
            'portfolios.file_path',
            'portfolios.youth_id',
            'portfolios.row_status',
            'portfolios.created_at',
            'portfolios.updated_at'
        ]);
        $portfolioBuilder->where('portfolios.id', $id);

        /** @var Portfolio $portfolio */
        $portfolio = $portfolioBuilder->first();

        return [
            "data" => $portfolio ?: null,
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];

    }

    /**
     * @param array $data
     * @return Portfolio
     */
    public function store(array $data): Portfolio
    {
        $portfolio = new Portfolio();
        $portfolio->fill($data);
        $portfolio->save();
        return $portfolio;
    }

    /**
     * @param Portfolio $portfolio
     * @param array $data
     * @return Portfolio
     */
    public function update(Portfolio $portfolio, array $data): Portfolio
    {
        $portfolio->fill($data);
        $portfolio->save();
        return $portfolio;
    }

    /**
     * @param Portfolio $portfolio
     * @return bool
     */
    public function destroy(Portfolio $portfolio): bool
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
        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];
        $rules = [
            'title' => [
                'required',
                'string'
            ],
            'description' => [
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
                "message" => 'Order must be within ASC or DESC',
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
            'title' => 'nullable|max:500|min:2',
            'youth_id' => 'required|min:1',
            'pageSize' => 'numeric|gt:0',
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
