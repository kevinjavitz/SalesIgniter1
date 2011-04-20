<?php
	require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
	$currencies = new currencies();
	
	$appContent = $App->getAppContentFile();
?>