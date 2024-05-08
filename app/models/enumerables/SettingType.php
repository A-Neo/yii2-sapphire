<?php

namespace app\models\enumerables;

class SettingType extends \yii2mod\settings\models\enumerables\SettingType
{

    //const TRANSLATED_STRING_TYPE = 'tstring';
    const STRING_TYPE = 'string';
    const INTEGER_TYPE = 'integer';
    const FLOAT_TYPE = 'float';
    const BOOLEAN_TYPE = 'boolean';

    /**
     * @var string message category
     */
    public static $messageCategory = 'site';

    /**
     * @var array
     */
    public static $list = [
        //self::TRANSLATED_STRING_TYPE => 'Translated string',
        self::STRING_TYPE            => 'String',
        self::INTEGER_TYPE           => 'Integer',
        self::BOOLEAN_TYPE           => 'Boolean',
        self::FLOAT_TYPE             => 'Float',
    ];
}