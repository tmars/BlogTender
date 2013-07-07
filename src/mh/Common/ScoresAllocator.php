<?php

namespace mh\Common;

// назначает количество баллов за посты

use mh\BTBundle\DBAL\ModerationStatusType;

class ScoresAllocator
{
	private $scoresForPost = array(
		1 => 10,
		2 => 15,
		3 => 20,
		4 => 25,
		5 => 50,
		6 => 20
	);

	private $ratesForPicture = array(
		1 => 1.2,
		2 => 1.2,
		3 => 1.5,
	);

	private $scoresForQuestion = array(
		1 => 3,
		2 => 3,
		3 => 3,
		4 => 3,
		5 => 3,
		6 => 3,
		7 => 3,
		8 => 3,
		9 => 3,
		10 => 3,
		11 => 0,
	);

	private $scoresForAnswer = array(
		1 => 10,
		2 => 10,
		3 => 10,
		4 => 10,
		5 => 10,
		6 => 10,
		7 => 10,
		8 => 10,
		9 => 10,
		10 => 10,
		11 => 0,
	);

	private $scoresForPostForeignLink = array(
		1 => 5,
		2 => 5,
		3 => 5,
		4 => 5,
		5 => 5,
		6 => 5,
		7 => 5,
		8 => 5,
		9 => 5,
		10 => 5,
		11 => 0,
	);

	private $scoresForPostForeignLinkOnTrustedSite = array(
		1 => 10,
		2 => 10,
		3 => 10,
		4 => 10,
		5 => 10,
		6 => 10,
		7 => 10,
		8 => 10,
		9 => 10,
		10 => 10,
		11 => 10,
		12 => 10,
		13 => 10,
		14 => 10,
		15 => 10,
		16 => 10,
		17 => 10,
		18 => 10,
		19 => 10,
		20 => 10,
		21 => 0,
	);

	public $scoresForInnerLike = 2;
	public $scoresForBestAnswer = 10;
	private $scoresForCompaintValid = 20;
	private $scoresForCompaintNotValid = -20;

	private $user;

	private function evaluate(array $scores, $count)
	{
		if ($count > count($scores)) $count = count($scores);
		return $scores[$count];
	}

	private function getUserCountAtDayOf($mode)
	{
		$this->user->resetCounters();

		$params = array();
		$count = call_user_func_array(array($this->user, "getCount{$mode}AtDay"), $params);
		$count++;
		$params = array($count);
		call_user_func_array(array($this->user, "setCount{$mode}AtDay"), $params);

		return $count;
	}

	private function evaluateFor($mode)
	{
		$arname = "scoresFor{$mode}";
		return $this->evaluate($this->$arname, $this->getUserCountAtDayOf($mode));
	}

	public function getTypes($user)
	{
		return array(
			'Посты' => "SELECT SUM(scores) as scores, COUNT(scores) as count FROM  post__post t WHERE user_id=".$user->getId()." AND is_published = 1",
			'Вопросы' => "SELECT SUM(scores) as scores, COUNT(scores) as count FROM question__question t WHERE user_id=".$user->getId()." AND is_published = 1",
			'Ответы' => "SELECT SUM(scores) as scores, COUNT(scores) as count FROM  answer__answer t WHERE user_id=".$user->getId()." AND is_published = 1",

			'Лайки за посты' => "SELECT COUNT(l.id)*{$this->scoresForInnerLike} as scores, COUNT(l.id) as count FROM content_object__like l JOIN post__post t ON (l.content_object_id = t.content_object_id) WHERE t.user_id=".$user->getId()." AND t.is_published = 1",
			'Лайки за вопросы' => "SELECT COUNT(l.id)*{$this->scoresForInnerLike} as scores, COUNT(l.id) as count FROM content_object__like l JOIN question__question t ON (l.content_object_id = t.content_object_id) WHERE t.user_id=".$user->getId()." AND t.is_published = 1",
			'Лайки за ответы' => "SELECT COUNT(l.id)*{$this->scoresForInnerLike} as scores, COUNT(l.id) as count FROM content_object__like l JOIN answer__answer t ON (l.content_object_id = t.content_object_id) WHERE t.user_id=".$user->getId()." AND t.is_published = 1",
			'Внешние ссылки' => "SELECT SUM(l.scores) as scores, COUNT(l.scores) as count  FROM post__foreign_link l JOIN post__post t ON (t.id = l.post_id) WHERE t.user_id=".$user->getId()." AND t.is_published = 1",

			'Жалобы' => "SELECT SUM(scores) as scores, COUNT(scores) as count FROM  content_object__complaint t WHERE t.user_id=".$user->getId(),
		);
	}


	public function allocateScoresForPost($object)
	{
		$this->user = $object->getUser();
		$scores = $this->evaluateFor("Post");
		$object->setScores($scores);
	}

	public function allocateScoresForAnswer($object)
	{
		$this->user = $object->getUser();
		$scores = $this->evaluateFor("Answer");
		$object->setScores($scores);
	}

	public function allocateScoresForQuestion($object)
	{
		$this->user = $object->getUser();
		$scores = $this->evaluateFor("Question");
		$object->setScores($scores);
	}

	public function allocateScoresForPostForeignLink($object)
	{
		$this->user = $object->getPost()->getUser();
		$scores = $this->evaluateFor("PostForeignLink");
		$object->setScores($scores);
	}

	public function allocateScoresForPostForeignLinkOnTrustedSite($object)
	{
		$this->user = $object->getPost()->getUser();
		$scores = $this->evaluateFor("PostForeignLinkOnTrustedSite");
		$object->setScores($scores);
	}

	public function allocateScoresForComplaint($object)
	{
		$this->user = $object->getUser();
		if ($object->getModerationStatus() == ModerationStatusType::VALID) {
			$object->setScores($this->scoresForCompaintValid);
		} else if ($object->getModerationStatus() == ModerationStatusType::NOT_VALID) {
			$object->setScores($this->scoresForCompaintNotValid);
		}
	}

	public function allocateScoresForBestAnswer($object)
	{
		$object->setScores($object->getScores() + $this->scoresForBestAnswer);
	}
}