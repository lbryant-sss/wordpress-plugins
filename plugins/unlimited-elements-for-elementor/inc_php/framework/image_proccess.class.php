<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
if ( ! defined( 'ABSPATH' ) ) exit;


	class UniteImageViewUC{
		
		private $pathCache;
		private $pathImages;
		private $urlImages;
		
		private $filename = null;
		private $maxWidth = null;
		private $maxHeight = null;
		private $type = null;
		private $effect = null;
		private $effect_arg1 = null;
		private $effect_arg2 = null;
		
		const EFFECT_BW = "bw";
		const EFFECT_BRIGHTNESS = "bright";
		const EFFECT_CONTRAST = "contrast";
		const EFFECT_EDGE = "edge";
		const EFFECT_EMBOSS = "emboss";
		const EFFECT_BLUR = "blur";
		const EFFECT_BLUR3 = "blur3";
		const EFFECT_MEAN = "mean";
		const EFFECT_SMOOTH = "smooth";
		const EFFECT_DARK = "dark";
		
		const TYPE_EXACT = "exact";
		const TYPE_EXACT_TOP = "exacttop";
		
		private $jpg_quality;
		
		public function __construct(){
			
			$this->jpg_quality = GlobalsUC::DEFAULT_JPG_QUALITY;
		
		}
				
		
		/**
		 * 
		 * throw error
		 */
		private function throwError($message,$code=null){
			UniteFunctionsUC::throwError($message,$code);
		}
		
		/**
		 *
		 * validate type
		 * @param unknown_type $type
		 */
		private function validateType($type){
			switch($type){
				case self::TYPE_EXACT:
				case self::TYPE_EXACT_TOP:
					break;
				default:
					$this->throwError("Wrong image type: ".$type);
				break;
			}
		}
		
		
		/**
		 * 
		 * get filename for thumbnail save / retrieve
		 */
		private function getThumbFilename(){
			
			$info = pathInfo($this->filename);
			
			
			//add dirname as postfix (if exists)
			$postfix = "";
			
			$dirname = UniteFunctionsUC::getVal($info, "dirname");
			if($dirname == ".")
				$dirname = null;
						
			if(!empty($dirname))
				$postfix = str_replace("/", "-", $dirname);			
			
			$ext = $info["extension"];
			$name = $info["filename"];
			$width = ceil($this->maxWidth);
			$height = ceil($this->maxHeight);
			$thumbFilename = $name."_".$width."x".$height;		
			$this->type = trim($this->type);
			
			
			if(!empty($this->type))
				$thumbFilename .= "_" . $this->type;
						
			if(!empty($this->effect)){
				$thumbFilename .= "_e" . $this->effect;
				if(!empty($this->effect_arg1)){
					$thumbFilename .= "x" . $this->effect_arg1;
				}
			}
			
			//add postfix
			if(!empty($postfix))
				$thumbFilename .= "_".$postfix;
			
			$thumbFilename .= ".".$ext;
			
			return($thumbFilename);
		}
		
		
		/**
		 * get thumbnail fielpath by parameters.
		 */
		private function getThumbFilepath(){
			$filename = $this->getThumbFilename();
			$filepath = $this->pathCache .$filename;
			return($filepath);
		}
	
		/**
		 * ouptput emtpy image code
		 */
		private function outputEmptyImageCode(){	
			echo "empty image";
			exit();
		}
		
		/**
		 * outputs image and exit
		 * 
		 */
		private function outputImage($filepath){
						
			$info = UniteFunctionsUC::getPathInfo($filepath);
			$ext = $info["extension"];				
			
			$ext = strtolower($ext);
			if($ext == "jpg")
				$ext = "jpeg";
			
			$numExpires = 31536000;	//one year
			$strExpires = @uelm_date('D, d M Y H:i:s',time()+$numExpires);
			
			$contents = UniteFunctionsUC::fileGetContents($filepath);
			$filesize = strlen($contents);
			/*header("Last-Modified: $strModified GMT");*/
			header("Expires: $strExpires GMT");
			header("Cache-Control: public");
			header("Content-Type: image/$ext");
			header("Content-Length: $filesize");
			
			uelm_echo($contents);
			exit();
		}
		
		
		//------------------------------------------------------------------------------------------
		// get src image from filepath according the image type
		private function getGdSrcImage($filepath,$type){
			// create the image
			$src_img = false;
			switch($type){
				case IMAGETYPE_JPEG:
					$src_img = @imagecreatefromjpeg($filepath);
				break;
				case IMAGETYPE_PNG:
					$src_img = @imagecreatefrompng($filepath);
				break;
				case IMAGETYPE_GIF:
					$src_img = @imagecreatefromgif($filepath);
				break;
				case IMAGETYPE_BMP:
					$src_img = @imagecreatefromwbmp($filepath);
				break;
				case IMAGETYPE_WEBP:
					$src_img = @imagecreatefromwebp($filepath);
				break;
				default:
					$this->throwError("wrong image format <b>$type</b> , can't resize");
				break;
			}
			
			if($src_img == false){
				$this->throwError("Can't resize image");
			}
			 
			return($src_img);
		}
		
		//------------------------------------------------------------------------------------------
		// save gd image to some filepath. return if success or not
		private function saveGdImage($dst_img,$filepath,$type){
			$successSaving = false;
			switch($type){
				case IMAGETYPE_JPEG:
					$successSaving = imagejpeg($dst_img,$filepath,$this->jpg_quality);
				break;
				case IMAGETYPE_PNG:
					$successSaving = imagepng($dst_img,$filepath);
				break;
				case IMAGETYPE_GIF:
					$successSaving = imagegif($dst_img,$filepath);
				break;
				case IMAGETYPE_BMP:
					$successSaving = imagewbmp($dst_img,$filepath);
				break;
			}
			
			return($successSaving);
		}
		
		//------------------------------------------------------------------------------------------
		// crop image to specifix height and width , and save it to new path
		private function cropImageSaveNew($filepath,$filepathNew){
			
			$imgInfo = getimagesize($filepath);
			$imgType = $imgInfo[2];
			
			$src_img = $this->getGdSrcImage($filepath,$imgType);
			
			$width = imageSX($src_img);
			$height = imageSY($src_img);
			
			//crop the image from the top
			$startx = 0;
			$starty = 0;
			
			//find precrop width and height:
			$percent = $this->maxWidth / $width;
			$newWidth = $this->maxWidth;
			$newHeight = ceil($percent * $height);
			
			if($this->type == "exact"){ 	//crop the image from the middle
				$startx = 0;
				$starty = ($newHeight-$this->maxHeight)/2 / $percent;
			}
			
			if($newHeight < $this->maxHeight){	//by width
				$percent = $this->maxHeight / $height;
				$newHeight = $this->maxHeight;
				$newWidth = ceil($percent * $width);
				
				if($this->type == "exact"){ 	//crop the image from the middle
					$startx = ($newWidth - $this->maxWidth) /2 / $percent;	//the startx is related to big image
					$starty = 0;
				}
			}
			
			//resize the picture:
			$tmp_img = ImageCreateTrueColor($newWidth,$newHeight);
			
			$this->handleTransparency($tmp_img,$imgType,$newWidth,$newHeight);
			
			imagecopyresampled($tmp_img,$src_img,0,0,$startx,$starty,$newWidth,$newHeight,$width,$height);
			
			$this->handleImageEffects($tmp_img);
			
			//crop the picture:
			$dst_img = ImageCreateTrueColor($this->maxWidth,$this->maxHeight);
			
			$this->handleTransparency($dst_img,$imgType,$this->maxWidth,$this->maxHeight);
			
			imagecopy($dst_img, $tmp_img, 0, 0, 0, 0, $newWidth, $newHeight);
			
			//save the picture
			$is_saved = $this->saveGdImage($dst_img,$filepathNew,$imgType);
			
			imagedestroy($dst_img);
			imagedestroy($src_img);
			imagedestroy($tmp_img);
			
			return($is_saved);		
		}
		
		//------------------------------------------------------------------------------------------
		// if the images are png or gif - handle image transparency
		private function handleTransparency(&$dst_img,$imgType,$newWidth,$newHeight){
			//handle transparency:
			if($imgType == IMAGETYPE_PNG || $imgType == IMAGETYPE_GIF){
			  imagealphablending($dst_img, false);
			  imagesavealpha($dst_img,true);
			  $transparent = imagecolorallocatealpha($dst_img, 255,  255, 255, 127);
			  imagefilledrectangle($dst_img, 0,  0, $newWidth, $newHeight,  $transparent);
			}
		}
		
		//------------------------------------------------------------------------------------------
		// handle image effects
		private function handleImageEffects(&$imgHandle){
			if(empty($this->effect))
				return(false);
			
			switch($this->effect){
				case self::EFFECT_BW:
					if(defined("IMG_FILTER_GRAYSCALE"))
						imagefilter($imgHandle,IMG_FILTER_GRAYSCALE);
				break;
				case self::EFFECT_BRIGHTNESS:
					if(defined("IMG_FILTER_BRIGHTNESS")){
						if(!is_numeric($this->effect_arg1))
							$this->effect_arg1 = 50;	//set default value
						UniteFunctionsUC::validateNumeric($this->effect_arg1,"'ea1' argument");
						imagefilter($imgHandle,IMG_FILTER_BRIGHTNESS,$this->effect_arg1);
					}
				break;
				case self::EFFECT_DARK:
					if(defined("IMG_FILTER_BRIGHTNESS")){
						if(!is_numeric($this->effect_arg1))
							$this->effect_arg1 = -50;	//set default value
						UniteFunctionsUC::validateNumeric($this->effect_arg1,"'ea1' argument");
						imagefilter($imgHandle,IMG_FILTER_BRIGHTNESS,$this->effect_arg1);
					}
				break;
				case self::EFFECT_CONTRAST:
					if(defined("IMG_FILTER_CONTRAST")){
						if(!is_numeric($this->effect_arg1))
							$this->effect_arg1 = -5;	//set default value	
						imagefilter($imgHandle,IMG_FILTER_CONTRAST,$this->effect_arg1);
					}
				break;
				case self::EFFECT_EDGE:
					if(defined("IMG_FILTER_EDGEDETECT"))
						imagefilter($imgHandle,IMG_FILTER_EDGEDETECT);
				break;
				case self::EFFECT_EMBOSS:
					if(defined("IMG_FILTER_EMBOSS"))
						imagefilter($imgHandle,IMG_FILTER_EMBOSS);
				break;
				case self::EFFECT_BLUR:
					$this->effect_Blur($imgHandle,5);
					/*
					if(defined("IMG_FILTER_GAUSSIAN_BLUR"))
						imagefilter($imgHandle,IMG_FILTER_GAUSSIAN_BLUR);
					*/
				break;
				case self::EFFECT_MEAN:
					if(defined("IMG_FILTER_MEAN_REMOVAL"))
						imagefilter($imgHandle,IMG_FILTER_MEAN_REMOVAL);
				break;
				case self::EFFECT_SMOOTH:
					if(defined("IMG_FILTER_SMOOTH")){
						if(!is_numeric($this->effect_arg1))
							$this->effect_arg1 = 15;	//set default value							
						imagefilter($imgHandle,IMG_FILTER_SMOOTH,$this->effect_arg1);
					}
				break;
				case self::EFFECT_BLUR3:
					$this->effect_Blur($imgHandle,5);
				break;
				default:
					$this->throwError("Effect not supported: <b>".$this->effect."</b>");
				break;
			}
			
		}

	private function effect_Blur(&$gdimg, $radius=0.5) {
		// Taken from Torstein Hרnsi's phpUnsharpMask (see phpthumb.unsharp.php)

		$radius = round(max(0, min($radius, 50)) * 2);
		if (!$radius) {
			return false;
		}

		$w = ImageSX($gdimg);
		$h = ImageSY($gdimg);
		if ($imgBlur = ImageCreateTrueColor($w, $h)) {
			// Gaussian blur matrix:
			//	1	2	1
			//	2	4	2
			//	1	2	1

			// Move copies of the image around one pixel at the time and merge them with weight
			// according to the matrix. The same matrix is simply repeated for higher radii.
			for ($i = 0; $i < $radius; $i++)	{
				ImageCopy     ($imgBlur, $gdimg, 0, 0, 1, 1, $w - 1, $h - 1);            // up left
				ImageCopyMerge($imgBlur, $gdimg, 1, 1, 0, 0, $w,     $h,     50.00000);  // down right
				ImageCopyMerge($imgBlur, $gdimg, 0, 1, 1, 0, $w - 1, $h,     33.33333);  // down left
				ImageCopyMerge($imgBlur, $gdimg, 1, 0, 0, 1, $w,     $h - 1, 25.00000);  // up right
				ImageCopyMerge($imgBlur, $gdimg, 0, 0, 1, 0, $w - 1, $h,     33.33333);  // left
				ImageCopyMerge($imgBlur, $gdimg, 1, 0, 0, 0, $w,     $h,     25.00000);  // right
				ImageCopyMerge($imgBlur, $gdimg, 0, 0, 0, 1, $w,     $h - 1, 20.00000);  // up
				ImageCopyMerge($imgBlur, $gdimg, 0, 1, 0, 0, $w,     $h,     16.666667); // down
				ImageCopyMerge($imgBlur, $gdimg, 0, 0, 0, 0, $w,     $h,     50.000000); // center
				ImageCopy     ($gdimg, $imgBlur, 0, 0, 0, 0, $w,     $h);
			}
			return true;
		}
		return false;
	}
		
		
		//------------------------------------------------------------------------------------------
		// resize image and save it to new path
		private function resizeImageSaveNew($filepath,$filepathNew){
			
			$imgInfo = getimagesize($filepath);
			$imgType = $imgInfo[2];
			
			$src_img = $this->getGdSrcImage($filepath,$imgType);
			 
			$width = imageSX($src_img);
			$height = imageSY($src_img);
			
			$newWidth = $width;
			$newHeight = $height;
			
			//find new width
			if($height > $this->maxHeight){
				$procent = $this->maxHeight / $height;
				$newWidth = ceil($width * $procent);
				$newHeight = $this->maxHeight;
			}
			
			//if the new width is grater than max width, find new height, and remain the width.
			if($newWidth > $this->maxWidth){
				$procent = $this->maxWidth / $newWidth;
				$newHeight = ceil($newHeight * $procent);
				$newWidth = $this->maxWidth;
			}
			
			//if the image don't need to be resized, just copy it from source to destanation.
			if($newWidth == $width && $newHeight == $height && empty($this->effect)){
				$success = copy($filepath,$filepathNew);				
				if($success == false) 
					$this->throwError("can't copy the image from one path to another");
			}
			else{		//else create the resized image, and save it to new path:				
				$dst_img = ImageCreateTrueColor($newWidth,$newHeight);			
				
				$this->handleTransparency($dst_img,$imgType,$newWidth,$newHeight);
				
				//copy the new resampled image:
				imagecopyresampled($dst_img,$src_img,0,0,0,0,$newWidth,$newHeight,$width,$height);
				
				$this->handleImageEffects($dst_img);
				
				$is_saved = $this->saveGdImage($dst_img,$filepathNew,$imgType);
				imagedestroy($dst_img);
			}
			
			imagedestroy($src_img);
			
			return(true);
		}
		
		
		
		
		/**
		 *
		 * set image effect
		 */
		public function setEffect($effect,$arg1 = ""){
			$this->effect = $effect;
			$this->effect_arg1 = $arg1;
		}
		
		/**
		 *
		 * set jpg quality output
		 */
		public function setJPGQuality($quality){
			$this->jpg_quality = $quality;
		}
		
		
		/**
		 * make thumbnail from the image, and save to some path
		 * return new path
		 */
		public function makeThumb($filepathImage, $pathThumbs, $maxWidth=-1, $maxHeight=-1, $type=""){
			
			//validate input
			UniteFunctionsUC::validateFilepath($filepathImage, "image not found");
			
			UniteFunctionsUC::validateDir($pathThumbs, "Thumbs folder don't exists.");
			
			if($type == self::TYPE_EXACT || $type == self::TYPE_EXACT_TOP){
				if($maxHeight == -1)
					$this->throwError("image with exact type must have height!");
				if($maxWidth == -1)
					$this->throwError("image with exact type must have width!");
			}
			
			//get filename
			$info = UniteFunctionsUC::getPathInfo($filepathImage);
			$filename = UniteFunctionsUC::getVal($info, "basename");
			
			UniteFunctionsUC::validateNotEmpty($filename, "image filename not given");
			
			//if gd library doesn't exists - output normal image without resizing.
			if(function_exists("gd_info") == false)
				$this->throwError("php must support GD Library");
			
			if($maxWidth == -1 && $maxHeight == -1)
				$this->throwError("Wrong thumb size");
				
			if($maxWidth == -1) 
				$maxWidth = 1000000;
			
			if($maxHeight == -1) 
				$maxHeight = 100000;			
			
			$this->filename = $filename;
			$this->maxWidth = $maxWidth;
			$this->maxHeight = $maxHeight;
			$this->type = $type;
			$this->pathCache = $pathThumbs;
			
			$filenameThumb = $this->getThumbFilename();
			$filepathNew = $this->pathCache.$filenameThumb;
			
			if(file_exists($filepathNew))
				return($filenameThumb);
			
			if($type == self::TYPE_EXACT || $type == self::TYPE_EXACT_TOP){
				$isSaved = $this->cropImageSaveNew($filepathImage, $filepathNew);
			}
			else
				$isSaved = $this->resizeImageSaveNew($filepathImage, $filepathNew);
							
			if($isSaved)
				return($filenameThumb);
			else
				return("");			
		}
		
		/**
		 * convert png data to png
		 */
		public function convertPngDataToPng($strPngData){
			
			$strPngData = str_replace("data:image/png;base64,", "", $strPngData);
			
			$strPng = base64_decode($strPngData);
			
			return($strPng);
		}
		
		/**
		 * convert png data to png
		 */
		public function convertJPGDataToJPG($strJpgData){
			
			$strJpgData = str_replace("data:image/jpeg;base64,", "", $strJpgData);
			
			$strJpg = base64_decode($strJpgData);
			
			return($strJpg);
		}
		
		
		/**
		 * convert png to jpg
		 * quality from 0 - compression to 70 - quality
		 */
		public function png2jpg($filepathPNG, $filepathJPGOutput, $quality = 70) {
			
		    $image = imagecreatefrompng($filepathPNG);
		    imagejpeg($image, $filepathJPGOutput, $quality);
		    imagedestroy($image);
		}	

		/**
		 * convert png string to jpg
		 */
		public function strPngToStrJpg($strPng, $quality = 70){
			
			$image = imagecreatefromstring($strPng);
			if(empty($image))
				UniteFunctionsUC::throwError("can't convert image");
		    
			UniteFunctionsUC::obStart();
			
			imagejpeg($image, null, $quality);
		    
			$strJpg = ob_get_contents();
			
			ob_end_clean();
		    
			imagedestroy($image);
			
			return($strJpg);
		}
		
	}
