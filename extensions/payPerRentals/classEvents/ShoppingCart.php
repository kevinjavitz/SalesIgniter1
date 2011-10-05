<?php
	class ShoppingCart_payPerRentals {
		
		public function __construct(){
		}
		
		public function init(){
			
			EventManager::attachEvents(array(
				'CountContents',
				'AddToCartAfterAction',
				'AddToCartBeforeAction'
			), 'ShoppingCart', $this);
		}

		public function AddToCartBeforeAction(&$pID_info, &$pInfo, &$cartProduct){
			$myQty = 0;
			if(isset($_POST['rental_qty'])){
				$pInfo['reservationInfo']['quantity'] = $_POST['rental_qty'];
				$myQty += $_POST['rental_qty'];
			}
			global $messageStack;

			$QModel = Doctrine_Query::create()
			->from('Products')
			->where('products_id = ?', $pID_info['id'])
			->execute();
			if($QModel && (isset($_POST['event_name']) && isset($_POST['event_date']) &&isset( $_POST['start_date']) && isset( $_POST['end_date']) )){
				$Qevent = Doctrine_Query::create()
				->from('PayPerRentalEvents')
				->where('events_name = ?', $_POST['event_name'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				$QProductEvents = Doctrine_Query::create()
				->from('ProductQtyToEvents')
				->where('events_id = ?', $Qevent[0]['events_id'])
				->andWhere('products_model = ?', $QModel[0]['products_model'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				if($QProductEvents && $QProductEvents[0]['qty'] > 0){

					$checkedQty = $myQty;

					$QRes = Doctrine_Query::create()
					->select('count(*) as total')
					->from('OrdersProducts op')
					->leftJoin('op.OrdersProductsReservation opr')
					->where('opr.start_date = ?', date('Y-m-d H:i:s',strtotime($_POST['start_date'])))
					->andWhere('opr.end_date = ?', date('Y-m-d H:i:s',strtotime($_POST['end_date'])))
					->andWhere('op.products_id = ?', $pID_info['id'])
					->andWhereIn('opr.rental_state', array('out', 'reserved'))
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if($QRes){
						if($QProductEvents[0]['qty'] < $checkedQty+$QRes[0]['total']){
							$messageStack->addSession('pageStack','<span style="text-align: justify;font-size:20px;color:red;">There is not enough inventory for the product. <br/>You can still come to our tents at the event because Walk-Up Stock is available on a first come first serve basis from open to close.</span>', 'success');
							tep_redirect(itw_app_link(null, 'products', 'all'));
							itwExit();
						}
					}
				}
			}

			if (isset($pInfo['rental_shipping']) && $_POST['rental_shipping'] !== false) {
				list($module, $method) = explode('_', $_POST['rental_shipping']);
				$pInfo['reservationInfo']['shipping']['module'] = $module;
				$pInfo['reservationInfo']['shipping']['id'] = $method;
			}
			if (isset($_POST['start_date'])){
				$pInfo['reservationInfo']['start_date'] = $_POST['start_date'];
			}

			if (isset($_POST['event_date'])) {
				$pInfo['reservationInfo']['event_date'] = $_POST['event_date'];
			}
			if (isset($_POST['event_name'])) {
				$pInfo['reservationInfo']['event_name'] = $_POST['event_name'];
			}

			if (isset($_POST['event_gate'])) {
				$pInfo['reservationInfo']['event_gate'] = $_POST['event_gate'];
			}

			if (isset($_POST['semester_name'])) {
				$pInfo['reservationInfo']['semester_name'] = $_POST['semester_name'];
			}

			if (isset($_POST['end_date'])) {
				$pInfo['reservationInfo']['end_date'] = $_POST['end_date'];
			}

			if (isset($_POST['rental_qty'])) {
				$pInfo['reservationInfo']['quantity'] = $_POST['rental_qty'];
			}
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
			$pID = $pInfo['id_string'];
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