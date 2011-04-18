<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	require(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/catalog/classes/imageTextObj.php');
	require(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/catalog/classes/imageClipartObj.php');
	require(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/catalog/classes/imageObj.php');

	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");								// Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");	// Always modified
	header("Cache-Control: no-store, no-cache, must-revalidate");		// HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);			// HTTP/1.1
	header("Pragma: no-cache");																			// HTTP/1.0
	header('Content-type: image/png');

	$width = (isset($_GET['w']) ? $_GET['w'] : '');
	$height = (isset($_GET['h']) ? $_GET['h'] : '');
	$pixelsPerInch = 72;

	if ($_GET['img'] == 'TEXT'){
		$fontSize = $_GET['fontSize'];
		if ($_GET['fontColor'] == 'primary' || $_GET['fontColor'] == 'secondary'){
			$fontColor = '000000';
		}else{
			$fontColor = $_GET['fontColor'];
		}
		
		$fontStrokeWidth = 0;
		$fontStrokeColor = '';
		if (isset($_GET['fontStroke']) && $_GET['fontStroke'] > 0){
			$fontStrokeWidth = $_GET['fontStroke'];
			if ($_GET['fontStrokeColor'] == 'primary' || $_GET['fontStrokeColor'] == 'secondary'){
				$fontStrokeColor = '000000';
			}else{
				$fontStrokeColor = $_GET['fontStrokeColor'];
			}
		}
		
		$imgClass = new imageTextObj(array(
			'ppi'             => $pixelsPerInch,
			'text'            => $_GET['imageText'],
			'fontSize'        => $fontSize,
			'fontFamily'      => $_GET['fontFamily'],
			'fontColor'       => $fontColor,
			'fontStrokeWidth' => $fontStrokeWidth,
			'fontStrokeColor' => $fontStrokeColor,
			'textTransform'   => $_GET['textTransform']
		));
	}elseif ($_GET['img'] == 'CLIPART'){
		if (isset($_GET['clipartVariable'])){
			$fontSize = 1;
			
			$imgClass = new imageTextObj(array(
				'ppi'             => $pixelsPerInch,
				'text'            => $_GET['clipartVariable'],
				'fontSize'        => $fontSize,
				'fontFamily'      => 'arial.ttf',
				'fontColor'       => '000000',
				'fontStrokeWidth' => 0,
				'fontStrokeColor' => null,
				'textTransform'   => $_GET['textTransform']
			));
		}else{
			$imageDir = sysConfig::getDirFsCatalog();
			if (isset($_GET['fileDir'])){
				$imageDir .= $_GET['fileDir'];
			}else{
				$imageDir .= 'extensions/productDesigner/images/clipart/';
			}
			$imgClass = new imageClipartObj(array(
				'dpi'                   => (isset($_GET['dpi']) ? $_GET['dpi'] : null),
				'imageDir'              => $imageDir,
				'imageFile'             => $_GET['file'],
				'useColorReplace'       => false,
				'colorReplacePrimary'   => null,
				'colorReplaceSecondary' => null,
				'isVariable'            => false
			));
		}
	}elseif ($_GET['img'] == 'IMAGE'){
		$imageDir = sysConfig::getDirFsCatalog();
		if (isset($_GET['fileDir'])){
			$imageDir .= $_GET['fileDir'];
		}else{
			$imageDir .= 'extensions/productDesigner/images/uploaded/';
		}
		$imgClass = new imageObj(array(
			'dpi'                   => (isset($_GET['dpi']) ? $_GET['dpi'] : null),
			'imageDir'              => $imageDir,
			'imageFile'             => $_GET['file'],
			'useColorReplace'       => false,
			'colorReplacePrimary'   => null,
			'colorReplaceSecondary' => null,
			'isVariable'            => false
		));
	}
	
	if ($imgClass){
		$imgObj = $imgClass->draw();
		
		if (($_GET['img'] == 'CLIPART' && !isset($_GET['clipartVariable'])) || $_GET['img'] == 'IMAGE'){
			if (empty($width) || empty($height)){
				// $imgObj->resampleImage(72, 72, imagick::FILTER_QUADRATIC, 0);
				$imgObj->trimImage(1);
			
				$scale = false;
				if (empty($width)){
					$scale = true;
					$width = 100;
				}
			
				if (empty($height)){
					$scale = true;
					$height = 100;
				}
			
				if ($scale === true){
					$imgObj->thumbnailImage($width, $height, true);
				}else{
					$imgObj->resizeImage($width * 36, $height * 36, Imagick::FILTER_QUADRATIC, .75);
				}
			}else{
				$width *= $pixelsPerInch;
				$height *= $pixelsPerInch;
				if (isset($_GET['scale'])){
					$width = $width / $_GET['scale'];
					$height = $height / $_GET['scale'];
				}
		
				if (isset($_GET['zoom'])){
					$width *= $_GET['zoom'];
					$height *= $_GET['zoom'];
				}
		
				$imgObj->resizeImage($width, $height, Imagick::FILTER_QUADRATIC, .75);
				//$imgObj->thumbnailImage($width, $height);
				//$imgObj->scaleImage($width, $height, true);
			}
		}
	}
			
	if (!$imgObj){
		echo 'Unable to load image';
	}else{
		$imgObj->setImageFormat('PNG');
		//$imgObj->thumbnailImage(150, 50);
		echo $imgObj;
		$imgObj->destroy();
	}
	itwExit();
?>