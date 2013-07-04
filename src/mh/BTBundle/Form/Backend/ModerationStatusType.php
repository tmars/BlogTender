<?php

namespace mh\BTBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ModerationStatusType extends AbstractType
{
    public function getName()
    {
        return 'moderation_status';
    }
}
