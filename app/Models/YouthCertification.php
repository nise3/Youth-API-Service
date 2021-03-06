<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\YouthCertification
 *
 * @property int id
 * @property int youth_id
 * @property string certification_name
 * @property string|null certification_name_en
 * @property string institute_name
 * @property string|null institute_name_en
 * @property string location
 * @property string|null location_en
 * @property string|null job_responsibilities
 * @property string|null job_responsibilities_en
 * @property string|null certificate_file_path
 * @property Carbon start_date
 * @property Carbon end_date
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class YouthCertification extends BaseModel
{
    use SoftDeletes, HasFactory;
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $dates = ['start_date','end_date'];

    /**
     * @return BelongsTo
     */
    public function youth(): BelongsTo
    {
        return $this->belongsTo(Youth::class);
    }
}
