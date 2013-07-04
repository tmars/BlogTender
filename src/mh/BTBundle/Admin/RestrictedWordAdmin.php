<?php

namespace mh\BTBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class RestrictedWordAdmin extends Base\BaseAdmin
{
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('word')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('word')
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('word')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('word')
        ;
    }
}