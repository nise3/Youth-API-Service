<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ExamDegree
 *
 * @property int id
 * @property int level_of_education
 * @property int row_status
 * @property string code
 * @property string title_en
 * @property string title
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ExamDegree extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

}
