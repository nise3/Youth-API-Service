<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Skill
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property-read  Collection youths
 */
class Skill extends BaseModel
{
    use HasFactory;

    public $timestamps = false;
    /**
     * @var string[]
     */
    protected $guarded = ['id'];
    /**
     * @var string[]
     */
    protected $hidden = ["pivot"];

    /**
     * @return BelongsToMany
     */
    public function youths(): BelongsToMany
    {
        return $this->belongsToMany(Youth::class, 'youth_skills');
    }
}
