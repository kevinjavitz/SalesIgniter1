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
	$randomId = time();
	ob_start();
	echo '<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?' . '>';
	?>
<svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" version="1.1" viewBox="0 0 1 1" width="<?php echo $width;?>" height="<?php echo $height;?>">
	<defs>
		<linearGradient id="<?php echo $randomId;?>"
			x1="<?php echo $xStart;?>%"
			y1="<?php echo $yStart;?>%"
			x2="<?php echo $xEnd;?>%"
			y2="<?php echo $yEnd;?>%"
			>
			<?php
   foreach($colorStops as $sInfo){
			$color = $sInfo->color;
			$opacity = $sInfo->opacity;
			if (substr($color, 0, 4) == 'rgba'){
				$matches = array();
				preg_match_all('/rgba\((.*),[\s?](.*),[\s?](.*),[\s?](.*)\)/', $color, $matches);
				$color = 'rgb(' . $matches[1][0] . ', ' . $matches[2][0] . ', ' . $matches[3][0] . ')';
				$opacity = $matches[4][0];
			}
			?>
			<stop
				offset="<?php echo $sInfo->pos;?>%"
				stop-color="<?php echo $color;?>"
				stop-opacity="<?php echo $opacity;?>"
				/>
			<?php

		}
			?>
		</linearGradient>
	</defs>

	<rect x="0" y="0" width="100%" height="100%" style="fill:url(#<?php echo $randomId;?>);" />
</svg>
<?php
   $svgInfo = ob_get_contents();
	ob_end_clean();

	$im = new Imagick();
	$im->setBackgroundColor(new ImagickPixel('transparent'));
	$svg = $svgInfo;
	$im->readImageBlob($svg);

	$im->setImageFormat("png32");

	ob_start();
	echo $im;
	$imageContent = ob_get_contents();
	ob_end_clean();

	if (isset($_GET['width']) && isset($_GET['height'])){
		$ImageCache->setContentType('image/png');
		$ImageCache->setContent($imageContent);
		$ImageCache->setExpires(time() + (60*60*24*2));
		$ImageCache->setLastModified(time());
		$ImageCache->store();
	}

	$ImageCache->output(false, true);
}