<?php
	$ShoppingCart->removeProduct($_GET['pID'], $_GET['type']);

	/*
	 * @TODO: Temporary until i can make it update php and js with the new shipping options, only an issue if 
	          there was a reservation and normal product in the cart and one was removed to make it only one 
	          reservation or new product in the cart
	if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
		$json .= '{ "success": true, "products": "' . $ShoppingCart->countContents() . '" }';
	}else{
		tep_redirect(itw_app_link(null, 'checkout', 'default'));
	}
	*/
	$json = array(
		'success' => true,
		'products' => $ShoppingCart->countContents(),
		'redirect' => itw_app_link(null, 'checkout', 'default', 'SSL')
	);
	EventManager::attachActionResponse($json, 'json');
?>