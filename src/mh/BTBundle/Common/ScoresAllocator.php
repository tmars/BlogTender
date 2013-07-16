<?php

namespace mh\BTBundle\Common;

// Назначает количество баллов

use mh\BTBundle\DBAL\ModerationStatusType;
use mh\BTBundle\Entity as Entity;

class ScoresAllocator
{
	private $em;

	public function __construct($em)
	{
		$this->em = $em;
	}

	public function forPost(Entity\Post $object)
	{

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