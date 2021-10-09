<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Skill
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property int | null description
 * @property int row_status
 */
class Skill extends BaseModel
{
    use SoftDeletes, HasFactory;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];
    /**
     * @var string[]
     */
    protected $hidden = ["pivot"];

    public function youths(): BelongsToMany
    {
        return $this->belongsToMany(Youth::class, 'youth_skills');
    }
}
