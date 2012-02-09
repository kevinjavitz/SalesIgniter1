<?php
class OrderShippingFlat extends OrderShippingModuleBase
{

	private $quotes;

	private $shipCost;

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Flat Rate');
		$this->setDescription('Flat Rate Shipping');

		$this->init('flat');

		if ($this->isEnabled() === true){
			$this->shipCost = $this->getConfigData('MODULE_ORDER_SHIPPING_FLAT_COST');
		}
	}

	public function quote($method = '') {
		global $order;

		$this->quotes = array(
			'id' => $this->getCode(),
			'module' => $this->getTitle(),
			'methods' => array(
				array(
					'id' => $this->getCode(),
					'title' => sysLanguage::get('MODULE_ORDER_SHIPPING_FLAT_TEXT_WAY'),
					'cost' => $this->shipCost
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