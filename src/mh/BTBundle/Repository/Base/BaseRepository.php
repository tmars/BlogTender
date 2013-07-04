<?php

namespace mh\BTBundle\Repository\Base;

use Doctrine\ORM\EntityRepository;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;

class BaseRepository extends EntityRepository
{
    protected function addConds(&$qb, &$conds)
    {
        foreach ($conds as $key => $value) {
            $qb->andWhere("p.$key = :$key")->setParameter($key, $value);
        }
    }

    public function getPaginated($query, $count, $page)
	{
		$pagerfanta = new Pagerfanta(new DoctrineORMAdapter($query));
		$pagerfanta->setMaxPerPage($count);

		try {
			$pagerfanta->setCurrentPage($page);
		} catch(Exception $e) {
			throw new NotFoundHttpException();
		}

		return $pagerfanta;
	}
}