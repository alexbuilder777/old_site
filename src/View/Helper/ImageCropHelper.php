<?php
namespace App\View\Helper;

use Cake\View\Helper;
//use App\Controllers;
//use App\Controller\AppController;

use Cake\Core;
use Cake\Core\App;
//use Cake\Controller\Controller;
//use Cake\Controller\Controller;
//use Cake\Event\Event;
//use Cake\Controller;
//use App\Controller\Component;
//use App\Controller\Component\ImageCropComponent as IM;
//use Cake\Controller\Component\ImageVersionComponent;
//use App\Controllers\Component\ImageCropComponent as IM;
//use Cake\Core\App as App;
//use Cake\Controller\Component;
/**
 * Image Version Helper class to embed thumbnail images on a page.
 * 
 * @link			http://www.shift8creative.com
 * @author			Tom Maiaroto
 * @modifiedby		Tom
 * @lastmodified	2010-05-03 14:55:07 
 * @created			2010-05-04 18:33:55 
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class ImageCropHelper extends Helper 
{


    public $helpers = ['Html'];
    public $components = ['ImageCrop'];
  
    
    public function image($options = [], $img_options = [])
    {


        $input = mb_substr( WWW_ROOT , 0, -1).$options['input'];
        

        $output = str_replace(basename($input), $options['width'].'x'.$options['height'].'/'.basename($input), $input);

        $reOptions = compact("input", "output");

        $options = array_merge($options,$reOptions);

        if(!file_exists($output)) {$image = $this->resize($options);}

        $image = str_replace(WWW_ROOT, '', $output);
        return $this->Html->image('http://'.$_SERVER['HTTP_HOST'].'/'.$image,$img_options);

    }

	/**
	 * Place watermark on image
	 *
	 * Options:
	 * - 'input' Input file (path or gd resource)
	 * - 'output' Output path. If not specified, gd resource is returned
	 * - 'watermark' Watermark file (path or gd resource)
	 * - 'quality' Output image quality (JPG only). Value from 0 to 100
	 * - 'compression' Output image compression (PNG only). Value from 0 to 9
	 * - 'chmod' What permissions should be applied to destination image
	 * - 'scale' If true, watermark will be scaled fullsize ('position' and 'repeat' won't be taken into account)
	 * - 'strech' If true and scale also set to true, strech watermark to cover whole image
	 * - 'repeat' Should watermark be repeated? This is ignored if 'scale' is set to true or 'position' is custom (array)
	 * - 'position' Watermark position. Possible values: 'top-left', 'top-right', 'bottom-right', 'bottom-left', 'center' or [x, y]
	 * - 'opacity' Watermark image's opacity (0-100). Default = 100
	 * - 'afterCallbacks' Functions to be executed after this one
	 *
	 * @param array $options An array of options.
	 * @return mixed boolean or GD resource if output was set to null
	 */
	public static function resize($options = []) {
		$options = array_merge([
			'afterCallbacks' => null,
			'compression' => null,
			'paddings' => true,
			'enlarge' => true,
			'quality' => null,
			'chmod' => 0777,
			'units' => 'px',
			'height' => null,
			'output' => null,
			'width' => null,
			'input' => null,
			'scale'=> true,
			'mode'=>'fit'
			//'enlarge'=> true
		], $options);
		// if output path (directories) doesn't exist, try to make whole path
		if (!self::createPath($options['output'])) {
			return false;
		}
		$input_extension = self::getImageType($options['input']);
		$output_extension = self::getExtension($options['output']);
		$src_im = self::openImage($options['input']);
		if (!$src_im) {
			return false;
		}
		// calculate new w, h, x and y
		if (!empty($options['width']) && !is_numeric($options['width'])) {
			return false;
		}
		if (!empty($options['height']) && !is_numeric($options['height'])) {
			return false;
		}
		// get size of the original image
		$input_width = imagesx($src_im);
		$input_height = imagesy($src_im);
		//calculate destination image w/h
		// turn % into px
		if ($options['units'] == '%') {
			if ($options['height'] != null) {
				$options['height'] = round($input_height * $options['height'] / 100);
			}
			if ($options['width'] != null) {
				$options['width'] = round($input_width * $options['width'] / 100);
			}
		}
		// if mode=fit, check output width/height and update them  as neccessary
		if ($options['mode'] === 'fit' && $options['width'] != null && $options['height'] != null) {
			$input_ratio = $input_width / $input_height;
			$output_ratio = $options['width'] / $options['height'];
			$original_width = $options['width'];
			$original_height = $options['height'];
			if ($input_ratio > $output_ratio) {
				$options['height'] = $input_height * $options['width'] / $input_width;
			} else {
				$options['width'] = $input_width * $options['height'] / $input_height;
			}
		}
		// calculate missing width/height (if any)
		if ($options['width'] == null && $options['height'] == null) {
			$options['width'] = $input_width;
			$options['height'] = $input_height;
		} else if ($options['height'] == null) {
			$options['height'] = round(($options['width'] * $input_height) / $input_width);
		} else if ($options['width'] == null) {
			$options['width'] = round(($options['height'] * $input_width) / $input_height);
		}
		$src_x = 0;
		$src_y = 0;
		$src_w = $input_width;
		$src_h = $input_height;
		if ($options['enlarge'] == false && ($options['width'] > $input_width || $options['height'] > $input_height)) {
			$options['width'] = $input_width;
			$options['height'] = $input_height;
		} else if ($options['mode'] === 'crop') {
			if (($input_width / $input_height) > ($options['width'] / $options['height'])) {
				$ratio = $input_height / $options['height'];
				$src_w = $ratio * $options['width'];
				$src_x = round(($input_width - $src_w) / 2);
			} else {
				$ratio = $input_width / $options['width'];
				$src_h = $ratio * $options['height'];
				$src_y = round(($input_height - $src_h) / 2);
			}
		}
		// if possible, just move file w/o modifying it
		$is_local = is_string($options['input']) && !preg_match('/^https?:\/\//', $options['input']);
		$is_same_type = $input_extension === $output_extension;
		$is_same_size = $input_width === $options['width'] && $input_height === $options['height'];
		if ($is_same_size && $is_same_type && $is_local && empty($options['afterCallbacks'])) {
			$r = copy($options['input'], $options['output']);
			if (!empty($options['chmod'])) {
				chmod($options['output'], $options['chmod']);
			}
			return $r;
		}
		$dst_im = imagecreatetruecolor($options['width'], $options['height']);
		if (!$dst_im) {
			imagedestroy($src_im);
			return false;
		}
		// transparency or white bg instead of black
		if (in_array($input_extension, ['png', 'gif'])) {
			if (in_array($output_extension, ['png', 'gif'])) {
				imagealphablending($dst_im, false);
				imagesavealpha($dst_im, true);
				$transparent = imagecolorallocatealpha($dst_im, 255, 255, 255, 127);
				imagefilledrectangle($dst_im, 0, 0,$options['width'], $options['height'], $transparent);
			} else {
				$white = imagecolorallocate($dst_im, 255, 255, 255);
				imagefilledrectangle($dst_im, 0, 0, $options['width'], $options['height'], $white);
			}
		}
		$r = imagecopyresampled($dst_im, $src_im, 0, 0, $src_x, $src_y, $options['width'], $options['height'], $src_w, $src_h);
		if (!$r) {
			imagedestroy($src_im);
			return false;
		}
		if ($options['mode'] === 'fit' && $options['paddings']) {
			if ($options['width'] != $original_width || $options['height'] != $original_height) {
				$bg_im = imagecreatetruecolor($original_width, $original_height);
				if (!$bg_im) {
					imagedestroy($bg_im);
					return false;
				}
				if ($options['paddings'] === true) {
					$rgb = [255, 255, 255];
				} else {
					$rgb = self::readColor($options['paddings']);
					if (!$rgb) {
						$rgb = [255, 255, 255];
					}
				}
				$color = imagecolorallocate($bg_im, $rgb[0], $rgb[1], $rgb[2]);
				imagefilledrectangle($bg_im, 0, 0, $original_width, $original_height, $color);
				$x = round(($original_width - $options['width']) / 2);
				$y = round(($original_height - $options['height']) / 2);
				imagecopy($bg_im, $dst_im, $x, $y, 0, 0, $options['width'], $options['height']);
				$dst_im = $bg_im;
			}
		}
		if (!self::afterCallbacks($dst_im, $options['afterCallbacks'])) {
			return false;
		}
		
		self::saveImage($dst_im, $options);
		return true;
	}

	/**
	 * Try to create specified path
	 *
	 * If specified path is empty, return true
	 *
	 * @param string $output_path
	 * @param mixed $chmod Each folder's permissions
	 * @return boolean
	 */
	public static function createPath($output_path, $chmod = 0777) {
		if (empty($output_path)) {
			return true;
		}
		$arr_output_path = explode(DIRECTORY_SEPARATOR, $output_path);
		unset($arr_output_path[count($arr_output_path)-1]);
		$dir_path = implode($arr_output_path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		if (!file_exists($dir_path)) {
			if (!mkdir($dir_path, $chmod, true)) {
				return false;
			}
		}
		return true;
	}




	/**
	 * Get image type from file
	 *
	 * @param string $input Input (path) image
	 * @param string $extension (optional) Extension (type)
	 * @param boolean $extension If true, check by extension
	 * @return string
	 */
	public static function getImageType($input, $extension = false) {
		if ($extension) {
			switch (self::getExtension($input)) {
				case 'jpg':
				case 'jpeg':
					return 'jpg';
				break;
				case 'png':
					return 'png';
				break;
				case 'gif':
					return 'gif';
				break;
			}
		} else if (is_string($input) && is_file($input)) {
			$info = getimagesize($input);
			switch ($info['mime']) {
				case 'image/pjpeg':
				case 'image/jpeg':
				case 'image/jpg':
					return 'jpg';
				break;
				case 'image/x-png':
				case 'image/png':
					return 'png';
				break;
				case 'image/gif':
					return 'gif';
				break;
			}
		}
		return '';
	}


	public static function getExtension($filename) {
		if (!is_string($filename)) {
			return '';
		}
		$pos = strrpos($filename, '.');
		if ($pos === false) {
			return '';
		}
		return strtolower(substr($filename, $pos + 1));
	}
	
	
	

	/**
	 * Open image as gd resource
	 *
	 * @param string $input Input (path) image
	 * @return mixed
	 */
	public static function openImage($input) {
		if (is_resource($input)) {
			if (get_resource_type($input) == 'gd') {
				return $input;
			}
		} else {
			if (is_string($input) && preg_match('/^https?:\/\//', $input)) {
				$image = file_get_contents($input);
				if (!$image) {
					return false;
				}
				return imagecreatefromstring($image);
			}
			switch (self::getImageType($input)) {
				case 'jpg':
					return imagecreatefromjpeg($input);
				break;
				case 'png':
					return imagecreatefrompng($input);
				break;
				case 'gif':
					return imagecreatefromgif($input);
				break;
			}
		}
		return false;
	}	
	
	/**
	 * Save image gd resource as image
	 *
	 * Image type is determined by $output extension so it must be present.
	 *
	 * Options:
	 * - 'compression' Output image's compression. Currently only PNG (value 0-9) supports this
	 * - 'quality' Output image's quality. Currently only JPG (value 0-100) supports this
	 * - 'output' Output path. If not specified, image resource is returned
	 *
	 * @param mixed $im Image resource
	 * @param string $output Output path
	 * @param mixed $options An array of additional options
	 * @return boolean
	 */
	public static function saveImage(&$im, $options = []) {
		foreach (['compression', 'quality', 'chmod'] as $v) {
			if (is_null($options[$v])) {
				unset($options[$v]);
			}
		}
		$options = array_merge([
			'compression' => 9,
			'quality' => 100,
			'output' => null
		], $options);
		switch (self::getImageType($options['output'], true)) {
			case 'jpg':
				if (ImageJPEG($im, $options['output'], $options['quality'])) {
					if (!empty($options['chmod'])) {
						chmod($options['output'], $options['chmod']);
					}
					return true;
				}
			break;
			case 'png':
				if (ImagePNG($im, $options['output'], $options['compression'])) {
					if (!empty($options['chmod'])) {
						chmod($options['output'], $options['chmod']);
					}
					return true;
				}
			break;
			case 'gif':
				if (ImageGIF($im, $options['output'])) {
					if (!empty($options['chmod'])) {
						chmod($options['output'], $options['chmod']);
					}
					return true;
				}
			break;
			case '':
				return $im;
			break;
		}
		unset($im);
		return false;
	}	
	/**
	 * Perform afterCallbacks on specified image
	 *
	 * @param resource $im Image to perform callback on
	 * @param mixed $functions Callback functions and their arguments
	 * @return boolean
	 */
	public static function afterCallbacks(&$im, $functions) {
		if (empty($functions)) {
			return true;
		}
		foreach ($functions as $v) {
			$v[1]['input'] = $im;
			$im = self::$v[0]($v[1]);
			if (!$im) {
				return false;
			}
		}
		return true;
	}


}
?>