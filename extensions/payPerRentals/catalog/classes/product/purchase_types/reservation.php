<?php
/*
	Pay Per Rentals Version 1
	Product Purchase Type: Reservation

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PurchaseType_reservation extends PurchaseTypeAbstract {
	public $typeLong = 'reservation';
	public $typeName;
	public $typeShow;

	private $enabledShipping = array();

	public function __construct($ProductCls, $forceEnable = false){

		$productInfo = $ProductCls->productInfo;
		$this->enabled = ($forceEnable === true ? true : (in_array($this->typeLong, $productInfo['typeArr'])));
		$this->typeName = sysLanguage::get('PURCHASE_TYPE_RESERVATION_NAME');
		$this->typeShow = sysLanguage::get('PURCHASE_TYPE_RESERVATION_SHOW');

		if ($this->enabled === true){
			$this->productInfo = array(
				'id'          => $productInfo['products_id'],
				'overbooking' => $productInfo['products_onetime_overbooking'],
				'taxRate'     => $productInfo['taxRate']
			);

			$Qproduct = Doctrine_Query::create()
			->from('ProductsPayPerRental')
			->where('products_id = ?', $this->productInfo['id'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qproduct){
				$this->payperrental = $Qproduct[0];
				//modify here
				if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
					$Module = OrderShippingModules::getModule('zonereservation');
				} else{
					$Module = OrderShippingModules::getModule('upsreservation');
				}
				
				if (!isset($Module) || !is_object($Module)){
					$this->enabledShipping = false;
				}else{
					$this->shipModuleCode = $Module->getCode();
					$this->enabledShipping = explode(',', $this->payperrental['shipping']);

					if (empty($this->enabledShipping)){
						$this->enabledShipping = false;
					}
				}
			}

			$this->inventoryCls = new ProductInventory(
				$this->productInfo['id'],
				$this->typeLong,
				$productInfo['products_inventory_controller']
			);

		}
	}

	public function getMinRentalDays(){
		return $this->payperrental['min_rental_days'];
	}

	public function getEnabledShippingMethods(){
		return $this->enabledShipping;
	}

	public function getMaxShippingDays($starting){

		return ReservationUtilities::getMaxShippingDays(
			$this->productInfo['id'],
			$starting,
			$this->overBookingAllowed()
		);

	}

	public function shippingIsStore(){
		return ($this->payperrental['shipping'] == 'store');
	}

	public function shippingIsNone(){
		return ($this->payperrental['shipping'] == 'false');
	}

	public function checkoutAfterProductName(&$cartProduct){
		if ($cartProduct->hasInfo('reservationInfo')){
			$resData = $cartProduct->getInfo('reservationInfo');
			if ($resData && !empty($resData['start_date'])){
				return $this->parse_reservation_info($cartProduct->getIdString(), $resData);
			}
		}
	}

	public function shoppingCartAfterProductName(&$cartProduct){
		if ($cartProduct->hasInfo('reservationInfo')){
			$resData = $cartProduct->getInfo('reservationInfo');
			if ($resData && !empty($resData['start_date'])){
				return $this->parse_reservation_info($cartProduct->getIdString(), $resData);
			}
		}
	}

	private function formatOrdersReservationArray($resData){
		$returningArray = array(
			'start_date' => (isset($resData[0]['start_date']) ? $resData[0]['start_date'] : date('Ymd')),
			'end_date' => (isset($resData[0]['end_date']) ? $resData[0]['end_date'] : date('Ymd')),
			'rental_state' => (isset($resData[0]['rental_state']) ? $resData[0]['rental_state'] : null),
			'date_shipped' => (isset($resData[0]['date_shipped']) ? $resData[0]['date_shipped'] : null),
			'date_returned' => (isset($resData[0]['date_returned']) ? $resData[0]['date_returned'] : null),
			'broken' => (isset($resData[0]['broken']) ? $resData[0]['broken'] : 0),
			'parent_id' => (isset($resData[0]['parent_id']) ? $resData[0]['parent_id'] : null),
			'deposit_amount' => $this->getDepositAmount(),
			'semester_name'	=>    (isset($resData[0]['semester_name']) ? $resData[0]['semester_name'] : ''),
			'event_name'	=>    (isset($resData[0]['event_name']) ? $resData[0]['event_name'] : ''),
			'event_date'	=>    (isset($resData[0]['event_date']) ? $resData[0]['event_date'] : date('Ymd')),
			'shipping' => array(
				'module' => 'reservation',
				'id' => (isset($resData[0]['shipping_method'])? $resData[0]['shipping_method'] : 'method1'),
				'title' => (isset($resData[0]['shipping_method_title']) ? $resData[0]['shipping_method_title'] : null),
				'cost' => (isset($resData[0]['shipping_cost']) ? $resData[0]['shipping_cost'] : 0),
				'days_before' => (isset($resData[0]['shipping_days_before']) ? $resData[0]['shipping_days_before'] : 0),
				'days_after' => (isset($resData[0]['shipping_days_after']) ? $resData[0]['shipping_days_after'] : 0)
			)
		);

		EventManager::notify('ReservationFormatOrdersReservationArray', &$returningArray, $resData);
		return $returningArray;
	}

	public function orderAfterProductName(&$orderedProduct){
		$resData = $orderedProduct->getInfo('OrdersProductsReservation');
		if ($resData && !empty($resData[0]['start_date'])){
			$resInfo = $this->formatOrdersReservationArray($resData);
			return $this->parse_reservation_info(
				$orderedProduct->getProductsId(),
				$resInfo
			);
		}
	}

	public function orderAfterEditProductName(&$orderedProduct){
		global $currencies;
		$return = '';
		$resInfo = null;
		if ($orderedProduct->hasInfo('OrdersProductsReservation')){
			$resData = $orderedProduct->getInfo('OrdersProductsReservation');
			$resInfo = $this->formatOrdersReservationArray($resData);
		}else{
			$resData = $orderedProduct->getPInfo();
			//print_r($orderedProduct);
			if(isset($resData['reservationInfo'])){
				$resInfo = $resData['reservationInfo'];
			}
		}
		$id = $orderedProduct->getId();

		$return .= '<br /><small><b><i><u>' . sysLanguage::get('TEXT_INFO_RESERVATION_INFO') . '</u></i></b>&nbsp;' . '</small>';
		/*This part will have to be changed for events*/



		/**/

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'False'){
				if (is_null($resInfo) === false){
					$start = date_parse($resInfo['start_date']);
					$end = date_parse($resInfo['end_date']);
					$startTime = mktime($start['hour'], $start['minute'], $start['second'], $start['month'], $start['day'], $start['year']);
					$endTime = mktime($end['hour'], $end['minute'], $end['second'], $end['month'], $end['day'], $end['year']);
					$return .= '<br /><small><i> - Dates ( Start,End ) <input type="text" class="ui-widget-content reservationDates" name="product[' . $id . '][reservation][dates]" value="' . date('m/d/Y H:i:s', $startTime) . ',' . date('m/d/Y H:i:s', $endTime) . '"></i></small><div class="selectDialog"></div>';
				}else{
					$return .= '<br /><small><i> - Dates ( Start,End ) <input type="text" class="ui-widget-content reservationDates" name="product[' . $id . '][reservation][dates]" value=""></i></small><div class="selectDialog"></div>';
				}
			}else{
			$Qevent = Doctrine_Query::create()
			->from('PayPerRentalEvents')
			->orderBy('events_date')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$eventb = htmlBase::newElement('selectbox')
			->setName('product[' . $id . '][reservation][events]')
			->addClass('eventf');
			//->attr('id', 'eventz');
			$eventb->addOption('0','Select an Event');
			if (count($Qevent) > 0){
				foreach($Qevent as $qev){
					$eventb->addOption($qev['events_id'], $qev['events_name']);
				}
			}
			if (isset($resInfo['event_name']) && !empty($resInfo['event_name'])){
				$QeventSelected = Doctrine_Query::create()
				->from('PayPerRentalEvents')
				->where('events_name = ?', $resInfo['event_name'])
				->fetchOne();

				if ($QeventSelected){
					$eventb->selectOptionByValue($QeventSelected->events_id);
				}
			}
			$return .= '<br /><small><i> - Events '.$eventb->draw().'</i></small>';
		}

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
			$Module = OrderShippingModules::getModule('zonereservation');
		} else{
			$Module = OrderShippingModules::getModule('upsreservation');
		}



		if ($this->shippingIsNone() === false && $this->shippingIsStore() === false){
			$shipInput = '';
			if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
				$selectBox = htmlBase::newElement('selectbox')
				->addClass('ui-widget-content reservationShipping')
				->setName('product[' . $id . '][reservation][shipping]');

				if (isset($Module) && is_object($Module)){
					$quotes = $Module->quote();
					foreach($quotes['methods'] as $method){
						$selectBox->addOption(
							$method['id'],
							$method['title'] . ' ( ' . $currencies->format($method['cost']) . ' )',
							false,
							array(
								'days_before' => $method['days_before'],
								'days_after' => $method['days_after']
							)
						);
					}
				}
			}else{
				$selectBox = htmlBase::newElement('input')
				->setType('hidden')
				->addClass('ui-widget-content reservationShipping')
				->setName('product[' . $id . '][reservation][shipping]');
			}
			if (is_null($resInfo) === false && isset($resInfo['shipping']) && $resInfo['shipping'] !== false && isset($resInfo['shipping']['title']) && !empty($resInfo['shipping']['title']) && isset($resInfo['shipping']['cost']) && !empty($resInfo['shipping']['cost'])){
				if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
					$selectBox->selectOptionByValue($resInfo['shipping']['id']);
				}else{
					$selectBox->setValue($resInfo['shipping']['id']);
				}
				$shipInput = '<span class="reservationShippingText">'.$resInfo['shipping']['title'].'</span>';
			}

			$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_SHIPPING_METHOD') . ' ' . $selectBox->draw() . $shipInput . '</i></small>';
		}
		//if (is_null($resInfo) === false && isset($resInfo['deposit_amount']) && $resInfo['deposit_amount'] > 0){
		if ($this->getDepositAmount() > 0){
			$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_DEPOSIT_AMOUNT') . ' ' . $currencies->format($this->getDepositAmount()) . '</i></small>';
		}
		//}

		EventManager::notify('ParseReservationInfoEdit', $return, $resInfo);
		return $return;
	}

	public function parse_reservation_info($pID_string, $resInfo, $showEdit = true){
		global $currencies;
		$return = '';
		$return .= '<br /><small><b><i><u>' . sysLanguage::get('TEXT_INFO_RESERVATION_INFO') . '</u></i></b></small>';

		$start = date_parse($resInfo['start_date']);
		$end = date_parse($resInfo['end_date']);

		$startTime = mktime($start['hour'], $start['minute'], $start['second'], $start['month'], $start['day'], $start['year']);
		$endTime = mktime($end['hour'], $end['minute'], $end['second'], $end['month'], $end['day'], $end['year']);

		//$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_START_DATE') . ' ' . strftime(sysLanguage::getDateFormat('long'), $startTime) . '</i></small>' .
		//	'<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_END_DATE') . ' ' . strftime(sysLanguage::getDateFormat('long'), $endTime) . '</i></small>';
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'False'){
			if($resInfo['semester_name'] == ''){
				if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_ALLOW_HOURLY') == 'True'){
					$stDate = strftime(sysLanguage::getDateTimeFormat('long'), $startTime);
					$enDate = strftime(sysLanguage::getDateTimeFormat('long'), $endTime);
				}else{
					$stDate = strftime(sysLanguage::getDateFormat('long'), $startTime);
					$enDate = strftime(sysLanguage::getDateFormat('long'), $endTime);
				}

				$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_START_DATE') . ' ' . $stDate . '</i></small>' .
					'<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_END_DATE') . ' ' . $enDate . '</i></small>';
			}else{
				$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_SEMESTER') . ' ' .$resInfo['semester_name']  . '</i></small>' ;
			}
		}else{
			$return .= '<br /><small><i> - Event Date: ' . date('M d, Y',strtotime($resInfo['event_date'])) . '</i></small>' .
						'<br /><small><i> - Event Name: ' . $resInfo['event_name']. '</i></small>';
		}

		if (isset($resInfo['shipping']) && $resInfo['shipping'] !== false && isset($resInfo['shipping']['title']) && !empty($resInfo['shipping']['title']) && isset($resInfo['shipping']['cost']) && !empty($resInfo['shipping']['cost'])){
			$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_SHIPPING_METHOD') . ' ' . $resInfo['shipping']['title'] . ' (' . $currencies->format($resInfo['shipping']['cost']) . ')</i></small>';
		}

		if (isset($resInfo['deposit_amount']) && $resInfo['deposit_amount'] > 0){
			$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_DEPOSIT_AMOUNT') . ' ' . $currencies->format($resInfo['deposit_amount']) . '</i></small>';
		}
		if (isset($resInfo['insurance']) && $resInfo['insurance'] > 0){
			$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_INSURANCE') . ' ' . $currencies->format($resInfo['insurance']) . '</i></small>';
		}
		//$return .= '<br />';
		EventManager::notify('ParseReservationInfo', &$return, &$resInfo);
		return $return;
	}

	public function hasInventory(){

		if ($this->enabled === false) return false;
		if (is_null($this->inventoryCls)) return true;
		$invItems = $this->inventoryCls->getInventoryItems();
		$hasInv = false;
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_SELECTION') != 'Using calendar after browsing products and clicking Reserve' && Session::exists('isppr_inventory_pickup') === false && sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') == 'True' && sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'False'){
			return false;
		}

		if(isset($invItems) && ($invItems != false)){
			foreach($invItems as $invInfo){
				$bookingInfo = array(
					'item_type' => 'barcode',
					'item_id'   => $invInfo['id']
				);

				if (Session::exists('isppr_date_start')){
					$startCheck = Session::get('isppr_date_start');
					if (!empty($startCheck)){
						$startDate = date_parse($startCheck);
						$endDate = date_parse(Session::get('isppr_date_end'));
						$bookingInfo['start_date'] = mktime(
							$startDate['hour'],
							$startDate['minute'],
							$startDate['second'],
							$startDate['month'],
							$startDate['day'],
							$startDate['year']
						);
						$bookingInfo['end_date'] = mktime(
							$endDate['hour'],
							$endDate['minute'],
							$endDate['second'],
							$endDate['month'],
							$endDate['day'],
							$endDate['year']
						);
					}
				}

				if (Session::exists('isppr_inventory_pickup')){
					$pickupCheck = Session::get('isppr_inventory_pickup');
					if (!empty($pickupCheck)){
						$bookingInfo['inventory_center_pickup'] = $pickupCheck;
					}
				}else{
					//check here if the invInfo has a specific inventory. If there are two or more
				}
				if (Session::exists('isppr_product_qty')){
					$bookingInfo['quantity'] = (int)Session::get('isppr_product_qty');
				}else{
					$bookingInfo['quantity'] = 1;
				}

				if (Session::exists('isppr_shipping_days_before')){
					$bookingInfo['start_date'] = strtotime('- '. Session::get('isppr_shipping_days_before').' days', $bookingInfo['start_date']);
				}
				if (Session::exists('isppr_shipping_days_after')){
					$bookingInfo['end_date'] = strtotime('+ '. Session::get('isppr_shipping_days_after').' days', $bookingInfo['end_date']);
				}

				$numBookings = ReservationUtilities::CheckBooking($bookingInfo);
				if ($numBookings == 0){
					$hasInv = true;
					break;
				}
			}
		}
		return $hasInv || (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_STOCK') == 'True');
	}

	public function updateStock($orderId, $orderProductId, &$cartProduct){
	}

	public function processRemoveFromCart(){
		global $ShoppingCart;
		if (isset($ShoppingCart->reservationInfo)){
			if ($ShoppingCart->countContents() <= 0){
				unset($ShoppingCart->reservationInfo);
			}
		}
	}

	public function processAddToOrderOrCart($resInfo, &$pInfo){
		global $App, $ShoppingCart;
		$shippingMethod = $resInfo['shipping_method'];
		$rShipping = false;
		if (isset($shippingMethod) && !empty($shippingMethod) && ($shippingMethod != 'zonereservation')){
			$shippingModule = $resInfo['shipping_module'];
			$Module = OrderShippingModules::getModule($shippingModule);
			if(is_object($Module) && $Module->getType() == 'Order' && $App->getEnv() == 'catalog'){
				foreach($ShoppingCart->getProducts() as $cartProduct) {
					if ($cartProduct->hasInfo('reservationInfo') === true){
						$reservationInfo1 = $cartProduct->getInfo();
						if(isset($reservationInfo1['reservationInfo']['shipping']) && isset($reservationInfo1['reservationInfo']['shipping']['module']) && $reservationInfo1['reservationInfo']['shipping']['module'] == 'zonereservation'){
							$reservationInfo1['reservationInfo']['shipping']['id']  = $shippingMethod;
							$ShoppingCart->updateProduct($cartProduct->getIdString(), $reservationInfo1);
						}

					}
				}

			}
			$product = new product($this->productInfo['id']);
			if(isset($resInfo['quantity'])){
 	            $total_weight = (int)$resInfo['quantity'] * $product->getWeight();
			}else{
				$total_weight = $product->getWeight();
			}
			if(is_object($Module)){
				$quote = $Module->quote($shippingMethod, $total_weight);

				$rShipping = array(
					'title'  => $quote['methods'][0]['title'],
					'cost'   => $quote['methods'][0]['cost'],
					'id'     => $quote['methods'][0]['id'],
					'module' => $shippingModule
				);

			}else{
				$rShipping = array(
					'title'  => '',
					'cost'   => '',
					'id'     => '',
					'module' => $shippingModule
				);
			}

			if (isset($resInfo['days_before'])){
				$rShipping['days_before'] = $resInfo['days_before'];
			}

			if (isset($resInfo['days_after'])){
				$rShipping['days_after'] = $resInfo['days_after'];
			}
		}

		$pInfo['reservationInfo'] = array(
			'start_date'    => $resInfo['start_date'],
			'end_date'      => $resInfo['end_date'],
			'quantity'      => $resInfo['quantity'],
			'shipping'      => $rShipping
		);

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
			$pInfo['reservationInfo']['event_date'] = $resInfo['event_date'];
			$pInfo['reservationInfo']['event_name'] = $resInfo['event_name'];
		}
		if(isset($resInfo['semester_name'])){
			$pInfo['reservationInfo']['semester_name'] = $resInfo['semester_name'];
		}else{
			$pInfo['reservationInfo']['semester_name'] = '';
		}

		$pricing = $this->figureProductPricing($pInfo['reservationInfo']);

		if (isset($pricing)){
			$pInfo['price'] = $pricing['price'];
			$pInfo['reservationInfo']['deposit_amount'] = $this->getDepositAmount();
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_SELECTION') == 'Using calendar after browsing products and clicking Reserve'){
				$pInfo['final_price'] = $pricing['price'];
			}else{
				$pInfo['final_price'] = $pricing['price']; //+ $pInfo['reservationInfo']['deposit_amount'];
			}
		}
	}

	public function processAddToOrder(&$pInfo){
		if (isset($pInfo['OrdersProductsReservation'])){
			$infoArray = array(
				'shipping_module' => 'zonereservation',
				'shipping_method' => $pInfo['OrdersProductsReservation'][0]['shipping_method'],
				'start_date'      => $pInfo['OrdersProductsReservation'][0]['start_date'],
				'end_date'        => $pInfo['OrdersProductsReservation'][0]['end_date'],
				'days_before'      => $pInfo['OrdersProductsReservation'][0]['days_before'],
				'days_after'        => $pInfo['OrdersProductsReservation'][0]['days_after'],
				'quantity'        => $pInfo['products_quantity']
			);
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
				$infoArray['event_date'] = $pInfo['OrdersProductsReservation'][0]['event_date'];
				$infoArray['event_name'] = $pInfo['OrdersProductsReservation'][0]['event_name'];
			}
			$infoArray['semester_name'] = $pInfo['OrdersProductsReservation'][0]['semester_name'];
		}else{
			//$shipping_modules = OrderShippingModules::getModule('zonereservation');
			//$quotes = $shipping_modules->quote('method');
			$infoArray = array(
				'shipping_module' => 'zonereservation',
				'shipping_method' => 'method1',//?
				'start_date'      => date('Ymd'),
				'end_date'        => date('Ymd'),
				'days_before'      => 0,
				'days_after'        => 0,
				'quantity'        => $pInfo['products_quantity']
			);
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
				$infoArray['event_date'] = date('Ymd');
				$infoArray['event_name'] = '';
			}
			$infoArray['semester_name'] = '';
		}
		$this->processAddToOrderOrCart($infoArray, $pInfo);

		EventManager::notify('ReservationProcessAddToOrder', &$pInfo);
	}

	public function processAddToCart(&$pInfo){
		$shippingInfo = array(
			'zonereservation',
			'zonereservation'
		);
		if (isset($_POST['rental_shipping']) && $_POST['rental_shipping'] !== false){
			$shippingInfo = explode('_', $_POST['rental_shipping']);
		}

		$reservationInfo = array(
			'shipping_module' => $shippingInfo[0],
			'shipping_method' => $shippingInfo[1],
			'start_date'      => $_POST['start_date'],
			'end_date'        => $_POST['end_date'],
			'days_before'     => $_POST['days_before'],
			'days_after'     => $_POST['days_after'],
			'quantity'        => $_POST['rental_qty']
		);
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
			$reservationInfo['event_date'] = $_POST['event_date'];
			$reservationInfo['event_name'] = $_POST['event_name'];
		}
		$reservationInfo['semester_name'] = $_POST['semester_name'];
		$this->processAddToOrderOrCart($reservationInfo, $pInfo);

		EventManager::notify('ReservationProcessAddToCart', &$pInfo['reservationInfo']);
	}

	public function processUpdateCart(&$pInfo){

		$reservationInfo =& $pInfo['reservationInfo'];
		if (isset($pInfo['reservationInfo']['shipping']['module']) && isset($pInfo['reservationInfo']['shipping']['id'])) {

				$shipping_modules = OrderShippingModules::getModule($pInfo['reservationInfo']['shipping']['module']);
				$product = new product($this->productInfo['id']);
				if(isset($pInfo['reservationInfo']['quantity'])){
			        	$total_weight = (int)$pInfo['reservationInfo']['quantity'] * $product->getWeight();
				}else{
					$total_weight = $product->getWeight();
				}
				$quotes = $shipping_modules->quote($pInfo['reservationInfo']['shipping']['id'], $total_weight);
				$reservationInfo['shipping'] = array(
					'title' => isset($quotes[0]['methods'][0]['title'])?$quotes[0]['methods'][0]['title']:$quotes['methods'][0]['title'],
					'cost'  => isset($quotes[0]['methods'][0]['cost'])?$quotes[0]['methods'][0]['cost']:$quotes['methods'][0]['cost'],
					'id'    => isset($quotes[0]['methods'][0]['id'])?$quotes[0]['methods'][0]['id']:$quotes['methods'][0]['id'],
					'module' => $pInfo['reservationInfo']['shipping']['module'],
					'days_before'  => $pInfo['reservationInfo']['shipping']['days_before'],
					'days_after'  => $pInfo['reservationInfo']['shipping']['days_after']
				);
		}

		$pInfo['quantity'] = $reservationInfo['quantity'];

		$pricing = $this->figureProductPricing($pInfo['reservationInfo']);

		if (isset($pricing)){
			$pInfo['price'] = $pricing['price'];
			$pInfo['reservationInfo']['deposit_amount'] = $this->getDepositAmount();
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_SELECTION') != 'Using calendar after browsing products and clicking Reserve'){
				$pInfo['final_price'] = $pricing['price']; //+ $pInfo['reservationInfo']['deposit_amount'];
			}else{
				$pInfo['final_price'] = $pricing['price'];
			}
		}
	}

	public function getPrice(){
		return false;
	}

	public function displayPrice(){
		return false;
	}

	public function canUseSpecial(){
		return false;
	}

	public function onInsertOrderedProduct($cartProduct, $orderId, &$orderedProduct, &$products_ordered){
		global $currencies, $onePageCheckout, $appExtension;
		$resInfo = $cartProduct->getInfo('reservationInfo');
		$pID = (int)$cartProduct->getIdString();

		$startDate = date_parse($resInfo['start_date']);
		$endDate = date_parse($resInfo['end_date']);
		if (!isset($resInfo['insurance'])){
			$resInfo['insurance'] = 0;
		}

		$insurance = $resInfo['insurance'];
		$eventName = '';
		$eventDate = '';
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
			$eventName = $resInfo['event_name'];
			$eventDate = $resInfo['event_date'];
		}else{
			$eventName ='';
			$eventDate = '0000-00-00 00:00:00';
		}
		$semesterName = $resInfo['semester_name'];
		$terms = '<p>Terms and conditions:</p><br/>';
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SAVE_TERMS') == 'True'){
			$infoPages = $appExtension->getExtension('infoPages');
			$termInfoPage = $infoPages->getInfoPage('conditions');
			$terms .= $termInfoPage['PagesDescription'][Session::get('languages_id')]['pages_html_text'];
			 if(sysConfig::get('TERMS_INITIALS') == 'true' && Session::exists('agreed_terms')){
				 $terms .= '<br/>Initials: '. Session::get('agreed_terms');
			 }
		}
		$startDateFormatted = date('Y-m-d H:i:s', mktime($startDate['hour'],$startDate['minute'],$startDate['second'],$startDate['month'],$startDate['day'],$startDate['year']));
		$endDateFormatted = date('Y-m-d H:i:s', mktime($endDate['hour'],$endDate['minute'],$endDate['second'],$endDate['month'],$endDate['day'],$endDate['year']));

		$trackMethod = $this->inventoryCls->getTrackMethod();

		$Reservations =& $orderedProduct->OrdersProductsReservation;
		$rCount = 0;
		$excludedBarcode = array();
		$excludedQuantity = array();

		for($count=0; $count < $resInfo['quantity']; $count++){
			$Reservations[$rCount]->start_date = $startDateFormatted;
			$Reservations[$rCount]->end_date = $endDateFormatted;
			$Reservations[$rCount]->insurance = $insurance;
			$Reservations[$rCount]->event_name = $eventName;
			$Reservations[$rCount]->semester_name = $semesterName;
			$Reservations[$rCount]->event_date = $eventDate;
			$Reservations[$rCount]->track_method = $trackMethod;
			$Reservations[$rCount]->rental_state = 'reserved';
			if (isset($resInfo['shipping']['id']) && !empty($resInfo['shipping']['id'])){
				$Reservations[$rCount]->shipping_method_title = $resInfo['shipping']['title'];
				$Reservations[$rCount]->shipping_method = $resInfo['shipping']['id'];
				$Reservations[$rCount]->shipping_days_before = $resInfo['shipping']['days_before'];
				$Reservations[$rCount]->shipping_days_after = $resInfo['shipping']['days_after'];
				$Reservations[$rCount]->shipping_cost = $resInfo['shipping']['cost'];
			}

			if ($trackMethod == 'barcode'){
				$Reservations[$rCount]->barcode_id = $this->getAvailableBarcode($cartProduct, $excludedBarcode);
				$excludedBarcode[] = $Reservations[$rCount]->barcode_id;
				$Reservations[$rCount]->ProductsInventoryBarcodes->status = 'R';
			}elseif ($trackMethod == 'quantity'){
				$Reservations[$rCount]->quantity_id = $this->getAvailableQuantity($cartProduct, $excludedQuantity);
				$excludedQuantity[] = $Reservations[$rCount]->quantity_id;
				$Reservations[$rCount]->ProductsInventoryQuantity->available -= 1;
				$Reservations[$rCount]->ProductsInventoryQuantity->reserved += 1;
			}
			EventManager::notify('ReservationOnInsertOrderedProduct', $Reservations[$rCount], &$cartProduct);

			/*
			 * @TODO: Move To Packages Extension/Plugin
			 */
			if (isset($packageProducts)){
				$Packaged =& $Reservations[$rCount]->Packaged;
				$pCount = 0;
				foreach($packageProducts as $pInfo){
					$usedBarcodes = array();

					for($count1=0; $count1 < ($resInfo['quantity'] * $pInfo['packageQuantity']); $count1++){
						$Packaged[$pCount]->start_date = $startDateFormatted;
						$Packaged[$pCount]->end_date = $endDateFormatted;
						$Packaged[$pCount]->track_method = $trackMethod;
						$Packaged[$pCount]->rental_state = 'reserved';

						if ($pInfo['track_method'] == 'barcode'){
							$Packaged[$pCount]->barcode_id = $this->getAvailableBarcode($cartProduct, $excludedBarcode);
							if (!empty($Packaged[$pCount]->barcode_id)){
								$Packaged[$pCount]->ProductsInventoryBarcodes->status = 'R';
								$excludedBarcode[] = $Packaged[$pCount]->barcode_id;
							}
						}elseif ($pInfo['track_method'] == 'quantity'){
							$Packaged[$pCount]->quantity_id = $this->getAvailableQuantity($cartProduct, $excludedQuantity);
							$excludedQuantity[] = $Packaged[$pCount]->quantity_id;
							$Packaged[$pCount]->ProductsInventoryQuantity->available -= 1;
							$Packaged[$pCount]->ProductsInventoryQuantity->reserved += 1;
						}

						$pCount++;
					}
				}
			}
			$rCount++;
		}
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'False'){
			if($resInfo['semester_name'] == ''){
				$products_ordered .= 'Reservation Info' .
					"\n\t" . 'Start Date: ' . $resInfo['start_date'] .
					"\n\t" . 'End Date: ' . $resInfo['end_date']
					;
			}else{
				$products_ordered .= 'Reservation Info' .
					"\n\t" . 'Semester Name: ' . $resInfo['semester_name'] ;
					;
			}
		}else{
			$products_ordered .= 'Reservation Info' .
				"\n\t" . 'Event Date: ' . date('M d, Y', strtotime($resInfo['event_date'])) .
				"\n\t" . 'Event Name: ' . $resInfo['event_name']
				;
		}

		if (isset($resInfo['shipping']) && !empty($resInfo['shipping']['title'])){
			$products_ordered .= "\n\t" . 'Shipping Method: ' . $resInfo['shipping']['title'] . ' (' . $currencies->format($resInfo['shipping']['cost']) . ')';
		}
		$products_ordered .= "\n\t" . 'Insurance: ' . $currencies->format($resInfo['insurance']);
		$products_ordered .= "\n";
		EventManager::notify('ReservationAppendOrderedProductsString', &$products_ordered, &$cartProduct);

		$orderedProduct->Orders->terms = $terms;
		$orderedProduct->save();
	}

