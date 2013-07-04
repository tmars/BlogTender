<?php

namespace mh\BTBundle\Admin\Base;

use Sonata\AdminBundle\Admin\Admin;

class BaseAdmin extends Admin
{
    protected function getEM($class)
    {
        return $this->modelManager->getEntityManager("BTBundle:$class");
    }

    protected function getRepository($class)
    {
        return $this->getEM($class)->getRepository('BTBundle:'.$class);
    }

    public function configure()
    {
        $this->baseRoutePattern = strtolower($this->classnameLabel);
    }
}