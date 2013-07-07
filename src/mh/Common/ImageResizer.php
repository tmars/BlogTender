<?php

namespace mh\Common;

class ImageResizer
{
	private $source;

    private $quality;

    private $back;

    private $background;

    private $dir;

	public function __construct($source)
    {
    	$this->source = $source;
		$this->setQuality(100);
		$this->setBack(true);
    	$this->setBackground(0xFFFFFF);
    }

    public function setQuality($quality)
    {
    	if($quality < 1 || $quality > 99)
			$quality = 100;
		$this->quality = $quality;
    }

    public function getQuality()
    {
    	return $this->quality;
    }

    public function setSize($w, $h)
    {
    	$this->width = $w;
        $this->height = $h;
    }

    public function getWidth()
    {
    	return $this->width;
    }

    public function getHeight()
    {
    	return $this->height;
    }

    public function setBack($f)
    {
    	$this->back = $f;
    }

    public function getBack()
    {
    	return $this->back;
    }

    public function setBackground($color)
    {
    	$this->background = $color;
    }

    public function getBackground()
    {
    	return $this->background;
    }

    public function setDir($dir)
    {
    	$this->dir = $dir;
    }

    public function getDir()
    {
    	return $this->dir;
    }


    function save($dest)
	{
        if (!file_exists($this->source)) return false;

		$size = getimagesize($this->source);
		if ($size === false) return false;

		if (!$this->width || !$this->height) {
			$this->width = $size[0];
			$this->height = $size[1];
		}

		if ($size[0] < $this->width && $size[1] < $this->height) {
			$this->width = $size[0];
			$this->height = $size[1];
		}

		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		$icfunc = "imagecreatefrom" . $format;

		if (!function_exists($icfunc)) return false;

		$x_ratio = $this->width / $size[0];
		$y_ratio = $this->height / $size[1];

		$ratio       = min($x_ratio, $y_ratio);
		$use_x_ratio = ($x_ratio == $ratio);

		$new_width   = $use_x_ratio  ? $this->width  : floor($size[0] * $ratio);
		$new_height  = !$use_x_ratio ? $this->height : floor($size[1] * $ratio);
		$new_left    = $use_x_ratio  ? 0 : floor(($this->width - $new_width) / 2);
		$new_top     = !$use_x_ratio ? 0 : floor(($this->height - $new_height) / 2);

		$isrc = $icfunc($this->source);

		if ($this->back) {
			$idest = imagecreatetruecolor($this->width, $this->height);
        } else {
			$new_left    = 0;
		    $new_top     = 0;
			$idest = imagecreatetruecolor($new_width, $new_height);
		}

		imagefill($idest, 0, 0, $this->background);
		imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);

		imagejpeg($idest, $this->dir.'/'.$dest, $this->quality);

		imagedestroy($isrc);
		imagedestroy($idest);

		return true;
	}
}
?>