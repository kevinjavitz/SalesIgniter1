<?php
	class ShoppingCartProduct_payPerRentals {

		public function __construct(){
		}

		public function init(){
			EventManager::attachEvents(array(
				'ProductNameAppend'
			), 'ShoppingCartProduct', $this);
		}
		
		public function ProductNameAppend(&$cartProduct){
			if ($cartProduct->hasInfo('reservationInfo')){
				$resData = $cartProduct->getInfo('reservationInfo');
				if ($resData && !empty($resData['start_date'])){
					$product = new product($cartProduct->getIdString());
					$purchaseTypeClass = $product->getPurchaseType('reservation');
					return $purchaseTypeClass->parse_reservation_info($cartProduct->getIdString(), $resData);
				}
			}
		}
	}
?>