<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Language
 *
 * @property int id
 * @property int youth_id
 * @property int language_info_id
 * @property int reading_proficiency_level
 * @property int writing_proficiency_level
 * @property int speaking_proficiency_level
 * @property int understand_proficiency_level
 * @property int row_status
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Language extends BaseModel
{
    protected $guarded=BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
