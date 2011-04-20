<?php
	require(sysConfig::getDirFsAdmin() . 'includes/classes/table_block.php');
	require(sysConfig::getDirFsAdmin() . 'includes/classes/box.php');
	require(sysConfig::getDirFsAdmin() . 'includes/classes/split_page_results.php');

	$appContent = $App->getAppContentFile();

	if (isset($_GET['cID'])){
		require(sysConfig::getDirFsCatalog() . 'includes/classes/product.php');
		
		$Qproducts = tep_db_query('select distinct products_id from ' . TABLE_RENTAL_QUEUE);
		if (tep_db_num_rows($Qproducts) > 0){
			$product_inventory_array = array();
			while($products = tep_db_fetch_array($Qproducts)){
				$product = new product($products['products_id'], 'rental');
				$purchaseTypeClass = $product->getPurchaseType('rental');
				if (!isset($product_inventory_array[$product->getID()])){
					$product_inventory_array[$product->getID()] = $purchaseTypeClass->getCurrentStock();
				}else{
					$product_inventory_array[$product->getID()] += $purchaseTypeClass->getCurrentStock();
				}
			}
		}
		
		$userAccount = new rentalStoreUser($_GET['cID']);
		$userAccount->loadPlugins();
		$membership =& $userAccount->plugins['membership'];
		$addressBook =& $userAccount->plugins['addressBook'];
		
		require(sysConfig::getDirFsAdmin() . 'includes/classes/rental_queue.php');
		$rentalQueue = new rentalQueue_admin($_GET['cID']);
	}
?>