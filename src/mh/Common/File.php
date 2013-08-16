<?php

namespace mh\Common;

use Symfony\Component\Validator\Constraints as Assert;

abstract class File
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
		$filename = '';
		foreach (explode('|', $this->filenameTemplate) as $t) {
			$t = explode(':', $t);
			$mode = $t[0];
			if (count($t) == 2) {
				$parameters = array();
				foreach (explode(',', $t[1]) as $p) {
					list($k, $v) = explode('=', $p);
					$parameters[$k] = $v;
				}
			}
			
			if ($mode == 'R') {
				$filename .= Random::generate(array('length' => 8));
			} else if ($mode == 'N') {
				$filename .= Slug::getSlug(pathinfo($originalName, PATHINFO_FILENAME));
			} else if ($mode == 'E') {
				$filename .= pathinfo($originalName, PATHINFO_EXTENSION);
			} else if ($mode == 'S') {
				$filename .= $parameters['v'];
			}
 		}
		
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