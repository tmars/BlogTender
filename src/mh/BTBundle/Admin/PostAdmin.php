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

class PostAdmin extends Base\BaseContentObjectAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('comments', '../postcomment/list?filter[post][value]='.$this->getRouterIdParameter());
        $collection->add('links', '../postforeignlink/list?filter[post][value]='.$this->getRouterIdParameter());
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        parent::configureShowFields($showMapper);
        $showMapper
            ->add('title')
            ->add('content', 'html')
            ->add('tags')
            ->add('showOnMain')
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
                ->add('showOnMain')
            ->end()
            ->with('Tags')
                ->add('tags')
            ->end()
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('title');
        parent::configureListFields($listMapper);
        $listMapper->add('showOnMain');
        $listMapper->add('tags');
        $listMapper->add('categories');
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title');
        parent::configureDatagridFilters($datagridMapper);
        $datagridMapper->add('tags', null, array('filter_field_options' => array('expanded' => true, 'multiple' => true)));
    }

    protected function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
    {
        if ($this->subject != null) {

            $id = $this->subject->getId();
            $status = $this->subject->getModerationStatus();

            if ($action == 'edit' || $action == 'show') {
                $menu->addChild(
                    'Комментарии ('.count($this->subject->getComments()).')',
                    array('uri' => $this->generateUrl('comments', array('id' => $id)))
                );
                $menu->addChild(
                    'Внешние ссылки ('.count($this->subject->getForeignLinks()).')',
                    array('uri' => $this->generateUrl('links', array('id' => $id)))
                );
            }
        }
        parent::configureSideMenu($menu, $action, $childAdmin);
    }
}