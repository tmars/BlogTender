<?php

namespace mh\BTBundle\Common;

// Назначает количество баллов

use mh\BTBundle\DBAL\ModerationStatusType;
use mh\BTBundle\Entity as Entity;
use mh\BTBundle\DBAL\EventCounterType;

class ScoresAllocator
{
	private $em;

	private $forPost = array(
		1 => 10,
		2 => 15,
		3 => 20,
		4 => 25,
		5 => 50,
		6 => 20
	);

	private $postBonusLength = 25;
	private $postBonusLengthFactor = 1.5;

	private function evaluate(array $scores, $number)
	{
		if ($count > count($scores)) $count = count($scores);
		return $scores[$count];
	}

	public function __construct($em)
	{
		$this->em = $em;
	}

	public function forPost(Entity\Post $object)
	{
		if ( ! $object->getId()) {
			$num = $this->em->getRepository('BTBundle:EventCounter')->incCounter($object->getUser(), EventCounterType::SHARE_POST);
			$scores = $this->evaluate($this->forPost, $num);

			//'Количество знаков: '.$post->getClearContentLength(), array('info' => 1)));
			//'Количество изображений: '.$post->getImageCount(), array('info' => 1)));
		} else {
			$scores = $object->getBaseRate();
		}

		if ($post->getClearContentLength() >= $this->postBonusLength) {
			$scores *= $this->postBonusLengthFactor;
		}

		$object->setScores($scores);
		return $scores;
	}

	public function forPostForeignLink(Entity\PostForeignList $object)
	{
	}

	public function forQuestion(Entity\Question $object)
	{
	}

	public function forAnswer(Entity\Answer $object)
	{
	}
}