<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventCounter
 */
class EventCounter
{
    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setActiveDay(new \DateTime('today'));
    }

    /*--------------------------------------------------------------------------*/

    /**
     * @var integer
     */
    private $id;

    /**
     * @var EventCounter
     */
    private $type;

    /**
     * @var \DateTime
     */
    private $activeDay;

    /**
     * @var integer
     */
    private $value;

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
     * Set type
     *
     * @param EventCounter $type
     * @return EventCounter
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return EventCounter 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set activeDay
     *
     * @param \DateTime $activeDay
     * @return EventCounter
     */
    public function setActiveDay($activeDay)
    {
        $this->activeDay = $activeDay;
    
        return $this;
    }

    /**
     * Get activeDay
     *
     * @return \DateTime 
     */
    public function getActiveDay()
    {
        return $this->activeDay;
    }

    /**
     * Set value
     *
     * @param integer $value
     * @return EventCounter
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return integer 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set user
     *
     * @param \mh\BTBundle\Entity\User $user
     * @return EventCounter
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