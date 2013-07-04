<?php

namespace mh\Common;

use Symfony\Component\Validator\Constraints as Assert;

abstract class Image
{
	/**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

	public function __construct($file = null)
    {
    	$this->setFormat('original');

		if ($file) {
			$this->setFile($file);
		}
	}

	protected function setFormat($name, $width = 0, $height = 0)
	{
		$formats[$name] = array(
			'name' => $name,
			'width' => $width,
			'height' => $height,
		);
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

    public function preUpload()
    {
        if (null !== $this->file) {
            // генерируем любое уникальное имя
            $this->setFilename(uniqid());
        }
    }

    public function upload()
    {
    	if (null === $this->file) {
    	    return;
    	}
		//var_dump($this->file);
        $imageResizer = new \mh\Common\ImageResizer($this->file->getRealPath());
        $imageResizer->setDir($this->getRootDir());

        $imageResizer->setSize($this->getWidth('original'), $this->getHeight('original'));
        $imageResizer->save($this->getName('original'));

		unset($this->file);
    }

    public function removeUpload()
    {
		foreach ($this->formats as $mode => $parameters) {
			if (file_exists($file = $this->getRootDir().'/'.$this->getName($mode))) {
				unlink($file);
			}
		}
    }

	protected function getName($mode)
	{
		return $this->formats[$mode]['name'].'/'.$this->getFilename().'.jpg';
	}

	protected function getWidth($mode)
	{
		return $this->formats[$mode]['width'];
	}

	protected function getHeight($mode)
	{
		return $this->formats[$mode]['height'];
	}

	protected function getBack($mode)
	{
		return $this->formats[$mode]['back'];
	}

	protected function setRootDir($rootDir)
	{
		$this->rootDir = $rootDir;
	}

	protected function getRootDir()
    {
        // абсолютный путь к каталогу, куда будут сохраняться загруженные документы
        return $this->rootDir;
    }

	protected function setDir($dir)
	{
		$this->dir = $dir;
	}

    protected function getDir()
    {
        //избавьтесь от __ DIR __, так чтобы его не было, когда отображался загруженный документ/изображение
        return $this->dir;
    }

	protected function getView($mode)
	{
		if (!file_exists($this->getRootDir().'/'.$this->getName($mode))) {

			$imageResizer = new \mh\Common\ImageResizer($this->getRootDir().'/'.$this->getName('original'));
			$imageResizer->setDir($this->getRootDir());

			$imageResizer->setBack($this->getBack($mode));
			$imageResizer->setSize($this->getWidth($mode), $this->getHeight($mode));
			$imageResizer->save($this->getName($mode));

		}

		return $this->getDir().'/'.$this->getName($mode);
	}
}