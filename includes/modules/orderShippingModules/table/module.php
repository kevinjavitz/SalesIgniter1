<?php
class OrderShippingTable extends OrderShippingModuleBase
{

	private $tableMode;

	private $tableCost;

	private $handlingCost;

	private $quotes;

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Table');
		$this->setDescription('Table Based Shipping');

		$this->init('table');

		if ($this->isEnabled() === true){
			$this->tableMode = $this->getConfigData('MODULE_ORDER_SHIPPING_TABLE_MODE');
			$this->tableCost = $this->getConfigData('MODULE_ORDER_SHIPPING_TABLE_COST');
			$this->handlingCost = $this->getConfigData('MODULE_ORDER_SHIPPING_TABLE_HANDLING');
		}
		$isEnabled = true;
		EventManager::notify('ShippingMethodCheckBeforeConstruct', &$isEnabled);
		$this->setEnabled($isEnabled);
	}

	public function quote($method = '') {
		global $order, $ShoppingCart, $shipping_weight, $shipping_num_boxes;

		if ($this->tableMode == 'Price'){
			$order_total = $ShoppingCart->showTotal();
		}
		else {
			$order_total = $shipping_weight;
		}

		$tableRates = explode(',', $this->tableCost);
		foreach($tableRates as $rate){
			$rInfo = explode(':', $rate);
			if ($order_total <= $rInfo[0]){
				$shipping = $rInfo[1];
				break;
			}
		}

		if ($this->tableMode == 'Weight'){
			$shipping = $shipping * $shipping_num_boxes;
		}

		$this->quotes = array(
			'id' => $this->getCode(),
			'module' => $this->getTitle(),
			'methods' => array(
				array(
					'id' => $this->getCode(),
					'title' => sysLanguage::get('MODULE_ORDER_SHIPPING_TABLE_TEXT_WAY'),
					'cost' => $shipping + $this->handlingCost
				)
			)
		);

		$classId = $this->getTaxClass();
		if ($classId > 0){
			$deliveryAddress = $this->getDeliveryAddress();
			$this->quotes['tax'] = tep_get_tax_rate($classId, $deliveryAddress['country_id'], $deliveryAddress['zone_id']);
		}

		return $this->quotes;
	}
}

?>