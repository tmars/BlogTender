<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserEvent
 */
class UserEvent
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
     * @var integer
     */
    private $targetId;

    /**
     * @var string
     */
    private $eventType;

    /**
     * @var string
     */
    private $objectType;

    /**
     * @var \DateTime
     */
    private $createdDate;


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
     * Set targetId
     *
     * @param integer $targetId
     * @return UserEvent
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
     * Set eventType
     *
     * @param string $eventType
     * @return UserEvent
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
    
        return $this;
    }

    /**
     * Get eventType
     *
     * @return string 
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * Set objectType
     *
     * @param string $objectType
     * @return UserEvent
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
     * @return UserEvent
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
}