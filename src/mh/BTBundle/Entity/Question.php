<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use mh\BTBundle\DBAL\ModerationStatusType;

/**
 * Question
 */
class Question extends Base\ContentObjectBase
{
    protected  $contentType = 'question';

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
		$this->setAnswerCount(0);
		$this->setCreatedDate(new \DateTime());
        $this->setModerationStatus(ModerationStatusType::NOT_MODERATED);
        $this->setIsPublished(true);
        if (!$this->getScores()) $this->setScores(0);

		parent::prePersist();
	}

    public function __toString()
    {
        return $this->getTitle();
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
    protected $title;

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
     * @var \mh\BTBundle\Entity\ContentObject
     */
    protected $contentObject;

    /**
     * @var \mh\BTBundle\Entity\User
     */
    protected $user;

    /**
     * @var \mh\BTBundle\Entity\Category
     */
    protected $category;


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
     * Set title
     *
     * @param string $title
     * @return Question
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Question
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
     * @return Question
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
     * @return Question
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
     * @return Question
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
     * Set contentObject
     *
     * @param \mh\BTBundle\Entity\ContentObject $contentObject
     * @return Question
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
     * @return Question
     */
    public function setUser(\mh\BTBundle\Entity\User $user = null)
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
     * Set category
     *
     * @param \mh\BTBundle\Entity\Category $category
     * @return Question
     */
    public function setCategory(\mh\BTBundle\Entity\Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \mh\BTBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * @var integer
     */
    private $answerCount;


    /**
     * Set answerCount
     *
     * @param integer $answerCount
     * @return Question
     */
    public function setAnswerCount($answerCount)
    {
        $this->answerCount = $answerCount;

        return $this;
    }

    /**
     * Get answerCount
     *
     * @return integer
     */
    public function getAnswerCount()
    {
        return $this->answerCount;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $answers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add answers
     *
     * @param \mh\BTBundle\Entity\Answer $answers
     * @return Question
     */
    public function addAnswer(\mh\BTBundle\Entity\Answer $answers)
    {
        $this->answers[] = $answers;
    
        return $this;
    }

    /**
     * Remove answers
     *
     * @param \mh\BTBundle\Entity\Answer $answers
     */
    public function removeAnswer(\mh\BTBundle\Entity\Answer $answers)
    {
        $this->answers->removeElement($answers);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswers()
    {
        return $this->answers;
    }
    /**
     * @var \mh\BTBundle\Entity\ScoreObject
     */
    private $scoreObject;


    /**
     * Set scoreObject
     *
     * @param \mh\BTBundle\Entity\ScoreObject $scoreObject
     * @return Question
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