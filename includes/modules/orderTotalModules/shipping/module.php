<?php
class OrderTotalShipping extends OrderTotalModuleBase
{

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Shipping');
		$this->setDescription('Order Shipping Cost');

		$this->init('shipping');

		if ($this->isInstalled() === true){
			$this->allowFreeShipping = $this->getConfigData('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING');
			$this->freeShipDestination = $this->getConfigData('MODULE_ORDER_TOTAL_SHIPPING_DESTINATION');
			$this->freeShipAmount = $this->getConfigData('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER');
		}
	}

	public function process() {
		global $order, $shippingModules, $ShoppingCart, $onePageCheckout;

		$userAccount = &Session::getReference('userAccount');
		$addressBook = $userAccount->plugins['addressBook'];
		if ($addressBook->entryExists('delivery') === true){
			$deliveryAddress = $addressBook->getAddress('delivery');
		}
		else {
			$deliveryAddress = $addressBook->getAddress('billing');
		}

		if (is_object($onePageCheckout)){
			$shippingInfo = $onePageCheckout->onePage['info']['shipping'];
		}
		elseif (Session::exists('pointOfSale')) {
			$pointOfSale = &Session::getReference('pointOfSale');
			$shippingInfo = $pointOfSale->order['info']['shipping'];
		}

		if (!empty($shippingInfo)){
			$shippingCost = $shippingInfo['cost'];
			$shippingModule = $shippingInfo['id'];
			$shippingTitle = $shippingInfo['title'];
		}

		if (isset($shippingTitle)){
			if ($this->allowFreeShipping == 'True'){
				switch($this->freeShipDestination){
					case 'National':
						if ($deliveryAddress['country_id'] == sysConfig::get('STORE_COUNTRY')) {
							$pass = true;
						}
						break;
					case 'International':
						if ($deliveryAddress['country_id'] != sysConfig::get('STORE_COUNTRY')) {
							$pass = true;
						}
						break;
					case 'Both':
						$pass = true;
						break;
					default:
						$pass = false;
						break;
				}

				if (($pass == true) && (($order->info['total'] - $shippingCost) >= $this->freeShipAmount)){
					$order->info['shipping_method'] = $this->getTitle();
					$order->info['total'] -= $shippingCost;
					$order->info['shipping_cost'] = 0;
				}
			}

			$module = substr($shippingModule, 0, strpos($shippingModule, '_'));
			$module = OrderShippingModules::getModule($module);

			$order->info['total'] += $shippingCost;

			$taxClassId = $module->getTaxClass();
			if ($taxClassId > 0) {
				$shipping_tax = tep_get_tax_rate($taxClassId, $deliveryAddress['entry_country_id'], $deliveryAddress['entry_zone_id']);
				$shipping_tax_description = tep_get_tax_description($taxClassId, $deliveryAddress['entry_country_id'], $deliveryAddress['entry_zone_id']);

				$order->info['tax'] += tep_calculate_tax($shippingCost, $shipping_tax);
				$order->info['tax_groups']["$shipping_tax_description"] += tep_calculate_tax($shippingCost, $shipping_tax);
				$order->info['total'] += tep_calculate_tax($shippingCost, $shipping_tax);

				if (sysConfig::get('DISPLAY_PRICE_WITH_TAX') == 'true') {
					$shippingCost += tep_calculate_tax($shippingCost, $shipping_tax);
				}
			}

			$this->addOutput(array(
					'module' => $module->getCode(),
					'method' => substr($shippingModule, strpos($shippingModule, '_') + 1),
					'title' => $shippingTitle . ':',
					'text' => $this->formatAmount($shippingCost),
					'value' => $shippingCost
				));
		}

		$totalShippingCost = 0;
		EventManager::notify('OrderTotalShippingProcess', &$totalShippingCost, &$shippingCost);

		if (isset($shippingCost) && $shippingCost > 0 && $totalShippingCost > 0){
			$totalShippingCost += $shippingCost;
			$this->addOutput(array(
					'title' => '<b>Total Shipping Cost</b>:',
					'text' => $this->formatAmount($totalShippingCost),
					'value' => $totalShippingCost
				));
		}
	}
}

?>