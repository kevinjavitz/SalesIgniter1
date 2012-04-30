<?php
require('../../../../includes/classes/system_cache.php');

$_GET['width'] = ($_GET['width'] > 1 ? $_GET['width'] : 1);
$_GET['height'] = ($_GET['height'] > 1 ? $_GET['height'] : 75);

$dataHash = 'browser=ie8' .
	'&width=' . $_GET['width'] .
	'&height=' . $_GET['height'] .
	'&angle=' . $_GET['angle'] .
	'&colorStops=' . urldecode($_GET['colorStops']);

$ImageCache = new SystemCache('ie8-gradient-' . md5($dataHash));
if ($ImageCache->loadData() === true){
	$ImageCache->output(false, true);
	exit;
}
else {
	$width = $_GET['width'];
	$height = $_GET['height'];

	$colorStops = json_decode(urldecode($_GET['colorStops']));
	$xStart = 0;
	$yStart = 0;
	$xEnd = 0;
	$yEnd = 0;
	switch($_GET['angle']){
		case '0':
			$xStart = 0;
			$yStart = 0;
			$xEnd = 100;
			$yEnd = 0;
			break;
		case '45':
			$xStart = 0;
			$yStart = 100;
			$xEnd = 100;
			$yEnd = 0;
			break;
		case '90':
			$xStart = 0;
			$yStart = 100;
			$xEnd = 0;
			$yEnd = 0;
			break;
		case '135':
			$xStart = 100;
			$yStart = 100;
			$xEnd = 0;
			$yEnd = 0;
			break;
		case '180':
			$xStart = 100;
			$yStart = 0;
			$xEnd = 0;
			$yEnd = 0;
			break;
		case '225':
			$xStart = 100;
			$yStart = 0;
			$xEnd = 0;
			$yEnd = 100;
			break;
		case '270':
			$xStart = 100;
			$yStart = 0;
			$xEnd = 100;
			$yEnd = 100;
			break;
		case '315':
			$xStart = 0;
			$yStart = 0;
			$xEnd = 100;
			$yEnd = 100;
			break;
		case '360':
			$xStart = 0;
			$yStart = 0;
			$xEnd = 100;
			$yEnd = 0;
			break;
	}
	$random = rand(500, 1000);
	$svgArr = array();
	$i = 0;
	$stops = '';
	$currentStopPos = 0;
	$remainingHeight = $height;
	$prevStopPos = 0;
	foreach($colorStops as $sInfo){
		$color = $sInfo->color;
		$opacity = $sInfo->opacity;
		if (substr($color, 0, 4) == 'rgba'){
			$matches = array();
			preg_match_all('/rgba\((.*),[\s?](.*),[\s?](.*),[\s?](.*)\)/', $color, $matches);
			$color = 'rgb(' . $matches[1][0] . ', ' . $matches[2][0] . ', ' . $matches[3][0] . ')';
			$opacity = $matches[4][0];
		}
		$stops .= '<stop offset="' . $currentStopPos . '%" stop-color="' . $color . '" stop-opacity="' . $opacity . '"/>';
		$i++;
		$currentStopPos = 100;
		if ($i > 1){
			$partHeight = ($height * (($sInfo->pos - $prevStopPos) / 100));
			$prevStopPos = $sInfo->pos;
			$remainingHeight -= $partHeight;
			//echo $height . ' * ( ' . $sInfo->pos / 100 . ' ) = ' . $partHeight . '<br>';
			//echo '<br><br>';
			$svgArr[] = '<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?' . '>' .
				'<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $partHeight . '" viewBox="0 0 1 1" preserveAspectRatio="none">' .
				'<linearGradient id="gradient' . $random . '" gradientUnits="userSpaceOnUse" x1="' . $xStart . '%" y1="' . $yStart . '%" x2="' . $xEnd . '%" y2="' . $yEnd . '%">' .
				$stops .
				'</linearGradient>' .
				'<rect x="0" y="0" width="100%" height="100%" fill="url(#gradient' . $random . ')" />' .
				'</svg>';
			$stops = '';
			$i = 0;
			$currentStopPos = 0;
		}
	}

	$im = new Imagick();
	$im->setBackgroundColor(new ImagickPixel('transparent'));
	foreach($svgArr as $svg){
		$im->readImageBlob($svg);
	}

	$im->resetIterator();
	$combined = $im->appendImages(true);

	$combined->setImageFormat("png32");

	ob_start();
	echo $combined;
	$imageContent = ob_get_contents();
	ob_end_clean();

	if (isset($_GET['width']) && isset($_GET['height'])){
		$ImageCache->setContentType('image/png');
		$ImageCache->setContent($imageContent);
		$ImageCache->setExpires(time() + (60 * 60 * 24 * 2));
		$ImageCache->setLastModified(time());
		$ImageCache->store();
	}

	$ImageCache->output(false, true);
}