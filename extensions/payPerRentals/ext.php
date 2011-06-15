<?php
/*
  Pay Per Rentals Version 1

  I.T. Web Experts, Rental Store v2
  http://www.itwebexperts.com

  Copyright (c) 2009 I.T. Web Experts

  This script and it's source is not redistributable
 */
class Extension_payPerRentals extends ExtensionBase {

	public function __construct(){
		parent::__construct('payPerRentals');
	}

	public function init(){
		global $App, $appExtension, $typeNames, $inventoryTypes;
		if ($this->enabled === false)
			return;

		$typeNames['reservation'] = 'Reservation';
		$inventoryTypes['reservation'] = 'Reservation';

		EventManager::attachEvents(array(
			'ApplicationTopActionCheckPost',
			'OrderQueryBeforeExecute',
			'OrderClassQueryFillProductArray',
			'ProductInventoryBarcodeGetItemCount',
			'ProductInventoryQuantityGetItemCount',
			'ApplicationTopAction_reserve_now',
			'BoxMarketingAddLink',
			'OrderBeforeSendEmail',
            'ProductBeforeTaxAddress',
			'NewProductAddBarcodeListingHeader',
			'NewProductAddBarcodeListingBody',
			'ApplicationTopAction_add_reservation_product',
			'CouponEditPurchaseTypeBeforeOutput',
			'CouponEditBeforeSave',
			'UpdateTotalsCheckout',
			'CouponsPurchaseTypeRestrictionCheck'
		), null, $this);

		if ($appExtension->isCatalog()){
			EventManager::attachEvent('ProductListingQueryBeforeExecute', null, $this);
			EventManager::attachEvent('ProductSearchQueryBeforeExecute', null, $this);
		}else{
			EventManager::attachEvent('OrderInfoAddBlock', null, $this);
			EventManager::attachEvent('OrderShowExtraPackingData', null, $this);
		}

		/*
		 * Shopping Cart Actions --BEGIN--
		 */
		require(dirname(__FILE__) . '/classEvents/ShoppingCart.php');
		$eventClass = new ShoppingCart_payPerRentals();
		$eventClass->init();

		require(dirname(__FILE__) . '/classEvents/ShoppingCartProduct.php');
		$eventClass = new ShoppingCartProduct_payPerRentals();
		$eventClass->init();

		require(dirname(__FILE__) . '/classEvents/ShoppingCartDatabase.php');
		$eventClass = new ShoppingCartDatabase_payPerRentals();
		$eventClass->init();

		require(dirname(__FILE__) . '/classes/Utilities.php');
		/*load google api key per store*/
		$multiStore = $appExtension->getExtension('multiStore');
		if ($multiStore !== false && $multiStore->isEnabled() === true){
			$QstoreInfo = Doctrine_Query::create()
			->select('google_key')
			->from('Stores')
			->where('stores_id = ?', $multiStore->getStoreId())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			Session::set('google_key', $QstoreInfo[0]['google_key']);
		}else{
			Session::set('google_key', sysConfig::get('EXTENSION_PAY_PER_RENTALS_GOOGLE_MAPS_API_KEY'));
		}
	}

	public function UpdateTotalsCheckout(){
		global $onePageCheckout, $ShoppingCart;
		$weight = 0;
		$selectedMethod = '';

		foreach($ShoppingCart->getProducts() as $cartProduct) {
					if ($cartProduct->hasInfo('reservationInfo') === true){
						$reservationInfo1 = $cartProduct->getInfo('reservationInfo');
						if(isset($reservationInfo1['shipping']) && isset($reservationInfo1['shipping']['module']) && $reservationInfo1['shipping']['module'] == 'zonereservation'){
							$selectedMethod = $reservationInfo1['shipping']['id'];
							$weight += $cartProduct->getWeight();
						}
					}
		}

		$Module = OrderShippingModules::getModule('zonereservation', true);
		$quotes = array($Module->quote($selectedMethod, $weight));


		$onePageCheckout->onePage['info']['reservationshipping'] = array(
								'id'     => 'zonereservation_'.$quotes[0]['methods'][0]['id'],
								'module' => 'zonereservation',
								'method' => 'zonereservation',
								'title'  => $quotes[0]['methods'][0]['title'],
								'cost'   => $quotes[0]['methods'][0]['cost']
		);
	}
	
