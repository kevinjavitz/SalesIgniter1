<?php
class OrderShippingCustom extends OrderShippingModuleBase
{

	private $methods = array();

	private $quotes;

	private $allowOther;

	private $pricingMethod;

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Custom');
		$this->setDescription('Custom Shipping Methods');

		$this->init('custom');

		if ($this->isEnabled() === true){
			$this->allowOther = $this->getConfigData('MODULE_ORDER_SHIPPING_CUSTOM_ALLOW_OTHER');
			$this->pricingMethod = $this->getConfigData('MODULE_ORDER_SHIPPING_CUSTOM_PRICING_METHOD');

			$Qmethods = Doctrine_Query::create()
				->from('ModulesShippingCustomMethods')
				->orderBy('sort_order')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qmethods){
				foreach($Qmethods as $mInfo){
					$this->methods[$mInfo['method_id']] = array(
						'text' => $mInfo['method_text'],
						'status' => $mInfo['method_status'],
						'cost' => $mInfo['method_cost'],
						'sort_order' => $mInfo['sort_order'],
						'default' => $mInfo['method_default']
					);
				}
			}
		}
	}

	public function getMethods() {
		return $this->methods;
	}

	public function quote($method = '') {
		global $order;

		$this->quotes = array(
			'id' => $this->getCode(),
			'module' => $this->getTitle(),
			'methods' => array()
		);

		foreach($this->methods as $methodId => $mInfo){
			if ($mInfo['status'] == 'True' && ($method == 'method' . $methodId || $method == '')){
				if ($this->pricingMethod == 'Product'){
					$perItem = $mInfo['cost'];
					$shippingCost = 0;
					for($i = 0, $n = sizeof($order->products); $i < $n; $i++){
						$shippingCost += ($order->products[$i]['quantity'] * $perItem);
					}
				}
				else {
					$shippingCost = $mInfo['cost'];
				}

				$this->quotes['methods'][] = array(
					'id' => 'method' . $methodId,
					'title' => $mInfo['text'],
					'cost' => $shippingCost
				);
			}
		}

		if ($this->allowOther == 'True' && ($method == 'methodOther' || $method == '')){
			$this->quotes['methods'][] = array(
				'id' => 'methodOther',
				'title' => 'Other' . ($method == '' ? ' (Please provide details in the comment box below)' : ''),
				'cost' => 0
			);
		}

		$classId = $this->getTaxClass();
		if ($classId > 0){
			$deliveryAddress = $this->getDeliveryAddress();
			$this->quotes['tax'] = tep_get_tax_rate($classId, $deliveryAddress['country_id'], $deliveryAddress['zone_id']);
		}

		return $this->quotes;
	}
}

?>