<?php

namespace mh\BTBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ContentObjectComplaintAdmin extends Base\BaseAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('createdDate')
            ->add('user')
            ->add('moderationStatus', 'moderation_status')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('createdDate')
            ->add('user')
            ->add('moderationStatus', 'moderation_status')
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('target')
            ->add('user')
            ->add('createdDate')
            ->add('moderationStatus', 'moderation_status')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('target')
            ->add('user')
            ->add('moderationStatus')
        ;
    }
}