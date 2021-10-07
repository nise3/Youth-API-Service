<?php

namespace App\Models;

use Carbon\Traits\Date;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Education
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
class Education extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $table = 'educations';

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    const DIVISION_FIRST_CLASS = 1;
    const DIVISION_SECOND_CLASS = 2;
    const DIVISION_THIRD_CLASS = 3;
    const DIVISION_PASS = 6;

    const EXAMINATION_ID_PSC = 1;
    const EXAMINATION_ID_JSC = 2;
    const EXAMINATION_ID_JDC = 3;
    const EXAMINATION_ID_SSC = 4;
    const EXAMINATION_ID_DAKHIL = 5;
    const EXAMINATION_ID_HSC = 6;
    const EXAMINATION_ID_ALIM = 7;
    const EXAMINATION_ID_DIBS = 8;
    const EXAMINATION_ID_DEGREE = 9;
    const EXAMINATION_ID_HONOURS = 10;
    const EXAMINATION_ID_PMASTERS = 11;
    const EXAMINATION_ID_MASTERS = 12;

    const GPA_OUT_OF_FIVE = 5;
    const GPA_OUT_OF_FOUR = 4;
    const RESULT_TYPE_DIVISION = 1;
    const RESULT_TYPE_GRADE_POINT = 2;

}
