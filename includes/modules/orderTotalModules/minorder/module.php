<?php
class OrderTotalMinorder extends OrderTotalModuleBase
{
	public $orderAmount;
	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Min Order');
		$this->setDescription('Min Order');

		$this->init('minorder');
		$this->orderAmount = $this->getConfigData('MODULE_ORDER_TOTAL_MINORDER_AMOUNT');
	}

	public function process() {
		global $order, $messageStack, $currencies;
		if($order->info['total'] < $this->orderAmount){
			if($messageStack->size('pageStack') == 0){
				$messageStack->addSession('pageStack','You need to have a minimum order amount of:'.$currencies->format($this->orderAmount));
			}
			//tep_redirect(itw_app_link(null,'shoppingCart','default'));
		}
	}
}

?>