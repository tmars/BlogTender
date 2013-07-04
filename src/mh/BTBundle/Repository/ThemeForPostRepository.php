<?php

namespace mh\BTBundle\Repository;

use mh\BTBundle\Repository\Base\BaseRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class ThemeForPostRepository extends BaseRepository
{
    public function getRandom($count = 1)
    {
        // mapper для результата
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('mh\BTBundle\Entity\ThemeForPost', 'p');
        $rsm->addFieldResult('p', 'id', 'id');
        $rsm->addFieldResult('p', 'title', 'title');

        $query = $this->_em
            ->createNativeQuery('SELECT
                p.id, p.title
                FROM theme_for_post p
                ORDER BY RAND() LIMIT 0,:count', $rsm);
        $query->setParameter(':count', $count);

        return $query->getResult();
    }
}