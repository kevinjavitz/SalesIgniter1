<?php
/*
	Product Purchase Type: Member Stream

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
require(sysConfig::getDirFsCatalog() . 'includes/classes/product/purchase_types/member_stream.php');

class OrderCreatorProductPurchaseTypeMember_stream extends PurchaseType_member_stream {

	public function addToOrdersProductCollection(&$ProductObj, &$CollectionObj){
	}
}
?>