<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostComment
 */
class PostComment
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
     * @var \DateTime
     */
    protected $createdDate;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var \mh\BTBundle\Entity\User
     */
    protected $user;

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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return PostComment
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
     * Set content
     *
     * @param string $content
     * @return PostComment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set user
     *
     * @param \mh\BTBundle\Entity\User $user
     * @return PostComment
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
     * Set post
     *
     * @param \mh\BTBundle\Entity\Post $post
     * @return PostComment
     */
    public function setPost(\mh\BTBundle\Entity\Post $post = null)
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