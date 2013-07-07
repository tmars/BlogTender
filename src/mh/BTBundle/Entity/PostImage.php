<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use mh\Common\Image;

/**
 * PostImage
 */
class PostImage extends Image
{
    protected $formats = array(
		'original' => array(
			'name' => 'original',
			'width' => 632,
			'height' => 800,
			'back' => false,
		),
		'miniview' => array(
			'name' => 'miniview',
			'width' => 183,
			'height' => 130,
			'back' => true,
		),
		'microview' => array(
			'name' => 'microview',
			'width' => 92,
			'height' => 75,
			'back' => true,
		),
	);

    protected $rootDir = '../web/images/post_image';

	protected $dir = '/images/post_image';

    public function getOriginal()
    {
		return $this->getView('original');
   	}

    public function getMiniview()
    {
		return $this->getView('miniview');
   	}

	public function getMicroview()
    {
		return $this->getView('microview');
   	}

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
     * @return PostImage
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
     * @var \mh\BTBundle\Entity\Post
     */
    private $post;


    /**
     * Set post
     *
     * @param \mh\BTBundle\Entity\Post $post
     * @return PostImage
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