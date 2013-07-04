<?php

namespace mh\BTBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;

class PostCommentAdmin extends Base\BaseAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('createdDate')
            ->add('content')
            ->add('user')
            ->add('post')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('content')
                ->add('user', null, array(
                    'disabled' => true,
                ), array(
                    'edit' => 'list',
                ))
                ->add('post', null, array(
                    'disabled' => true,
                ), array(
                    'edit' => 'list',
                ))
            ->end()
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('content')
            ->add('createdDate')
            ->add('user')
            ->add('post')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('content')
            ->add('user')
            ->add('post')
        ;
    }

    protected function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
    {

    }
}