<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 */
class Tag
{
	public function __toString()
    {
        return $this->getLabel();
    }

    public static function formatLabel(&$tag_label)
    {
        $tag_label = preg_replace("/\s+/u", ' ', trim($tag_label));
        $tag_label = mb_strtolower($tag_label, 'UTF8');
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setCreatedDate(new \DateTime());
        $this->setIsModered(false);
    }

    /*--------------------------------------------------------------------------*/
	
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var \DateTime
     */
    protected $createdDate;

    /**
     * @var boolean
     */
    protected $isModered;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $posts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set label
     *
     * @param string $label
     * @return Tag
     */
    public function setLabel($label)
    {
        $this->label = $label;
    
        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Tag
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
     * Set isModered
     *
     * @param boolean $isModered
     * @return Tag
     */
    public function setIsModered($isModered)
    {
        $this->isModered = $isModered;
    
        return $this;
    }

    /**
     * Get isModered
     *
     * @return boolean 
     */
    public function getIsModered()
    {
        return $this->isModered;
    }

    /**
     * Add posts
     *
     * @param \mh\BTBundle\Entity\Post $posts
     * @return Tag
     */
    public function addPost(\mh\BTBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;
    
        return $this;
    }

    /**
     * Remove posts
     *
     * @param \mh\BTBundle\Entity\Post $posts
     */
    public function removePost(\mh\BTBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }
}