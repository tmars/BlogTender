<?php

namespace mh\BTBundle\Admin\Base;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

use Knp\Menu\ItemInterface as MenuItemInterface;
class BaseContentObjectAdmin extends BaseAdmin
{

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('approve', $this->getRouterIdParameter().'/approve');
        $collection->add('disapprove', $this->getRouterIdParameter().'/disapprove');
        $collection->add('complaints', '../contentobjectcomplaint/list?filter[target][value]='.$this->getRouterIdParameter());
        /*if ("Post" == $this->classnameLabel) {
            $collection->add('history', '../'.$this->getBaseRoutePattern().'moderationhistory/list?filter[target][value]='.$this->getRouterIdParameter());
        }*/
        $collection->remove('create');
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('user')
            ->add('moderationStatus', 'moderation_status')
            ->add('isPublished')
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('user')
            ->add('moderationStatus', 'moderation_status')
            ->add('isPublished')
            ->add('contentObject.complaintsCount', 'complaints')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            //->add('contentObject.moderationStatus', null, array('filter_field_options' => array('expanded' => true, 'multiple' => true, 'choices' => array(1,2,3))))
            ->add('isPublished')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('moderationStatus', 'moderation_status')
                ->add('isPublished', null, array('disabled' => true))
                ->add('user', null, array(
                    'disabled' => true,
                ), array(
                    'edit' => 'list',
                ))
            ->end()
        ;
    }

    protected function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
    {
        if ($this->subject != null) {

            $id = $this->subject->getId();
            $status = $this->subject->getModerationStatus();

            if ($action == 'edit' || $action == 'show') {
                $menu->addChild(
                    'Жалобы ('.$this->subject->getContentObject()->getComplaintsCount().')',
                    array('uri' => $this->generateUrl('complaints', array('id' => $id)))
                );

                /*if ("Post" == $this->classnameLabel) {
                    $menu->addChild(
                        'История модерации ('.count($this->subject->getModerationHistories()).')',
                        array('uri' => $this->generateUrl('history', array('id' => $id)))
                    );
                }*/

                $menu->addChild(
                    'Утвердить',
                    array('uri' => $this->generateUrl('approve', array('id' => $id)))
                );

                $menu->addChild(
                    'Отказать',
                    array('uri' => $this->generateUrl('disapprove', array('id' => $id)))
                );
            }
        }
    }

    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            array('BTBundle:Backend/Default:edit_moderation_status.html.twig')
        );
    }
}