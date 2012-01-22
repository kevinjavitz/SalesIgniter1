<?php
class OrderTotalPaymentfee extends OrderTotalModuleBase
{

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Payment Fee');
		$this->setDescription('Payment Fee');

		$this->init('paymentfee');

		if ($this->isInstalled() === true){
			$this->showPaymentFee = $this->getConfigData('MODULE_ORDER_TOTAL_PAYMENTFEE_STATUS');
			$this->allowPaymentFee = $this->getConfigData('MODULE_ORDER_TOTAL_PAYMENTFEE_ENABLE');
		}
	}

	public function process() {
		global $order, $appExtension, $userAccount, $onePageCheckout;

		if ($this->allowPaymentFee == 'True' && isset($onePageCheckout->onePage['info']['payment']['id'])){
			$paymentFee = explode(',', $this->getConfigData('MODULE_ORDER_TOTAL_PAYMENTFEE_VALUE'));
			$val = '0';
			foreach($paymentFee as $sPayment){
				$method_value = explode('-', $sPayment);
				if ($method_value[0] == $onePageCheckout->onePage['info']['payment']['id']){
					$val = $method_value[1];
					break;
				}
			}

			if (substr($val, -1) == '%'){
				$val = (float)substr($val, 0, strlen($val) - 1);
				$fee = $order->info['total'] * $val / 100;
			}
			else {
				$fee = (float)$val;
			}

			$order->info['total'] += $fee;

			if ($fee > 0 && ($this->showPaymentFee == 'True')){
				$this->addOutput(array(
						'title' => $this->getTitle() . ':',
						'text' => $this->formatAmount($fee),
						'value' => $fee
					));
			}
		}
	}
}

?>