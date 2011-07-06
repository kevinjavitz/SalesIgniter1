<?php
	$addressBook =& $userAccount->plugins['addressBook'];
	$deliveryAddress = $addressBook->getAddress('delivery');

	$order->loadProducts();
	if (sysConfig::get('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') == 'true') {
		$pass = false;

		switch (sysConfig::get('MODULE_ORDER_TOTAL_SHIPPING_DESTINATION')) {
			case 'national':
				if ($deliveryAddress['entry_country_id'] == sysConfig::get('STORE_COUNTRY')) {
					$pass = true;
				}
				break;
			case 'international':
				if ($deliveryAddress['entry_country_id'] != sysConfig::get('STORE_COUNTRY')) {
					$pass = true;
				}
				break;
			case 'both':
				$pass = true;
				break;
		}

		$free_shipping = false;
		if ($pass == true && $order->info['total'] >= sysConfig::get('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER')) {
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
					$quote[0]['methods'][0] = array(
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
	OrderTotalModules::process();

	EventManager::attachActionResponse(array(
		'success' => true,
		'orderTotalRows' => OrderTotalModules::output()
	), 'json');
?>