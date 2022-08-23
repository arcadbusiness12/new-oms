<?php

namespace App\Platform\Helpers;

class Image {
	private $file;
	private $image;
	private $width;
	private $height;
	private $bits;
	private $mime;

	/**
	 * Constructor
	 *
	 * @param	string	$file
	 *
 	*/
	public function __construct($file) {
		if (!extension_loaded('gd')) {
			exit('Error: PHP GD is not installed!');
		}
	

		if (file_exists($file)) {
			$this->file = $file;

			$info = getimagesize($file);

			$this->width  = $info[0];
			$this->height = $info[1];
			$this->bits = isset($info['bits']) ? $info['bits'] : '';
			$this->mime = isset($info['mime']) ? $info['mime'] : '';

			if ($this->mime == 'image/gif') {
				$this->image = imagecreatefromgif($file);
			} elseif ($this->mime == 'image/png') {
				$this->image = imagecreatefrompng($file);
			} elseif ($this->mime == 'image/jpeg') {
				$this->image = imagecreatefromjpeg($file);
			}
		} else {
			exit('Error: Could not load image ' . $file . '!');
		}
	}


	/**
     * 
	 * 
	 * @return	string
     */
	public function getFile() {
		return $this->file;
	}

	/**
     * 
	 * 
	 * @return	array
     */
	public function getImage() {
		return $this->image;
	}


	/**
     * 
	 * 
	 * @return	string
     */
	public function getWidth() {
		return $this->width;
	}


	/**
     * 
	 * 
	 * @return	string
     */
	public function getHeight() {
		return $this->height;
	}


	/**
     * 
	 * 
	 * @return	string
     */
	public function getBits() {
		return $this->bits;
	}


	/**
     * 
	 * 
	 * @return	string
     */
	public function getMime() {
		return $this->mime;
	}


	/**
     * 
     *
     * @param	string	$file
	 * @param	int		$quality
     */
	public function save($file, $quality = 90) {
		$info = pathinfo($file);

		$extension = strtolower($info['extension']);

		if (is_resource($this->image)) {
			if ($extension == 'jpeg' || $extension == 'jpg') {
				imagejpeg($this->image, $file, $quality);
			} elseif ($extension == 'png') {
				imagepng($this->image, $file);
			} elseif ($extension == 'gif') {
				imagegif($this->image, $file);
			}

			imagedestroy($this->image);
		}
	}


	/**
     * 
     *
     * @param	int	$width
	 * @param	int	$height
	 * @param	string	$default
     */
	public function resize($width = 0, $height = 0, $default = '') {
		if (!$this->width || !$this->height) {
			return;
		}

		$xpos = 0;
		$ypos = 0;
		$scale = 1;

		$scale_w = $width / $this->width;
		$scale_h = $height / $this->height;

		if ($default == 'w') {
			$scale = $scale_w;
		} elseif ($default == 'h') {
			$scale = $scale_h;
		} else {
			$scale = min($scale_w, $scale_h);
		}

		if ($scale == 1 && $scale_h == $scale_w && $this->mime != 'image/png') {
			return;
		}

		$new_width = (int)($this->width * $scale);
		$new_height = (int)($this->height * $scale);
		$xpos = (int)(($width - $new_width) / 2);
		$ypos = (int)(($height - $new_height) / 2);

		$image_old = $this->image;
		$this->image = imagecreatetruecolor($width, $height);

		if ($this->mime == 'image/png') {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, 255, 255, 255);
		}

		imagefilledrectangle($this->image, 0, 0, $width, $height, $background);

		imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->width, $this->height);
		imagedestroy($image_old);

		$this->width = $width;
		$this->height = $height;
	}


	/**
     * 
     *
     * @param	string	$watermark
	 * @param	string	$position
     */
	public function watermark($watermark, $position = 'bottomright') {
		switch($position) {
			case 'topleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = 0;
				break;
			case 'topcenter':
				$watermark_pos_x = intval(($this->width - $watermark->getWidth()) / 2);
				$watermark_pos_y = 0;
				break;
			case 'topright':
				$watermark_pos_x = $this->width - $watermark->getWidth();
				$watermark_pos_y = 0;
				break;
			case 'middleleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = intval(($this->height - $watermark->getHeight()) / 2);
				break;
			case 'middlecenter':
				$watermark_pos_x = intval(($this->width - $watermark->getWidth()) / 2);
				$watermark_pos_y = intval(($this->height - $watermark->getHeight()) / 2);
				break;
			case 'middleright':
				$watermark_pos_x = $this->width - $watermark->getWidth();
				$watermark_pos_y = intval(($this->height - $watermark->getHeight()) / 2);
				break;
			case 'bottomleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = $this->height - $watermark->getHeight();
				break;
			case 'bottomcenter':
				$watermark_pos_x = intval(($this->width - $watermark->getWidth()) / 2);
				$watermark_pos_y = $this->height - $watermark->getHeight();
				break;
			case 'bottomright':
				$watermark_pos_x = $this->width - $watermark->getWidth();
				$watermark_pos_y = $this->height - $watermark->getHeight();
				break;
		}
	

		imagealphablending( $this->image, true );
		imagesavealpha( $this->image, true );
		imagecopy($this->image, $watermark->getImage(), $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark->getWidth(), $watermark->getHeight());

		imagedestroy($watermark->getImage());
	}


	/**
     * 
     *
     * @param	int		$top_x
	 * @param	int		$top_y
	 * @param	int		$bottom_x
	 * @param	int		$bottom_y
     */
	public function crop($top_x, $top_y, $bottom_x, $bottom_y) {
		$image_old = $this->image;
		$this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

		imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->width, $this->height);
		imagedestroy($image_old);

		$this->width = $bottom_x - $top_x;
		$this->height = $bottom_y - $top_y;
	}


	/**
     * 
     *
     * @param	int		$degree
	 * @param	string	$color
     */
	public function rotate($degree, $color = 'FFFFFF') {
		$rgb = $this->html2rgb($color);

		$this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));

		$this->width = imagesx($this->image);
		$this->height = imagesy($this->image);
	}


	/**
     * 
     *
     */
	private function filter() {
        $args = func_get_args();

        call_user_func_array('imagefilter', $args);
	}


	/**
     * 
     *
     * @param	string	$text
	 * @param	int		$x
	 * @param	int		$y 
	 * @param	int		$size
	 * @param	string	$color
     */
	private function text($text, $x = 0, $y = 0, $size = 5, $color = '000000') {
		$rgb = $this->html2rgb($color);

		imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
	}


	/**
     * 
     *
     * @param	object	$merge
	 * @param	object	$x
	 * @param	object	$y
	 * @param	object	$opacity
     */
	private function merge($merge, $x = 0, $y = 0, $opacity = 100) {
		imagecopymerge($this->image, $merge->getImage(), $x, $y, 0, 0, $merge->getWidth(), $merge->getHeight(), $opacity);
	}


	/**
     * 
     *
     * @param	string	$color
	 * 
	 * @return	array
     */
	private function html2rgb($color) {
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}

		if (strlen($color) == 6) {
			list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
		} elseif (strlen($color) == 3) {
			list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
		} else {
			return false;
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);

		return array($r, $g, $b);
	}
}

class ToolImage
{
	public function resize($image_path, $image_url, $filename, $width, $height) {
		if (!is_file($image_path . $filename) || substr(str_replace('\\', '/', realpath($image_path . $filename)), 0, strlen($image_path)) != str_replace('\\', '/', $image_path)) {
			return;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$image_old = $filename;
		$image_new = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . '.' . $extension;

		if (!is_file($image_path . $image_new) || (filemtime($image_path . $image_old) > filemtime($image_path . $image_new))) {
			list($width_orig, $height_orig, $image_type) = getimagesize($image_path . $image_old);
				 
			if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) { 
				return $image_path . $image_old;
			}

			$path = '';

			$directories = explode('/', dirname($image_new));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir($image_path . $path)) {
					@mkdir($image_path . $path, 0777);
				}
			}

			if ($width_orig != $width || $height_orig != $height) {
				$image = new Image($image_path . $image_old);
				$image->resize($width, $height);
				$image->save($image_path . $image_new);
			} else {
				copy($image_path . $image_old, $image_path . $image_new);
				$this->resizeImage($image_path . $image_new, $image_path . $image_new, 5);
			}
		}

		$image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +
    
        return rtrim($image_url, '/') . '/' . $image_new;
	}
	public function resizeImage($SrcImage,$DestImage,$Quality){
		list($iWidth,$iHeight,$type) = @getimagesize($SrcImage);
		$NewCanves = imagecreatetruecolor($iWidth, $iHeight);
		$mine_type = strtolower(image_type_to_mime_type($type));
		switch($mine_type){
			case 'image/jpeg':
				$NewImage = imagecreatefromjpeg($SrcImage);
				$ImageQuality = $this->getCompressionLevel($Quality);
				break;
			case 'image/png':
				$NewImage = imagecreatefrompng($SrcImage);
				$ImageQuality = $this->getCompressionLevelForPng($Quality);
				break;
			case 'image/gif':
				$NewImage = imagecreatefromgif($SrcImage);
				$ImageQuality = $this->getCompressionLevel($Quality);
				break;
			default:
              	return false;
      	}
		imagealphablending($NewCanves, false);
		imagesavealpha($NewCanves, true);
      	if(imagecopyresampled($NewCanves, $NewImage,0, 0, 0, 0, $iWidth, $iHeight, $iWidth, $iHeight)){
          	if($mine_type == 'image/png' && is_callable('imagepng') && imagepng($NewCanves,$DestImage,$ImageQuality,PNG_ALL_FILTERS)){
				imagedestroy($NewCanves);
				return true;
			}else if(imagejpeg($NewCanves,$DestImage,$ImageQuality)){
				imagedestroy($NewCanves);
				return true;
          	}
      	}
      	return false;
    }
    public function getCompressionLevel($qualitySetting) {
        $compressionLevel = intval($qualitySetting);
        $quality_array = array(1=>96,2=>94,3=>88,4=>82,5=>76,6=>70,7=>64,8=>58,9=>52,10=>46);
        return (isset($quality_array[$compressionLevel]) ? $quality_array[$compressionLevel] : 70);
    }
    public function getCompressionLevelForPng($qualitySetting){
        $compressionLevel = intval($qualitySetting);
        $quality_array = array(1=>8,2=>8,3=>7,4=>7,5=>6,6=>5,7=>4,8=>4,9=>3,10=>2);
        return (isset($quality_array[$compressionLevel]) ? $quality_array[$compressionLevel] : 3);
    }
}
