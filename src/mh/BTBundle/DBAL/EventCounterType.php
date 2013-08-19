<?php

namespace mh\BTBundle\DBAL;

class EventCounterType extends EnumType
{
    const SHARE_POST = 'share_post';
    const SHARE_LINK_TO_POST = 'share_link_to_post';
    const MAKE_QUESTION = 'make_question';
    const MAKE_ANSWER= 'make_answer';
    
    protected $name = 'EventCounter';
    protected $values = array('share_post', 'share_link_to_post', 'make_question', 'make_answer');

    public static function getNameByValue($value)
    {
        $names = array(
            'share_post' => 'Размещение поста',
            'share_link_to_post' => 'Размещение ссылки на пост',
            'make_question' => 'Создание вопроса',
            'make_answer' => 'Создание ответа',
        );

        return $names[$value];
    }

    public static function getStatusList()
    {
        return array(
            'share_post' => 'Размещение поста',
            'share_link_to_post' => 'Размещение ссылки на пост',
            'make_question' => 'Создание вопроса',
            'make_answer' => 'Создание ответа',
        );
    }

}