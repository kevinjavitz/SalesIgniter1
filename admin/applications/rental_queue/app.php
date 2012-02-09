<?php
	$appContent = $App->getAppContentFile();
    set_time_limit(0);
	require(sysConfig::getDirFsAdmin() . 'includes/classes/table_block.php');
	require(sysConfig::getDirFsAdmin() . 'includes/classes/box.php');
	require(sysConfig::getDirFsAdmin() . 'includes/classes/split_page_results.php');

		$QUsePickupRequests = Doctrine_Query::create()
		->from('PickupRequests pr')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$usePickupRequest = false;
		if(count($QUsePickupRequests) > 0){
			$usePickupRequest = true;
		}

	if (isset($_GET['cID'])){
		require(sysConfig::getDirFsCatalog() . 'includes/classes/product.php');

		$userAccount = new rentalStoreUser($_GET['cID']);
		$userAccount->loadPlugins();
		$membership =& $userAccount->plugins['membership'];
		$addressBook =& $userAccount->plugins['addressBook'];
		
		require(sysConfig::getDirFsAdmin() . 'includes/classes/rental_queue.php');
		$rentalQueue = new rentalQueue_admin($_GET['cID']);
	}
?>