<?php
	$Ext = $appExtension->getExtension('uspsCass');
	
	$billingAddress = array(
		'entry_company' => $_POST['billing_company'],
		'entry_street_address' => $_POST['billing_street_address'],
		'entry_city' => $_POST['billing_city'],
		'entry_postcode' => $_POST['billing_postcode'],
		'entry_country_id' => $_POST['billing_country'],
		'entry_state' => $_POST['billing_state']
	);
	
	$cassBillingAddress = $Ext->getCassAddresses($billingAddress);
	if (isset($cassBillingAddress['errorMsg'])){
		$success = false;
		$addresses = 'Billing Address Error' . "\n\n" . $cassBillingAddress['errorMsg'];
	}else{
		$success = true;
		$addresses = array(
			array(
				'billing_company' => $cassBillingAddress[0]['entry_company'],
				'billing_street_address' => $cassBillingAddress[0]['entry_street_address'],
				'billing_city' => $cassBillingAddress[0]['entry_city'],
				'billing_postcode' => $cassBillingAddress[0]['entry_postcode'],
				'billing_country' => $cassBillingAddress[0]['entry_country_id'],
				'billing_state' => $cassBillingAddress[0]['entry_state']
			)
		);
	}
	
	if ($success === true && isset($_POST['shipping_diff'])){
		$deliveryAddress = array(
			'entry_company' => $_POST['shipping_company'],
			'entry_street_address' => $_POST['shipping_street_address'],
			'entry_city' => $_POST['shipping_city'],
			'entry_postcode' => $_POST['shipping_postcode'],
			'entry_country_id' => $_POST['shipping_country'],
			'entry_state' => $_POST['shipping_state']
		);
			
		$cassDeliveryAddress = $Ext->getCassAddresses($deliveryAddress);
		if (isset($cassDeliveryAddress['errorMsg'])){
			$success = false;
			$addresses = 'Shipping Address Error' . "\n\n" . $cassDeliveryAddress['errorMsg'];
		}else{
			$success = true;
			$addresses[] = array(
				'shipping_company' => $cassDeliveryAddress[0]['entry_company'],
				'shipping_street_address' => $cassDeliveryAddress[0]['entry_street_address'],
				'shipping_city' => $cassDeliveryAddress[0]['entry_city'],
				'shipping_postcode' => $cassDeliveryAddress[0]['entry_postcode'],
				'shipping_country' => $cassDeliveryAddress[0]['entry_country_id'],
				'shipping_state' => $cassDeliveryAddress[0]['entry_state']
			);
		}
	}
	
	EventManager::attachActionResponse(array(
		'success' => $success,
		'addresses' => $addresses
	), 'json');
?>