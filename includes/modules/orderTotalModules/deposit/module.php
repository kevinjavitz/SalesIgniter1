<?php
class OrderTotalDeposit extends OrderTotalModuleBase
{
	public $allowDeposit;

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Deposit');
		$this->setDescription('Deposit');

		$this->init('deposit');
		$this->allowDeposit = $this->getConfigData('MODULE_ORDER_TOTAL_DEPOSIT_ENABLE');

	}

	public function process() {
		global $order, $appExtension, $userAccount, $onePageCheckout;
		if ($this->allowDeposit == 'True'){
			$depositValue = $this->getConfigData('MODULE_ORDER_TOTAL_DEPOSIT_VALUE');


			if (substr($depositValue, -1) == '%'){
				$depositValue = (float)substr($depositValue, 0, strlen($depositValue) - 1);
				$fee = $order->info['total'] * $depositValue / 100;
			}
			else {
				$fee = (float)$depositValue;
			}
			$rest = $order->info['total'] - $fee;
			$order->info['total'] = $fee;


			$this->addOutput(array(
						'title' => $this->getTitle() . ':',
						'text' => $this->formatAmount($fee),
						'value' => $fee
			));
			$this->addOutput(array(
					'title' => 'Due to be paid in store:',
					'text' => $this->formatAmount($rest),
					'value' => 0
				));

		}
	}
}

?>