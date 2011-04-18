<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	require(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/catalog/classes/imageObj.php');
	require(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/catalog/classes/imageTextObj.php');
	require(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/catalog/classes/imageClipartObj.php');

	$pixelsPerInch = 72;
	
	if (isset($_GET['products_id']) || isset($_GET['orders_products_id'])){
		$mainImgObj = new Imagick();
		
		if (isset($_GET['orders_products_id'])){
			$Qproduct = Doctrine_Query::create()
			->select('design_info, products_id')
			->from('OrdersProducts')
			->where('orders_products_id = ?', (int)$_GET['orders_products_id'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$product = array(
				'custom_design' => unserialize($Qproduct[0]['design_info'])
			);
			$productId = $Qproduct[0]['products_id'];
		}else{
			$productId = $_GET['products_id'];
			$product = $ShoppingCart->getProduct($productId, $_GET['purchaseType']);
		}

  		$ySorted = array();
		$biggestWidth = 0;
  		foreach($product->getInfo('custom_design') as $type => $itemInfo){
  			foreach($itemInfo as $item){
  				$imgObj = false;
  				switch($type){
  					case 'text':
						$imgObj = new imageTextObj(array(
							'ppi'             => $pixelsPerInch,
							'scale'           => $item['scale'],
							'text'            => $item['imageText'],
							'fontSize'        => $item['fontSize'],
							'fontFamily'      => $item['fontFamily'],
							'fontColor'       => $item['fontColor'],
							'fontStrokeWidth' => $item['fontStroke'],
							'fontStrokeColor' => $item['fontStrokeColor'],
							'textTransform'   => $item['textTransform']
						));
  						break;
  					case 'clipart':
 						$imgObj = new imageClipartObj(array(
							'ppi'         => $pixelsPerInch,
							'dpi'         => (isset($_GET['dpi']) ? $_GET['dpi'] : null),
							'imageDir'    => sysConfig::getDirFsCatalog() . 'extensions/productDesigner/images/clipart/',
							'imageFile'   => $item['imageSrc'],
							'scale'       => $item['scale'],
							'imageWidth'  => (isset($item['imageWidth']) ? $item['imageWidth'] : null),
							'imageHeight' => (isset($item['imageHeight']) ? $item['imageHeight'] : null)
						));
  						break;
  					case 'image':
 						$imgObj = new imageObj(array(
							'ppi'         => $pixelsPerInch,
							'dpi'         => (isset($_GET['dpi']) ? $_GET['dpi'] : null),
							'imageDir'    => sysConfig::getDirFsCatalog() . 'extensions/productDesigner/images/uploaded/',
							'imageFile'   => $item['imageSrc'],
							'scale'       => $item['scale'],
							'imageWidth'  => (isset($item['imageWidth']) ? $item['imageWidth'] : null),
							'imageHeight' => (isset($item['imageHeight']) ? $item['imageHeight'] : null)
						));
  						break;
  				}
						
				if (isset($item['scale']) && !empty($item['scale'])){
 					$item['xPos'] = $item['xPos'] / $item['scale'];
 					$item['yPos'] = $item['yPos'] / $item['scale'];
				}
  				
  				if ($imgObj !== false){
  					$ySorted[$item['yPos']] = array(
  						'itemInfo' => $item,
  						'imgObj'   => $imgObj->draw()
  					);
  				}
  			}
  		}
  		ksort($ySorted);
  		
  		$sorted = array();
  		foreach($ySorted as $itemInfo){
  			$sorted[$itemInfo['itemInfo']['zIndex']] = $itemInfo;
  		}
  		ksort($sorted);
  		
  		$images = array();
  		foreach($sorted as $itemInfo){
  			$images[] = $itemInfo;
  		}

  		if (!empty($images)){
  			if (isset($_GET['useProductImage']) && $_GET['useProductImage'] == '1'){
				$productInfo = Doctrine_Query::create()
				->select('ea.*, p.products_id, p.products_image')
				->from('Products p')
				->leftJoin('p.ProductDesignerEditableAreas ea')
				->where('p.products_id = ?', tep_get_prid($productId))
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				
				$mainImgObj = new Imagick(sysConfig::getDirFsCatalog() . 'images/' . $productInfo[0]['products_image']);
				
				$editableArea = $productInfo[0]['ProductDesignerEditableAreas'][0];
				foreach($productInfo[0]['ProductDesignerEditableAreas'] as $idx => $aInfo){
					if ($aInfo['area_location'] == 'front'){
						$editableArea = $aInfo;
					}
				}
				$baseX = $editableArea['area_x1'];
				$baseY = $editableArea['area_y1'];
				
				$areaWidthIn = $editableArea['area_width_inches'];
				$areaHeightIn = $editableArea['area_height_inches'];
				$areaWidthPx = $editableArea['area_width'];
				$areaHeightPx = $editableArea['area_height'];
				
				$areaWidthReal = $areaWidthIn * $pixelsPerInch;
				$areaWidthRatio = $areaWidthPx / $areaWidthReal;
  			}else{
 				$mainImgObj->newImage((10*$pixelsPerInch), (10*$pixelsPerInch), 'transparent');
				$baseX = 0;
 				$baseY = 0;
 			}
  			
  			for($i=0, $n=sizeof($images); $i<$n; $i++){
 				$images[$i]['itemInfo']['xPos'] = ($images[$i]['itemInfo']['xPos'] * $pixelsPerInch) + $baseX;
 				$images[$i]['itemInfo']['yPos'] = ($images[$i]['itemInfo']['yPos'] * $pixelsPerInch) + $baseY;
  				
  				$mainImgObj->compositeImage(
  					$images[$i]['imgObj'],
  					Imagick::COMPOSITE_DEFAULT,
  					$images[$i]['itemInfo']['xPos'],
  					$images[$i]['itemInfo']['yPos']
  				);
  			}
  			//print_r($images);
  			
			$mainImgObj->trimImage(0);
	
			if (!$mainImgObj){
				echo 'Unable to load image';
			}else{
				$mainImgObj->setImageFormat('PNG');
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");				// Date in the past
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");	// Always modified
				header("Cache-Control: no-store, no-cache, must-revalidate");	// HTTP/1.1
				header("Cache-Control: post-check=0, pre-check=0", false);		// HTTP/1.1
				header("Pragma: no-cache");										// HTTP/1.0
				header('Content-type: image/png');
				if (isset($_GET['w']) && isset($_GET['h'])){
					$mainImgObj->scaleImage($_GET['w'], $_GET['h'], true);
				}elseif (isset($_GET['dpi'])){
					$resolution = $mainImgObj->getImageResolution();
					//$mainImgObj->setImageResolution($_GET['dpi'], $_GET['dpi']);
					//print_r($resolution);
					$mainImgObj->resizeImage(
						$mainImgObj->getImageWidth() * ($_GET['dpi'] / $resolution['x']),
						$mainImgObj->getImageHeight() * ($_GET['dpi'] / $resolution['y']),
						Imagick::FILTER_QUADRATIC,
						1
					);
					//$mainImgObj->scaleImage($mainImgObj->getImageWidth() * ($_GET['dpi'] / $resolution['x']), $mainImgObj->getImageHeight() * ($_GET['dpi'] / $resolution['y']));
				}else{
					$mainImgObj->scaleImage(88, 66, true);
				}
				echo $mainImgObj;
				$mainImgObj->destroy();
				itwExit();
			}
		}
	}
?>