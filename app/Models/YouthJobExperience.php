<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\YouthJobExperience
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
 * @property string|null job_responsibilities
 * @property string|null job_responsibilities_en
 * @property Carbon start_date
 * @property Carbon end_date
 * @property bool is_currently_working
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class YouthJobExperience extends BaseModel
{
    /** Working Status  */
    public const CURRENTLY_WORKING = 1;
    public const CURRENTLY_NOT_WORKING = 0;

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
    public function areaOfExperiences(): hasMany
    {
        return $this->hasMany(YouthJobExperienceAreaOfExperience::class, 'area_of_experience_id','id');
    }

    public function areaOfBusinesses(): hasMany
    {
        return $this->hasMany(YouthJobExperienceAreaOfBusiness::class, 'area_of_business_id','id');
    }
}
