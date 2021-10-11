<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Certification
 *
 * @property int id
 * @property int youth_id
 * @property string certification_name
 * @property string|null certification_name_en
 * @property string institute_name
 * @property string|null institute_name_en
 * @property string location
 * @property string|null location_en
 * @property string|null job_description
 * @property string|null job_description_en
 * @property string|null certificate_file_path
 * @property Carbon start_date
 * @property Carbon end_date
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Certification extends BaseModel
{
    use SoftDeletes, HasFactory;
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    /**
     * @return BelongsTo
     */
    public function youth(): BelongsTo
    {
        return $this->belongsTo(Youth::class);
    }
}
