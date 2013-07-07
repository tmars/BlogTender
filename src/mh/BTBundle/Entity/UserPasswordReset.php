<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserPasswordReset
 */
class UserPasswordReset
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var \mh\BTBundle\Entity\User
     */
    protected $user;


    /**
     * Set id
     *
     * @param integer $id
     * @return UserPasswordReset
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }

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
     * @return UserPasswordReset
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
     * Set user
     *
     * @param \mh\BTBundle\Entity\User $user
     * @return UserPasswordReset
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
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        // Add your code here
    }
}