<?php
class OrderTotalTotal extends OrderTotalModuleBase
{

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Total');
		$this->setDescription('Order Total');

		$this->init('total');
	}

	public function process() {
		global $order;

		$this->addOutput(array(
				'title' => $this->getTitle() . ':',
				'text' => '<b>' . $this->formatAmount($order->info['total']) . '</b>',
				'value' => $order->info['total']
			));
	}
}

?>