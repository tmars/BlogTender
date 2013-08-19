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
	
	private $forAnswer = array(
		1 => 3,
		2 => 3,
		3 => 3,
		4 => 3,
		5 => 3,
		6 => 0,
	);
	
	private $forInnerLike = 1;

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
		$num = $this->em->getRepository('BTBundle:EventCounter')->incCounter($object->getUser(), EventCounterType::MAKE_ANSWER);
		$scores = $this->evaluate($this->forAnswer, $num);
		$object->setScores($scores);
	}
	
	public function getTypes($user)
	{
		return array(
			'Посты' => "SELECT SUM(scores) as scores, COUNT(scores) as count FROM  post__post t WHERE user_id=".$user->getId()." AND is_published = 1",
			'Вопросы' => "SELECT SUM(scores) as scores, COUNT(scores) as count FROM question__question t WHERE user_id=".$user->getId()." AND is_published = 1",
			'Ответы' => "SELECT SUM(scores) as scores, COUNT(scores) as count FROM  answer__answer t WHERE user_id=".$user->getId()." AND is_published = 1",

			'Лайки за посты' => "SELECT COUNT(l.id)*{$this->forInnerLike} as scores, COUNT(l.id) as count FROM content_object__like l JOIN post__post t ON (l.content_object_id = t.content_object_id) WHERE t.user_id=".$user->getId()." AND t.is_published = 1",
			'Лайки за вопросы' => "SELECT COUNT(l.id)*{$this->forInnerLike} as scores, COUNT(l.id) as count FROM content_object__like l JOIN question__question t ON (l.content_object_id = t.content_object_id) WHERE t.user_id=".$user->getId()." AND t.is_published = 1",
			'Лайки за ответы' => "SELECT COUNT(l.id)*{$this->forInnerLike} as scores, COUNT(l.id) as count FROM content_object__like l JOIN answer__answer t ON (l.content_object_id = t.content_object_id) WHERE t.user_id=".$user->getId()." AND t.is_published = 1",
			'Внешние ссылки' => "SELECT SUM(l.scores) as scores, COUNT(l.scores) as count  FROM post__foreign_link l JOIN post__post t ON (t.id = l.post_id) WHERE t.user_id=".$user->getId()." AND t.is_published = 1",

			'Жалобы' => "SELECT SUM(scores) as scores, COUNT(scores) as count FROM  content_object__complaint t WHERE t.user_id=".$user->getId(),
		);
	}
}