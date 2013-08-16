<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use mh\Common\File;

/**
* PostImage
*/
class PostImage extends File
{
	protected $browserDir = '/media/p/i';

	protected $defaultFilename = 'unknow.jpg';

	protected $filenameTemplate = 'R:length=8|S:v=_|N:length=12|S:v=.|E';

	public function preUpload()
	{
		parent::preUpload();
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
