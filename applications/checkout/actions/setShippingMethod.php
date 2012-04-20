<?php
	$addressBook =& $userAccount->plugins['addressBook'];
	$deliveryAddress = $addressBook->getAddress('delivery');

	$order->loadProducts();
	$moduleShipping = OrderTotalModules::getModule('shipping');

	if ($moduleShipping->allowFreeShipping == 'True') {
		$pass = false;

		switch ($moduleShipping->freeShipDestination) {
			case 'National':
				if ($deliveryAddress['entry_country_id'] == sysConfig::get('STORE_COUNTRY')) {
					$pass = true;
				}
				break;
			case 'International':
				if ($deliveryAddress['entry_country_id'] != sysConfig::get('STORE_COUNTRY')) {
					$pass = true;
				}
				break;
			case 'Both':
				$pass = true;
				break;
		}

		$free_shipping = false;
		if ($pass == true && $order->info['total'] >= $moduleShipping->freeShipAmount) {
			$free_shipping = true;
			sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/modules/order_total/ot_shipping.xml');
		}
	} else {
		$free_shipping = false;
	}

	$onePageCheckout->onePage['info']['shipping'] = false;
	if (OrderShippingModules::countEnabled() > 0 || $free_shipping == true) {
		if (strpos($_POST['shipping_method'], '_')) {
			$selected = $_POST['shipping_method'];
			list($module, $method) = explode('_', $selected);
			$Module = OrderShippingModules::getModule($module);
			if ($Module->isEnabled() || $free_shipping == true) {
				if ($free_shipping == true) {
					$quote['methods'][0] = array(
						'id'    => 'free_free',
						'title' => sysLanguage::get('FREE_SHIPPING_TITLE'),
						'cost'  => '0'
					);
				} else {
					$quote = $Module->quote($method);
				}

				if (isset($quote['error'])) {
					$onePageCheckout->onePage['info']['shipping'] = false;
				} else {
					if (isset($quote['methods'][0]['title']) && isset($quote['methods'][0]['cost'])) {
						$onePageCheckout->onePage['info']['shipping'] = array(
							'id'     => $selected,
							'module' => $module,
							'method' => $method,
							'title'  => (($free_shipping == true) ?  $quote['methods'][0]['title'] : $quote['module'] . ' (' . $quote['methods'][0]['title'] . ')'),
							'cost'   => $quote['methods'][0]['cost']
						);
					}
				}
			} else {
				$onePageCheckout->onePage['info']['shipping'] = false;
			}
		}
	}
    EventManager::notify('UpdateTotalsCheckout');

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>