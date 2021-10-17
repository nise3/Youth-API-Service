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

    /** Idp User Information */
    public const IDP_USERNAME = 'admin';
    public const IDP_USER_PASSWORD = 'admin';

    /** Client Url End Point Type*/
    public const ORGANIZATION_CLIENT_URL_TYPE = "ORGANIZATION";
    public const INSTITUTE_URL_CLIENT_TYPE = "INSTITUTE";
    public const CORE_CLIENT_URL_TYPE = "CORE";
    public const IDP_SERVER_CLIENT_URL_TYPE = "IDP_SERVER";

    /** Youth Row Statues  */
    public const ROW_STATUS_INACTIVE = 0;
    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_PENDING = 2;
    public const ROW_STATUS_REJECT = 3;

    /** Row Status */
    public const ROW_ORDER_ASC = 'ASC';
    public const ROW_ORDER_DESC = 'DESC';

    /** Youth User Type */
    public const YOUTH_USER_TYPE = 4;

    /** True False Flag */
    public const TRUE = 1;
    public const FALSE = 0;

    /** Freelance Status */
    public const FREELANCE_PROFILE_YES = self::TRUE;
    public const FREELANCE_PROFILE_NO = self::FALSE;
    public const FREELANCE_PROFILE_STATUS = [
        self::FREELANCE_PROFILE_YES,
        self::FREELANCE_PROFILE_NO
    ];

    /** Physical Disability Statues  */
    public const PHYSICAL_DISABILITIES_STATUS = [
        self::TRUE,
        self::FALSE
    ];

    /** Gender Statuses */
    public const MALE = 1;
    public const FEMALE = 2;
    public const OTHERS = 3;
    public const GENDERS = [
        self::MALE,
        self::FEMALE,
        self::OTHERS
    ];

    /** Marital Statuses */
    public const MARITAL_STATUS_SINGLE = 1;
    public const MARITAL_STATUS_MARRIED = 2;
    public const MARITAL_STATUS_WIDOWED = 3;
    public const MARITAL_STATUS_DIVORCED = 4;
    public const MARITAL_STATUSES = [
        self::MARITAL_STATUS_SINGLE,
        self::MARITAL_STATUS_MARRIED,
        self::MARITAL_STATUS_WIDOWED,
        self::MARITAL_STATUS_DIVORCED
    ];

    /** Religions Mapping  */
    public const RELIGION_ISLAM = 1;
    public const RELIGION_HINDUISM = 2;
    public const RELIGION_CHRISTIANITY = 3;
    public const RELIGION_BUDDHISM = 4;
    public const RELIGION_JUDAISM = 5;
    public const RELIGION_SIKHISM = 6;
    public const RELIGION_ETHNIC = 7;
    public const RELIGION_AGNOSTIC_ATHEIST = 8;
    public const RELIGIONS = [
        self::RELIGION_ISLAM,
        self::RELIGION_HINDUISM,
        self::RELIGION_CHRISTIANITY,
        self::RELIGION_BUDDHISM,
        self::RELIGION_JUDAISM,
        self::RELIGION_SIKHISM,
        self::RELIGION_ETHNIC,
        self::RELIGION_AGNOSTIC_ATHEIST
    ];

    /** Freedom fighter statuses */
    public const NON_FREEDOM_FIGHTER = 0;
    public const FREEDOM_FIGHTER = 1;
    public const CHILD_OF_FREEDOM_FIGHTER = 2;
    public const GRAND_CHILD_OF_FREEDOM_FIGHTER = 3;
    public const FREEDOM_FIGHTER_STATUSES = [
        self::NON_FREEDOM_FIGHTER,
        self::FREEDOM_FIGHTER,
        self::CHILD_OF_FREEDOM_FIGHTER,
        self::GRAND_CHILD_OF_FREEDOM_FIGHTER
    ];

    /** YouthLanguagesProficiency Level */
    public const EASY = 1;
    public const NOT_EASY = 0;
    public const FLUENTLY = 1;
    public const NOT_FLUENTLY = 0;

    /**  Identity Number Type  */
    public const NID = 1;
    public const BIRTH_CARD = 2;
    public const PASSPORT = 3;
    public const IDENTITY_TYPES = [
        self::NID,
        self::BIRTH_CARD,
        self::PASSPORT
    ];

    /** BIRTHDATE FORMAT  */
    public const BIRTHDATE_FORMAT = "Y-m-d";

    /** MOBILE REGEX  */
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

    /** UserName Type */
    public const USER_NAME_TYPE_EMAIL = 1;
    public const USER_NAME_TYPE_MOBILE_NUMBER = 2;
    public const USER_NAME_TYPE = [
        self::USER_NAME_TYPE_EMAIL,
        self::USER_NAME_TYPE_MOBILE_NUMBER
    ];

    /** Working Status  */
    public const CURRENTLY_WORKING = 1;
    public const CURRENTLY_NOT_WORKING = 0;
    public const DEFAULT_PAGE_SIZE = 10;

    /** Education Level Status Code */
    public const PSC_5_PASS = "PSC_5_PASS";
    public const JSC_JDC_8_PASS = "JSC_JDC_8_PASS";
    public const SECONDARY = "SECONDARY";
    public const HIGHER_SECONDARY = "HIGHER_SECONDARY";
    public const DIPLOMA = "DIPLOMA";
    public const BACHELOR = "BACHELOR";
    public const MASTERS = "MASTERS";
    public const PHD = "PHD";

    /** Result Level Status Code */
    public const FIRST_DIVISION = "FIRST_DIVISION";
    public const SECOND_DIVISION = "SECOND_DIVISION";
    public const THIRD_DIVISION = "THIRD_DIVISION";
    public const GRADE = "GRADE";
    public const APPEARED = "APPEARED";
    public const AWARDED = "AWARDED";
    public const ENROLLED = "ENROLLED";
    public const DO_NOT_MENTION = "DO_NOT_MENTION";
    public const PASS = "PASS";

    public const ADDRESS_TYPE_PRESENT = 1;
    public const ADDRESS_TYPE_PERMANENT = 2;
    public const ADDRESS_TYPE_OTHERS = 3;

    public const  RELATIONSHIP_TYPE_FATHER = 1;
    public const  RELATIONSHIP_TYPE_MOTHER = 2;
    public const  RELATIONSHIP_TYPE_UNCLE = 3;
    public const  RELATIONSHIP_TYPE_AUNT = 4;
    public const  RELATIONSHIP_TYPE_OTHER = 5;

}
