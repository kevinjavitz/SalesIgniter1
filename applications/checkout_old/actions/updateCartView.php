<?php
	if ($ShoppingCart->countContents() == 0){
		$html = 'none';
	}else{
		$html = '';
		ob_start();
		include(sysConfig::getDirFsCatalog() . 'applications/checkout/pages_modules/cart.php');
		$html = ob_get_contents();
		ob_end_clean();
	}
	
	EventManager::attachActionResponse($html, 'html');
?>