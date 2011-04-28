<?php
	header('Content-Type: image/svg+xml;');
	echo '<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?>' ;
	
	$colorStops = json_decode(urldecode($_GET['colorStops']));
?>
<svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" version="1.1">
	<defs>
		<linearGradient id="myLinearGradient1"
			x1="<?php echo $_GET['start_pos_x'];?>%" 
			y1="<?php echo $_GET['start_pos_y'];?>%" 
			x2="<?php echo $_GET['end_pos_x'];?>%" 
			y2="<?php echo $_GET['end_pos_y'];?>%" 
			spreadMethod="pad"
		>
		<?php
			foreach($colorStops as $sInfo){
		?>
			<stop
				offset="<?php echo $sInfo->pos;?>%" 
				stop-color="<?php echo $sInfo->color;?>" 
				stop-opacity="<?php echo $sInfo->opacity;?>"
			/>
		<?php
			}
		?>
		</linearGradient>
	</defs>
	
	<rect x="0" y="0" width="100%" height="100%" style="fill:url(#myLinearGradient1);" />
</svg>
