<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use mh\Common\File;

/**
 * PostImage
 */
class UserFoto extends File
{
    protected $formats = array(
		'original' => array(
			'name' => 'original',
			'width' => 800,
			'height' => 800,
			'back' => false,
		),
		'miniview' => array(
			'name' => 'miniview',
			'width' => 100,
			'height' => 100,
			'back' => true,
		),
		'microview' => array(
			'name' => 'microview',
			'width' => 50,
			'height' => 50,
			'back' => true,
		),
		'q122' => array(
			'name' => 'q122',
			'width' => 122,
			'height' => 122,
			'back' => true,
		),
		'q190' => array(
			'name' => 'q190',
			'width' => 190,
			'height' => 190,
			'back' => true,
		),
	);

    protected $rootDir = '../web/images/user_foto';

	protected $dir = '/images/user_foto';

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

	public function getQ122()
    {
		return $this->getView('q122');
   	}

	public function getQ190()
    {
		return $this->getView('q190');
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
     * @var \mh\BTBundle\Entity\User
     */
    private $user;


    /**
     * Set user
     *
     * @param \mh\BTBundle\Entity\User $user
     * @return UserFoto
     */
    public function setUser(\mh\BTBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }
}