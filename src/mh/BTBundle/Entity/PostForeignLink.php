<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostForeignLink
 */
class PostForeignLink
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
    protected $url;

    /**
     * @var \DateTime
     */
    protected $createdDate;

    /**
     * @var integer
     */
    protected $scores;

    /**
     * @var \mh\BTBundle\Entity\Post
     */
    protected $post;


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
     * Set url
     *
     * @param string $url
     * @return PostForeignLink
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return PostForeignLink
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
     * Set scores
     *
     * @param integer $scores
     * @return PostForeignLink
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
     * Set post
     *
     * @param \mh\BTBundle\Entity\Post $post
     * @return PostForeignLink
     */
    public function setPost(\mh\BTBundle\Entity\Post $post)
    {
        $this->post = $post;
    
        return $this;
    }

    /**
     * Get post
     *
     * @return \mh\BTBundle\Entity\Post 
     */
    public function getPost()
    {
        return $this->post;
    }
}