<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentObject
 */
class ContentObject
{

    public function __toString()
    {
        return 'Объект '.$this->getId();
    }

    /*--------------------------------------------------------------------------*/

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var integer
     */
    private $complaintsCount;

    /**
     * @var integer
     */
    private $likesCount;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $likes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $complaints;


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
     * Set contentType
     *
     * @param string $contentType
     * @return ContentObject
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    
        return $this;
    }

    /**
     * Get contentType
     *
     * @return string 
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set complaintsCount
     *
     * @param integer $complaintsCount
     * @return ContentObject
     */
    public function setComplaintsCount($complaintsCount)
    {
        $this->complaintsCount = $complaintsCount;
    
        return $this;
    }

    /**
     * Get complaintsCount
     *
     * @return integer 
     */
    public function getComplaintsCount()
    {
        return $this->complaintsCount;
    }

    /**
     * Set likesCount
     *
     * @param integer $likesCount
     * @return ContentObject
     */
    public function setLikesCount($likesCount)
    {
        $this->likesCount = $likesCount;
    
        return $this;
    }

    /**
     * Get likesCount
     *
     * @return integer 
     */
    public function getLikesCount()
    {
        return $this->likesCount;
    }

    /**
     * Add likes
     *
     * @param \mh\BTBundle\Entity\ContentObjectLike $likes
     * @return ContentObject
     */
    public function addLike(\mh\BTBundle\Entity\ContentObjectLike $likes)
    {
        $this->likes[] = $likes;
    
        return $this;
    }

    /**
     * Remove likes
     *
     * @param \mh\BTBundle\Entity\ContentObjectLike $likes
     */
    public function removeLike(\mh\BTBundle\Entity\ContentObjectLike $likes)
    {
        $this->likes->removeElement($likes);
    }

    /**
     * Get likes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Add complaints
     *
     * @param \mh\BTBundle\Entity\ContentObjectComplaint $complaints
     * @return ContentObject
     */
    public function addComplaint(\mh\BTBundle\Entity\ContentObjectComplaint $complaints)
    {
        $this->complaints[] = $complaints;
    
        return $this;
    }

    /**
     * Remove complaints
     *
     * @param \mh\BTBundle\Entity\ContentObjectComplaint $complaints
     */
    public function removeComplaint(\mh\BTBundle\Entity\ContentObjectComplaint $complaints)
    {
        $this->complaints->removeElement($complaints);
    }

    /**
     * Get complaints
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComplaints()
    {
        return $this->complaints;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->likes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->complaints = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
}