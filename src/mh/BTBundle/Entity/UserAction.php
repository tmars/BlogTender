<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserAction
 */
class UserAction
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
    protected $fromUrl;

    /**
     * @var string
     */
    protected $toUrl;

    /**
     * @var \DateTime
     */
    protected $createdDate;

    /**
     * @var \mh\BTBundle\Entity\UserSession
     */
    protected $userSession;


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
     * Set fromUrl
     *
     * @param string $fromUrl
     * @return UserAction
     */
    public function setFromUrl($fromUrl)
    {
        $this->fromUrl = $fromUrl;
    
        return $this;
    }

    /**
     * Get fromUrl
     *
     * @return string 
     */
    public function getFromUrl()
    {
        return $this->fromUrl;
    }

    /**
     * Set toUrl
     *
     * @param string $toUrl
     * @return UserAction
     */
    public function setToUrl($toUrl)
    {
        $this->toUrl = $toUrl;
    
        return $this;
    }

    /**
     * Get toUrl
     *
     * @return string 
     */
    public function getToUrl()
    {
        return $this->toUrl;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return UserAction
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
     * Set userSession
     *
     * @param \mh\BTBundle\Entity\UserSession $userSession
     * @return UserAction
     */
    public function setUserSession(\mh\BTBundle\Entity\UserSession $userSession = null)
    {
        $this->userSession = $userSession;
    
        return $this;
    }

    /**
     * Get userSession
     *
     * @return \mh\BTBundle\Entity\UserSession 
     */
    public function getUserSession()
    {
        return $this->userSession;
    }
}