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
	
	public static function generateUniqueFilename($originalName, $template = 'R:length=8|S:v=_|N:length=12|S:v=.|E')
	{
		$filename = '';
		
		foreach (explode('|', $template) as $t) {
			if (strpos($t, ':')) {
				list($mode, $parameters_str) = explode(':', $t);
			
				$parameters = array();
				foreach (explode(',', $parameters_str) as $p) {
					list($k, $v) = explode('=', $p);
					$parameters[$k] = $v;
				}	
			} else {
				$mode = $t;	
			}
			
			if ($mode == 'R') {
				$filename .= Random::generate($parameters);
			} else if ($mode == 'S') {
				$filename .= $parameters['v'];
			} else if ($mode == 'N') {
				$name = Slug::getSlug(pathinfo($originalName, PATHINFO_FILENAME));
				if (array_key_exists('length', $parameters)) {
					$name = substr($name, 0, $parameters['length']);
				}
				$filename .= $name;
			} else if ($mode == 'E') {
				$filename .= pathinfo($originalName, PATHINFO_EXTENSION);
			}
		}
		
		return $filename;
	}

    public function preUpload()
    {
		if (null !== $this->file) {
            // генерируем любое уникальное имя
            $this->setFilename(self::generateUniqueFilename($this->file->getClientOriginalName(), $this->filenameTemplate));
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