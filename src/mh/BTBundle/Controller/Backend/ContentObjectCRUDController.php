<?php

namespace mh\BTBundle\Controller\Backend;

use mh\BTBundle\DBAL\ModerationStatusType;

class ContentObjectCRUDController extends Base\BaseCRUDController
{
    // подтверждение корректности материала
    public function approveAction($id)
    {
        $class = $this->getClassname();
        $object = $this->getRepository($this->getClassname())->find($id);

        if ($object) {
            $em = $this->getEM();

            $object->setModerationStatus(ModerationStatusType::VALID);
            //if ($class == "Post" && $object->getDelayedPublication() == null) {
                $object->setIsPublished(true);
            //}

            $contentObject = $object->getContentObject();
            // отвергаем все жалобы
            $complaints = $this->getRepository('ContentObjectComplaint')->findBy(array(
                'moderationStatus' => ModerationStatusType::NOT_MODERATED,
                'target' => $contentObject->getId(),
            ));

            $allocator = $this->get('scores_allocator');
            foreach ($complaints as $complaint) {
                $complaint->setModerationStatus(ModerationStatusType::NOT_VALID);
    			$allocator->allocateScoresForComplaint($complaint);
            }

            $contentObject->setComplaintsCount(0);



            $em->flush();
        }

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    // материал некорректен по какой то причине
    public function disapproveAction($id)
    {
        $class = $this->getClassname();
        $object = $this->getRepository($this->getClassname())->find($id);

        if ($object) {
            $em = $this->getEM();

            $object->setModerationStatus(ModerationStatusType::NOT_VALID);
            $object->setIsPublished(false);

            // подтверждаем все жалобы
            $complaints = $this->getRepository( 'ContentObjectComplaint')->findBy(array(
                'moderationStatus' => ModerationStatusType::NOT_MODERATED,
                'target' => $object->getContentObject()->getId(),
            ));

            $allocator = $this->get('scores_allocator');
            foreach ($complaints as $complaint) {
                $complaint->setModerationStatus(ModerationStatusType::VALID);
    			$allocator->allocateScoresForComplaint($complaint);
            }

            $object->getContentObject()->setComplaintsCount(0);

            $em->flush();
        }

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
}