/*
 * Get Available Barcode Function
 */

	public function getAvailableBarcode($cartProduct, $excluded, $usableBarcodes = array()){
		$invItems = $this->inventoryCls->getInventoryItems();
		if ($cartProduct->hasInfo('barcode_id') === false){
			$resInfo = $cartProduct->getInfo('reservationInfo');
			if (isset($resInfo['shipping']['days_before'])){
				$shippingDaysBefore = (int)$resInfo['shipping']['days_before'];
			}else{
				$shippingDaysBefore = 0;
			}

			if (isset($resInfo['shipping']['days_after'])){
				$shippingDaysAfter = (int)$resInfo['shipping']['days_after'];
			}else{
				$shippingDaysAfter = 0;
			}

			$startArr = date_parse($resInfo['start_date']);
			$startDate = mktime($startArr['hour'],$startArr['minute'],$startArr['second'],$startArr['month'],$startArr['day']-$shippingDaysBefore,$startArr['year']);

			$endArr = date_parse($resInfo['end_date']);
			$endDate = mktime($endArr['hour'],$endArr['minute'],$endArr['second'],$endArr['month'],$endArr['day']+$shippingDaysAfter,$endArr['year']);
			$barcodeID = -1;
			foreach($invItems as $barcodeInfo){
				if(count($usableBarcodes)==0 || in_array($barcodeInfo['id'], $usableBarcodes)){
					if (in_array($barcodeInfo['id'], $excluded)){
						continue;
					}

					$bookingInfo = array(
						'item_type'               => 'barcode',
						'item_id'                 => $barcodeInfo['id'],
						'start_date'              => $startDate,
						'end_date'                => $endDate,
						'cartProduct'             => $cartProduct
					);
					if (Session::exists('isppr_inventory_pickup')){
						$pickupCheck = Session::get('isppr_inventory_pickup');
						if (!empty($pickupCheck)){
							$bookingInfo['inventory_center_pickup'] = $pickupCheck;
						}
					}
					$bookingInfo['quantity'] = 1;
					//if allow overbooking is enabled what barcode should be chosen.. I think any is good.
					$bookingCount = ReservationUtilities::CheckBooking($bookingInfo);
					if ($bookingCount <= 0 || sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_STOCK') == 'True'){
						$barcodeID = $barcodeInfo['id'];
						break;
					}
				}
			}
		}else{
			$barcodeID = $cartProduct->getInfo('barcode_id');
		}
		return $barcodeID;
	}

	/*
	 * Get Available Quantity Function
	 */

	public function getAvailableQuantity($cartProduct, $excluded){
		$invItems = $this->inventoryCls->getInventoryItems();
		if ($cartProduct->hasInfo('quantity_id') === false){
			$resInfo = $cartProduct->getInfo('reservationInfo');
			if (isset($resInfo['shipping']['days_before'])){
				$shippingDaysBefore = (int)$resInfo['shipping']['days_before'];
			}else{
				$shippingDaysBefore = 0;
			}

			if (isset($resInfo['shipping']['days_after'])){
				$shippingDaysAfter = (int)$resInfo['shipping']['days_after'];
			}else{
				$shippingDaysAfter = 0;
			}

			$startArr = date_parse($resInfo['start_date']);
			$startDate = mktime($startArr['hour'],$startArr['minute'],$startArr['second'],$startArr['month'],$startArr['day']-$shippingDaysBefore,$startArr['year']);
			$endArr = date_parse($resInfo['end_date']);
			$endDate = mktime($endArr['hour'],$endArr['minute'],$endArr['second'],$endArr['month'],$endArr['day']+$shippingDaysAfter,$endArr['year']);
			$qtyID = -1;
			foreach($invItems as $qInfo){
				if (in_array($qInfo, $excluded)){
					continue;
				}
				$bookingCount = ReservationUtilities::CheckBooking(array(
					'item_type'  => 'quantity',
					'item_id'    => $qInfo['id'],
					'start_date' => $startDate,
					'end_date'   => $endDate,
					'cartProduct' => $cartProduct
				));
				if ($bookingCount <= 0 || sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_STOCK') == 'True'){
					$qtyID = $qInfo['id'];
					break;
				}else{
					if ($qInfo['available'] > $bookingCount){
						$qtyID = $qInfo['id'];
						break;
					}
				}
			}
		}else{
			$qtyID = $cartProduct->getInfo('quantity_id');
		}
		return $qtyID;
	}


	public function getPurchaseHtml($key){
		global $currencies;
		$return = null;
		switch($key){
			case 'product_info':
			    if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_CALENDAR_PRODUCT_INFO') == 'False') {


					$priceTableHtml = '';
					//if ($canReserveDaily || $canReserveWeekly || $canReserveMonthly || $canReserve6Months || $canReserve1Year || $canReserve3Years || $canReserveHourly || $canReserveTwoHours || $canReserveFourHours){
					$priceTable = htmlBase::newElement('table')
					->setCellPadding(3)
					->setCellSpacing(0)
					->attr('align', 'center');

					$QPricePerRentalProducts = Doctrine_Query::create()
					->from('PricePerRentalPerProducts pprp')
					->leftJoin('pprp.PricePayPerRentalPerProductsDescription pprpd')
					->where('pprp.pay_per_rental_id =?',$this->getId())
					->andWhere('pprpd.language_id=?', Session::get('languages_id'))
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					foreach($QPricePerRentalProducts as $iPrices){
							$priceHolder = htmlBase::newElement('span')
							->css(array(
								'font-size' => '1.3em',
								'font-weight' => 'bold'
							))
							->html($this->displayReservePrice($iPrices['price']));

							$perHolder = htmlBase::newElement('span')
							->css(array(
								'white-space' => 'nowrap',
								'font-size' => '1.1em',
								'font-weight' => 'bold'
							))
							->html($iPrices['PricePayPerRentalPerProductsDescription'][0]['price_per_rental_per_products_name']);

							$priceTable->addBodyRow(array(
								'columns' => array(
									array('addCls' => 'main', 'align' => 'right', 'text' => $priceHolder->draw()),
									array('addCls' => 'main', 'align' => 'left', 'text' => $perHolder->draw())
								)
							));
					}


						if ($this->getDepositAmount() > 0){
							$priceHolder = htmlBase::newElement('span')
							->css(array(
								'font-size' => '1.1em',
								'font-weight' => 'bold'
							))
							->html($currencies->format($this->getDepositAmount()));

							$infoIcon = htmlBase::newElement('icon')
							->setType('info')
							->attr('onclick', 'popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'ppr_deposit_info') . '\',400,300);')
							->css(array(
								'display' => 'inline-block',
								'cursor' => 'pointer'
							));

							$perHolder = htmlBase::newElement('span')
							->css(array(
								'white-space' => 'nowrap',
								'font-size' => '1.0em',
								'font-weight' => 'bold'
							))
							->html(' - Deposit ' . $infoIcon->draw());

							$priceTable->addBodyRow(array(
								'columns' => array(
									array('addCls' => 'main', 'align' => 'right', 'text' => $priceHolder->draw()),
									array('addCls' => 'main', 'align' => 'left', 'text' => $perHolder->draw())
								)
							));
						}

						if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_PRICES_DATES_BEFORE') == 'True' || sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_SELECTION') == 'Using calendar after browsing products and clicking Reserve'){
							$priceTableHtmlPrices = $priceTable->draw();
						}else{
							$priceTableHtmlPrices = '';
						}
					//}

				if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_SELECTION') == 'Using calendar after browsing products and clicking Reserve'){
					$button = htmlBase::newElement('button')
					->setType('submit')
					->setName('reserve_now')
					->setText(sysLanguage::get('TEXT_BUTTON_PAY_PER_RENTAL'));

					if ($this->hasInventory() === false){
						$button->disable();
					}

					$link = itw_app_link('appExt=payPerRentals&products_id=' . $_GET['products_id'], 'build_reservation', 'default');

					$return = array(
						'form_action'   => $link,
						'purchase_type' => $this->typeLong,
						'allowQty'      => false,
						'header'        => $this->typeShow,
						'content'       => $priceTableHtmlPrices,
						'button'        => $button
					);
				}else{
					$priceTable = htmlBase::newElement('table')
					->setCellPadding(3)
					->setCellSpacing(0)
					->attr('align', 'center');
					if(Session::exists('isppr_inventory_pickup') === false && Session::exists('isppr_city') === true && Session::get('isppr_city') != ''){
						$Qproducts = Doctrine_Query::create()
						->from('ProductsInventoryBarcodes b')
						->leftJoin('b.ProductsInventory i')
						->leftJoin('i.Products p')
						->leftJoin('b.ProductsInventoryBarcodesToInventoryCenters b2c')
						->leftJoin('b2c.ProductsInventoryCenters ic');

						$Qproducts->where('p.products_id=?',  $_GET['products_id']);
						$Qproducts->andWhere('i.use_center = ?', '1');

						if (Session::exists('isppr_continent') === true && Session::get('isppr_continent') != '') {
							$Qproducts->andWhere('ic.inventory_center_continent = ?', Session::get('isppr_continent'));
						}
						if (Session::exists('isppr_country') === true && Session::get('isppr_country') != '') {
							$Qproducts->andWhere('ic.inventory_center_country = ?', Session::get('isppr_country'));
						}
						if (Session::exists('isppr_state') === true && Session::get('isppr_state') != '') {
							$Qproducts->andWhere('ic.inventory_center_state = ?', Session::get('isppr_state'));
						}
						if (Session::exists('isppr_city') === true && Session::get('isppr_city') != '') {
							$Qproducts->andWhere('ic.inventory_center_city = ?', Session::get('isppr_city'));
						}
						$Qproducts = $Qproducts->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						$invCenter = -1;
						$isdouble = false;
						foreach($Qproducts as $iProduct){
							if($invCenter == -1){
								$invCenter = $iProduct['ProductsInventoryBarcodesToInventoryCenters']['ProductsInventoryCenters']['inventory_center_id'];
							}elseif($iProduct['ProductsInventoryBarcodesToInventoryCenters']['ProductsInventoryCenters']['inventory_center_id'] != $invCenter){
								$isdouble = true;
								break;
							}

						}


						if(!$isdouble){
							Session::set('isppr_inventory_pickup', $Qproducts[0]['ProductsInventoryBarcodesToInventoryCenters']['ProductsInventoryCenters']['inventory_center_id']);
							$deleteS = true;

						}
					}

				if(Session::exists('isppr_selected') && Session::get('isppr_selected') == true){
						$start_date = '';
						$end_date = '';
						$event_date = '';
						$event_name = '';
						$pickup = '';
						$dropoff = '';
						if (Session::exists('isppr_date_start')){
							$start_date = Session::get('isppr_date_start');
						}
						if (Session::exists('isppr_date_end')){
							$end_date = Session::get('isppr_date_end');
						}
						if (Session::exists('isppr_event_date')){
							$event_date = Session::get('isppr_event_date');
						}
						if (Session::exists('isppr_event_name')){
							$event_name = Session::get('isppr_event_name');
						}
						if (Session::exists('isppr_inventory_pickup')){
							$pickup = Session::get('isppr_inventory_pickup');
						}else{
							//check the inventory center for this one...if multiple output a text to show...select specific//use continent, city for comparison
						}
						if (Session::exists('isppr_inventory_dropoff')){
							$dropoff = Session::get('isppr_inventory_dropoff');
						}
						if (Session::exists('isppr_product_qty')){
							$qtyVal = (int)Session::get('isppr_product_qty');
						}else{
							$qtyVal = 1;
						}

						$payPerRentalButton = htmlBase::newElement('button')
						->setType('submit')
						->setText(sysLanguage::get('TEXT_BUTTON_RESERVE'))
						->setId('inCart')
						->setName('add_reservation_product');

						if ($this->hasInventory()){
							if(Session::exists('isppr_shipping_cost')){
								$ship_cost = (float)Session::get('isppr_shipping_cost');
							}else{
								$payPerRentalButton->disable();
							}
							$price = $this->getReservationPrice($start_date, $end_date);
							$pricing = $currencies->format($qtyVal*$price['price'] + $ship_cost);

							$pageForm =  htmlBase::newElement('div');

							if (isset($start_date)) {
								$htmlStartDate = htmlBase::newElement('input')
								->setType('hidden')
								->setName('start_date')
								->setValue($start_date);
							}
							if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
								$htmlEventDate = htmlBase::newElement('input')
								->setType('hidden')
								->setName('event_date')
								->setValue($event_date);
								$htmlEventName = htmlBase::newElement('input')
								->setType('hidden')
								->setName('event_name')
								->setValue($event_name);
							}
							if (isset($pickup)) {
								$htmlPickup = htmlBase::newElement('input')
								->setType('hidden')
								->setName('pickup')
								->setValue($pickup);
							}
							if (isset($dropoff)) {
								$htmlDropoff = htmlBase::newElement('input')
								->setType('hidden')
								->setName('dropoff')
								->setValue($dropoff);
							}
							$htmlRentalQty = htmlBase::newElement('input')
							->setType('hidden')
							->setName('rental_qty')
							->setValue($qtyVal);
							$htmlProductsId = htmlBase::newElement('input')
							->setType('hidden')
							->setName('products_id')
							->setValue($_GET['products_id']);
							if (isset($end_date)) {
								$htmlEndDate = htmlBase::newElement('input')
								->setType('hidden')
								->setName('end_date')
								->setValue($end_date);
							}

							if (isset($htmlStartDate)) {
								$pageForm->append($htmlStartDate);
							}
							if (isset($htmlEndDate)) {
								$pageForm->append($htmlEndDate);
							}
							if (isset($htmlPickup)) {
								$pageForm->append($htmlPickup);
							}
							if (isset($htmlDropoff)) {
								$pageForm->append($htmlDropoff);
							}
							$pageForm->append($htmlRentalQty);
							$pageForm->append($htmlProductsId);

							if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
								$pageForm->append($htmlEventDate)
								 ->append($htmlEventName);
							}

							if (Session::exists('isppr_shipping_method')){
								$htmlShippingDays = htmlBase::newElement('input')
								->setType('hidden')
								->setName('rental_shipping')
								->setValue("zonereservation_" . Session::get('isppr_shipping_method'));
								$pageForm->append($htmlShippingDays);
							}

							$priceHolder = htmlBase::newElement('span')
							->css(array(
								'font-size' => '1.3em',
								'font-weight' => 'bold'
							))
							->html($pricing);

							$perHolder = htmlBase::newElement('span')
							->css(array(
								'white-space' => 'nowrap',
								'font-size' => '1.1em',
								'font-weight' => 'bold'
							))
							->html('Price per selected period');

							$priceTable->addBodyRow(array(
								'columns' => array(
									array('addCls' => 'main', 'align' => 'right', 'text' => $priceHolder->draw()),
									array('addCls' => 'main', 'align' => 'left', 'text' => $perHolder->draw())
								)
							));
							$pageForm->append($priceTable);
						  	$priceTableHtml = $pageForm->draw();

							$return = array(
								'form_action'   => itw_app_link('appExt=payPerRentals&products_id=' . $_GET['products_id'], 'build_reservation', 'default'),
								'purchase_type' => $this->typeLong,
								'allowQty'      => false,
								'header'        => $this->typeShow,
								'content'       => $priceTableHtmlPrices . $priceTableHtml,
								'button'        => $payPerRentalButton
							);
						}
					}else{
						$payPerRentalButton = htmlBase::newElement('button')->setType('submit')->setText(sysLanguage::get('TEXT_BUTTON_RESERVE'))->setId('noDatesSelected')->setName('no_dates_selected');

						if ($this->hasInventory() === false){
							$payPerRentalButton->disable();
						}

						$return = array(
							'form_action'   => '#',
							'purchase_type' => $this->typeLong,
							'allowQty'      => false,
							'header'        => $this->typeShow,
							'content'       => $priceTableHtmlPrices,
							'button'        => $payPerRentalButton
						);
					}
				}
			}else{
					ob_start();
					require(sysConfig::getDirFsCatalog() . 'extensions/payPerRentals/catalog/base_app/build_reservation/pages/default.php');
					$pageHtml = ob_get_contents();
					ob_end_clean();
					$return = array(
								'form_action'   => '',
								'purchase_type' => $this->typeLong,
								'allowQty'      => false,
								'header'        => $this->typeShow,
								'content'       => $pageHtml,
								'button'        => ''
					);
					//echo $pageHtml;
			}
				break;
		}
		return $return;
	}

	public function getDepositAmount(){
		return $this->payperrental['deposit_amount'];
	}

	public function getPriceSemester($semName){
		$QPeriodsNames = Doctrine_Query::create()
		->from('PayPerRentalPeriods')
		->where('period_name=?', $semName)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if(count($QPeriodsNames) >0){
			$QPricePeriod = Doctrine_Query::create()
			->from('ProductsPayPerPeriods')
			->where('period_id=?', $QPeriodsNames[0]['period_id'])
			->andWhere('products_id=?', $this->getProductId())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			return $QPricePeriod[0]['price'];
		}else{
			return 0;
		}
	}

	public function getReservePrice($type){
		if (isset($this->payperrental)){
			return $this->payperrental['price_' . $type];
		}
		return;
	}

	public function getId(){
		return $this->payperrental['pay_per_rental_id'];
	}

	public function displayReservePrice($price){
		global $currencies;

		EventManager::notify('ReservationPriceBeforeSetup', &$price);
		return $currencies->display_price($price, $this->productInfo['taxRate']);
	}

	public function hasMaxDays(){
		if (isset($this->payperrental)){
			return $this->payperrental['max_days'] > 0;
		}
		return false;
	}

	public function hasMaxMonths(){
		if (isset($this->payperrental)){
			return $this->payperrental['max_months'] > 0;
		}
		return false;
	}

	public function getMaxDays(){
		if (isset($this->payperrental)){
			return $this->payperrental['max_days'];
		}
		return;
	}

	public function getMaxMonths(){
		if (isset($this->payperrental)){
			return $this->payperrental['max_months'];
		}
		return;
	}

	public function getPricingTable(){
		global $currencies;
		$table = '';
		if ($this->inventoryCls->hasInventory($this->typeLong)){

			$table .= '<table cellpadding="0" cellspacing="0" border="0">';

			$QPricePerRentalProducts = Doctrine_Query::create()
			->from('PricePerRentalPerProducts pprp')
			->leftJoin('pprp.PricePayPerRentalPerProductsDescription pprpd')
			->where('pprp.pay_per_rental_id =?', $this->getId())
			->andWhere('pprpd.language_id=?', Session::get('languages_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			foreach($QPricePerRentalProducts as $iPrices){
				$table .= '<tr>' .
				'<td class="main">'.$iPrices['PricePayPerRentalPerProductsDescription'][0]['price_per_rental_per_products_name'].': </td>' .
				'<td class="main">' . $this->displayReservePrice($iPrices['price']) . '</td>' .

				'</tr>';
			}

			$table .= '</table>';
		}
		return $table;
	}

	public function buildSemesters($semDates){

		$QPeriods = Doctrine_Query::create()
		->from('ProductsPayPerPeriods')
		->where('products_id=?', $this->getProductId())
		->andWhere('price > 0')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$table = '';
		if(count($QPeriods) > 0){
		ob_start();
	?>
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
      <td class="main" colspan="2">
		  <?php
		  	$CalOrSemester = htmlBase::newElement('radio')
			->addGroup(array(
				'checked' => 1,
				'separator' => '<br />',
				'name' => 'cal_or_semester',
				'data' => array(
					array(
						'label' => sysLanguage::get('TEXT_USE_CALENDAR'),
						'labelPosition' => 'before',
						'value' => '1'
					),
					array(
						'label' => sysLanguage::get('TEXT_USE_SEMESTER'),
						'labelPosition' => 'before',
						'value' => '0'
					)
				)
			));
			  echo $CalOrSemester->draw();
		  ?>

      </td>
     </tr>
	<tr class="semRow">
      <td class="main" colspan="2">
		  <?php
		  	$selectSem = htmlBase::newElement('selectbox')
		  	->setName('semester_name')
		  	->setLabel(sysLanguage::get('TEXT_SELECT_PERIOD'))
		  	->setLabelPosition('before')
		  	->attr('id','selected_period');
			$selectSem->addOption('',sysLanguage::get('TEXT_SELECT_SEMESTER'));

			foreach($semDates as $sDate){

				$attr = array(
						array(
							'name' => 'start_date',
							'value' => $sDate['start_date']
						),
						array(
							'name' => 'end_date',
							'value' => $sDate['end_date']
						)
					);
				$selectSem->addOptionWithAttributes($sDate['period_name'], $sDate['period_name'],$attr);
			}
			$moreInfo = htmlBase::newElement('a')
			->attr('id','moreInfoSem')
		  	->html(sysLanguage::get('TEXT_MORE_INFO_SEM'));
			echo $selectSem->draw();//.$moreInfo;
		  ?>

      </td>
     </tr>
	</table>
			<?php
			$table = ob_get_contents();
			ob_end_clean();
		}
		return $table;
	}

	public function buildShippingTable(){
		global $userAccount, $ShoppingCart, $App;

		if ($this->enabledShipping === false) return;

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
			$Module = OrderShippingModules::getModule($this->shipModuleCode);
			$dontShow = '';
			$selectedMethod = '';
			/*
			if($Module->getType() == 'Order'){
				foreach($ShoppingCart->getProducts() as $cartProduct) {
					if ($cartProduct->hasInfo('reservationInfo') === true){
						$reservationInfo1 = $cartProduct->getInfo('reservationInfo');
						if(isset($reservationInfo1['shipping']) && isset($reservationInfo1['shipping']['module']) && $reservationInfo1['shipping']['module'] == 'zonereservation'){
							$selectedMethod = $reservationInfo1['shipping']['id'];
							$dontShow = '';
							break;
						}
					}
				}

			} */
			$weight = 0;
			if($Module->getType() == 'Order' && $App->getEnv() == 'catalog'){
				foreach($ShoppingCart->getProducts() as $cartProduct) {
					if ($cartProduct->hasInfo('reservationInfo') === true){
						/*$reservationInfo1 = $cartProduct->getInfo('reservationInfo');
						if(isset($reservationInfo1['shipping']) && isset($reservationInfo1['shipping']['module']) && $reservationInfo1['shipping']['module'] == 'zonereservation'){
							$selectedMethod = $reservationInfo1['shipping']['id'];
							$dontShow = '';
							break;
						}*/
						$weight += $cartProduct->getWeight();
					}
				}
			}

			$product = new product($this->productInfo['id']);
			if(isset($_POST['rental_qty'])){
 	            $prod_weight = (int)$_POST['rental_qty'] * $product->getWeight();
			}else{
				$prod_weight = $product->getWeight();
			}

			$weight += $prod_weight;

			$quotes = array($Module->quote($selectedMethod, $weight));
			$table = '<div class="shippingTable" style="display:'.$dontShow.'">';
			if (sizeof($quotes[0]['methods']) > 0){
				$table .= sysLanguage::get('PPR_SHIPPING_SELECT') . $this->parseQuotes($quotes) ;
			}
			$table .= '</div>';

		}else{
			$table = '<div class="shippingUPS"><table cellpadding="0" cellspacing="0" border="0">';

			$table .= '<tr id="shipMethods">' .
						    '<td class="main">'.sysLanguage::get('PPR_SHIPPING_SELECT').':</td>' .
						    '<td class="main" id="rowquotes">' .  '</td>' .
						 '</tr>' ;

			$checkAddressButton = htmlBase::newElement('button')
			->usePreset('continue')
			->setId('getQuotes')
			->setName('getQuotes')
			->setText( sysLanguage::get('TEXT_BUTTON_GET_QUOTES'));

			$getQuotes = htmlBase::newElement('div');

			$checkAddressBox = htmlBase::newElement('div');
			if ($App->getEnv() == 'catalog'){
				$addressBook = $userAccount->plugins['addressBook'];
				$shippingAddress = $addressBook->getAddress('delivery');
			}else{
				global $Editor;
				$shippingAddress = $Editor->AddressManager->getAddress('delivery')->toArray();
			}

			$checkAddressBox->html('<table border="0" cellspacing="2" cellpadding="2" id="fullAddress">' .
				'<tr>' .
					'<td>' . sysLanguage::get('ENTRY_STREET_ADDRESS') . '</td>' .
					'<td>' . tep_draw_input_field('street_address',$shippingAddress['entry_street_address'],'id="street_address"') . '</td>' .
				'</tr>' .
				'<tr>' .
					'<td>' . sysLanguage::get('ENTRY_CITY') . '</td>' .
					'<td>' . tep_draw_input_field('city',$shippingAddress['entry_city'],'id="city"') . '</td>' .
				'</tr>' .
				'<tr>' .
					'<td>' . sysLanguage::get('ENTRY_STATE') . '</td>' .
					'<td id="stateCol">' . tep_draw_input_field('state',$shippingAddress['entry_state'],'id="state"') . '</td>' .
				'</tr>' .
				'<tr>' .
					'<td>' . sysLanguage::get('ENTRY_POST_CODE') . '</td>' .
					'<td>' . tep_draw_input_field('postcode',$shippingAddress['entry_postcode'],'id="postcode1"') . '</td>' .
				'</tr>' .
			 	'<tr>' .
					'<td>' . sysLanguage::get('ENTRY_COUNTRY') . '</td>' .
					'<td>' . tep_get_country_list('country', isset($shippingAddress['entry_country'])?$shippingAddress['entry_country']:sysConfig::get('STORE_COUNTRY'), 'id="countryDrop"') . '</td>' .
				'</tr>' .
			'</table>');
			$checkAddressBoxZip = htmlBase::newElement('div');
			$checkAddressBoxZip->html('<table border="0" cellspacing="2" cellpadding="2" id="zipAddress">' .
				'<tr>' .
					'<td>' . sysLanguage::get('ENTRY_POST_CODE') . '</td>' .
					'<td>' . tep_draw_input_field('postcode',$shippingAddress['entry_postcode'],'id="postcode2"') . '</td>' .
				'</tr>' .
			'</table>');

			$hiddenField = htmlBase::newElement('input')
			->setType('hidden')
			->setId('pid')
			->setValue($_GET['products_id']);

			$getQuotes->append($checkAddressBox)
			->append($checkAddressBoxZip)
			->append($hiddenField)
			->append($checkAddressButton);

			$table .= '<tr style="text-align:center">' .
					    '<td colspan="2" class="main" style="text-align:center">' . sysLanguage::get('TEXT_BEFORE_QUOTES') . $getQuotes->draw() . '</td>' .
			    	  '</tr>' ;
			$table .= '</table></div>';
		}

		return $table;
	}

	public function parseQuotes($quotes){
		global $currencies, $userAccount, $App;
		$table = '';
		if ($this->enabledShipping !== false){
			$table = '<table cellpadding="0" cellspacing="0" border="0">';

			$newMethods = array();

			foreach($quotes[0]['methods'] as $mInfo){
				if (!in_array($mInfo['id'], $this->enabledShipping)){
					continue;
				}
				$newMethods[] = $mInfo;
			}
			$quotes[0]['methods'] = $newMethods;
            $this->getMaxShippingDays = -1;
			for ($i=0, $n=sizeof($quotes); $i<$n; $i++) {
				$table .= '<tr>' .
				'<td><table border="0" width="100%" cellspacing="0" cellpadding="2">' .

				'<tr>' .
				'<td class="main" colspan="3"><b>' . $quotes[$i]['module'] . '</b>&nbsp;' . (isset($quotes[$i]['icon']) && ($quotes[$i]['icon'] != '') ? $quotes[$i]['icon'] : '') . '</td>' .
				'</tr>';

				for ($j=0, $n2=sizeof($quotes[$i]['methods']); $j<$n2; $j++) {

					if ($quotes[$i]['methods'][$j]['default'] == 1) {
						$checked = true;
					} else {
						$checked = false;
					}


					if ($this->getMaxShippingDays < $quotes[$i]['methods'][$j]['days_before']) {
						$this->getMaxShippingDays = (int) $quotes[$i]['methods'][$j]['days_before'];
					}
					if ($this->getMaxShippingDays < $quotes[$i]['methods'][$j]['days_after']) {
						$this->getMaxShippingDays = (int) $quotes[$i]['methods'][$j]['days_after'];
					}

					$table .= '<tr class="shipmethod row_'.$quotes[$i]['methods'][$j]['id'].'">' .
					'<td class="main" width="75%">' . $quotes[$i]['methods'][$j]['title'] . '</td>';

					if ( ($n > 1) || ($n2 > 1) ) {
						//$radioShipping = tep_draw_radio_field('rental_shipping', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'], $checked, 'days_before="' . $quotes[$i]['methods'][$j]['days_before'] . '" days_after="' . $quotes[$i]['methods'][$j]['days_after'] . '"');
						$radioShipping = '<input type="radio" '.(($checked==true)?'checked="checked"':'').' name="rental_shipping" value="'. $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'].'" days_before="' . $quotes[$i]['methods'][$j]['days_before'] . '" days_after="' . $quotes[$i]['methods'][$j]['days_after'] . '">';

						$table .= '<td class="main" class="cost_'.$quotes[$i]['methods'][$j]['id'].'">' . $currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['showCost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))) . '</td>' .
						'<td class="main" align="right">' . $radioShipping . '</td>';
					} else {
						$radioShipping = '<input type="radio" '.(($checked==true)?'checked="checked"':'').' name="rental_shipping" value="'. $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'].'" days_before="' . $quotes[$i]['methods'][$j]['days_before'] . '" days_after="' . $quotes[$i]['methods'][$j]['days_after'] . '">';
						$table .= '<td class="main" class="cost_'.$quotes[$i]['methods'][$j]['id'].'">' . $currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['showCost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))) . '</td>' .
						'<td class="main" align="right">' . $radioShipping . '</td>';
					}

					$table .= '</tr>';
				}
				$table .= '</table></td>' .
				'</tr>';
			}

			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHECK_GOOGLE_ZONES_BEFORE') == 'True' && $App->getEnv() == 'catalog'){
				$table1 = '<div class="checkgooglezones"><table cellpadding="0" cellspacing="0" border="0">';


				$checkAddressButton = htmlBase::newElement('button')
				->usePreset('continue')
				->setId('checkAddress')
				->setName('checkAdress')
				->setText( sysLanguage::get('TEXT_BUTTON_CHECK_ADDRESS'));

				$changeAddressButton = htmlBase::newElement('button')
				->usePreset('continue')
				->setId('changeAddress')
				->setName('changeAdress')
				->setText( sysLanguage::get('TEXT_BUTTON_CHANGE_ADDRESS'));

				$changeAddress = htmlBase::newElement('div');

				$checkAddressBox = htmlBase::newElement('div');

				$addressBook = $userAccount->plugins['addressBook'];
				$shippingAddress = $addressBook->getAddress('delivery');
				if(Session::exists('PPRaddressCheck')){
					$pprAddress = Session::get('PPRaddressCheck');
					$street = $pprAddress['address']['street_address'];
					$city = $pprAddress['address']['city'];
					$country = $pprAddress['address']['country'];
					$state = $pprAddress['address']['state'];
					$zip = $pprAddress['address']['postcode'];

				}else{
					$street = $shippingAddress['entry_street_address'];
					$city = $shippingAddress['entry_city'];
					$state = $shippingAddress['entry_state'];
					$zip = $shippingAddress['entry_postcode'];
					$country = isset($shippingAddress['entry_country'])?$shippingAddress['entry_country']:sysConfig::get('STORE_COUNTRY');

				}
				$checkAddressBox->html('<table border="0" cellspacing="2" cellpadding="2" id="googleAddress">' .
					'<tr>' .
						'<td>' . sysLanguage::get('ENTRY_STREET_ADDRESS') . '</td>' .
						'<td>' . tep_draw_input_field('street_address', $street,'id="street_address"') . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . sysLanguage::get('ENTRY_CITY') . '</td>' .
						'<td>' . tep_draw_input_field('city', $city,'id="city"') . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . sysLanguage::get('ENTRY_STATE') . '</td>' .
						'<td id="stateCol">' . tep_draw_input_field('state', $state,'id="state"') . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . sysLanguage::get('ENTRY_POST_CODE') . '</td>' .
						'<td>' . tep_draw_input_field('postcode', $zip,'id="postcode1"') . '</td>' .
					'</tr>' .
					'<tr>' .
						'<td>' . sysLanguage::get('ENTRY_COUNTRY') . '</td>' .
						'<td>' . tep_get_country_list('country', $country, 'id="countryDrop"') . '</td>' .
					'</tr>' .
				'</table>');

				ob_start();
				?>
				<script type="text/javascript">
					$(document).ready(function (){
						<?php
						if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHECK_GOOGLE_ZONES_BEFORE') == 'True' && $App->getEnv() == 'catalog'){
							if(Session::exists('PPRaddressCheck') === false){
								?>
								$('#googleAddress').show();
								$('#checkAddress').show();
								$('#changeAddress').hide();
								$('.dateRow').hide();
								<?php
							}else{
								?>
								$('#checkAddress').trigger('click');
								$('#checkAddress').hide();
								$('#googleAddress').hide();
								$('#changeAddress').show();
								$('.dateRow').show();

								<?php
							}

						}
						?>
					});
				</script>
				<?php
				$script = ob_get_contents();
				ob_end_clean();

				$changeAddress->append($checkAddressBox)
				->append($checkAddressButton)
				->append($changeAddressButton);

				$table1 .= '<tr style="text-align:center">' .
							'<td colspan="2" class="main" style="text-align:center">' .  $changeAddress->draw() .  '</td>' .
						  '</tr>' ;
				$table1 .= '</table></div>';
				$table .= '<tr><td>'.$table1. $script.'</td></tr>';
			}
			$table .= '</table>';

		}
		return $table;
	}

	public function getHiddenFields(){
		global $appExtension;
		$this->hiddenFields = array();

		$extAttributes = $appExtension->getExtension('attributes');
		if ($extAttributes && $extAttributes->isEnabled()){
			if (isset($_POST[$extAttributes->inputKey])){
				if (isset($_POST[$extAttributes->inputKey]['reservation'])){
					foreach($_POST[$extAttributes->inputKey]['reservation'] as $oID => $vID){
						$this->hiddenFields[] = tep_draw_hidden_field('id[reservation][' . $oID . ']', $vID);
					}
				}
				Session::remove('postedVars');
			}
		}

		if (isset($this->hiddenFields) && is_array($this->hiddenFields)){
			return implode("\n", $this->hiddenFields);
		}
	}

	public function overBookingAllowed(){
		return ($this->productInfo['overbooking'] == '1');
	}

	public function getProductsBarcodes(){
		return $this->inventoryCls->getInventoryItems($this->typeLong);
	}

	public function getBookedDaysArray($starting, $qty, &$reservationsArr, &$bookedDates, $usableBarcodes = array()){
		$reservationsArr = ReservationUtilities::getMyReservations(
			$this->productInfo['id'],
			$starting,
			$this->overBookingAllowed(),
			$usableBarcodes
		);
		//$bookedDates = array();
		foreach($reservationsArr as $iReservation){
			if(isset($iReservation['start']) && isset($iReservation['end'])){
				$startTime = strtotime($iReservation['start']);
				$endTime = strtotime($iReservation['end']);
				while($startTime<=$endTime){
					$dateFormated = date('Y-n-j', $startTime);
					if ($this->getTrackMethod() == 'barcode'){
						$bookedDates[$dateFormated]['barcode'][] = $iReservation['barcode'];
						//check if all the barcodes are already or make a new function to make checks by qty... (this function can return also the free barcode?)
					}else{
						if(isset($bookedDates[$dateFormated]['qty'])){
							$bookedDates[$dateFormated]['qty'] = $bookedDates[$dateFormated]['qty'] + 1;
						}else{
							$bookedDates[$dateFormated]['qty'] = 1;
						}
						//check if there is still qty available.
					}

					$startTime += 60*60*24;
				}
			}
		}
		$bookingsArr = array();
		$prodBarcodes = array();

		foreach($this->getProductsBarcodes() as $iBarcode){
			if(count($usableBarcodes) == 0 || in_array($iBarcode['id'], $usableBarcodes)){
					$prodBarcodes[] = $iBarcode['id'];
			}
		}
		//print_r($prodBarcodes);
		//echo '------------'.$qty;
		//print_r($bookedDates);

		if(count($prodBarcodes) < $qty){
			return false;
		}else{
			foreach($bookedDates as $dateFormated => $iBook){
				if ($this->getTrackMethod() == 'barcode'){
					$myqty = 0;
					foreach($iBook['barcode'] as $barcode){
						if(in_array($barcode,$prodBarcodes)){
							$myqty ++;
						}
					}
					if(count($prodBarcodes) - $myqty<$qty){
						$bookingsArr[] = $dateFormated;
					}
				}else{
					if($prodBarcodes['available'] - $iBook['qty'] < $qty){
						$bookingsArr[] = $dateFormated;
					}
				}
			}
		}
		return $bookingsArr;
	}

	public function getBookedTimeDaysArray($starting, $qty, $minTime, &$reservationsArr, &$bookedDates){
		/*$reservationsArr = ReservationUtilities::getMyReservations(
			$this->productInfo['id'],
			$starting,
			$this->overBookingAllowed()
		);*/
		$bookedTimes = array();
		//print_r($bookedDates);
		//print_r($reservationsArr);


		foreach($reservationsArr as $iReservation){
			if(isset($iReservation['start_time']) && isset($iReservation['end_time'])){
				$startTime = strtotime($iReservation['start_date'].' '.$iReservation['start_time']);
				$endTime = strtotime($iReservation['start_date'].' '.$iReservation['end_time']);
				while($startTime<=$endTime){
					$dateFormated = date('Y-n-j H:i', $startTime);
					if ($this->getTrackMethod() == 'barcode'){
						$bookedTimes[$dateFormated]['barcode'][] = $iReservation['barcode'];
						if(isset($bookedDates[$iReservation['start_date']]['barcode'])){
							foreach($bookedDates[$iReservation['start_date']]['barcode'] as $iBarc){
								$bookedTimes[$dateFormated]['barcode'][] = $iBarc;
							}
						}
						//check if all the barcodes are already or make a new function to make checks by qty... (this function can return also the free barcode?)
					}else{
						if(isset($bookedTimes[$dateFormated]['qty'])){
							$bookedTimes[$dateFormated]['qty'] = $bookedTimes[$dateFormated]['qty'] + 1;
						}else{
							$bookedTimes[$dateFormated]['qty'] = 1;
						}
						if(isset($bookedDates[$iReservation['start_date']]['qty'])){
							  $bookedTimes[$dateFormated]['qty'] = $bookedTimes[$dateFormated]['qty'] + count($bookedDates[$iReservation['start_date']]['qty']);
						}
						//check if there is still qty available.
					}

					$startTime += $minTime*60;
				}
			}
		}
		$bookingsArr = array();
		$prodBarcodes = array();
		foreach($this->getProductsBarcodes() as $iBarcode){
			$prodBarcodes[] = $iBarcode['id'];
		}

		foreach($bookedTimes as $dateFormated => $iBook){
			if ($this->getTrackMethod() == 'barcode'){
				$myqty = 0;
				foreach($iBook['barcode'] as $barcode){
					if(in_array($barcode,$prodBarcodes)){
						$myqty ++;
					}
				}
				if(count($prodBarcodes) - $myqty<$qty){
					$bookingsArr[] = $dateFormated;
				}
			}else{
				if($prodBarcodes['available'] - $iBook['qty'] < $qty){
					$bookingsArr[] = $dateFormated;
				}
			}
		}

		return $bookingsArr;
	}



































	public function getReservations($start, $end){
		$booked = ReservationUtilities::getReservations(
			$this->productInfo['id'],
			$start,
			$end,
			$this->overBookingAllowed()
		);

		return $booked;
	}

	public function dateIsBooked($date, $bookedDays, $invItems, $qty = 1){
		if ($invItems === false){
			return true;
		}
		$totalAvail = 0;
		foreach($invItems as $item){
			if ($this->getTrackMethod() == 'barcode'){
				if (!isset($bookedDays['barcode'][$date]) || !in_array($item['id'], $bookedDays['barcode'][$date])){
					$totalAvail++;
				}
			}elseif ($this->getTrackMethod() == 'quantity'){
				$realAvail = ($item['available'] + $item['reserved'])/* - $Qcheck[0]['total']*/;
				if (!isset($bookedDays['quantity'][$date]) || !isset($bookedDays['quantity'][$date][$item['id']])){
					$totalAvail += $realAvail;
				}elseif ($realAvail > $qty){
					$totalAvail += $realAvail;
				}
			}

			if ($totalAvail >= $qty){
				break;
			}
		}
		if ($totalAvail >= $qty){
			return false;
		}else{
			if ($this->overBookingAllowed() === true){
				return false;
			}else{
				return true;
			}
		}
	}

	public function findBestPrice($dateArray){
		global $currencies;
        $dateArray['end'] = $this->addDays($dateArray['end']);

        $price = 0;
        $start = date_parse($dateArray['start']);
        $end = date_parse($dateArray['end']);
        $startTime = mktime($start['hour'], $start['minute'], $start['second'], $start['month'], $start['day'], $start['year']);
        $endTime = mktime($end['hour'], $end['minute'], $end['second'], $end['month'], $end['day'], $end['year']);

        $nMinutes = (($endTime - $startTime) / 60) ;
		$minutesArray = array();

		$QPricePerRentalProducts = Doctrine_Query::create()
		->from('PricePerRentalPerProducts pprp')
		->where('pprp.pay_per_rental_id =?', $this->getId())
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$QPayPerRentalTypes = Doctrine_Query::create()
		->from('PayPerRentalTypes')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$pprTypes = array();
		$pprTypesDesc = array();
		foreach($QPayPerRentalTypes as $iType){
			$pprTypes[$iType['pay_per_rental_types_id']] = $iType['minutes'];
			$pprTypesDesc[$iType['pay_per_rental_types_id']] = $iType['pay_per_rental_types_name'];
		}

		foreach ($QPricePerRentalProducts as $iPrices) {
			$minutesArray[$iPrices['number_of']*$pprTypes[$iPrices['pay_per_rental_types_id']]] = $iPrices['price'];
			$messArr[$iPrices['number_of']*$pprTypes[$iPrices['pay_per_rental_types_id']]] = $iPrices['number_of'].' '.$pprTypesDesc[$iPrices['pay_per_rental_types_id']];
		}
		ksort($minutesArray);
		ksort($messArr);

		$firstMinUnity = $messArr[key($messArr)];
		$firstMinMinutes = key($messArr);
		$myKeys = array_keys($minutesArray);
		$message = sysLanguage::get('PPR_PRICE_BASED_ON');
		//if(count($myKeys) > 1) {
		$is_bigger = true;
		for ($i = 0; $i < count($myKeys); $i++) {
			if ($myKeys[$i] > $nMinutes) {
				$biggerPrice = $minutesArray[$myKeys[$i]];
				if ($i > 0) {
					$normalPrice = (float)($minutesArray[$myKeys[$i - 1]] / $myKeys[$i - 1]) * $nMinutes;
				} else {
					$normalPrice = -1;
				}
				if ($normalPrice > $biggerPrice || $normalPrice == -1) {
					$price = $biggerPrice;
					$message .= '1X' . substr($messArr[$myKeys[$i]], 0, strlen($messArr[$myKeys[$i]]) - 1) . '@' . $currencies->format($minutesArray[$myKeys[$i]]);
				} else {
					$price = $normalPrice;
					$message .= (int)($nMinutes / $myKeys[$i - 1]) . 'X' . $messArr[$myKeys[$i - 1]] . '@' . $currencies->format($minutesArray[$myKeys[$i - 1]]) . '/' . substr($messArr[$myKeys[$i - 1]], 0, strlen($messArr[$myKeys[$i - 1]]) - 1);
					if ($nMinutes % $myKeys[$i - 1] > 0) {
						$message .= ' + ' . number_format($nMinutes % $myKeys[$i - 1] / $firstMinMinutes, 2) . 'X' . $firstMinUnity . '@' . $currencies->format((float)($minutesArray[$myKeys[$i - 1]] / $myKeys[$i - 1] * $firstMinMinutes)) . '/' . $firstMinUnity;
					}
				}
				$is_bigger = false;
				break;
			}
		}
		if ($is_bigger) {
			$i = count($myKeys) - 1;
			$normalPrice = (float)($minutesArray[$myKeys[$i]] / $myKeys[$i]) * $nMinutes;
			$price = $normalPrice;
			$message .= (int)($nMinutes / $myKeys[$i]) . 'X' . $messArr[$myKeys[$i]] . '@' . $currencies->format($minutesArray[$myKeys[$i]]) . '/' . substr($messArr[$myKeys[$i]], 0, strlen($messArr[$myKeys[$i]]) - 1);
			if ($nMinutes % $myKeys[$i] > 0) {
				$message .= ' + ' . number_format($nMinutes % $myKeys[$i] / $firstMinMinutes, 2) . ' X' . $firstMinUnity . '@' . $currencies->format((float)($minutesArray[$myKeys[$i]] / $myKeys[$i] * $firstMinMinutes)) . '/' . $firstMinUnity;
			}
		}

        $return['price'] = round($price,2);
        if(sysconfig::get('EXTENSION_PAY_PER_RENTALS_SHORT_PRICE') == 'False'){
			$return['message'] = $message;
        }else{
	        $return['message'] = '';
        }
		return $return;
	}

    public function addDays($date){
        $days = 0;
		switch(sysConfig::get('EXTENSION_PAY_PER_RENTALS_LENGTH_METHOD')){
			case 'First':
			case 'Last':
				$days = 0;
				break;
			case 'Both':
				$days+=1;
				break;
			default:
				$days = ($days <= 0 ? 1 : $days);
				break;
		}
		$date = date('Y-m-d H:i:s', strtotime('+' . $days .' days', strtotime($date)));
		return $date;
	}

	public function getReservationPrice($start, $end, &$rInfo = '', $semName = '', $includeInsurance = false){
		global $currencies;
		$productPricing = array();


		if ($rInfo != '' && isset($rInfo['shipping']) && isset($rInfo['shipping']['cost'])){
			$productPricing['shipping'] = $rInfo['shipping']['cost'];
		}elseif (isset($_POST['rental_shipping']) && tep_not_null($_POST['rental_shipping']) && $_POST['rental_shipping'] != 'undefined'){
			$shippingMethod = explode('_', $_POST['rental_shipping']);
			$Module = OrderShippingModules::getModule($shippingMethod[0]);
			$product = new product($this->productInfo['id']);
			if(isset($_POST['rental_qty'])){
 	            $total_weight = (int)$_POST['rental_qty'] * $product->getWeight();
			}else{
				$total_weight = $product->getWeight();
			}
			$quote = $Module->quote($shippingMethod[1], $total_weight);

			if ($quote['methods'][0]['cost'] > 0){
				$productPricing['shipping'] = (float)$quote['methods'][0]['cost'];
			}
		}

		$dateArray = array(
			'start' => $start,
			'end'   => $end
		);

		$f = true;
		if (isset($rInfo['semester_name']) && $rInfo['semester_name'] == ''){
			$f = true;
		}else{
			if(!isset($rInfo['semester_name'])){
				$f = true;
			}else{
				$f = false;
			}
		}
		if($semName == '' && $f){
			$returnPrice = $this->findBestPrice($dateArray);
		}else{
			if($semName == ''){
				$semName = $rInfo['semester_name'];
			}
			$returnPrice['price'] = $this->getPriceSemester($semName);
			$returnPrice['message'] = sysLanguage::get('PPR_PRICE_BASED_ON_SEMESTER').$semName.' ';
		}

		if (is_array($returnPrice)){


			if (isset($productPricing['shipping'])){
				$returnPrice['price'] += $productPricing['shipping'];
				$returnPrice['message'] .= ' + '. $currencies->format($productPricing['shipping']). ' '. sysLanguage::get('EXTENSION_PAY_PER_RENTALS_CALENDAR_SHIPPING');
			}
			if ($this->getDepositAmount() > 0){
				$returnPrice['price'] += $this->getDepositAmount();
				$returnPrice['message'] .= ' + '. $currencies->format($this->getDepositAmount()).' '. sysLanguage::get('EXTENSION_PAY_PER_RENTALS_CALENDAR_DEPOSIT') ;
			}

			if (isset($rInfo['insurance'])){
				$returnPrice['price'] += (float)$rInfo['insurance'];
			}elseif($includeInsurance){
				$payPerRentals = Doctrine_Query::create()
				->select('insurance')
				->from('ProductsPayPerRental')
				->where('products_id = ?', $this->productInfo['id'])
				->fetchOne();
				$rInfo['insurance'] = $payPerRentals->insurance;
				$returnPrice['price'] += (float)$rInfo['insurance'];
				$returnPrice['message'] .= ' + '. $currencies->format($rInfo['insurance']).' '. sysLanguage::get('EXTENSION_PAY_PER_RENTALS_CALENDAR_INSURANCE') ;
			}

			EventManager::notify('PurchaseTypeAfterSetup', &$returnPrice);
		}
		return $returnPrice;
	}

	public function figureProductPricing(&$pID_string, $externalResInfo = false){
		global $ShoppingCart;

		if ($externalResInfo === true){
			$rInfo = $ShoppingCart->reservationInfo;
		}elseif (is_array($pID_string)){
			$rInfo =& $pID_string;
		}else{
			$cartProduct = $ShoppingCart->contents->find($pID_string, 'reservation');
			$rInfo = $cartProduct->getInfo('reservationInfo');
		}

		$pricing = $this->getReservationPrice($rInfo['start_date'], $rInfo['end_date'], &$rInfo);

		return $pricing;
	}

	public function formatDateArr($format, $date){
		return date($format,mktime($date['hour'],$date['minute'],$date['second'],$date['month'],$date['day'],$date['year']));
	}
}
?>