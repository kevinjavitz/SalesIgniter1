<?php
class OrderTotalLoworderfee extends OrderTotalModuleBase
{

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Low Order Fee');
		$this->setDescription('Low Order Fee');

		$this->init('loworderfee');

		if ($this->isInstalled() === true){
			$this->taxClass = $this->getConfigData('MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS');
			$this->allowFees = $this->getConfigData('MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE');
			$this->feesDestination = $this->getConfigData('MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION');
			$this->lowOrderAmount = $this->getConfigData('MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER');
			$this->lowOrderFee = $this->getConfigData('MODULE_ORDER_TOTAL_LOWORDERFEE_FEE');
		}
	}

	public function process() {
		global $order;

		if ($this->allowFees == 'True'){
			switch($this->feesDestination){
				case 'National':
					if ($order->delivery['country_id'] == sysConfig::get('STORE_COUNTRY')) {
						$pass = true;
					}
					break;
				case 'International':
					if ($order->delivery['country_id'] != sysConfig::get('STORE_COUNTRY')) {
						$pass = true;
					}
					break;
				case 'Both':
					$pass = true;
					break;
				default:
					$pass = false;
					break;
			}

			if (($pass == true) && (($order->info['total'] - $order->info['shipping_cost']) < $this->lowOrderAmount)){
				$tax = tep_get_tax_rate($this->taxClass, $order->delivery['country']['id'], $order->delivery['zone_id']);
				$tax_description = tep_get_tax_description($this->taxClass, $order->delivery['country']['id'], $order->delivery['zone_id']);

				$order->info['tax'] += tep_calculate_tax($this->lowOrderFee, $tax);
				$order->info['tax_groups']["$tax_description"] += tep_calculate_tax($this->lowOrderFee, $tax);
				$order->info['total'] += $this->lowOrderFee + tep_calculate_tax($this->lowOrderFee, $tax);

				$this->addOutput(array(
						'title' => $this->getTitle() . ':',
						'text' => $this->formatAmount(tep_add_tax($this->lowOrderFee, $tax)),
						'value' => tep_add_tax($this->lowOrderFee, $tax)
					));
			}
		}
	}
}

?>