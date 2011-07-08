<?php
/*
	Product Purchase Type: Used

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
require(sysConfig::getDirFsCatalog() . 'includes/classes/product/purchase_types/used.php');

class OrderCreatorProductPurchaseTypeUsed extends PurchaseType_used {

	public function addToOrdersProductCollection(&$ProductObj, &$CollectionObj){
		if(!isset($_POST['estimateOrder'])){
			$this->inventoryCls->addStockToCollection($ProductObj, $CollectionObj);
		}
	}
}
?>