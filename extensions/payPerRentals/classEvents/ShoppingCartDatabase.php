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
			$insert->reservation_info = (isset($pInfo) ? serialize($pInfo) : '');
		}
		
		public function GetCartFromDatabase(&$cartContent, $basketId, $product){
			if (!empty($product['reservation_info'])){
				$dataArr = unserialize($product['reservation_info']);
				$cartContent['reservationInfo'] = $dataArr['reservationInfo'];
				$cartContent['price'] = $dataArr['price'];
				$cartContent['final_price'] = $dataArr['final_price'];

			}
		}
	}
?>