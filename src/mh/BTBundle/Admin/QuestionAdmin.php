<?php

namespace mh\BTBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;

use mh\BTBundle\DBAL\ModerationStatusType;

class QuestionAdmin extends Base\BaseContentObjectAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('answers', '/../../answer/list?filter[question][value]='.$this->getRouterIdParameter());
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        parent::configureShowFields($showMapper);
        $showMapper
            ->add('title')
            ->add('content', 'html')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);
        $formMapper
            ->with('General')
                ->add('title', null, array(
                    'disabled' => true,
                ))
                ->add('content')
            ->end()
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('title');
        parent::configureListFields($listMapper);
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title');
        parent::configureDatagridFilters($datagridMapper);
    }

    protected function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
    {
        if ($this->subject != null) {

            $id = $this->subject->getId();

            if ($action == 'edit' || $action == 'show') {
                $menu->addChild(
                    'Просмотреть ответы',
                    array('uri' => $this->generateUrl('answers', array('id' => $id)))
                );
            }
        }
        parent::configureSideMenu($menu, $action, $childAdmin);
    }
}