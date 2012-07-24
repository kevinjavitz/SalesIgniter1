<?php
    if(!class_exists('rentalStoreUserExtended')){
		require(sysConfig::getDirFsCatalog().'extensions/inventoryCenters/admin/classes/user_extended.php');
	}

	$userAccountExt = new rentalStoreUserExtended($userAccount);
	$userAccountExt->loadPlugins();
if(isset($_POST['isProvider'])){
	$userAccountExt->setProvider(1);
}else{
	$userAccountExt->setProvider(0);
}
$userAccountExt->updateCustomerAccountExt();

?>