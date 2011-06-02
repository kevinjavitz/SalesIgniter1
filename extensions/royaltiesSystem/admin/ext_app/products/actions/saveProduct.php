<?php
$royalties =& $Product->RoyaltiesSystemProductsRoyalties;
$royalties->delete();

$i=0;
foreach($_POST['content_provider_id'] as $purchaseType => $content_provider_id){
	$royalties[$i]->content_provider_id = $content_provider_id;
	$royalties[$i]->products_price_rental = $_POST['products_price_rental'];
	$royalties[$i]->purchase_type = $purchaseType;
	if(tep_not_null($_POST['royalty_fee'][$purchaseType]) && $_POST['royalty_fee'][$purchaseType] > 0)
		$royalties[$i]->royalty_fee = $_POST['royalty_fee'][$purchaseType];
	$i++;
}
$Product->save();
?>