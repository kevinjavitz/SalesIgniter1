<?php
class OrderTotalExtrafees extends OrderTotalModuleBase
{

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Extra Fees');
		$this->setDescription('Extra Fees');

		$this->init('extrafees');
	}

	public function process() {
		global $order, $ShoppingCart;
		//get session exists extra fees.
		//i calculate again the mandatory fees
		$hasFees = false;
		if(isset($ShoppingCart)){
			foreach($ShoppingCart->getProducts() as $cartProduct) {
				$purchaseType = $cartProduct->getPurchaseType();
				if($purchaseType == 'reservation' && $cartProduct->hasInfo('reservationInfo')){
					$hasFees = true;
					break;
				}
			}
		}
		if($hasFees){
		if(Session::exists('pickupFees_time') && Session::get('pickupFees_fee') > 0){
			$order->info['total'] += Session::get('pickupFees_fee');
			$this->addOutput(array(
					'title' => 'Pickup Time - '.Session::get('pickupFees_name'). ':',
					'text' => '<b>' . $this->formatAmount(Session::get('pickupFees_fee')) . '</b>',
					'value' => Session::get('pickupFees_fee')
			));
		}

		if(Session::exists('deliveryFees_time') && Session::get('deliveryFees_fee') > 0){
			$order->info['total'] += Session::get('deliveryFees_fee');
			$this->addOutput(array(
					'title' => 'Delivery Time - '.Session::get('deliveryFees_name'). ':',
					'text' => '<b>' . $this->formatAmount(Session::get('deliveryFees_fee')) . '</b>',
					'value' => Session::get('deliveryFees_fee')
				));
		}

		if(Session::exists('extraFees_time') && Session::get('extraFees_fee') > 0){
			$order->info['total'] += Session::get('extraFees_fee');
			$this->addOutput(array(
					'title' => 'Extra Fee - '.Session::get('extraFees_name'). ':',
					'text' => '<b>' . $this->formatAmount(Session::get('extraFees_fee')) . '</b>',
					'value' => Session::get('extraFees_fee')
				));
		}

		$QExtraFees = Doctrine_Query::create()
			->from('PayPerRentalExtraFees')
			->where('timefees_mandatory = ?', '1')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if(count($QExtraFees) > 0){
			foreach($QExtraFees as $extraFee){
				if($extraFee['timefees_hours'] == 0){
					$order->info['total'] += $extraFee['timefees_fee'];
					if($extraFee['timefees_fee'] > 0){
					$this->addOutput(array(
							'title' => $extraFee['timefees_name']. ':',
							'text' => '<b>' . $this->formatAmount($extraFee['timefees_fee']) . '</b>',
							'value' => $extraFee['timefees_fee']
					));
					}

					continue;
				}
				if(isset($ShoppingCart)){
					foreach($ShoppingCart->getProducts() as $cartProduct) {
						$pID_string = $cartProduct->getIdString();
						$purchaseType = $cartProduct->getPurchaseType();
						$purchaseQuantity = $cartProduct->getQuantity();
						if($purchaseType == 'reservation' && $cartProduct->hasInfo('reservationInfo')){
							$pInfo = $cartProduct->getInfo('reservationInfo');
							$startDate = $pInfo['start_date'];
							$diffHours = floor((strtotime($startDate) - time())/3600);
							if($diffHours < $extraFee['timefees_hours']){
								$order->info['total'] += $extraFee['timefees_fee'];
								if($extraFee['timefees_fee'] > 0){
								$this->addOutput(array(
										'title' => $extraFee['timefees_name']. ':',
										'text' => '<b>' . $this->formatAmount($extraFee['timefees_fee']) . '</b>',
										'value' => $extraFee['timefees_fee']
								));
								}
								break;
							}
						}
					}
				}
			}
		}
	}
}
}

?>