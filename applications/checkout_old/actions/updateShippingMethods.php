<?php
	$html = '';
	ob_start();
	include(sysConfig::getDirFsCatalog() . 'applications/checkout/pages_modules/shipping_method.php');
	$html = ob_get_contents();
	ob_end_clean();
	
	EventManager::attachActionResponse($html, 'html');
?>