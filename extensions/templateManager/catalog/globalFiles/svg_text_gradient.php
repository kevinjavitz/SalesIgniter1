<?php
header('Content-Type: image/svg+xml;');
echo '<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?' . '>';

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
?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20001102//EN"
	"http://www.w3.org/TR/2000/CR-SVG-20001102/DTD/svg-20001102.dtd">
<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" version="1.2">
	<defs>
		<linearGradient id="gradientDefinition"
			x1="0"
			y1="0"
			x2="300"
			y2="0"
			gradientUnits="userSpaceOnUse"
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
					style="stop-color: <?php echo $color;?>;"
					opacity="<?php echo $opacity;?>"
					/>
				<?php
			}
			?>
		</linearGradient>
	</defs>

	<g transform="translate(50,150)">
		<text id="horizontalText" x="0" y="0"
			fill="url(#gradientDefinition)"
			font-size="96">
			<?php echo $_GET['string'];?>
		</text>
	</g>
</svg>