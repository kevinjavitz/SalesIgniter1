<?php
	if (isset($_GET['products_id'])){
		$Products = Doctrine_Core::getTable('Products')->findOneByProductsId($_GET['products_id']);
		if ($Products){
			$ProductsToBox = $Products->ProductsToBox;
			$ProductsToBox->delete();
			$Products->delete();
		}
		
		$messageStack->addSession('pageStack', 'Product has been removed', 'success');
	}

	if (USE_CACHE == 'true') {
		tep_reset_cache_block('categories');
		tep_reset_cache_block('also_purchased');
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'products_id'))), 'redirect');
?>