<?php
class OrderTotalSubtotal extends OrderTotalModule {

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Sub-Total');
		$this->setDescription('Order Sub-Total');
		
		$this->init('subtotal');
	}

	public function process() {
		global $order;

		$this->addOutput(array(
			'title' => $this->getTitle() . ':',
			'text'  => $this->formatAmount($order->info['subtotal']),
			'value' => $order->info['subtotal']
		));
	}
}
?>