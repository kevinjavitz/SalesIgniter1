<?php
	$appContent = $App->getAppContentFile();
    define('TABLE_SHIPPING_MANIFEST','shipping_manifest');
    require( sysConfig::getDirFsCatalog() . 'extensions/fedEX/admin/classes/fedexdc.php');     
?>