<?php
	ob_start();
	include(sysConfig::getDirFsCatalog() . 'applications/checkout/pages_modules/shipping_address.php');
	$html = ob_get_contents();
	ob_end_clean();
	
	EventManager::attachActionResponse($html, 'html');
?>