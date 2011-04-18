<?php
$width = $_GET['w'];
$height = $_GET['h'];
$img = $_SERVER['DOCUMENT_ROOT'] . $_GET['img'];

if (!isset($_GET['noCalc'])){
	$imgInfo = getimagesize($img);

	if ($imgInfo[1] < $height && $imgInfo[0] < $width){
		$width = $imgInfo[0];
		$height = $imgInfo[1];
	}else{
		$ratio = $imgInfo[1] / $imgInfo[0];

		// Set the width and height to the proper ratio
		if (!$width && $height) {
			$ratio = $height / $imgInfo[1];
			$width = intval($imgInfo[0] * $ratio);
		} elseif ($width && !$height) {
			$ratio = $width / $imgInfo[0];
			$height = intval($imgInfo[1] * $ratio);
		} elseif (!$width && !$height) {
			$width = $imgInfo[0];
			$height = $imgInfo[1];
		}

		// Scale the image if not the original size
		if ($imgInfo[0] != $width || $imgInfo[1] != $height) {
			$rx = $imgInfo[0] / $width;
			$ry = $imgInfo[1] / $height;

			if ($rx < $ry) {
				$width = intval($height / $ratio);
			} else {
				$height = intval($width * $ratio);
			}
		}
	}
}

if ($_GET['img'] == 'TEXT'){
	$imgObj = new Imagick();

	$fontColor = new ImagickPixel('#' . $_GET['fontColor']);

	$textObj = new ImagickDraw();
	$textObj->setFont('/home/itweb1/public_html/rentalstore2/extensions/productDesigner/fonts/' . strtolower($_GET['fontFamily']));
	$textObj->setFontSize((($_GET['fontSize'] * 72) / $_GET['scale']) * (float)$_GET['zoom']);
	$textObj->setFillColor($fontColor);
	$metrics = $imgObj->queryFontMetrics($textObj, urldecode($_GET['imageText']), false);
	if (isset($_GET['w'])){
		$textObj->scale($_GET['w']/$metrics['textWidth'], 1);
	}
	$textObj->annotation(1, $metrics['ascender'], urldecode($_GET['imageText']));
	
	$imgObj->newImage($metrics['textWidth'] + 2, $metrics['textHeight'] + 2, 'transparent');
	$imgObj->setImageFormat('png');
	$imgObj->drawImage($textObj);
	
	switch($_GET['textTransform']){
		case 'arc_up':
			$imgObj->distortImage(imagick::DISTORTION_ARC, array(60), true);
			break;
		case 'arc_down':
			$imgObj->rotateImage(new ImagickPixel(), 180);
			$imgObj->distortImage(imagick::DISTORTION_ARC, array(60, 180), true);
			break;
		default:
			break;
	}
	$imgObj->trimImage(0);
	
	if (!$imgObj){
		echo 'Unable to load image';
	}else{
		$imgObj->setImageFormat('PNG');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");								// Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");	// Always modified
		header("Cache-Control: no-store, no-cache, must-revalidate");		// HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);			// HTTP/1.1
		header("Pragma: no-cache");																			// HTTP/1.0
		header('Content-type: image/png');
		//$imgObj->thumbnailImage(150, 50);
		echo $imgObj;
		$imgObj->destroy();
	}
}else{
	$imgObj = new Imagick($img);
	if (!$imgObj){
		echo 'Unable to load image';
	}else{
		$imgObj->setImageFormat('PNG');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");								// Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");	// Always modified
		header("Cache-Control: no-store, no-cache, must-revalidate");		// HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);			// HTTP/1.1
		header("Pragma: no-cache");																			// HTTP/1.0
		header('Content-type: image/png');
		if (isset($_GET['noCalc'])){
			// $imgObj->resampleImage(72, 72, imagick::FILTER_QUADRATIC, 0);
			$imgObj->trimImage(1);
			$imgObj->resizeImage($width, $height, Imagick::FILTER_QUADRATIC, .75);
		}else{
			$imgObj->thumbnailImage($width, $height);
		}
		echo $imgObj;
		$imgObj->destroy();
	}
}
?>