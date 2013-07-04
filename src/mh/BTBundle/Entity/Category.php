<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 */
class Category
{
    public function __toString()
    {
        return $this->getLabel();
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
     * @return Category
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
     * Add posts
     *
     * @param \mh\BTBundle\Entity\Post $posts
     * @return Category
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