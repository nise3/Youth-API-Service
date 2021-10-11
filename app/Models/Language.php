<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Language
 *
 * @property int id
 * @property int row_status
 * @property string lang_code
 * @property string title
 * @property string title_en
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Language extends BaseModel
{
    use SoftDeletes, HasFactory;
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
