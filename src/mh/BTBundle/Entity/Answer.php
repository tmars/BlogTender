<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use mh\BTBundle\DBAL\ModerationStatusType;

/**
 * Answer
 */
class Answer extends Base\ContentObjectBase
{
    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
		$this->setCreatedDate(new \DateTime());
        $this->setModerationStatus(ModerationStatusType::NOT_MODERATED);
        $this->setIsPublished(true);
        $this->setIsBest(false);
        if (!$this->getScores()) $this->setScores(0);

		parent::prePersist();
	}

    public function getPreview()
    {
        return strip_tags($this->getContent());
    }

    public function __toString()
    {
        return $this->getPreview();
    }

    public function getTextPreview()
    {
        return mb_substr($this->getContent(), 0, 100, 'UTF-8');
    }

    /*--------------------------------------------------------------------------*/


    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var \DateTime
     */
    protected $createdDate;

    /**
     * @var ModerationStatus
     */
    protected $moderationStatus;

    /**
     * @var boolean
     */
    protected $isPublished;

    /**
     * @var boolean
     */
    protected $isBest;

    /**
     * @var \mh\BTBundle\Entity\ContentObject
     */
    protected $contentObject;

    /**
     * @var \mh\BTBundle\Entity\User
     */
    protected $user;

    /**
     * @var \mh\BTBundle\Entity\Question
     */
    protected $question;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Answer
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Answer
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set moderationStatus
     *
     * @param ModerationStatus $moderationStatus
     * @return Answer
     */
    public function setModerationStatus($moderationStatus)
    {
        $this->moderationStatus = $moderationStatus;

        return $this;
    }

    /**
     * Get moderationStatus
     *
     * @return ModerationStatus
     */
    public function getModerationStatus()
    {
        return $this->moderationStatus;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     * @return Answer
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set isBest
     *
     * @param boolean $isBest
     * @return Answer
     */
    public function setIsBest($isBest)
    {
        $this->isBest = $isBest;

        return $this;
    }

    /**
     * Get isBest
     *
     * @return boolean
     */
    public function getIsBest()
    {
        return $this->isBest;
    }

    /**
     * Set contentObject
     *
     * @param \mh\BTBundle\Entity\ContentObject $contentObject
     * @return Answer
     */
    public function setContentObject(\mh\BTBundle\Entity\ContentObject $contentObject = null)
    {
        $this->contentObject = $contentObject;

        return $this;
    }

    /**
     * Get contentObject
     *
     * @return \mh\BTBundle\Entity\ContentObject
     */
    public function getContentObject()
    {
        return $this->contentObject;
    }

    /**
     * Set user
     *
     * @param \mh\BTBundle\Entity\User $user
     * @return Answer
     */
    public function setUser(\mh\BTBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \mh\BTBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set question
     *
     * @param \mh\BTBundle\Entity\Question $question
     * @return Answer
     */
    public function setQuestion(\mh\BTBundle\Entity\Question $question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \mh\BTBundle\Entity\Question
     */
    public function getQuestion()
    {
        return $this->question;
    }
    /**
     * @var \mh\BTBundle\Entity\ScoreObject
     */
    private $scoreObject;


    /**
     * Set scoreObject
     *
     * @param \mh\BTBundle\Entity\ScoreObject $scoreObject
     * @return Answer
     */
    public function setScoreObject(\mh\BTBundle\Entity\ScoreObject $scoreObject = null)
    {
        $this->scoreObject = $scoreObject;
    
        return $this;
    }

    /**
     * Get scoreObject
     *
     * @return \mh\BTBundle\Entity\ScoreObject 
     */
    public function getScoreObject()
    {
        return $this->scoreObject;
    }
}