<?php
	$ShoppingCart->updateProduct($_POST['pID'], array(
					'quantity'      => $_POST['qty'],
					'purchase_type' => $_POST['type']
	));

 	ob_start();
	require(sysConfig::getDirFsCatalog() . 'applications/checkout/pages/cart.php');
	$pageHtml = ob_get_contents();
	ob_end_clean();

	EventManager::attachActionResponse(array(
		'success' => true,
		'pageHtml' => $pageHtml
	), 'json');

?>