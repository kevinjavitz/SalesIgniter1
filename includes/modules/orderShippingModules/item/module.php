<?php
class OrderShippingItem extends OrderShippingModuleBase
{

	private $perItemCost;

	private $handelingFee;

	private $quotes;

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Per Item');
		$this->setDescription('Per Item Shipping');

		$this->init('item');

		if ($this->isEnabled() === true){
			$this->perItemCost = $this->getConfigData('MODULE_ORDER_SHIPPING_ITEM_COST');
			$this->handelingFee = $this->getConfigData('MODULE_ORDER_SHIPPING_ITEM_HANDLING');
		}
	}

	public function quote($method = '') {
		global $order, $ShoppingCart;

		$total_count = $ShoppingCart->countContents();

		$this->quotes = array(
			'id' => $this->getCode(),
			'module' => $this->getTitle(),
			'methods' => array(
				array(
					'id' => $this->getCode(),
					'title' => sysLanguage::get('MODULE_ORDER_SHIPPING_ITEM_TEXT_WAY'),
					'cost' => ($this->perItemCost * $total_count) + $this->handelingFee
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