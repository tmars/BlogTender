<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserSession
 */
class UserSession
{
	/**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setBeginDate(new \DateTime());
    }

    public function close()
    {
    	$this->setEndDate(new \DateTime());
    }
	
	/*--------------------------------------------------------------------------*/
	
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $ip;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @var \DateTime
     */
    protected $beginDate;

    /**
     * @var \DateTime
     */
    protected $endDate;

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
     * Set ip
     *
     * @param string $ip
     * @return UserSession
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return UserSession
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    
        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set beginDate
     *
     * @param \DateTime $beginDate
     * @return UserSession
     */
    public function setBeginDate($beginDate)
    {
        $this->beginDate = $beginDate;
    
        return $this;
    }

    /**
     * Get beginDate
     *
     * @return \DateTime 
     */
    public function getBeginDate()
    {
        return $this->beginDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return UserSession
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    
        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set user
     *
     * @param \mh\BTBundle\Entity\User $user
     * @return UserSession
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
     * @var string
     */
    private $userAgent;


    /**
     * Set userAgent
     *
     * @param string $userAgent
     * @return UserSession
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    
        return $this;
    }

    /**
     * Get userAgent
     *
     * @return string 
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }
}