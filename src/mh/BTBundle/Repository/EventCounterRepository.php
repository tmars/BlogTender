<?php

namespace mh\BTBundle\Repository;

use mh\BTBundle\Repository\Base\BaseRepository;
use mh\BTBundle\Entity\EventCounter;

class EventCounterRepository extends BaseRepository
{
	public function getCounter($user, $type)
	{
		$counter = $this->getForUserByType($user, $type);
		return $counter->getValue();
	}

	public function incCounter($user, $type)
	{
		$counter = $this->getForUserByType($user, $type);
		$counter->setValue($counter->getValue() + 1);
		$this->_em->flush();
		return $counter->getValue();
	}

	public function getForUserByType($user, $type)
	{
		$d = new \DateTime('today');
		$counter = $this->findOneBy(array(
			'user' => $user,
			'type' => $type,
			'activeDay' => $d,
		));

		if ( ! $counter) {
			$counter = new EventCounter();
			$counter->setType($type);
			$counter->setUser($user);
			$counter->setValue(0);

			$this->_em->persist($counter);
			$this->_em->flush();
			$val = 0;
		}

		return $counter;
	}
}