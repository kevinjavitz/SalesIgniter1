<?php
	class ShoppingCart_ordersProductsNotes {

		public function __construct(){
		}

		public function init(){
			EventManager::attachEvents(array(
				'AddToCartBeforeAction',
				'UpdateProductBeforeAction'
			), 'ShoppingCart', $this);
		}
		
		public function AddToCartBeforeAction(&$pID_info, &$pInfo, &$cartProduct){
			$pInfo['note'] = '';
		}
		
		public function UpdateProductBeforeAction(&$pID_string, &$pInfo){
			if (array_key_exists('note', $pInfo) && isset($_POST['products_note'][$pID_string])){
				$pInfo['note'] = $_POST['products_note'][$pID_string];
			}
		}
	}
?>