<?php
class OrderTotalReservationshipping extends orderTotalModule {

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Reservation Shipping');
		$this->setDescription('Reservation Shipping');
		
		$this->init('reservationshipping');

		if ($this->isInstalled() === true){
			$this->showReservationShipping = $this->getConfigData('MODULE_ORDER_TOTAL_RESERVATIONSHIPPING_STATUS');
			$this->allowReservationShipping = $this->getConfigData('MODULE_ORDER_TOTAL_RESERVATION_SHIPPING_ENABLE');
		}

	}

	public function process() {
		global $order, $appExtension, $userAccount, $onePageCheckout;

		if ($this->allowReservationShipping == 'True' && isset($onePageCheckout->onePage['info']['reservationshipping']['id'])) {
			$order->info['total'] += $onePageCheckout->onePage['info']['reservationshipping']['cost'];

			if($onePageCheckout->onePage['info']['reservationshipping']['cost'] > 0 && ($this->showReservationShipping == 'True') ){
				$this->addOutput(array(
					'title' => $this->getTitle() .'('.$onePageCheckout->onePage['info']['reservationshipping']['title'].')'. ':',
					'text'  => $this->formatAmount($onePageCheckout->onePage['info']['reservationshipping']['cost']),
					'value' => $onePageCheckout->onePage['info']['reservationshipping']['cost']
				));
			}
		}
	}
}
?>