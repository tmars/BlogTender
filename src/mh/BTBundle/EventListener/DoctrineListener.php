<?php

namespace mh\BTBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use mh\BTBundle\Entity as Entity;

class DoctrineListener
{
    public function getShortClassName($obj)
    {
        $className = get_class($obj);
        return substr($className, strrpos($className, '\\') + 1);
    }

    public function preRemove (LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        $className = $this->getShortClassName($entity);

        // Удаляем связанные события
        if (in_array($className, Entity\UserEvent::getTrackedEntityNames())) {
            $events = $em->getRepository('BTBundle:UserEvent')->findBy(array(
                'objectType' => $className,
                'targetId' => $entity->getId(),
            ));
            foreach ($events as $event) {
                $em->remove($event);
            }
        }

        // Удаляем начисления баллов
        if (in_array($className, Entity\ScoreObject::getTrackedEntityNames())) {
            $scores = $em->getRepository('BTBundle:ScoreObject')->findBy(array(
                'objectType' => $className,
                'targetId' => $entity->getId(),
            ));
            foreach ($scores as $score) {
                $em->remove($score);
            }
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        $className = $this->getShortClassName($entity);

        // Переставляем указатель на оцениваемый объект
        if (in_array($className, Entity\ScoreObject::getTrackedEntityNames())) {
            $entity->getScoreObject()->setTargetId($entity->getId());
            $em->flush();
        }

    }
}