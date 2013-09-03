<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use mh\Common\File;

/**
 * PostAttachmentFile
 */
class PostAttachmentFile extends File
{
    protected $browserDir = '/media/p/a/f';

	protected $filenameTemplate = 'R:length=8|S:v=_|N:length=12|S:v=.|E';
    
    /*--------------------------------------------------------------------------*/
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var \mh\BTBundle\Entity\Post
     */
    private $post;


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
     * Set filename
     *
     * @param string $filename
     * @return PostAttachmentFile
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    
        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set post
     *
     * @param \mh\BTBundle\Entity\Post $post
     * @return PostAttachmentFile
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
