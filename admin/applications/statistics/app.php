<?php
	$appContent = $App->getAppContentFile();
	
	require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
	$currencies = new currencies();
?>