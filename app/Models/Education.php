<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\YouthEducation
 *
 * @property int id
 * @property int youth_id
 * @property int level_of_education
 * @property int exam_degree_id
 * @property int|null edu_group_id
 * @property int|null board_id
 * @property int|null is_foreign_institute
 * @property int|null foreign_institute_country_id
 * @property int result
 * @property int|null duration
 * @property string|null exam_degree_name
 * @property string|null exam_degree_name_en
 * @property string|null major_or_concentration
 * @property string|null major_or_concentration_en
 * @property string|null institute_name_en
 * @property float|null marks_in_percentage
 * @property float|null cgpa_scale
 * @property float|null cgpa
 * @property string year_of_passing
 * @property string|null achievements
 * @property string|null achievements_en
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Education extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $table = "educations";

    /**
     * @return BelongsTo
     */
    public function youth(): BelongsTo
    {
        return $this->belongsTo(Youth::class);
    }
}
