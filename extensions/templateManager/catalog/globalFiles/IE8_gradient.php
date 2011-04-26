<?php
header('Content-Type: image/png;');

$width = 1;
$height = 150;
if (isset($_GET['width'])){
	$width = $_GET['width'];
}

if (isset($_GET['height'])){
	$height = $_GET['height'];
}

$colorStops = json_decode(urldecode($_GET['colorStops']));
$xStart=0;$yStart=0;$xEnd=0;$yEnd=0;
switch($_GET['angle']){
	case '0':
		$xStart=0;$yStart=0;$xEnd=100;$yEnd=0;
		break;
	case '45':
		$xStart=0;$yStart=100;$xEnd=100;$yEnd=0;
		break;
	case '90':
		$xStart=0;$yStart=100;$xEnd=0;$yEnd=0;
		break;
	case '135':
		$xStart=100;$yStart=100;$xEnd=0;$yEnd=0;
		break;
	case '180':
		$xStart=100;$yStart=0;$xEnd=0;$yEnd=0;
		break;
	case '225':
		$xStart=100;$yStart=0;$xEnd=0;$yEnd=100;
		break;
	case '270':
		$xStart=100;$yStart=0;$xEnd=100;$yEnd=100;
		break;
	case '315':
		$xStart=0;$yStart=0;$xEnd=100;$yEnd=100;
		break;
	case '360':
		$xStart=0;$yStart=0;$xEnd=100;$yEnd=0;
		break;
}
$randomId = time();
ob_start();
echo '<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?' . '>' ;
?>
<svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" version="1.1" viewBox="0 0 1 1" width="<?php echo $width;?>" height="<?php echo $height;?>">
	<defs>
		<linearGradient id="<?php echo $randomId;?>"
			x1="<?php echo $xStart;?>%"
			y1="<?php echo $yStart;?>%"
			x2="<?php echo $xEnd;?>%"
			y2="<?php echo $yEnd;?>%"
			spreadMethod="pad"
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

	<rect x="0" y="0" width="<?php echo $width;?>" height="<?php echo $height;?>" style="fill:url(#<?php echo $randomId;?>);" />
</svg>
<?php
$svgInfo = ob_get_contents();
ob_end_clean();

$im = new Imagick();
$im->setBackgroundColor(new ImagickPixel('transparent'));
$svg = $svgInfo;
$im->readImageBlob($svg);

$im->setImageFormat("png32");

echo $im;
