<?php

namespace mh\BTBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ThemeForPostAdmin extends Base\BaseAdmin
{
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
        ;
    }
}