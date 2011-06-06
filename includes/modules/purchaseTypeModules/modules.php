<?php
if (!class_exists('PurchaseTypeAbstract')){
	require(dirname(__FILE__) . '/PurchaseTypeAbstract.php');
}

class PurchaseTypeModules extends SystemModulesLoader {
	public static $dir = 'purchaseTypeModules';
	public static $classPrefix = 'PurchaseType_';
	
}
?>