<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\YouthPortfolio
 *
 * @property int id
 * @property string title
 * @property string|null title_en
 * @property string|null description
 * @property string|null description_bn
 * @property string|null file_path
 * @property int youth_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class YouthPortfolio extends BaseModel
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
