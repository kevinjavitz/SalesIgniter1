<?php
	$appContent = $App->getAppContentFile();
	if(!class_exists('Product')){
		require(sysConfig::getDirFsCatalog() . 'includes/classes/product.php');
	}
?>