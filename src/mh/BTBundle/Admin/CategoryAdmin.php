<?php

namespace mh\BTBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CategoryAdmin extends Base\BaseAdmin
{
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('label')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('label')
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('label')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('label')
        ;
    }
}