<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use mh\Common\File;

/**
 * UserFoto
 */
class UserFoto extends File
{
    protected $browserDir = '/media/u';

	protected $defaultFilename = 'no-avatar.jpg';

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