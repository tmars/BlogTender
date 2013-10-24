<?php

namespace mh\BTBundle\Repository;

use mh\BTBundle\Repository\Base\BaseRepository;
use mh\BTBundle\Entity\EventCounter;

class ScoreObjectRepository extends BaseRepository
{
    private function getByUserOnDate($user, $date)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb->select('s')
            ->from('BTBundle:ScoreObject', 's')
            ->where('s.user = ?1')
            //->andWhere('s.createdDate = ?2')
            ->setParameters(array(1 => $user, /*2 => $date*/))
            ->getQuery();

        return $query->getResult();
    }

    // result array(
    //      $scoreObjectId => array(
    //            'score' => $scoreObject,
    //            'object' => $object
    //      ),
    // ...);
    public function getScoreEvents($user, $date)
    {
        $em = $this->getEntityManager();

        $events = array();

        $objectQueries = array();
        foreach ($this->getByUserOnDate($user, $date) as $so) {
            $objectQueries[$so->getObjectType()][] = $so->getId();
            $events[$so->getId()]['score'] = $so;
        }

        $qb = $em->createQueryBuilder();
        foreach ($objectQueries as $entityName => $scoreObjectIds) {
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

    public function getScoresByGroups($user, $date)
    {
        $groups = array();
        foreach ($this->getByUserOnDate($user, $date) as $so) {
            @$groups[$so->getObjectType()]['scores'] += $so->getScores();
            @$groups[$so->getObjectType()]['count'] ++;
        }
        return $groups;
    }
}