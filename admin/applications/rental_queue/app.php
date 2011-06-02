<?php
	$appContent = $App->getAppContentFile();
    set_time_limit(0);
	if (isset($_GET['cID'])){
		require('../includes/classes/product.php');

		$userAccount = new rentalStoreUser($_GET['cID']);
		$userAccount->loadPlugins();
		$membership =& $userAccount->plugins['membership'];
		$addressBook =& $userAccount->plugins['addressBook'];
		
		require('includes/classes/rental_queue.php');
		$rentalQueue = new rentalQueue_admin($_GET['cID']);
	}
?>