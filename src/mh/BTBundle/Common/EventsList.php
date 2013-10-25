<?php

namespace mh\BTBundle\Common;

// История событий

use mh\BTBundle\DBAL\ModerationStatusType;
use mh\BTBundle\Entity as Entity;
use mh\BTBundle\DBAL\EventCounterType;

class EventsList
{
	private $em;

	const ADDED_POST = 'added_post';
	const PUBLISHED_POST = 'published_post';
    const UNPUBLISHED_POST = 'unpublished_post';
    const LIKE_POST = 'like_post';

    const ADDED_QUESTION = 'added_question';
    const LIKE_QUESTION = 'like_question';

    const ADDED_ANSWER = 'added_answer';
    const LIKE_ANSWER = 'like_answer';

	public function __construct($em)
	{
		$this->em = $em;
	}

	public function happened($type, $object)
	{
        $className = get_class($object);
        $className = substr($className, strrpos($className, '\\') + 1);

        $event = new Entity\UserEvent();
        $event->setEventType($type);
        $event->setObjectType($className);
        $event->setTargetId($object->getId());
        $this->em->persist($event);
        $this->em->flush();
    }

    public function  getNext($id, $count)
    {
        $qb = $this->em->createQueryBuilder();
        $query = $qb->select('e')
            ->from('BTBundle:UserEvent', 'e')
            ->orderBy('e.id', 'DESC')
            ->add('where', $qb->expr()->gt('e.id', $id))
            ->setMaxResults($count)
            ->getQuery();
        $records = $query->getArrayResult();

        $this->unpackData($records);

        return $records;
    }

    public function getPrev($id, $count)
    {
        $qb = $this->em->createQueryBuilder();
        $query = $qb->select('e')
            ->from('BTBundle:UserEvent', 'e')
            ->orderBy('e.id', 'DESC')
            ->add('where', $qb->expr()->lt('e.id', $id))
            ->setMaxResults($count)
            ->getQuery();
        $records = $query->getArrayResult();

        $this->unpackData($records);

        return $records;
    }

    public function getLast($count)
    {
        $qb = $this->em->createQueryBuilder();
        $query = $qb->select('e')
            ->from('BTBundle:UserEvent', 'e')
            ->orderBy('e.id', 'DESC')
            ->setMaxResults($count)
            ->getQuery();
        $records = $query->getArrayResult();

        $this->unpackData($records);

        return $records;
    }

    private function unpackData(&$records)
    {
        //@todo сделать запросы эффективнее
        foreach ($records as &$rec) {
            switch($rec['eventType']) {
                case self::ADDED_POST:
                case self::PUBLISHED_POST:
                case self::UNPUBLISHED_POST:
                    $rec['data'] = array(
                        'post' => $this->em->getRepository('BTBundle:Post')->find($rec['targetId'])
                    );
                    break;

                case self::ADDED_QUESTION:
                    $rec['data'] = array(
                        'question' => $this->em->getRepository('BTBundle:Question')->find($rec['targetId'])
                    );
                    break;

                case self::ADDED_ANSWER:
                    $rec['data'] = array(
                        'answer' => $this->em->getRepository('BTBundle:Answer')->find($rec['targetId'])
                    );
                    break;

                case self::LIKE_POST:
                    $like = $this->em->getRepository('BTBundle:ContentObjectLike')->find($rec['targetId']);
                    $post = $this->em->getRepository('BTBundle:Post')->findOneByContentObject($like->getTarget());
                    $rec['data'] = array(
                        'like' => $like,
                        'post' => $post,
                    );
                    break;

                case self::LIKE_QUESTION:
                    $like = $this->em->getRepository('BTBundle:ContentObjectLike')->find($rec['targetId']);
                    $question = $this->em->getRepository('BTBundle:Question')->findOneByContentObject($like->getTarget());
                    $rec['data'] = array(
                        'like' => $like,
                        'question' => $question,
                    );
                    break;

                case self::LIKE_ANSWER:
                    $like = $this->em->getRepository('BTBundle:ContentObjectLike')->find($rec['targetId']);
                    $answer = $this->em->getRepository('BTBundle:Answer')->findOneByContentObject($like->getTarget());
                    $rec['data'] = array(
                        'like' => $like,
                        'answer' => $answer,
                    );
                    break;
            }
        }
    }
}