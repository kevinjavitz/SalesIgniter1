<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class imageObj {
		private $imageFormat = 'PNG',
				$ppi = null,
				$dpi = null,
				$imageDir = null,
				$imageFile = null,
				$imageWidth = null,
				$imageHeight = null,
				$imageScale = null;
				
		public function __construct($settings){
			$this->setImageDir($settings['imageDir']);
			$this->setImageFile($settings['imageFile']);
			
			if (isset($settings['ppi'])) $this->setPpi($settings['ppi']);
			if (isset($settings['dpi'])) $this->setDpi($settings['dpi']);
			if (isset($settings['imageWidth'])) $this->setWidth($settings['imageWidth']);
			if (isset($settings['imageHeight'])) $this->setHeight($settings['imageHeight']);
			if (isset($settings['scale'])) $this->setScale($settings['scale']);
		}
		
		public function setPpi($val){
			$this->ppi = $val;
		}
		
		public function setDpi($val){
			$this->dpi = $val;
		}
		
		public function setImageDir($val){
			$this->imageDir = $val;
		}
		
		public function setImageFile($val){
			$this->imageFile = $val;
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
		
		public function draw(){
			$imgObj = new Imagick();
			//$imgObj->setResolution($this->dpi, $this->dpi);
			$imgObj->readImage($this->imageDir . $this->imageFile);
			
			if (is_null($this->imageWidth) || is_null($this->imageHeight)){
				$width = 100;
				$height = 100;
			}elseif (is_null($this->imageScale) === false){
				$width = ($this->imageWidth * $this->ppi) / $this->imageScale;
				$height = ($this->imageHeight * $this->ppi) / $this->imageScale;
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