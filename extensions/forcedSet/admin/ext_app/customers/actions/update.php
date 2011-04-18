<?php

	include(sysConfig::getDirFsCatalog().'extensions/forcedSet/admin/classes/user_extended.php');

	$userAccountExt = new rentalStoreUserExtended($userAccount);
	$userAccountExt->loadPlugins();
	if(isset($_POST['allowOne'])){
		$userAccountExt->setAllow(1);
	}else{
		$userAccountExt->setAllow(0);
	}
	$userAccountExt->updateCustomerAccountExt();

?>