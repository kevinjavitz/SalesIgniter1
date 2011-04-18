<?php
	class ShoppingCartDatabase_payPerRentals {

		public function __construct(){
		}

		public function init(){
			EventManager::attachEvents(array(
				'InsertBasketBeforeProcess',
				'GetCartFromDatabase'
			), 'ShoppingCartDatabase', $this);
		}
		
		public function InsertBasketBeforeProcess(&$insert, &$pInfo){
			$insert->reservation_info = (isset($pInfo['reservationInfo']) ? serialize($pInfo['reservationInfo']) : '');
		}
		
		public function GetCartFromDatabase(&$cartContent, $basketId, $product){
			if (!empty($product['reservation_info'])){
				$cartContent['reservationInfo'] = unserialize($product['reservation_info']);
			}
		}
	}
?>