	public function CouponEditPurchaseTypeBeforeOutput(&$checkbox, $name, $Coupon){
		if ($name == 'reservation'){
			$periodBox = htmlBase::newElement('selectbox')
			->setName('min_reservation_period')
			//->addOption('h', 'Hour(s)');
			->addOption('d', 'Day(s)')
			->addOption('w', 'Week(s)')
			->addOption('m', 'Month(s)')
			->addOption('y', 'Year(s)')
			->selectOptionByValue($Coupon->min_reservation_period);
			
			$timeBox = htmlBase::newElement('input')
			->attr('size', '4')
			->setName('min_reservation_time')
			->val($Coupon->min_reservation_time);
			
			$checkbox = '<table cellpadding="0" cellspacing="0" border="0">' . 
				'<tr>' . 
					'<td>' . $checkbox . '</td>' . 
					'<td>&nbsp;Min Length:</td>' . 
					'<td>&nbsp;' . $timeBox->draw() . ' ' . $periodBox->Draw() . '</td>' . 
				'</tr>' . 
			'</table>';
		}
	}
	
	public function CouponEditBeforeSave($Coupon){
		if (isset($_POST['restrict_to_purchase_type']) && in_array('reservation', $_POST['restrict_to_purchase_type'])){
			$Coupon->min_reservation_time = $_POST['min_reservation_time'];
			$Coupon->min_reservation_period = $_POST['min_reservation_period'];
		}
	}
	
	public function CouponsPurchaseTypeRestrictionCheck($cartProduct, $Coupon, &$success){
		if ($success === true && $cartProduct->getPurchaseType() == 'reservation'){
			$minTime = $Coupon['min_reservation_time'];
			$minPeriod = $Coupon['min_reservation_period'];
			
			$resInfo = $cartProduct->getInfo('reservationInfo');
			$startParsed = date_parse($resInfo['start_date']);
			$endParsed = date_parse($resInfo['end_date']);
			
			$startTime = mktime(
				$startParsed['hour'],
				$startParsed['minute'],
				$startParsed['second'],
				$startParsed['month'],
				$startParsed['day'],
				$startParsed['year']
			);
			$endTime = mktime(
				$endParsed['hour'],
				$endParsed['minute'],
				$endParsed['second'],
				$endParsed['month'],
				$endParsed['day'],
				$endParsed['year']
			);
			
			$timeDiff = $endTime - $startTime;
			
			switch($minPeriod){
				case 'h':
					$checkTime = floor($timeDiff/(60*60));
					break;
				case 'd':
					$checkTime = floor($timeDiff/(60*60*24));
					break;
				case 'w':
					$checkTime = floor($timeDiff/(60*60*24*7));
					break;
				case 'm':
					$checkTime = floor($timeDiff/(60*60*24*30));
					break;
				case 'y':
					$checkTime = floor($timeDiff/(60*60*24*365));
					break;
			}

			if ($minTime > $checkTime){
				$success = false;
			}
		}
	}

	public function ApplicationTopActionCheckPost(&$action){
		if (isset($_POST['reserve_now']) || (isset($_GET['action']) && $_GET['action'] == 'reserve_now')){
			$action = 'reserve_now';
		}elseif (isset($_POST['add_reservation_product']) || (isset($_GET['action']) && $_GET['action'] == 'add_reservation_product')){
			$action = 'add_reservation_product';
		}
	}

