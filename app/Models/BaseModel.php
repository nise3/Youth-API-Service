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

    /** Idp User */
    public const IDP_USERNAME = 'admin';
    public const IDP_USER_PASSWORD = 'admin';
    public const IDP_USER_CREATE_ENDPOINT ='https://identity.bus.softbd.xyz/scim2/Users';
    public const IDP_USER_CREATE_ENDPOINT_ENV = 'https://is.local:3443/scim2/Users';

    public const ROW_STATUS_ACTIVE = '1';
    public const ROW_STATUS_INACTIVE = '0';

    public const TRUE = 1;
    public const FALSE = 0;

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

    /** Password Validation Rules */

    /** Password Regex
     * Regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/
     * Means:
     * 1) Should have At least one Uppercase letter.
     * 2) At least one Lower case letter.
     * 3) Also,At least one numeric value.
     * 4) And, At least one special character.
     * 5) Must be more than 8 characters long.
     */

    public const PASSWORD_REGEX = "regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/";
    public const PASSWORD_TYPE = "string";
    public const PASSWORD_MIN_LENGTH = "min:8";
    public const PASSWORD_MAX_LENGTH = "max:50";

    public const PASSWORD_COMMON_RULES=[
        self::PASSWORD_TYPE,
        self::PASSWORD_MIN_LENGTH,
        self::PASSWORD_MAX_LENGTH,
        self::PASSWORD_REGEX
    ];

    public const USER_TYPE_EMAIL=1;
    public const USER_TYPE_MOBILE_NUMBER=2;
    public const USER_TYPE=[
        self::USER_TYPE_EMAIL,
        self::USER_TYPE_MOBILE_NUMBER
    ];

}
