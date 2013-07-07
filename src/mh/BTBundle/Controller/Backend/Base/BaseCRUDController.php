<?php

namespace mh\BTBundle\Controller\Backend\Base;

use Sonata\AdminBundle\Controller\CRUDController;

class BaseCRUDController extends CRUDController
{
    protected function getClassname()
    {
        $parts = explode('\\', $this->admin->getClass());
        return array_pop($parts);
    }

    protected function getRepository($name = '')
    {
        if ($name == '') {
            $name = $this->getClassname();
        }

        return $this->getEM()->getRepository('BTBundle:'.$name);
    }

    protected function getEM()
    {
        return $this->getDoctrine()->getEntityManager();
    }

    /*public function render($view, array $parameters = array(), \Symfony\Component\HttpFoundation\Response $response = null)
    {
        echo 'sd';
        //$parameters['env'] = $this;
        return parent::render($view, $parameters, $response);
    }*/

}