	public function ProductInventoryQuantityGetItemCount(&$invData, &$invItem, &$addTotal){
		if ($invData['type'] == 'reservation'){
			$today = date('Y-n-j', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
			$plusFive = date('Y-n-j', mktime(0, 0, 0, date('m'), date('d') + 5, date('Y')));

			$Qreserved = Doctrine_Query::create()
					->select('count(orders_products_reservations_id) as total')
					->from('OrdersProducts op')
					->leftJoin('op.OrdersProductsReservation opr')
					->where('op.products_id = ?', $invData['products_id'])
					->andWhere('opr.track_method = ?', 'quantity')
					->andWhere('((opr.start_date between CAST("' . $today . '" as DATE) and CAST("' . $plusFive . '" as DATE)) or (opr.end_date between CAST("' . $today . '" as DATE) and CAST("' . $plusFive . '" as DATE)))')
					->execute();
			if ($Qreserved && $Qreserved[0]->total > $invItem['available']){
				$addTotal = false;
			}
		}
	}

	public function ProductInventoryBarcodeGetItemCount(&$invData, &$invItem, &$addTotal){
		if ($invData['type'] == 'reservation'){
			$today = date('Y-n-j', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
			$plusFive = date('Y-n-j', mktime(0, 0, 0, date('m'), date('d') + 5, date('Y')));

			$Qreserved = Doctrine_Query::create()
					->select('orders_products_reservations_id')
					->from('OrdersProductsReservation')
					->where('barcode_id = ?', $invItem['id'])
					->andWhere('track_method = ?', 'barcode')
					->andWhere('((start_date between CAST("' . $today . '" as DATE) and CAST("' . $plusFive . '" as DATE)) or (end_date between CAST("' . $today . '" as DATE) and CAST("' . $plusFive . '" as DATE)))')
					->fetchOne();
			if ($Qreserved){
				$addTotal = false;
			}
		}
	}

	public function OrderQueryBeforeExecute(&$Qorder){
		$Qorder->leftJoin('op.OrdersProductsReservation opr')
			->leftJoin('opr.ProductsInventoryBarcodes pib')
			->leftJoin('opr.ProductsInventoryQuantity piq');
	}


	public function ApplicationTopAction_reserve_now(){
		global $messageStack, $appExtension;

		if (Session::exists('post_array') && isset($_POST['is_change_address'])){
			$_POST = array_merge($_POST, Session::get('post_array'));
			Session::remove('post_array');
		}

		$pID = (int) tep_get_prid($_GET['products_id']);
		$product = new product($pID);
		$purchaseType = $product->getPurchaseType('reservation');
	
		if ($purchaseType->hasInventory() === false){
			$messageStack->addSession('pageStack', 'This product has no inventory for reservations', 'error');
			tep_redirect(itw_app_link(tep_get_all_get_params(array('action', 'appExt')), 'product', 'info'));
		}

		$extAttributes = $appExtension->getExtension('attributes');
		if ($extAttributes && $extAttributes->isEnabled() === true) {
			if (attributesUtil::productHasAttributes($pID, 'reservation')) {
				if (!isset($_POST) || !isset($_POST[$extAttributes->inputKey]) || !isset($_POST[$extAttributes->inputKey]['reservation'])) {
					$messageStack->addSession('pageStack', 'This product has attributes to select', 'warning');
					tep_redirect(itw_app_link('products_id=' . $pID, 'product', 'info'));
				}
			}
		}

		if (empty($_POST)){
			tep_redirect(itw_app_link(tep_get_all_get_params(array('action', 'appExt')) . 'appExt=payPerRentals', 'build_reservation', 'default'));
		}
	}

	public function ApplicationTopAction_add_reservation_product(){
		if(is_array($_POST['products_id'])){
			$productID = $_POST['products_id'][0];
		}else{
			$productID = $_POST['products_id'];
		}
		$qty = $_POST['rental_qty'];
		ReservationUtilities::addReservationProductToCart($productID, $qty);
		tep_redirect(itw_app_link(null, 'shoppingCart', 'default'));
	}

	public function OrderClassQueryFillProductArray(&$pInfo, &$product){
		$Reservations = $pInfo['OrdersProductsReservation'];
		if (sizeof($Reservations) > 0){
			$mainReservation = false;
			foreach($Reservations as $rInfo){
				if (is_null($rInfo['parent_id'])){
					$mainReservation = $rInfo;
					break;
				}
			}

			if ($mainReservation !== false){
				if ($mainReservation['track_method'] == 'barcode'){
					$product['barcode_number'] = $mainReservation['ProductsInventoryBarcodes']['barcode'];
				}

				$product['reservationInfo'] = array(
					'start_date' => $mainReservation['start_date'],
					'end_date' => $mainReservation['end_date'],
					'insurance' => $mainReservation['insurance'],
					'quantity' =>  (isset($mainReservation['rental_qty'])?$mainReservation['rental_qty']:1)
				);

				if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
					$product['reservationInfo']['event_date'] = $mainReservation['event_date'];
				    $product['reservationInfo']['event_name'] = $mainReservation['event_name'];
			    }
				$product['reservationInfo']['semester_name'] = $mainReservation['semester_name'];

				if (isset($mainReservation['shipping_method']) && !empty($mainReservation['shipping_method'])){
					$product['reservationInfo']['shipping'] = array(
						'title' => $mainReservation['shipping_method_title'],
						'cost' => $mainReservation['shipping_cost'],
						'id' => $mainReservation['shipping_method'],
						'days_before' => $mainReservation['shipping_days_before'],
						'days_after' => $mainReservation['shipping_days_after']
					);
				}

				EventManager::notify('Extension_payPerRentalsOrderClassQueryFillProductArray', &$mainReservation, &$product);
			}else{
				$product['name'] .= '<br />NO VALID ROOT RESERVATION!';
			}
		}
	}

	public function BoxMarketingAddLink(&$contents){
		$contents['children'][] = array(
			'link'       => itw_app_link('appExt=payPerRentals','show_reports','default_orders','SSL'),
			'text' => 'Rental Order Reports'
		);
		$contents['children'][] = array(
			'link'       => itw_app_link('appExt=payPerRentals','reservations_reports','default','SSL'),
			'text' => 'Rental Inventory Report'
		);
	}

	public function ProductListingQueryBeforeExecute(&$Qproducts){
		$Qproducts->leftJoin('p.ProductsPayPerRental pppr');
		$Qproducts->leftJoin('pppr.PricePerRentalPerProducts ppprp');
		$Qproducts->leftJoin('p.PayPerRentalHiddenDates pprhd');
		if(Session::exists('isppr_date_start') && (Session::get('isppr_date_start') != '') && Session::exists('isppr_date_end') && (Session::get('isppr_date_end') != '')){
			//i update hidden_start_dates for every run
			$QHiddenDatesUpdateStart = Doctrine_Query::create()
			->from('PayPerRentalHiddenDates')
			->where('hidden_start_date < ?', date('Y-m-d'))
			->andWhere('hidden_end_date < ?', date('Y-m-d'))
			->execute();
			foreach($QHiddenDatesUpdateStart as $iHidden){
				$iHidden->hidden_start_date = date('Y-m-d', strtotime('+1 year', strtotime($iHidden->hidden_start_date)));
				$iHidden->hidden_end_date = date('Y-m-d', strtotime('+1 year', strtotime($iHidden->hidden_end_date)));
				$iHidden->save();
			}


			$Qproducts->andWhere('hidden_start_date is null OR
								  hidden_end_date is null OR
								  hidden_start_date NOT BETWEEN CAST("'.date('Y-m-d',strtotime(Session::get('isppr_date_start'))).'" AS DATE) AND CAST("'.date('Y-m-d', strtotime(Session::get('isppr_date_end'))).'" AS DATE) AND
								  hidden_end_date NOT BETWEEN CAST("'.date('Y-m-d',strtotime(Session::get('isppr_date_start'))).'" AS DATE) AND CAST("'.date('Y-m-d', strtotime(Session::get('isppr_date_end'))).'" AS DATE) AND
			                      CAST("'.date('Y-m-d', strtotime(Session::get('isppr_date_end'))).'" AS DATE) NOT BETWEEN hidden_start_date AND hidden_end_date
			');
		}
	}

	public function ProductSearchQueryBeforeExecute(&$Qproducts){
		$Qproducts->leftJoin('p.ProductsPayPerRental pppr');
		$Qproducts->leftJoin('pppr.PricePerRentalPerProducts ppprp');
	}

	public function NewProductAddBarcodeListingBody(&$bInfo, &$currentBarcodesTableBody){

		$currentBarcodesTableBody[1]['text'] = $currentBarcodesTableBody[1]['text'] . "<a href='" . itw_app_link('appExt=payPerRentals&product_id=' . $_GET['pID'] . '&barcode_id=' . $bInfo['barcode_id'], 'show_reports', 'default_orders') . "'>&nbsp;&nbsp;<img src='images/money-icon.png' alt='Rental Orders Report'/></a>";
	}

	public function NewProductAddBarcodeListingHeader(&$currentBarcodesTableHeaders){
		
	}

	 public function ProductBeforeTaxAddress(&$zoneId, &$countryId, $product, $order, $userAccount){
        $pInfo = $product->getInfo();
        if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
            $shippingTitles = explode(',', sysConfig::get('EXTENSION_PAY_PER_RENTALS_TAX_PER_EVENT_ADDRESS'));
            if (isset($pInfo['reservationInfo']['shipping']['title']) && !empty($pInfo['reservationInfo']['shipping']['title'])){
                if (in_array($pInfo['reservationInfo']['shipping']['title'], $shippingTitles)){
                    $eventsTable = Doctrine_Core::getTable('PayPerRentalEvents')->findOneByEventsName($pInfo['reservationInfo']['event_name']);
                    $zoneId = $eventsTable->events_zone_id;
                    $countryId = $eventsTable->events_country_id;
                }
            }
        }
    }
	public function OrderBeforeSendEmail(&$order, &$emailEvent, &$products_ordered){

			$Qorders = Doctrine_Query::create()
					->from('Orders o')
					->leftJoin('o.OrdersProducts op')
					->leftJoin('op.OrdersProductsReservation ops')
					->where('o.orders_id =?', $order['orderID'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			//here the order must exists...
			if(count($Qorders) > 0){
				if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True' && sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_EVENT_EMAIL') == 'True'){
					if (isset($Qorders[0]['OrdersProducts'][0]['OrdersProductsReservation'][0]['event_name'])){
						$evInfo = ReservationUtilities::getEvent($Qorders[0]['OrdersProducts'][0]['OrdersProductsReservation'][0]['event_name']);
						$emailEvent->setVar('event_description', $evInfo['events_details']);
					}
				}
				if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_TERMS_EMAIL') == 'True'){
					$emailEvent->setVar('terms', $Qorders[0]['terms']);
				}
			}

		}

