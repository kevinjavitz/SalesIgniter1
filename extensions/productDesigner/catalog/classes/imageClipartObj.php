<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class imageClipartObj {
		private $imageFormat = 'PNG',
				$primaryColorToReplace = 'black',
				$secondaryColorToReplace = 'white',
				$dpi = null,
				$ppi = 72,
				$imageDir = null,
				$imageFile = null,
				$useColorReplace = false,
				$colorReplacePrimary = null,
				$colorReplaceSecondary = null,
				$isVariable = false,
				$imageWidth = null,
				$imageHeight = null,
				$imageScale = null;
				
		public function __construct($settings){
			//$this->setDpi($settings['dpi']);
			$this->setImageDir($settings['imageDir']);
			$this->setImageFile($settings['imageFile']);
			
			if (isset($settings['ppi'])) $this->setPpi($settings['ppi']);
			if (isset($settings['imageWidth'])) $this->setWidth($settings['imageWidth']);
			if (isset($settings['imageHeight'])) $this->setHeight($settings['imageHeight']);
			if (isset($settings['useColorReplace'])) $this->setColorReplace($settings['useColorReplace']);
			if (isset($settings['colorReplacePrimary'])) $this->setColorReplacePrimary($settings['colorReplacePrimary']);
			if (isset($settings['colorReplaceSecondary'])) $this->setColorReplaceSecondary($settings['colorReplaceSecondary']);
			if (isset($settings['isVariable'])) $this->setIsVariable($settings['isVariable']);
			if (isset($settings['scale'])) $this->setScale($settings['scale']);
		}
		
		public function setDpi($val){
			$this->dpi = $val;
		}
		
		public function setPpi($val){
			$this->ppi = $val;
		}
		
		public function setImageDir($val){
			$this->imageDir = $val;
		}
		
		public function setImageFile($val){
			$this->imageFile = $val;
		}
		
		public function setColorReplace($val){
			$this->useColorReplace = $val;
		}
		
		public function setColorReplacePrimary($val){
			$this->colorReplacePrimary = '#' . $val;
		}
		
		public function setColorReplaceSecondary($val){
			$this->colorReplaceSecondary = '#' . $val;
		}
		
		public function setIsVariable($val){
			$this->isVariable = $val;
		}
		
		public function setWidth($val){
			$this->imageWidth = $val;
		}
		
		public function setHeight($val){
			$this->imageHeight = $val;
		}
		
		public function setScale($val){
			$this->imageScale = $val;
		}
		
		public function getPpi(){
			return $this->ppi;
		}
		
		public function draw(){
			$imgObj = new Imagick();
			//$imgObj->setResolution($this->dpi, $this->dpi);
			$imgObj->readImage($this->imageDir . $this->imageFile);
			
			if ($this->useColorReplace === true){
				$imgObj->paintOpaqueImage($this->primaryColorToReplace, $this->colorReplacePrimary, 1);
				$imgObj->paintOpaqueImage($this->secondaryColorToReplace, $this->colorReplaceSecondary, 1);
			}

			if ($this->isVariable === true){
				$this->imageWidth = $imgObj->getImageWidth() / $this->ppi;
				$this->imageHeight = $imgObj->getImageHeight() / $this->ppi;
			}
			
			$resolution = $imgObj->getImageResolution();
			if (is_null($this->imageWidth) || is_null($this->imageHeight)){
				$width = 1.1 * $this->ppi;
				$height = 1.1 * $this->ppi;
			}elseif (is_null($this->imageScale) === false){
				$width = ($this->imageWidth / $this->imageScale) * $this->ppi;
				$height = ($this->imageHeight / $this->imageScale) * $this->ppi;
			}else{
				$width = $this->imageWidth * $this->ppi;
				$height = $this->imageHeight * $this->ppi;
			}
			$imgObj->scaleImage($width, $height);
			$imgObj->trimImage(1);
				
			return $imgObj;
		}
	}
?>