<?php

namespace mh\BTBundle\DBAL;

class ModerationStatusType extends EnumType
{
    const VALID = 'valid';
    const NOT_VALID = 'not_valid';
    const NOT_MODERATED= 'not_moderated';

    protected $name = 'ModerationStatus';
    protected $values = array('not_moderated', 'valid', 'not_valid');

    public static function getNameByValue($value)
    {
        $names = array(
            'not_moderated' => 'Не модерирован',
            'valid' => 'Прошел модерацию',
            'not_valid' => 'Не прошел модерацию'
        );

        return $names[$value];
    }

    public static function getStatusList()
    {
        return array(
            'not_moderated' => 'Не модерирован',
            'valid' => 'Прошел модерацию',
            'not_valid' => 'Не прошел модерацию'
        );
    }

}