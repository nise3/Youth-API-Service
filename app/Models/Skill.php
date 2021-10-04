<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Skill
 * @package App\Models
 * @property string title_en
 * @property string title_bn
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

    protected $hidden=["pivot"];
}
