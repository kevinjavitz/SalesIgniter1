<?php
	if (isset($_GET['products_id'])){
		$Products = Doctrine_Core::getTable('Products')->findOneByProductsId($_GET['products_id']);
		if ($Products){
			$ProductsToBox = $Products->ProductsToBox;
			$ProductsToBox->delete();
			$ProductsInv = $Products->ProductsInventory;
			$ProductsInv->delete();
			//clean attributes table
			$Products->delete();
		}
		
		$messageStack->addSession('pageStack', 'Product has been removed', 'success');
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'products_id'))), 'redirect');
?>