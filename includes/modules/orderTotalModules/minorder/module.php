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
		if($order->info['subtotal'] < $this->orderAmount){
			if(Session::exists('minfee') == false){
			if($messageStack->size('pageStack') == 0){
					$messageStack->addSession('pageStack','You need to have a minimum order amount of:'.$currencies->format($this->orderAmount).'. You can go <a href="'.itw_app_link(null,'products','all').'">here</a> and add more products to the cart. <a href="'.tep_href_link('ext/modules/orderTotal/minorder/minorder.php','minfeeVal='.($this->orderAmount - $order->info['subtotal'])).'">Click here</a> to checkout anyway by adding '.$currencies->format($this->orderAmount - $order->info['subtotal']) .' as a fee');
				}
			}else{
				$order->info['total'] += Session::get('minfee');
				$this->addOutput(array(
					'title' =>  'Minimum Order Fee:',
					'text' => '<b>' . $this->formatAmount(Session::get('minfee')) . '</b>',
					'value' => Session::get('minfee')
				));
			}
			//tep_redirect(itw_app_link(null,'shoppingCart','default'));
		}
	}
}

?>