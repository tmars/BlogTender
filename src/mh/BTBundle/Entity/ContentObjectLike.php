<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentObjectLike
 */
class ContentObjectLike
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
     * @var \mh\BTBundle\Entity\User
     */
    protected $user;

    /**
     * @var \mh\BTBundle\Entity\ContentObject
     */
    protected $target;


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
     * @return ContentObjectLike
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
     * @return ContentObjectLike
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
     * Set target
     *
     * @param \mh\BTBundle\Entity\ContentObject $target
     * @return ContentObjectLike
     */
    public function setTarget(\mh\BTBundle\Entity\ContentObject $target = null)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return \mh\BTBundle\Entity\ContentObject
     */
    public function getTarget()
    {
        return $this->target;
    }
}