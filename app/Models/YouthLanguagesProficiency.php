<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\YouthLanguagesProficiency
 * @property int id
 * @property int youth_id
 * @property int language_id
 * @property int reading_proficiency_level
 * @property int writing_proficiency_level
 * @property int speaking_proficiency_level
 * @property int understand_proficiency_level
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class YouthLanguagesProficiency extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $table = 'youth_languages_proficiencies';

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    /**
     * @return BelongsTo
     */
    public function youth(): BelongsTo
    {
        return $this->belongsTo(Youth::class);
    }
}
