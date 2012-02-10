<?php
class OrderTotalTax extends OrderTotalModuleBase
{

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Tax');
		$this->setDescription('Order Tax');

		$this->init('tax');
	}

	public function process() {
		global $order, $currencies;
		reset($order->info['tax_groups']);
		foreach($order->info['tax_groups'] as $key => $value){
			if ($value > 0){
				$order->info['total'] +=$value;
				$this->addOutput(array(
						'title' => $key . ':',
						'text' => $this->formatAmount($value),
						'value' => $value
					));
			}
		}
	}
}

?>