		public function OrderShowExtraPackingData(&$order){			
			$QOrder = Doctrine_Query::create()
				->from('Orders o')
				->leftJoin('o.OrdersProducts op')
				->leftJoin('op.OrdersProductsReservation ops')
				->where('o.orders_id = ?', $_GET['oID'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
				$evInfo = ReservationUtilities::getEvent($QOrder[0]['OrdersProducts'][0]['OrdersProductsReservation'][0]['event_name']);
				$htmlEventDetails = '<br/><br/><b>Event Details:</b><br/>' .  trim($evInfo['events_details']);
				$htmlTermsDetails = $QOrder[0]['terms'];
			}

			if (isset($htmlEventDetails)){
				echo $htmlEventDetails;
			}
			echo '<br/>';
			if (isset($htmlTermsDetails)){
				echo $htmlTermsDetails;
			}
		}
		public function OrderInfoAddBlock($orderId){
		return
			'<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' .
				'<table cellpadding="3" cellspacing="0">' .
					'<tr>' .
						'<td><table cellpadding="3" cellspacing="0">' .
							'<tr>
							 <td class="main" valign="top">' . '<a target="_blank" href="'.itw_app_link('oID='.$orderId,'orders','printTerms').'">Print Terms and Conditions The Client Agreed</a></td>
							</tr>
						</table></td>' .
					'</tr>' .
				'</table>' .
			'</div>';
	}

	
}
?>