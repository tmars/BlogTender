<?php

namespace mh\BTBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;

class TrustedSiteAdmin extends Base\BaseAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('url')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('url')
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('url')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('url')
        ;
    }

    protected function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
    {

    }
}