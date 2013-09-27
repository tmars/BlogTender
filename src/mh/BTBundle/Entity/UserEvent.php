<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserEvent
 */
class UserEvent
{
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
    private $target_id;

    /**
     * @var string
     */
    private $event_type;

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
     * Set target_id
     *
     * @param integer $targetId
     * @return UserEvent
     */
    public function setTargetId($targetId)
    {
        $this->target_id = $targetId;
    
        return $this;
    }

    /**
     * Get target_id
     *
     * @return integer 
     */
    public function getTargetId()
    {
        return $this->target_id;
    }

    /**
     * Set event_type
     *
     * @param string $eventType
     * @return UserEvent
     */
    public function setEventType($eventType)
    {
        $this->event_type = $eventType;
    
        return $this;
    }

    /**
     * Get event_type
     *
     * @return string 
     */
    public function getEventType()
    {
        return $this->event_type;
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