<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrustedSite
 */
class TrustedSite
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $url;


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
     * @return TrustedSite
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
}