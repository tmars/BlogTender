<?php

namespace mh\BTBundle\Repository;

use mh\BTBundle\Repository\Base\BaseRepository;
use mh\BTBundle\Entity\EventCounter;

class ScoreObjectRepository extends BaseRepository
{
    private function getByUserOnDate($user, $date = NULL)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s')
            ->from('BTBundle:ScoreObject', 's')
            ->where('s.user = ?1')
            ->setParameter(1, $user)
            ->orderBy('s.id', 'DESC');

        if ($date !== NULL) {
            $next = new \DateTime($date->format('Y-m-d'));
            $next->modify('+1 day');

            $qb
                ->andWhere('s.createdDate >= ?2')
                ->andWhere('s.createdDate < ?3')
                ->setParameter(2, $date->format('Y-m-d'))
                ->setParameter(3, $next->format('Y-m-d'));
        }
        $query = $qb->getQuery();
        return $query->getResult();
    }

    // result array(
    //      $scoreObjectId => array(
    //            'score' => $scoreObject,
    //            'object' => $object
    //      ),
    // ...);
    public function getScoreEvents($user, $date = NULL)
    {
        $em = $this->getEntityManager();

        $events = array();

        $objectQueries = array();
        foreach ($this->getByUserOnDate($user, $date) as $so) {
            $objectQueries[$so->getObjectType()][] = $so->getId();
            $events[$so->getId()]['score'] = $so;
        }

        foreach ($objectQueries as $entityName => $scoreObjectIds) {
            $qb = $em->createQueryBuilder();
            $query = $qb->select('o')
                ->from('BTBundle:'.$entityName, 'o')
                ->where('o.scoreObject in (?1)')
                ->setParameters(array(1 => $scoreObjectIds))
                ->getQuery();

           foreach ($query->getResult() as $o) {
                $events[$o->getScoreObject()->getId()]['object'] = $o;
           }
        }

        return $events;
    }

    public function getScoresByGroups($user, $date = NULL)
    {
        $groups = array();
        foreach ($this->getByUserOnDate($user, $date) as $so) {
            @$groups[$so->getObjectType()]['scores'] += $so->getScores();
            @$groups[$so->getObjectType()]['count'] ++;
        }
        return $groups;
    }
}