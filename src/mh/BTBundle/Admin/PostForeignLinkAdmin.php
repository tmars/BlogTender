<?php

namespace mh\BTBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;

class PostForeignLinkAdmin extends Base\BaseAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('createdDate')
            ->add('post')
            ->add('url')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('createdDate', null, array('disabled' => true,))
                ->add('url', null, array('disabled' => true,))
                ->add('user', null, array(
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
            ->add('createdDate')
            ->add('post')
            ->add('url')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('post')
            ->add('url')
        ;
    }

    protected function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
    {

    }
}