<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package App\Models
 */
abstract class BaseModel extends Model
{
    public const COMMON_GUARDED_FIELDS_SIMPLE = ['id', 'created_at', 'updated_at'];
    public const COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public const COMMON_GUARDED_FIELDS_SOFT_DELETE = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];
    public const COMMON_GUARDED_FIELDS_NON_SOFT_DELETE = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];


    public const ROW_STATUS_ACTIVE = '1';
    public const ROW_STATUS_INACTIVE = '0';

    public const ROW_ORDER_ASC = 'ASC';
    public const ROW_ORDER_DESC = 'DESC';

    public const TRUE = 1;
    public const FALSE = 1;

    public const PHYSICAL_DISABILITIES_STATUS = [
        self::TRUE,
        self::FALSE
    ];

    public const MALE = 1;
    public const FEMALE = 2;
    public const OTHERS = 3;
    public const GENDER = [
        self::MALE,
        self::FEMALE,
        self::OTHERS
    ];

    /** Language Level */
    public const EASY = 1;
    public const NOT_EASY = 0;
    public const FLUENTLY = 1;
    public const NOT_FLUENTLY = 0;

    public const BIRTHDATE_FORMAT = "Y-m-d";

    public const MOBILE_REGEX = 'regex: /^(01[3-9]\d{8})$/';

}
