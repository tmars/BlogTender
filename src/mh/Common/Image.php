<?php

namespace mh\Common;

use Symfony\Component\Validator\Constraints as Assert;

abstract class Image
{
	/**
     * @Assert\File(maxSize="2M")
     */
    private $file;
	
	protected $dir;
	
	protected $defaultFilename = '';
	
	public function getBrowserPath()
	{
		return sprintf("%s/%s",
			$this->getBrowserDir(),
			$this->getFilename() ? $this->getFilename() : $this->defaultFilename);
	}
	
	public function getPath()
	{
		return sprintf("%s/%s", $this->getDir(), $this->getFilename()); 
	}

	public function __construct($file = null)
    {
		if ($file) {
			$this->setFile($file);
		}
	}

	public function setFile($file)
    {
    	$this->file = $file;
    }

    public function getFile()
    {
    	return $this->file;
    }

    abstract public function setFilename($filename);

    abstract public function getFilename();
	
	protected function generateUniqueFilename($originalName)
	{
		$prefix = \Random::generate(array('length' => 8));
		$name = \Slug::getSlug(pathinfo($originalName, PATHINFO_FILENAME));
		$ext = pathinfo($originalName, PATHINFO_EXTENSION);
		
		$filename = sprintf("%s_%s.%s", $prefix, $name, $ext);
		
		return $filename;
	}

    public function preUpload()
    {
		if (null !== $this->file) {
            // генерируем любое уникальное имя
            $this->setFilename($this->generateUniqueFilename($this->file->getClientOriginalName()));
        }
    }

    public function upload()
    {
    	if (null === $this->file) {
    	    return;
    	}
        $this->file->move($this->getDir(), $this->getFilename());
		unset($this->file);
    }

    public function removeUpload()
    {
		if (file_exists($file = $this->getPath())) {
			unlink($file);
		}
    }

	protected function getDir()
    {
        return '../web' . $this->getBrowserDir();
    }

    protected function getBrowserDir()
    {
        return $this->browserDir;
    }
}