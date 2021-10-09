<?php

namespace App\Models;


use Carbon\Carbon;

/**
 * App\Models\Portfolio
 *
 * @property int id
 * @property string title
 * @property string title
 * @property string description
 * @property string description_bn
 * @property int youth_id
 * @property int row_status
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Portfolio extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
