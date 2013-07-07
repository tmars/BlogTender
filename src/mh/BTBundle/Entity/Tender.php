<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tender
 */
class Tender
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $beginDate;

    /**
     * @var \DateTime
     */
    protected $endDate;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var \mh\BTBundle\Entity\Tender
     */
    protected $parent;


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
     * Set beginDate
     *
     * @param \DateTime $beginDate
     * @return Tender
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
     * @return Tender
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
     * Set title
     *
     * @param string $title
     * @return Tender
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set parent
     *
     * @param \mh\BTBundle\Entity\Tender $parent
     * @return Tender
     */
    public function setParent(\mh\BTBundle\Entity\Tender $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \mh\BTBundle\Entity\Tender 
     */
    public function getParent()
    {
        return $this->parent;
    }
}