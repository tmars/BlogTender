<?php

namespace mh\BTBundle\DBAL;

class EventCounterType extends EnumType
{
    const SHARE_POST = 'share_post';

    protected $name = 'EventType';
    protected $values = array('share_post');

    public static function getNameByValue($value)
    {
        $names = array(
            'share_post' => 'Размещение поста',
        );

        return $names[$value];
    }

    public static function getStatusList()
    {
        return array(
            'share_post' => 'Размещение поста',
        );
    }

}