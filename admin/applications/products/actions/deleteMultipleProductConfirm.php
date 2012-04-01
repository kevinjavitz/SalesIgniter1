<?php
if(isset($_POST['selectedProducts'])){
		foreach($_POST['selectedProducts'] as $productId){
			$Products = Doctrine_Core::getTable('Products')->findOneByProductsId($productId);
			if ($Products){
				$ProductsToBox = $Products->ProductsToBox;
				$ProductsToBox->delete();
				$ProductsInv = $Products->ProductsInventory;
				$ProductsInv->delete();
				//clean attributes table
				$Products->delete();
			}
		}
		$messageStack->addSession('pageStack', 'Products has been removed', 'success');
	}

$json = array(
	'success' => true
);
EventManager::attachActionResponse($json, 'json');
?>