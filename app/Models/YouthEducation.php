<?php

namespace App\Models;

use Carbon\Traits\Date;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class YouthEducation
 * @package App\Models
 * @property int id
 * @property int youth_id
 * @property int examination_id
 * @property string institute_name
 * @property int|null board_id
 * @property int group_id
 * @property int result_type
 * @property int result
 * @property double|null cgpa
 * @property Date passing_year
 */
class YouthEducation extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $table = 'education';

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    const GPA_OUT_OF_FIVE = 5;
    const GPA_OUT_OF_FOUR = 4;

}
