<?php
	ob_start();
	include(sysConfig::getDirFsCatalog() . 'applications/checkout/pages_modules/pickup_address.php');
	$html = ob_get_contents();
	ob_end_clean();	
	EventManager::attachActionResponse($html, 'html');
?>