<?php
	class predesignThumb {
		
		public function __construct(){
			$this->ppi = 72;
			$this->colorTone = 'light';
			$this->images = array();
			$this->imageWidth = null;
			$this->imageHeight = null;
			$this->imageLocation = 'front';
			$this->productImageDirectory = sysConfig::getDirFsCatalog() . sysConfig::get('DIR_WS_IMAGES');
			$this->useStoreId = Session::get('current_store_id');
		}
		
		public function setPPI($val){
			$this->ppi = $val;
		}
		
		public function setStoreId($val){
			$this->useStoreId = $val;
		}
		
		public function setWidth($val){
			$this->imageWidth = $val;
		}
		
		public function setHeight($val){
			$this->imageHeight = $val;
		}
		
		public function setProductImageLocation($val){
			$this->imageLocation = $val;
		}
		
		public function setProductImageDirectory($val){
			$this->productImageDirectory = $val;
		}
		
		public function loadTextKeys(){
			$QtextKeys = Doctrine_Query::create()
			->from('ProductDesignerPredesignKeys k')
			->leftJoin('k.ProductDesignerPredesignKeysToStores k2s')
			->where('k.set_from = ?', 'admin')
			//->andWhere('k.key_type = ?', 'text')
			->andWhere('k2s.stores_id = ?', $this->useStoreId)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$this->textKeys = $QtextKeys;
		}
		
		public function loadProductInfo($productsId){
			global $appExtension;
			$this->productInfo = Doctrine_Query::create()
			->select('ea.*, p.products_id, p.products_image, p.products_image_back, p.product_designer_color_tone')
			->from('Products p')
			->leftJoin('p.ProductDesignerEditableAreas ea')
			->where('p.products_id = ?', (int)$productsId)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$this->productImage = array(
				'front' => $this->productInfo[0]['products_image'],
				'back' => $this->productInfo[0]['products_image_back']
			);
			$this->colorTone = $this->productInfo[0]['product_designer_color_tone'];
				
			$multiStore = $appExtension->getExtension('multiStore');
			if ($multiStore !== false && $multiStore->isEnabled()){
				if (!isset($_GET['images_id']) || is_numeric($_GET['images_id'])){
					$Qimage = Doctrine_Query::create()
					->select('front_image, back_image, color_tone')
					->from('ProductDesignerProductImages')
					->where('products_id = ?', (int)$this->productInfo[0]['products_id'])
					->andWhere('FIND_IN_SET("' . $this->useStoreId . '", default_set)');

					if (isset($_GET['images_id'])){
						$Qimage->andWhere('images_id = ?', $_GET['images_id']);
					}
					
					$Result = $Qimage->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if ($Result){
						$this->productImage = array(
							'front' => $Result[0]['front_image'],
							'back' => $Result[0]['back_image']
						);
						$this->colorTone = $Result[0]['color_tone'];
					}
				}
			}
		}
		
		public function setupOutputImage($imagesId){
			if (is_numeric($imagesId)){
				$Qimage = Doctrine_Query::create()
				->select('front_image, back_image, color_tone')
				->from('ProductDesignerProductImages');

				if (isset($this->productInfo)){
					$Qimage->where('products_id = ?', (int)$this->productInfo[0]['products_id']);
					$Qimage->andWhere('images_id = ?', (int)$imagesId);
				}else{
					$Qimage->where('images_id = ?', (int)$imagesId);
				}

				$Result = $Qimage->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				if ($Result){
					$this->productImage = array(
						'front' => $Result[0]['front_image'],
						'back' => $Result[0]['back_image']
					);
					$this->colorTone = $Result[0]['color_tone'];
				}
			}
		}
		
		public function addTextItemToImage($item){
			$useColorReplacement = false;
			if (isset($item['useColorReplace'])){
				if ($item['useColorReplace'] == 'true'){
					$useColorReplacement = true;
				}
			}
		
			if (isset($item['textVariable'])){
				$imageText = $item['textVariable'];
				if (is_array($this->textKeys) && sizeof($this->textKeys) > 0){
					foreach($this->textKeys as $keyInfo){
						if ($keyInfo['key_text'] == strtoupper($imageText)){
							$imageText = $keyInfo['ProductDesignerPredesignKeysToStores'][0]['content'];
							$useColorReplacement = ($keyInfo['ProductDesignerPredesignKeysToStores'][0]['use_color_replace'] == '1');
							break;
						}
					}
				}
				
				if (array_key_exists(strtolower($imageText), $_GET)){
					$imageText = $_GET[strtolower($imageText)];
					if (empty($imageText)){
						$imageText = $item['textVariable'];
					}
				}

				if ($imageText == 'PLAYER_NUMBER'){
					$imageText = date('d');
				}elseif ($imageText == 'PLAYER_NAME'){
					$imageText = 'Your Name';
				}
			}else{
				$imageText = $item['imageText'];
			}
			
			$fontColor = $item['fontColor'];
			$fontStrokeColor = (array_key_exists('fontStrokeColor', $item) ? $item['fontStrokeColor'] : false);
		
			if ($useColorReplacement === true){
				$colors = $this->getStoresColors();
			
				$fontColor = $colors['primary'];
				$fontStrokeColor = $colors['secondary'];
			}
			
			$textTransform = null;
			if (isset($item['textTransform']) && !empty($item['textTransform'])){
				$textTransform = $item['textTransform'];
			}
		
			$fontSize = null;
			if (isset($item['fontSize']) && !empty($item['fontSize'])){
				$fontSize = $item['fontSize'];
			}
		
			$fontFamily = null;
			if (isset($item['fontFamily']) && !empty($item['fontFamily'])){
				$fontFamily = $item['fontFamily'];
			}
		
			$fontStrokeWidth = null;
			if (isset($item['fontStroke']) && !empty($item['fontStroke'])){
				$fontStrokeWidth = $item['fontStroke'];
			}
		
			$textObj = new imageTextObj(array(
				'ppi'             => $this->ppi,
				'text'            => $imageText,
				'fontSize'        => $fontSize,
				'fontFamily'      => $fontFamily,
				'fontColor'       => $fontColor,
				'fontStrokeWidth' => $fontStrokeWidth,
				'fontStrokeColor' => $fontStrokeColor,
				'textTransform'   => $textTransform
			));
			return $textObj->draw();
		}
		
		public function addClipartItemToImage($item){
			$useColorReplacement = false;
			if (isset($item['useColorReplace'])){
				if ($item['useColorReplace'] == 'true'){
					$useColorReplacement = true;
				}
			}
		
			if (isset($item['clipartVariable'])){
				$imageClipart = $item['clipartVariable'];
				if (is_array($this->textKeys) && sizeof($this->textKeys) > 0){
					foreach($this->textKeys as $keyInfo){
						if ($keyInfo['key_text'] == strtoupper($imageClipart)){
							$kInfo = $keyInfo['ProductDesignerPredesignKeysToStores'][0];
							if (!empty($kInfo['content_light']) && !empty($kInfo['content_dark'])){
								$imageClipart = $kInfo['content_' . $this->colorTone];
								$useColorReplacement = ($kInfo['use_color_replace_' . $this->colorTone] == '1');
							}else{
								$imageClipart = $kInfo['content'];
								$useColorReplacement = ($kInfo['use_color_replace'] == '1');
							}
							break;
						}
					}
				}
				
				if (array_key_exists(strtolower($imageClipart), $_GET)){
					$imageClipart = $_GET[strtolower($imageClipart)];
				}
				
				$dir = sysConfig::getDirFsCatalog() . 'extensions/productDesigner/images/dynamic/';
			}else{
				/* @TODO: need to do some kind of query for the clipart...... */
				$imageClipart = $item['imageSrc'];
				$dir = sysConfig::getDirFsCatalog() . 'extensions/productDesigner/images/clipart/';
			}
		
			if (file_exists($dir . $imageClipart)){
				if ($useColorReplacement === true){
					$colors = $this->getStoresColors();
	
					if (isset($_GET['primary_color']) && !empty($_GET['primary_color'])){
						$colors['primary'] = $_GET['primary_color'];
					}elseif (isset($_GET['secondary_color']) && !empty($_GET['secondary_color'])){
						$colors['secondary'] = $_GET['secondary_color'];
					}
				}

				$imgObj = new imageClipartObj(array(
					'ppi'                   => (isset($_GET['dpi']) ? $_GET['dpi'] : 72),
					'imageDir'              => $dir,
					'imageFile'             => $imageClipart,
					'useColorReplace'       => $useColorReplacement,
					'colorReplacePrimary'   => ($useColorReplacement === true ? $colors['primary'] : null),
					'colorReplaceSecondary' => ($useColorReplacement === true ? $colors['secondary'] : null),
					'isVariable'            => isset($item['clipartVariable'])
				));
				
				if (isset($item['imageWidth'])){
					$imgObj->setWidth($item['imageWidth']);
				}
				
				if (isset($item['imageHeight'])){
					$imgObj->setHeight($item['imageHeight']);
				}
				
				return $imgObj->draw();
			}
			return false;
		}
		
		public function getStoresColors(){
			global $appExtension;
			$multiStore = $appExtension->getExtension('multiStore');
			if ($this->useStoreId != Session::get('current_store_id')){
				$Qstore = Doctrine_Query::create()
				->from('Stores')
				->where('stores_id = ?', $this->useStoreId)
				->execute(array(), Doctrine::HYDRATE_ARRAY);
				$storeInfo = $Qstore[0];
			}else{
				$storeInfo = $multiStore->storeInfo;
			}
	
			$colorReplacePrimary = $storeInfo['designer_' . $this->colorTone . '_primary_color'];
			$colorReplaceSecondary = $storeInfo['designer_' . $this->colorTone . '_secondary_color'];
			return array(
				'primary'   => $storeInfo['designer_' . $this->colorTone . '_primary_color'],
				'secondary' => $storeInfo['designer_' . $this->colorTone . '_secondary_color']
			);
		}
		
		public function addWatermarkToImage($item){
			$draw = new ImagickDraw();
			$draw->setFontSize($this->ppi);
			
			/* Make the watermark semi-transparent */
			$draw->setFillAlpha(0.2);
			
			/* Set gravity to the center. More about gravity: http://www.imagemagick.org/Usage/annotating/#gravity */
			$draw->setGravity(Imagick::GRAVITY_CENTER);
			
			/* Write the text on the image
			   Position x0,y0 (Because gravity is set to center)
			   Rotation 45 degrees.
			*/
			//$mainImgObj->annotateImage($draw, 0, 0, 45, "No Design Selected");
			return $draw;
		}
		
		public function draw($images){
			if (isset($this->productImage) && !empty($this->productImage[$this->imageLocation])){
				$imgObj = new Imagick($this->productImageDirectory . $this->productImage[$this->imageLocation]);
				$newImage = false;
				$editableArea = $this->productInfo[0]['ProductDesignerEditableAreas'][0];
				foreach($this->productInfo[0]['ProductDesignerEditableAreas'] as $idx => $aInfo){
					if ($aInfo['area_location'] == $this->imageLocation){
						$editableArea = $aInfo;
					}
				}
				$baseX = $editableArea['area_x1'];
				$baseY = $editableArea['area_y1'];
				
				$areaWidthIn = $editableArea['area_width_inches'];
				$areaHeightIn = $editableArea['area_height_inches'];
				$areaWidthPx = $editableArea['area_width'];
				$areaHeightPx = $editableArea['area_height'];
				
				$areaWidthReal = $areaWidthIn * $this->ppi;
				$areaWidthRatio = $areaWidthPx / $areaWidthReal;
			}else{
				$imgObj = new Imagick();
				$newImage = true;
				$baseX = 0;
				$baseY = 0;
			}
			
  			if (!empty($images)){
				if ($newImage === true){
  					$newImageWidth = 0;
					$newImageHeight = 0;
  					for($i=0, $n=sizeof($images); $i<$n; $i++){
						if (isset($images[$i]['itemInfo']['isWatermark']) && $images[$i]['itemInfo']['isWatermark'] === true){
							$imgHeight = $images[$i]['imgObj']->getImageWidth();
							$imgWidth = ($imgHeight/2);
						}else{
 							$imgWidth = $images[$i]['imgObj']->getImageWidth();
							$imgHeight = $images[$i]['imgObj']->getImageHeight();
  						}
 						if ($imgWidth > $newImageWidth){
  							$newImageWidth = $imgWidth;
							$newImageWidth += ($images[$i]['itemInfo']['xPos'] * $this->ppi);
  						}
						$newImageHeight += $imgHeight + ($images[$i]['itemInfo']['yPos'] * $this->ppi);
  					}
					$imgObj->newImage($newImageWidth + 50, $newImageHeight + 50, 'transparent');
  					$areaHeightPx = $imgObj->getImageHeight();
 					$areaWidthPx = $imgObj->getImageWidth();
 				}
 				
				$diffPosY = 0;
  				for($i=0, $n=sizeof($images); $i<$n; $i++){
  					if (isset($images[$i]['itemInfo']['isWatermark']) && $images[$i]['itemInfo']['isWatermark'] === true){
  						$imgObj->annotateImage(
  							$images[$i]['imgObj'],
  							$images[$i]['itemInfo']['xPos'],
  							$images[$i]['itemInfo']['yPos'],
  							$images[$i]['itemInfo']['angle'],
  							$images[$i]['itemInfo']['imageText']
  						);
  					}else{
 						$images[$i]['itemInfo']['yPos'] -= $diffPosY;
 						$images[$i]['itemInfo']['xPos'] *= $this->ppi;
 						$images[$i]['itemInfo']['yPos'] *= $this->ppi;
						
 						$imgHeight = $images[$i]['imgObj']->getImageHeight();
 						$imgWidth = $images[$i]['imgObj']->getImageWidth();

 						if (isset($images[$i]['itemInfo']['clipartVariable'])){
 							if ($imgWidth > $areaWidthPx){
 								$images[$i]['imgObj']->thumbnailImage($areaWidthPx, $imgHeight, true);
								$diffPosY += ($imgHeight - $images[$i]['imgObj']->getImageHeight()) / $this->ppi;
 							}
 						}else{
							if ($imgWidth > $areaWidthPx){
 								$images[$i]['imgObj']->scaleImage($areaWidthPx, $imgHeight * ($imgWidth / $areaWidthPx), true);
								$diffPosY += ($imgHeight - $images[$i]['imgObj']->getImageHeight()) / $this->ppi;
							}
 						}
 						
 						$imgHeight = $images[$i]['imgObj']->getImageHeight();
 						$imgWidth = $images[$i]['imgObj']->getImageWidth();
			
 						$centerVertical = 'false';
 						$centerHorizontal = 'false';
 						if (isset($images[$i]['itemInfo']['centerVertical'])){
	  						$centerVertical = $images[$i]['itemInfo']['centerVertical'];
 						}
 						if (isset($images[$i]['itemInfo']['centerHorizontal'])){
	  						$centerHorizontal = $images[$i]['itemInfo']['centerHorizontal'];
 						}
 					
	 					if ($centerVertical == 'true' && $centerHorizontal == 'false'){
	 						$images[$i]['itemInfo']['yPos'] = ($areaHeightPx/2) - ($imgHeight/2);
	 					}elseif ($centerVertical === 'false' && $centerHorizontal == 'true'){
	 						$images[$i]['itemInfo']['xPos'] = ($areaWidthPx/2) - ($imgWidth/2);
	 					}elseif ($centerVertical == 'true' && $centerHorizontal == 'true'){
	 						$images[$i]['itemInfo']['yPos'] = ($areaHeightPx/2) - ($imgHeight/2);
	 						$images[$i]['itemInfo']['xPos'] = ($areaWidthPx/2) - ($imgWidth/2);
	 					}
  				
	  					$imgObj->compositeImage(
	  						$images[$i]['imgObj'],
	  						Imagick::COMPOSITE_OVER,
	  						$images[$i]['itemInfo']['xPos'] + $baseX,
	  						$images[$i]['itemInfo']['yPos'] + $baseY
	  					);
  					}
  				}
  			}

  			
  			if (!isset($this->productImage)){
  				$imgObj->trimImage(0);
				if (isset($_GET['dpi'])){
					$imgObj->setResolution($_GET['dpi'], $_GET['dpi']);
				}elseif (!is_null($this->imageWidth) && !is_null($this->imageHeight)){
					$imgObj->thumbnailImage($this->imageWidth * $this->ppi, $this->imageHeight * $this->ppi, true);
				}else{
					$imgObj->thumbnailImage(1.1 * $this->ppi, 1.1 * $this->ppi, true);
				}
  			}else{
 				if (!is_null($this->imageWidth) && !is_null($this->imageHeight)){
					$imgObj->thumbnailImage($this->imageWidth * $this->ppi, $this->imageHeight * $this->ppi, true);
  				}
  			}
  			
			return $imgObj;
		}
	}
?>