<?php
	class ShoppingCart_payPerRentals {
		
		public function __construct(){
		}
		
		public function init(){
			
			EventManager::attachEvents(array(
				'CountContents',
				'AddToCartAfterAction',
				'UpdateProductBeforeAction',
				'AddToCartBeforeAction',
				'OnConstruct'
			), 'ShoppingCart', $this);
		}
	public function OnConstruct(){
		global $userAccount, $ShoppingCart;
		if($userAccount->isLoggedIn()){
			if(sysConfig::get('EXTENSION_PAY_PER_RENTAL_ALLOW_MEMBERSHIP') == 'True'){
				$QQueue = Doctrine_Query::create()
				->from('QueueProductsReservation qpr')
				->where('customers_id = ?', $userAccount->getCustomerId())
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				foreach($QQueue as $iQueue){
					$pInfo = unserialize($iQueue['pinfo']);
					$pInfo['queue_products_reservations_id'] = $iQueue['queue_products_reservations_id'];
					$ShoppingCart->addProduct($iQueue['products_id'],$iQueue['purchase_type'],$iQueue['products_quantity'], $pInfo,true, true);
				}
			}
		}
		}


	public function UpdateProductBeforeAction(&$pID_info, &$pInfo){
		if($pInfo['purchase_type'] == 'reservation'){
			$myQty = 0;
			if(isset($pInfo['quantity'])){
				$pInfo['reservationInfo']['quantity'] = $pInfo['quantity'];
				$myQty += $pInfo['quantity'];
			}
			global $messageStack;

			$QModel = Doctrine_Query::create()
				->from('Products')
				->where('products_id = ?', $pID_info['id'])
				->execute();
			if($QModel && (isset($pInfo['postVal']['event_name']) && isset($pInfo['postVal']['event_date']) &&isset( $pInfo['postVal']['start_date']) && isset( $pInfo['postVal']['end_date']) )){
				$Qevent = Doctrine_Query::create()
					->from('PayPerRentalEvents')
					->where('events_name = ?', $pInfo['postVal']['event_name'])
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
						->where('opr.start_date = ?', date('Y-m-d H:i:s',strtotime($pInfo['postVal']['start_date'])))
						->andWhere('opr.end_date = ?', date('Y-m-d H:i:s',strtotime($pInfo['postVal']['end_date'])))
						->andWhere('op.products_id = ?', $pID_info['id'])
						->andWhereIn('opr.rental_state', array('out', 'reserved'))
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if($QRes){
						if($QProductEvents[0]['qty'] < $checkedQty+$QRes[0]['total']){
							$messageStack->addSession('pageStack',sysLanguage::get('TEXT_NOT_ENOUGH_INVENTORY'), 'success');
							tep_redirect(itw_app_link(null, 'products', 'all'));
							itwExit();
						}
					}
				}
			}else{
				$product = new product($pID_info['id']);
				$purchaseTypeClass = $product->getPurchaseType('reservation');
				if($purchaseTypeClass->hasInventory($myQty) === false){
					$messageStack->addSession('pageStack',sysLanguage::get('TEXT_NOT_ENOUGH_INVENTORY'), 'success');
					tep_redirect(itw_app_link(null, 'products', 'all'));
					itwExit();
				}
			}

			if (isset($pInfo['postVal']['start_date'])){
				$pInfo['reservationInfo']['start_date'] = $pInfo['postVal']['start_date'];
				if(Session::exists('isppr_date_start')){
					Session::set('isppr_date_start', $pInfo['postVal']['start_date']);
				}

			}

			if (isset($pInfo['postVal']['event_date'])) {
				$pInfo['reservationInfo']['event_date'] = $pInfo['postVal']['event_date'];
			}
			if (isset($pInfo['postVal']['event_name'])) {
				$pInfo['reservationInfo']['event_name'] = $pInfo['postVal']['event_name'];
			}

			if (isset($pInfo['postVal']['event_gate'])) {
				$pInfo['reservationInfo']['event_gate'] = $pInfo['postVal']['event_gate'];
			}

			if (isset($pInfo['postVal']['semester_name'])) {
				$pInfo['reservationInfo']['semester_name'] = $pInfo['postVal']['semester_name'];
			}

			if (isset($pInfo['postVal']['pickup'])) {
				$pInfo['reservationInfo']['pickup'] = $pInfo['postVal']['pickup'];
			}
			if (isset($pInfo['postVal']['lp'])) {
				$pInfo['reservationInfo']['lp'] = $pInfo['postVal']['lp'];
			}
			if (isset($pInfo['postVal']['dropoff'])) {
				$pInfo['reservationInfo']['dropoff'] = $pInfo['postVal']['dropoff'];
			}

			if (isset($pInfo['postVal']['end_date'])) {
				$pInfo['reservationInfo']['end_date'] = $pInfo['postVal']['end_date'];
				if(Session::exists('isppr_date_end')){
					Session::set('isppr_date_end', $pInfo['postVal']['end_date']);
				}
			}

			$pInfo['reservationInfo']['quantity'] = $pInfo['quantity'];
		}
	}

		public function AddToCartBeforeAction(&$pID_info, &$pInfo, &$cartProduct){
			if($pInfo['purchase_type'] == 'reservation'){
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
								$messageStack->addSession('pageStack',sysLanguage::get('TEXT_NOT_ENOUGH_INVENTORY'), 'success');
								tep_redirect(itw_app_link(null, 'products', 'all'));
								itwExit();
							}
						}
					}
				}else{
					$product = new product($pID_info['id']);
					$purchaseTypeClass = $product->getPurchaseType('reservation');

					if($purchaseTypeClass->hasInventory($myQty) === false){
						$messageStack->addSession('pageStack',sysLanguage::get('TEXT_NOT_ENOUGH_INVENTORY'), 'success');
						tep_redirect(itw_app_link(null, 'products', 'all'));
						itwExit();
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

				if (isset($_POST['pickup'])) {
					$pInfo['reservationInfo']['inventory_center_pickup'] = $_POST['pickup'];
				}

				if (isset($_POST['lp'])) {
					$pInfo['reservationInfo']['inventory_center_lp'] = $_POST['lp'];
				}
				if (isset($_POST['dropoff'])) {
					$pInfo['reservationInfo']['inventory_center_dropoff'] = $_POST['dropoff'];
				}

				if (isset($_POST['rental_qty'])) {
					$pInfo['reservationInfo']['quantity'] = $_POST['rental_qty'];
				}
			}
		}
		
		public function CountContents(&$totalItems){
			global $order;
			if (is_object($order)){
				$reservationProducts = 0;
				$products = $order->products;
				for ($i=0, $n=sizeof($products); $i<$n; $i++) {
					if ($products[$i]['purchase_type'] == 'reservation'){
						$reservationProducts++;
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
			$shoppingProducts = $ShoppingCart->getProducts();
			for ($i=0;$i<count($shoppingProducts)-1;$i++){
				if(is_object($shoppingProducts[$i])){
					$shoppingInfo = $shoppingProducts[$i]->getInfo();
					if($shoppingInfo['products_id'] == $pID){
						$cartInfo = $shoppingProducts[$i]->getInfo();
						break;
					}
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