<?php
	$ShoppingCart->removeProduct($_POST['pID'], $_POST['type']);

    ob_start();
	require(sysConfig::getDirFsCatalog() . 'applications/checkout/pages/cart.php');
	$pageHtml = ob_get_contents();
	ob_end_clean();

	$shoppingProducts = $ShoppingCart->getProducts()->getContents();
	$reservationProducts = 0;
	for ($i = 0; $i < count($shoppingProducts); $i++) {
		//if ($shoppingProducts[$i]->getPurchaseType() == 'reservation') {
		$reservationProducts++;
		//		}
	}

 	if ($reservationProducts == 0){
		 $empty = true;
	 }else{
		 $empty = false;
	 }

	EventManager::attachActionResponse(array(
		'success' => true,
		'empty'	=> $empty,
		'pageHtml' => $pageHtml
	), 'json');
?>