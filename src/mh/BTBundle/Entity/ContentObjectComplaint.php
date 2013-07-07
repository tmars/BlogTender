<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use mh\BTBundle\DBAL\ModerationStatusType;

/**
 * ContentObjectComplaint
 */
class ContentObjectComplaint
{
	/**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setModerationStatus(ModerationStatusType::NOT_MODERATED);
    	$this->setCreatedDate(new \DateTime());
		$this->setScores(0);
    }

	/*--------------------------------------------------------------------------*/

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $createdDate;

    /**
     * @var ModerationStatus
     */
    protected $moderationStatus;

    /**
     * @var integer
     */
    protected $scores;

    /**
     * @var \mh\BTBundle\Entity\User
     */
    protected $user;

    /**
     * @var \mh\BTBundle\Entity\ContentObject
     */
    protected $target;


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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return ContentObjectComplaint
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
     * @return ContentObjectComplaint
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
     * Set scores
     *
     * @param integer $scores
     * @return ContentObjectComplaint
     */
    public function setScores($scores)
    {
        $this->scores = $scores;

        return $this;
    }

    /**
     * Get scores
     *
     * @return integer
     */
    public function getScores()
    {
        return $this->scores;
    }

    /**
     * Set user
     *
     * @param \mh\BTBundle\Entity\User $user
     * @return ContentObjectComplaint
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
     * Set target
     *
     * @param \mh\BTBundle\Entity\ContentObject $target
     * @return ContentObjectComplaint
     */
    public function setTarget(\mh\BTBundle\Entity\ContentObject $target = null)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return \mh\BTBundle\Entity\ContentObject
     */
    public function getTarget()
    {
        return $this->target;
    }
}