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

class AnswerAdmin extends Base\BaseContentObjectAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('question', '/../../question/{question_id}/show');
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('question.title')
            ->add('content', 'html')
        ;
        parent::configureShowFields($showMapper);
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);
        $formMapper
            ->with('General')
                ->add('content')
            ->end()
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('preview');
        $listMapper->add('question.title');
        parent::configureListFields($listMapper);
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('question');
        $datagridMapper->add('question.title');
        parent::configureDatagridFilters($datagridMapper);
    }

    protected function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
    {
        if ($this->subject != null) {

            $question_id = $this->subject->getQuestion()->getId();

            if ($action == 'edit' || $action == 'show') {
                $menu->addChild(
                    'Просмотреть вопрос',
                    array('uri' => $this->generateUrl('question', array('question_id' => $question_id)))
                );
            }
        }
        parent::configureSideMenu($menu, $action, $childAdmin);
    }
}