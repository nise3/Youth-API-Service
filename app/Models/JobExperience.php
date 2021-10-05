<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\JobExperience
 *
 * @property int id
 * @property int youth_id
 * @property string company_name
 * @property string|null company_name_en
 * @property string position
 * @property string|null position_en
 * @property int employment_type_id
 * @property string location
 * @property string|null location_en
 * @property string|null job_description
 * @property string|null job_description_en
 * @property Carbon start_date
 * @property Carbon end_date
 * @property bool is_currently_work
 * @property int row_status
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class JobExperience extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
