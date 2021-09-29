<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $guarded=BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    /** Result Type Info */
    public const RESULT_FIRST_CLASS = 1;
    public const RESULT_SECOND_CLASS = 2;
    public const RESULT_THIRD_CLASS = 3;
    public const RESULT_OUT_OF_FOUR = 4;
    public const RESULT_OUT_OF_FIVE = 5;
    public const RESULT_PASSED = 6;
}
