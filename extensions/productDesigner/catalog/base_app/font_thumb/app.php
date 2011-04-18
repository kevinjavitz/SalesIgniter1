<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$mainImgObj = false;
	
	if (file_exists(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/fonts/' . strtolower($_GET['font']))){
		$fontText = ucwords(str_replace(array('_', '-'), ' ', substr(urldecode($_GET['font']), 0, -4)));
		
		$mainImgObj = new Imagick();
		$mainImgObj->newImage((10*72), (10*72), 'transparent');
		
		$textObj = new ImagickDraw();
		$textObj->setFont(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/fonts/' . strtolower($_GET['font']));
		$textObj->setFontSize(18);
		$textObj->setFillColor(new ImagickPixel('#000000'));
		$textObj->setTextAlignment(Imagick::ALIGN_CENTER);
		$metrics = $mainImgObj->queryFontMetrics($textObj, $fontText, false);
		$textObj->annotation($metrics['textWidth'], $metrics['textHeight'], $fontText);
	
		$mainImgObj->drawImage($textObj);
 		$mainImgObj->trimImage(0);
	}
	
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
?>