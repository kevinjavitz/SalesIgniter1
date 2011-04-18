<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	require(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/catalog/classes/predesignThumb.php');
	require(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/catalog/classes/imageTextObj.php');
	require(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/catalog/classes/imageClipartObj.php');

	$cacheDir = sysConfig::getDirFsCatalog() . 'extensions/productDesigner/images/predesign_cache/';
	$imageName = 'cached';
	if (isset($_GET['products_id'])){
		$imageName .= '_' . $_GET['products_id'];
	}

	if (isset($_GET['predesign_id'])){
		$imageName .= '_' . $_GET['predesign_id'];
	}

	if (Session::exists('current_store_id')){
		$imageName .= '_' . Session::get('current_store_id');
	}

	if (isset($_GET['location']) && $_GET['location'] == 'back'){
		$imageName .= '_back';
	}else{
		$imageName .= '_front';
	}

	if (isset($_GET['width']) && isset($_GET['height'])){
		$imageName .= '_w' . $_GET['width'];
		$imageName .= '_h' . $_GET['height'];
	}

	if (isset($_GET['dpi'])){
		$imageName .= '_dpi' . $_GET['dpi'];
	}

	$imageName .= '.png';

	if (file_exists($cacheDir . $imageName)){
		//$mainImgObj = new Imagick($cacheDir . $imageName);
	}

	if (!isset($mainImgObj) && isset($_GET['predesign_id'])){
		$thumb = new predesignThumb();
		if (isset($_GET['orders_store_id'])){
			$thumb->setStoreId($_GET['orders_store_id']);
		}

		$thumb->loadTextKeys();
		if (isset($_GET['dpi'])){
			$thumb->setPPI($_GET['dpi']);
		}

		if (isset($_GET['location'])){
			$thumb->setProductImageLocation($_GET['location']);
		}

		if ($_GET['predesign_id'] == 'none'){
			$itemInfo = array(
				'imageText' => 'NONE',
				'fontColor' => '#000000',
				'fontSize' => 1,
				'scale' => 1.92,
				'xPos' => 0,
				'yPos' => 0,
				'fontFamily' => 'arial.ttf'
			);
			if (isset($_GET['products_id'])){
				$itemInfo['imageText'] = 'No Design Selected';
				$itemInfo['angle'] = 45;
				$itemInfo['isWatermark'] = true;
				$images[] = array(
					'itemInfo' => $itemInfo,
					'imgObj' => $thumb->addWatermarkToImage($itemInfo)
				);
			}else{
				$images[] = array(
					'itemInfo' => $itemInfo,
					'imgObj' => $thumb->addTextItemToImage($itemInfo)
				);
			}
		}

		if (isset($_GET['products_id'])){
			$thumb->loadProductInfo($_GET['products_id']);
			if (isset($_GET['location']) && $_GET['location'] == 'back'){
				$thumb->setProductImageLocation('back');
			}

		}

		if (isset($_GET['width']) && isset($_GET['height'])){
			$thumb->setWidth($_GET['width'] / 72);
			$thumb->setHeight($_GET['height'] / 72);
		}

		if (isset($_GET['images_id'])){
			$thumb->setupOutputImage($_GET['images_id']);
		}

		$Qdesign = Doctrine_Query::create()
		->select('predesign_settings')
		->from('ProductDesignerPredesigns')
		->where('predesign_id = ?', $_GET['predesign_id'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qdesign){
			$design = $Qdesign[0];

		  	$designSettings = unserialize($design['predesign_settings']);

  			$ySorted = array();
  			foreach($designSettings as $type => $itemInfo){
  				foreach($itemInfo as $item){
  					$img = false;
  					switch($type){
  						case 'text':
  							$img = $thumb->addTextItemToImage($item);
  							break;
  						case 'clipart':
  							$img = $thumb->addClipartItemToImage($item);
  							break;
  					}

  					if ($img !== false){
  						$ySorted[$item['yPos']] = array(
  							'itemInfo' => $item,
  							'imgObj'   => $img
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

  			foreach($sorted as $itemInfo){
  				$images[] = $itemInfo;
  			}
		}

  		$mainImgObj = $thumb->draw($images);
		//$mainImgObj->writeImage($cacheDir . $imageName);
	}

	if (isset($mainImgObj)){
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

			echo $mainImgObj;
			$mainImgObj->destroy();
			itwExit();
		}
	}
?>