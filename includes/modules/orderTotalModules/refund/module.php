<?php
class OrderTotalRefund extends OrderTotalModuleBase
{

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Refund Amount');
		$this->setDescription('Order Refund');

		$this->init('refund');
	}

	public function process() {
		global $order;

		$this->addOutput(array(
				'title' => $this->getTitle() . ':',
				'text' => $this->formatAmount($order->info['refund']),
				'value' => $order->info['refund']
			));
	}
}

?>