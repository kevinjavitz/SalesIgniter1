<?php
	$aProducts = Doctrine_Core::getTable('Products');
	if (isset($_GET['products_id'])){
			$aProduct = $aProducts->findOneByProductsId((int)$_GET['products_id']);
			$aProduct->ProductsDescription;
			$aProduct->ProductsToBox;
			$aProduct->ProductsToCategories;
			$aProduct->ProductsAdditionalImages;
		    $aProduct2 = $aProduct->copy(true);
		    $aProduct2->save();

		$messageStack->addSession('pageStack', 'Product has been copied', 'success');
	}



	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'products_id'))), 'redirect');
?>