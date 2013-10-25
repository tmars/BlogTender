<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScoreObject
 */
class ScoreObject
{
    public static function getTrackedEntityNames()
    {
        return array(
            'Post',
            'Answer',
            'Question',
        );
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setCreatedDate(new \DateTime());
    }

    /*--------------------------------------------------------------------------*/

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $objectType;

    /**
     * @var \DateTime
     */
    private $createdDate;

    /**
     * @var integer
     */
    private $targetId;

    /**
     * @var integer
     */
    private $scores;

    /**
     * @var \mh\BTBundle\Entity\User
     */
    private $user;


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
     * Set objectType
     *
     * @param string $objectType
     * @return ScoreObject
     */
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;
    
        return $this;
    }

    /**
     * Get objectType
     *
     * @return string 
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return ScoreObject
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
     * Set targetId
     *
     * @param integer $targetId
     * @return ScoreObject
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;
    
        return $this;
    }

    /**
     * Get targetId
     *
     * @return integer 
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * Set scores
     *
     * @param integer $scores
     * @return ScoreObject
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
     * @return ScoreObject
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
}