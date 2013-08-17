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

	private $postBonusLength = 2500;
	private $postBonusLengthFactor = 1.5;

	private $postBonusImageCount = 3;
	private $postBonusImageCountScores = 3;

	private $forPostForeignLink = array(
		1 => 5,
		2 => 5,
		3 => 5,
		4 => 5,
		5 => 5,
		5 => 0,
	);

	private $forQuestion = array(
		1 => 3,
		2 => 3,
		3 => 3,
		4 => 3,
		5 => 3,
		6 => 0,
	);

	private function evaluate(array $scores, $number)
	{
		if ($number > count($scores)) $number = count($scores);
		return $scores[$number];
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
			$object->setBaseRate($scores);

		} else {
			$scores = $object->getBaseRate();
		}

		if ($object->getClearContentLength() >= $this->postBonusLength) {
			$scores *= $this->postBonusLengthFactor;
		}

		if ($object->getAttachedImageCount() >= $this->postBonusImageCount) {
			$scores += $this->postBonusImageCountScores;
		}

		$scores = round($scores);
		$object->setScores($scores);

		return $scores;
	}

	public function forPostForeignLink(Entity\PostForeignLink $object)
	{
		$num = $this->em->getRepository('BTBundle:EventCounter')->incCounter($object->getPost()->getUser(), EventCounterType::SHARE_LINK_TO_POST);
		$scores = $this->evaluate($this->forPost, $num);
		$object->setScores($scores);
	}

	public function forQuestion(Entity\Question $object)
	{
		$num = $this->em->getRepository('BTBundle:EventCounter')->incCounter($object->getUser(), EventCounterType::MAKE_QUESTION);
		$scores = $this->evaluate($this->forQuestion, $num);
		$object->setScores($scores);
	}

	public function forAnswer(Entity\Answer $object)
	{
	}
}