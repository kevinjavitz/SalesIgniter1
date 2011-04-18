<?php
	class ShoppingCart_payPerRentals {
		
		public function __construct(){
		}
		
		public function init(){
			
			EventManager::attachEvents(array(
				'CountContents',
				'AddToCartAfterAction'
			), 'ShoppingCart', $this);
		}
		
		public function CountContents(&$totalItems){
			global $order;
			if (is_object($order)){
				$reservationProducts = 0;
				if ($order->hasReservation() === true){
					$products = $order->products;
					for ($i=0, $n=sizeof($products); $i<$n; $i++) {
						if ($products[$i]['purchase_type'] == 'reservation'){
							$reservationProducts++;
						}
					}
				}

				if ($totalItems > 1){
					$totalItems -= $reservationProducts;
				}
			}
		}
		public function AddToCartAfterAction(&$pID_info, &$pInfo, &$cartProduct){
			global $messageStack, $ShoppingCart;
			$pID = $_GET['products_id'];
			$isRemoved = false;
			$shoppingProducts = $ShoppingCart->getProducts()->getContents();
			for ($i=0;$i<count($shoppingProducts)-1;$i++){
				$shoppingInfo = $shoppingProducts[$i]->getInfo();
				if($shoppingInfo['products_id'] == $pID){
					$cartInfo = $shoppingProducts[$i]->getInfo();
					break;
				}
			}
			//
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_DIFFERENT_SHIPPING_METHODS') == 'False'){
				//echo print_r($shoppingProducts->getContents());

				if (!empty($cartInfo['reservationInfo'])){
					for ($i=0;$i<count($shoppingProducts)-1;$i++){

						$shoppingInfo = $shoppingProducts[$i]->getInfo();
						if (!empty($shoppingInfo['reservationInfo']) && $shoppingInfo['products_id'] != $pID){
							if ($cartInfo['reservationInfo']['shipping']['id'] != $shoppingInfo['reservationInfo']['shipping']['id']){
								$isRemoved = true;
							}
						}
					}
				}
				if ($isRemoved){
					$ShoppingCart->contents->remove($pID, $pInfo['purchase_type']);
					$messageStack->addSession('pageStack','You cannot add products with different level of service on the same order','error');
				}
			}
			if ($isRemoved === false){
				if (!empty($cartInfo['reservationInfo'])){
					$product = new product($pID);
					$purchaseTypeClass = $product->getPurchaseType('reservation');
					$shippingArray = $purchaseTypeClass->getEnabledShippingMethods();

					if (is_array($shippingArray) && !in_array($cartInfo['reservationInfo']['shipping']['id'], $shippingArray) && !$purchaseTypeClass->shippingIsNone() && !$purchaseTypeClass->shippingIsStore()){
						$ShoppingCart->contents->remove($pID, $pInfo['purchase_type']);
						$messageStack->addSession('pageStack','You are not allowed to use this level of service with this product. Please choose another level of service','error');
					}
				}
			}
		}
	}
?>