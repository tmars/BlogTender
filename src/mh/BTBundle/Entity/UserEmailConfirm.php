<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserEmailConfirm
 */
class UserEmailConfirm
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
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var \DateTime
     */
    protected $createdDate;

    /**
     * @var \mh\BTBundle\Entity\User
     */
    protected $user;


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
     * Set code
     *
     * @param string $code
     * @return UserEmailConfirm
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return UserEmailConfirm
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
     * Set user
     *
     * @param \mh\BTBundle\Entity\User $user
     * @return UserEmailConfirm
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
}