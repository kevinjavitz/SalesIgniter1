<?php
    $product = new product((int)$_GET['products_id']);
	$purchaseTypeClass = $product->getPurchaseType('reservation');
    if ((isset($_POST['postcode1']) && !empty($_POST['postcode1']) && isset($_POST['iszip']) && $_POST['iszip'] == 'false')){
		$postcode = $_POST['postcode1'];
	}elseif ((isset($_POST['postcode2']) && !empty($_POST['postcode2']))){
		$postcode = $_POST['postcode2'];
	}else{
		$postcode = '';
	}
	$shippingAddressArray = array(
						'entry_street_address' => (isset($_POST['street_address']) && !empty($_POST['street_address']))?$_POST['street_address']: '',
						'entry_postcode' => $postcode,
						'entry_city' => (isset($_POST['city']) && !empty($_POST['city']))?$_POST['city']: '',
						'entry_state' => (isset($_POST['state']) && ($_POST['state'] != 'undefined'))?$_POST['state']: '',
						'entry_country_id' => (isset($_POST['country']) && !empty($_POST['country']))?$_POST['country']: '',
						'entry_zone_id' => (isset($_POST['state']) && ($_POST['state'] != 'undefined'))?$_POST['state']: ''
	);
    $addressBook =& $userAccount->plugins['addressBook'];
	$addressBook->addAddressEntry('delivery', $shippingAddressArray);
	//global $current_product_weight;
	$current_product_weight = $product->getWeight()* $_GET['qty'];
    //OrderShippingModules::calculateWeight();
	$Module = OrderShippingModules::getModule('upsreservation');
	$quotes = array($Module->quote('', $current_product_weight));

 	$html = $purchaseTypeClass->parseQuotes($quotes ,true);
	$nr = 0;
    if (strpos($html, 'upsreservation_method') > 0){
		$nr = 1;
	}

	EventManager::attachActionResponse(array(
		'success' => true,
		'nr' => $nr,
		'html' => $html
	), 'json');
?>