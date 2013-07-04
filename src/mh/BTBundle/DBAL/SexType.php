<?php

namespace mh\BTBundle\DBAL;

class SexType extends EnumType
{
    const MAN = 'man';
    const WOMAN = 'woman';

    protected $name = 'Sex';
    protected $values = array('man', 'woman');

    public static function getNameByValue($value)
    {
        $names = array(
            'man' => '�������',
            'woman' => '�������',
        );

        return $names[$value];
    }

    public static function getStatusList()
    {
        return array(
            'man' => '�������',
            'woman' => '�������',
        );
    }

}