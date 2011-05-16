<?php
/*
	Product Purchase Type: Rental

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
require(sysConfig::getDirFsCatalog() . 'includes/classes/product/purchase_types/rental.php');

class OrderCreatorRentalMembershipProduct extends PurchaseType_rental {

	public function addToOrdersProductCollection(&$ProductObj, &$CollectionObj){
	}
}
?>