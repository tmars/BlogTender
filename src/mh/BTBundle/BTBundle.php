<?php

namespace mh\BTBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\DBAL\Types\Type;

class BTBundle extends Bundle
{
    public function boot()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $conn = $em->getConnection();
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Type::addType('ModerationStatus', 'mh\BTBundle\DBAL\ModerationStatusType');
        Type::addType('EventCounter', 'mh\BTBundle\DBAL\EventCounterType');
        Type::addType('Sex', 'mh\BTBundle\DBAL\SexType');
    }
}
