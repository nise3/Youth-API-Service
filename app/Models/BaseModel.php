<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package App\Models
 */
abstract class BaseModel extends Model
{
    public const ROW_STATUS_ACTIVE = '1';
    public const ROW_STATUS_INACTIVE = '0';

    public const TRUE=1;

    public const PHYSICAL_DISABILITIES_STATUS=[
        self::TRUE,
        !self::TRUE
    ];

    public const MALE=1;
    public const FEMALE=2;
    public const OTHERS=3;
    public const GENDER=[
        self::MALE,
        self::FEMALE,
        self::OTHERS
    ];

    /** Language Level */
    public const EASY=1;
    public const NOT_EASY=0;
    public const FLUENTLY=1;
    public const NOT_FLUENTLY=0;

    public const BIRTHDATE_FORMAT="Y-m-d";

    public const MOBILE_REGEX= 'regex: /^(01[3-9]\d{8})$/';

}
