<?php


namespace App\Services\YouthManagementServices;


use App\Models\JobExperience;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

class JobExperienceService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */

    public function getAllJobExperiences(array $request, Carbon $startTime): array
    {

        $companyNameEn = $request['company_name_en'] ?? "";
        $companyNameBn = $request['company_name_bn'] ?? "";
        $youthId = $request['youth_id'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $jobExperienceBuilder */
        $jobExperienceBuilder = JobExperience::select([
            'job_experiences.id',
            'job_experiences.company_name_en',
            'job_experiences.company_name_bn',
            'job_experiences.position',
            'job_experiences.youth_id',
            'job_experiences.location',
            'job_experiences.job_description',
            'job_experiences.start_date',
            'job_experiences.end_date',
            'job_experiences.row_status',
            'job_experiences.created_at',
            'job_experiences.updated_at'
        ]);

        if ($youthId) {
            $jobExperienceBuilder->where('job_experiences.youth_id', $youthId);
        }

        $jobExperienceBuilder->orderBy('job_experiences.id', $order);

        if (is_numeric($rowStatus)) {
            $jobExperienceBuilder->where('job_experiences.row_status', $rowStatus);
        }
        if (!empty($companyNameEn)) {
            $jobExperienceBuilder->where('job_experiences.company_name_en', 'like', '%' . $companyNameEn . '%');
        }
        if (!empty($companyNameBn)) {
            $jobExperienceBuilder->where('job_experiences.company_name_bn', 'like', '%' . $companyNameBn . '%');
        }

        /** @var Collection $jobExperiences */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $jobExperiences = $jobExperienceBuilder->paginate($pageSize);
            $paginateData = (object)$jobExperiences->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $jobExperiences = $jobExperienceBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $jobExperiences->toArray()['data'] ?? $jobExperiences->toArray();
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
    public function getOneJobExperience(int $id, Carbon $startTime): array
    {
        /** @var Builder $jobExperienceBuilder */
        $jobExperienceBuilder = JobExperience::select([
            'job_experiences.id',
            'job_experiences.company_name_en',
            'job_experiences.company_name_bn',
            'job_experiences.position',
            'job_experiences.youth_id',
            'job_experiences.location',
            'job_experiences.job_description',
            'job_experiences.start_date',
            'job_experiences.end_date',
            'job_experiences.row_status',
            'job_experiences.created_at',
            'job_experiences.updated_at'
        ]);
        $jobExperienceBuilder->where('job_experiences.id', $id);

        /** @var JobExperience $jobExperience */
        $jobExperience = $jobExperienceBuilder->first();

        return [
            "data" => $jobExperience ?: null,
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];

    }
}
