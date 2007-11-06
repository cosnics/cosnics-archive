<?php
/**
 * $Id$
 * @package imagemanipulation
 */
 /**
 * This class provide image manipulation using php's GD-extension
 */
class GdImageManipulation extends ImageManipulation
{
	private $gd_image = null;
	public function GdImageManipulation($source_file)
	{
		parent::ImageManipulation($source_file);
		$this->load_gd_image();
	}
 	function crop($width,$height,$offset_x = ImageManipulation::CROP_CENTER,$offset_y = ImageManipulation::CROP_CENTER)
 	{
  		if($offset_x == ImageManipulation::CROP_CENTER)
  		{
  			$offset_x = ($this->width - $width)/2;
  		}
  		if($offset_y == ImageManipulation::CROP_CENTER)
  		{
  			$offset_y = ($this->height - $height)/2;
  		}
  		$result = imagecreatetruecolor($width, $height);
  		imagecopy($result, $this->gd_image, 0, 0, $offset_x, $offset_y, $width, $height);
  		$this->gd_image = $result;
		$this->width = $width;
		$this->height = $height;
 	}
	function resize($width,$height)
	{
		$result = imagecreatetruecolor($width, $height);
		imagecopyresampled($result, $this->gd_image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
		$this->gd_image = $result;
		$this->width = $width;
		$this->height = $height;
	}
	function write_to_file($file = null)
	{
		if(is_null($file))
		{
			$file = $this->source_file;
		}
		$extension = $this->get_image_extension();
		$extension = str_replace('jpg', 'jpeg', $extension);
		$create_function = 'image'.$extension;
		return $create_function($this->gd_image,$file);
	}
	private function load_gd_image()
	{
		$extension = $this->get_image_extension();
		$extension = str_replace('jpg', 'jpeg', $extension);
		$create_function = 'imagecreatefrom'.$extension;
		$this->gd_image =  $create_function($this->source_file);
	}
}
?>