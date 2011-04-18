<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class imageTextObj {
		private $imageFormat = 'PNG',
				$ppi = 72,
				$scale = 1,
				$imageText = 'NONE',
				$fontFamily = 'arial.ttf',
				$fontSize = 72,
				$fontStrokeWidth = null,
				$fontStrokeColor = '#000000',
				$textTransform = 'striaght';

		public function __construct($settings){
			$this->setPixelsPerInch($settings['ppi']);
			$this->setText($settings['text']);
			$this->setFontSize($settings['fontSize']);
			$this->setFontFamily($settings['fontFamily']);
			$this->setFontColor($settings['fontColor']);
			$this->setFontStrokeWidth($settings['fontStrokeWidth']);
			$this->setFontStrokeColor($settings['fontStrokeColor']);
			$this->setTranformEffect($settings['textTransform']);
			if (isset($settings['scale'])){
				$this->setScale($settings['scale']);
			}
		}

		public function setImageFormat($val){
			$this->imageFormat = $val;
		}

		public function setScale($val){
			$this->scale = $val;
		}

		public function setPixelsPerInch($val){
			$this->ppi = $val;
		}

		public function setText($val){
			$this->imgText = urldecode($val);
		}

		public function setFontFamily($val){
			$this->fontFamily = strtolower($val);
		}

		public function setFontSize($val){
			$this->fontSize = ($val * $this->ppi);
			if (is_null($this->scale) === false){
				$this->fontSize = $this->fontSize / $this->scale;
			}elseif (isset($_GET['scale'])){
				$this->fontSize = $this->fontSize / $_GET['scale'];
			}

			if (isset($_GET['zoom'])){
				$this->fontSize *= $_GET['zoom'];
			}
		}

		public function setFontColor($val){
			$this->fontColor = '#' . $val;
		}

		public function setFontStrokeWidth($val){
			$this->fontStrokeWidth = $val;
		}

		public function setFontStrokeColor($val){
			$this->fontStrokeColor = '#' . $val;
		}

		public function setTranformEffect($val){
			if ($val == 'arc_up' || $val == 'arc_down'){
				$this->textTransform = $val;
			}
		}

		public function draw(){
			$imgObj = new Imagick();

			$textObj = new ImagickDraw();
			$textObj->setFont(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/fonts/' . $this->fontFamily);
			$textObj->setFontSize($this->fontSize);
			$textObj->setTextAlignment(Imagick::ALIGN_CENTER);

			$textObj->setFillColor(new ImagickPixel($this->fontColor));

			if (is_null($this->fontStrokeWidth) === false){
				$textObj->setStrokeColor(new ImagickPixel($this->fontStrokeColor));
				$textObj->setStrokeWidth($this->fontStrokeWidth);
			}

			$metrics = $imgObj->queryFontMetrics($textObj, $this->imgText, false);
			$textObj->annotation($metrics['textWidth'], $metrics['textHeight'], $this->imgText);

			$imgObj->newImage($metrics['textWidth']*2, $metrics['textHeight']*2, 'transparent');
			$imgObj->drawImage($textObj);
			$imgObj->setImageFormat($this->imageFormat);
			//$textImgObj->setImageOpacity(.8);

			if (is_null($this->textTransform) === false){
				switch($this->textTransform){
					case 'arc_up':
						$imgObj->distortImage(imagick::DISTORTION_ARC, array(90), true);
						break;
					case 'arc_down':
						$imgObj->rotateImage(new ImagickPixel(), 180);
						$imgObj->distortImage(imagick::DISTORTION_ARC, array(60, 180), true);
						break;
					default:
						break;
				}
			}
			$imgObj->trimImage(0);

			return $imgObj;
		}
	}
?>