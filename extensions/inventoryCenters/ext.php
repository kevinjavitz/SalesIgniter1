<?php
/*
	Inventory Centers Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
 
class Extension_inventoryCenters extends ExtensionBase {

	public function __construct(){
		parent::__construct('inventoryCenters');
		$this->stockMethod = 'Zone';

		if ($this->isEnabled() === true){
			$this->stockMethod = sysConfig::get('EXTENSION_INVENTORY_CENTERS_STOCK_METHOD');
		}
		$this->ignoreCenter = (sysConfig::get('EXTENSION_INVENTORY_CENTERS_IGNORE_STOCK_METHOD') == 'True')?true:false;
	}
	
	public function init(){
		global $appExtension;
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'ApplicationTopBeforeCartAction',
			'ProductInventoryQuantityGetInventoryItemsArrayPopulate',
			'ProductInventoryBarcodeGetInventoryItemsArrayPopulate',
			'ProductListingQueryBeforeExecute',
			'ProductIsInInventory',
			'MonthlySalesAddFilters',
			'OrdersListingBeforeExecuteLeft',
			'OrdersProductsReservationListingBeforeExecuteUtilities'
		), null, $this);
		
		if ($appExtension->isEnabled('payPerRentals') === true){
			EventManager::attachEvents(array(
				'ReservationProcessAddToCart',
				'ReservationCheckQueryAfterExecute',
				'ParseReservationInfo',
				'ProductInventoryReportsListingQueryBeforeExecute',
				'ProductsInventoryReportsDefaultAddFilterOptions',
				'ReservationOnInsertOrderedProduct',
				'Extension_payPerRentalsOrderClassQueryFillProductArray',
				'ReservationAppendOrderedProductsString',
				'ReservationFormatOrdersReservationArray',
				'ReservationProcessAddToOrder',
				'ReservationProcessUpdateCart',
				'OrderBeforeSendEmail'
			), null, $this);
		}	
		
		if ($appExtension->isAdmin()){
			EventManager::attachEvent('BoxMarketingAddLink', null, $this);
		}
		if ($appExtension->isCatalog()){
			EventManager::attachEvents(array(
					'ProductInventoryQuantityHasInventoryQueryBeforeExecute',
					'ProductInventoryBarcodeHasInventoryQueryBeforeExecute',
					'ProductInventoryBarcodeUpdateStockQueryBeforeExecute',
					'ProductInventoryBarcodeGetInventoryItemsQueryBeforeExecute',
					'ProductInventoryQuantityUpdateStockQueryBeforeExecute',
					'ProductInventoryQuantityGetInventoryItemsQueryBeforeExecute'
			), null, $this);
		}
	}
	
	public function ProductListingQueryBeforeExecute(&$Qproducts){
        global $appExtension;
		$Qproducts->leftJoin('p.ProductsInventory i')
			->leftJoin('i.ProductsInventoryBarcodes b')
			->leftJoin('b.ProductsInventoryBarcodesToInventoryCenters b2c')
			->leftJoin('b2c.ProductsInventoryCenters ic');
        if ($appExtension->isEnabled('streamProducts') === true){
            $Qproducts->leftJoin('p.ProductsStreams ps')
                ->orwhere('p.products_id = ps.products_id');
        }
        if ($appExtension->isEnabled('downloadProducts') === true){
            $Qproducts->leftJoin('p.ProductsDownloads pdl')
                ->orwhere('p.products_id = pdl.products_id');
        }
		if (Session::exists('isppr_inventory_pickup') === true && Session::get('isppr_inventory_pickup') != ''){
			$Qproducts->andWhere('ic.inventory_center_id = ?', Session::get('isppr_inventory_pickup'));
		}
		if($this->ignoreCenter == false){
			$Qproducts->andWhere('i.use_center = ?', '1');
		}
		if (Session::exists('isppr_continent') === true && Session::get('isppr_continent') != ''){
			$Qproducts->andWhere('ic.inventory_center_continent = ?', Session::get('isppr_continent'));
		}
		if (Session::exists('isppr_country') === true && Session::get('isppr_country') != ''){
			$Qproducts->andWhere('ic.inventory_center_country = ?', Session::get('isppr_country'));
		}
		if (Session::exists('isppr_state') === true && Session::get('isppr_state') != ''){
			$Qproducts->andWhere('ic.inventory_center_state = ?', Session::get('isppr_state'));
		}
		if (Session::exists('isppr_city') === true && Session::get('isppr_city') != ''){
			$Qproducts->andWhere('ic.inventory_center_city = ?', Session::get('isppr_city'));
		}
	}

	public function MonthlySalesAddFilters(){

		$centerDrop = htmlBase::newElement('selectbox')
			->setName('inventory_center_id')
			->attr('onchange', 'this.form.submit();');

		$centerDrop->addOption('0', 'Please Select');

		$Qcenters = Doctrine_Query::create()
			->from('ProductsInventoryCenters')
			->orderBy('inventory_center_name asc')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if(isset($_GET['inventory_center_id'])){
			$centerDrop->selectOptionByValue($_GET['inventory_center_id']);
		}

		foreach($Qcenters as $iCenter){
			$centerDrop->addOption($iCenter['inventory_center_id'], $iCenter['inventory_center_name']);
		}

		echo sysLanguage::get('HEADING_TITLE_INVENTORY_CENTER') . ': ' . $centerDrop->draw();
	}

	public function OrdersListingBeforeExecuteLeft(&$Qsales){
		if(isset($_GET['inventory_center_id']) && $_GET['inventory_center_id'] > 0){
			$Qsales//->leftJoin('o.OrdersProducts op')
			->leftJoin('op.Products p')
			->leftJoin('p.ProductsInventory pi')
			->leftJoin('pi.ProductsInventoryBarcodes pib')
			->leftJoin('pib.ProductsInventoryBarcodesToInventoryCenters piq')
			->leftJoin('piq.ProductsInventoryCenters pic')
			->andWhere('pic.inventory_center_id = ?', $_GET['inventory_center_id'])
			->andWhere('pi.use_center = ?', '1');
		}

	}

	public function ProductIsInInventory(&$isInInventory, $products_id){
		$Qproducts = Doctrine_Query::create()
			->from('Products p')
			->leftJoin('p.ProductsInventory i')
			->leftJoin('i.ProductsInventoryBarcodes b')
			->leftJoin('b.ProductsInventoryBarcodesToInventoryCenters b2c')
			->leftJoin('b2c.ProductsInventoryCenters ic')
			->where('p.products_id = ?', $products_id);
		if (Session::exists('isppr_inventory_pickup') === true && Session::get('isppr_inventory_pickup') != ''){
			$Qproducts->andWhere('ic.inventory_center_id = ?', Session::get('isppr_inventory_pickup'));
		}
		$Qproducts->andWhere('i.use_center = ?', '1');
		if (Session::exists('isppr_continent') === true && Session::get('isppr_continent') != ''){
			$Qproducts->andWhere('ic.inventory_center_continent = ?', Session::get('isppr_continent'));
		}
		if (Session::exists('isppr_country') === true && Session::get('isppr_country') != ''){
			$Qproducts->andWhere('ic.inventory_center_country = ?', Session::get('isppr_country'));
		}
		if (Session::exists('isppr_state') === true && Session::get('isppr_state') != ''){
			$Qproducts->andWhere('ic.inventory_center_state = ?', Session::get('isppr_state'));
		}
		if (Session::exists('isppr_city') === true && Session::get('isppr_city') != ''){
			$Qproducts->andWhere('ic.inventory_center_city = ?', Session::get('isppr_city'));
		}
		$Qproducts = $Qproducts->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if(isset($Qproducts[0])){
			$isInInventory = true;
		}else{
			$isInInventory = false;
		}
	}

	public function ProductBeforeTaxAddress(&$zoneId, &$countryId, $product, $order, $userAccount){

		if (sysConfig::get('EXTENSION_INVENTORY_CENTERS_SET_TAX_PER_CENTER_LOCATION') == 'True'){
			if (Session::exists('isppr_country') === true && Session::get('isppr_country') != ''){
				$countryId = Session::get('isppr_country');
			}
			if (Session::exists('isppr_state') === true && Session::get('isppr_state') != ''){
				$Qcheck = Doctrine_Query::create()
					->select('zone_id, zone_code, zone_name')
					->from('Zones')
					->where('zone_country_id = ?', (int)$countryId)
					->andWhere('zone_name = ?', Session::get('isppr_state'))
					->orderBy('zone_name')
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if(isset($Qcheck[0]['zone_id'])){
					$zoneId = $Qcheck[0]['zone_id'];
				}
			}
		}
	}

	public function ProductSearchQueryBeforeExecute(&$Qproducts){
        global $appExtension;
		$Qproducts->leftJoin('p.ProductsInventory i')
		->leftJoin('i.ProductsInventoryBarcodes b')
		->leftJoin('b.ProductsInventoryBarcodesToInventoryCenters b2c')
		->leftJoin('b2c.ProductsInventoryCenters ic');
        if ($appExtension->isEnabled('streamProducts') === true){
            $Qproducts->leftJoin('p.ProductsStreams ps')
                ->orwhere('p.products_id = ps.products_id');
        }
        if ($appExtension->isEnabled('downloadProducts') === true){
            $Qproducts->leftJoin('p.ProductsDownloads pdl')
                ->orwhere('p.products_id = pdl.products_id');
        }
	}

	public function BoxMarketingAddLink(&$contents){
		$contents['children'][] = array(
			'link'       => itw_app_link('appExt=inventoryCenters','show_reports','default_totals','SSL'),
			'text' => 'Comissions Report'
		);		
	}

	public function OrderBeforeSendEmail(&$order, &$emailEvent, &$products_ordered){

		/* TODO: Get into extension */
		$pickupz = Doctrine_Query::create()
		->from('Orders o')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('op.OrdersProductsReservation ops')
		->where('o.orders_id =?', $order['orderID'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if (count($pickupz) > 0){
			foreach($pickupz[0]['OrdersProducts'] as $opInfo){
				if (isset($opInfo['OrdersProductsReservation'])){
					foreach($opInfo['OrdersProductsReservation'] as $oprInfo){
						if (!empty($oprInfo['inventory_center_pickup'])){
							$Qinv = Doctrine_Query::create()
							->select('inventory_center_customer, inventory_center_specific_address, inventory_center_delivery_instructions')
							->from('ProductsInventoryCenters')
							->where('inventory_center_id = ?', $oprInfo['inventory_center_pickup'])
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							if ($Qinv){
								//$customer_id = $Qinv[0]['inventory_center_customer'];
								//$inv_adress = $Qinv[0]['inventory_center_specific_address'];

								$Qcustomer = Doctrine_Query::create()
								->select('customers_email_address, concat(customers_firstname, " ", customers_lastname) as name')
								->from('Customers')
								->where('customers_id = ?', $Qinv[0]['inventory_center_customer'])
								->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
								if ($Qcustomer){
									$emailEvent_inv = new emailEvent('order_inventory_success');
									$emailEvent_inv->setVar('order_id', $order['orderID']);
									$emailEvent_inv->setVar('invoice_link', itw_app_link('appExt=inventoryCenters', 'account_addon', 'view_orders_inventory', 'SSL'));
									$emailEvent_inv->setVar('date_ordered', strftime(sysLanguage::getDateTimeFormat('long'), strtotime(date('Y-m-d h:i:s'))));
									$emailEvent_inv->setVar('ordered_products', (isset($order['productsOrdered']) ? $order['productsOrdered'] : $products_ordered));

									$comments = "\n\t";
									$contents = EventManager::notifyWithReturn('OrderInfoAddBlock', $order['orderID']);
									if (!empty($contents)){
										foreach($contents as $content){
											$comments .= $content;
										}
									}

									$emailEvent_inv->setVar('order_comments', $comments.$this->info['comments']);

									$emailEvent_inv->sendEmail(array(
										'email' => $Qcustomer[0]['customers_email_address'],
										'name'  => $Qcustomer[0]['name']
									));
								}
							}
						}
					}
				}
			}
		}


		if(sysConfig::get('EXTENSION_INVENTORY_CENTERS_SHOW_DELIVERY_INSTRUCTIONS_ON_EMAILS') == 'True' && (count($Qinv) > 0)){
			$inv_address = $comments . "\n\tSpot Address: ".$Qinv[0]['inventory_center_specific_address'];
			$deliveryInstructions = "\n\tDelivery Instructions: ".$Qinv[0]['inventory_center_delivery_instructions'];

			$emailEvent->setVar('inv_address', $inv_address);
			$emailEvent->setVar('deliveryInstructions', $deliveryInstructions);
		}
	}
	
	public function ApplicationTopBeforeCartAction(){
		global $App, $navigation;
		$checkPreReg = false;
		if (sysConfig::get('EXTENSION_INVENTORY_CENTERS_REQUIRE_PREREG') == 'True' && $App->getPageName() != 'center_address_check'){
			$checkPreReg = true;
		}

		if ($checkPreReg === true){
			if (Session::exists('addressCheck') === false && $App->getAppName() != 'center_address_check'){
				tep_redirect(itw_app_link('appExt=inventoryCenters', 'center_address_check', 'default', 'NONSSL'));
			}
		}
	}



	public function ReservationFormatOrdersReservationArray(&$returningArray, $resData){
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') == 'True'){
			$returningArray['inventory_center_pickup'] = (!empty($resData) ? $resData[0]['inventory_center_pickup'] : '');
		}

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_DROPOFF') == 'True'){
			$returningArray['inventory_center_dropoff'] = (!empty($resData) ? $resData[0]['inventory_center_dropoff'] : '');
		}

	}
	
	public function ProductInventoryQuantityUpdateStockQueryBeforeExecute($invData, &$Qcheck){
		$this->ProductInventoryQuantityHasInventoryQueryBeforeExecute($invData, &$Qcheck);
	}
	
	public function ProductInventoryQuantityGetInventoryItemsQueryBeforeExecute($invData, &$Qcheck){
		$this->ProductInventoryQuantityHasInventoryQueryBeforeExecute($invData, &$Qcheck);
	}
	
	public function ProductInventoryQuantityGetInventoryItemsArrayPopulate($qInfo, &$qty){
		if ($this->stockMethod == 'Store'){
			$qty['store_id'] = $qInfo['inventory_store_id'];
		}else{
			$qty['center_id'] = $qInfo['inventory_center_id'];
		}
	}

	public function OrdersProductsReservationListingBeforeExecuteUtilities(&$Qorders){
		global $Editor, $appExtension;
		$Qorders->leftJoin('ib.ProductsInventoryBarcodesToInventoryCenters b2c');

		if ($appExtension->isAdmin()){
			if (is_object($Editor) && $Editor->hasData('inventory_center_id')){
				$Qorders->andWhere('b2c.inventory_center_id = ?', $Editor->getData('inventory_center_id'));
			}else{
				$Qorders->andWhereIn('b2c.inventory_center_id', Session::get('isppr_inventory_pickup'));
			}
		}else{
			if(Session::exists('isppr_inventory_pickup') === true){
				$Qorders->andWhere('b2c.inventory_center_id = ?', Session::get('isppr_inventory_pickup'));
			}
		}

	}


	
	public function ProductInventoryQuantityHasInventoryQueryBeforeExecute($invData, &$Qcheck){
		if ($this->stockMethod == 'Store'){
			$colName = 'inventory_store_id';
		}else{
			$colName = 'inventory_center_id';
		}
		$colValue = '0';
		
		if ((isset($invData['useCenter']) && $invData['useCenter'] == '1') || (isset($invData['use_center']) && $invData['use_center'] == '1')){
			if (isset($invData['useCenterId'])){
				$colValue = $invData['useCenterId'];
			}else{
				if ($this->stockMethod == 'Store'){
					$colValue = Session::get('current_store_id');
				}else{
					$colValue = $this->getSelectedInventoryCenter();
				}
			}
		}
		if($colValue > 0){
			$Qcheck->andWhere($colName . ' = ?', $colValue);
		}
	}
	
	public function ProductInventoryBarcodeUpdateStockQueryBeforeExecute($invData, &$Qcheck){
		$this->ProductInventoryBarcodeHasInventoryQueryBeforeExecute($invData, &$Qcheck);
	}
	
	public function ProductInventoryBarcodeGetInventoryItemsQueryBeforeExecute($invData, &$Qcheck){
		$this->ProductInventoryBarcodeHasInventoryQueryBeforeExecute($invData, &$Qcheck);
	}
	
	public function ProductInventoryBarcodeGetInventoryItemsArrayPopulate($barcode, &$barcodes){
		if (isset($barcode['ProductsInventoryBarcodesToStores']) || isset($barcode['ProductsInventoryBarcodesToInventoryCenters'])){
			if ($this->stockMethod == 'Store'){
				$barcodes['center_id'] = $barcode['ProductsInventoryBarcodesToStores']['inventory_store_id'];
				$barcodes['center_name'] = $barcode['ProductsInventoryBarcodesToStores']['Stores']['stores_name'];
			}else{
				$barcodes['center_id'] = $barcode['ProductsInventoryBarcodesToInventoryCenters']['inventory_center_id'];
				$barcodes['center_name'] = $barcode['ProductsInventoryBarcodesToInventoryCenters']['ProductsInventoryCenters']['inventory_center_name'];
			}
		}
	}
	
	public function ProductInventoryBarcodeHasInventoryQueryBeforeExecute($invData, &$Qcheck){
		global $Editor, $appExtension;
		if ($this->stockMethod == 'Zone' && $appExtension->isAdmin()){
			$Qcheck->leftJoin('ib.ProductsInventoryBarcodesToInventoryCenters ib2c');
			if (is_object($Editor)&& $Editor->hasData('inventory_center_id')){
				$Qcheck->andWhere('ib2c.inventory_center_id = ?', $Editor->getData('inventory_center_id'));
			}else{
				if(Session::exists('isppr_inventory_pickup') === true){
					$Qcheck->andWhereIn('ib2c.inventory_center_id', Session::get('isppr_inventory_pickup'));
				}
			}
		}else{
			if ($invData['use_center'] == '1'){
				$invCenterID = 0;

				if (isset($invData['useCenterId'])){
					$invCenterID = $invData['useCenterId'];
				}elseif (Session::exists('isppr_inventory_pickup')){
					$invCenterID = Session::get('isppr_inventory_pickup');
				}else{
					if ($this->stockMethod == 'Store'){
						$invCenterID = Session::get('current_store_id');
					}else{
						$invCenterID = $this->getSelectedInventoryCenter();
					}
				}
				if ($this->stockMethod == 'Store'){
					if ($invCenterID > 0){
						$Qcheck->leftJoin('ib.ProductsInventoryBarcodesToStores b2s')
								->leftJoin('b2s.Stores');
						$Qcheck->andWhere('b2s.inventory_store_id = ?', $invCenterID);
					}
				}else{

					if ($invCenterID > 0){
						$Qcheck->leftJoin('ib.ProductsInventoryBarcodesToInventoryCenters b2c')
								->leftJoin('b2c.ProductsInventoryCenters');
						$Qcheck->andWhere('b2c.inventory_center_id = ?', $invCenterID);
					}
				}
			}
		}
	}
	
	public function getCentersArray(){
		$Qcenters = Doctrine_Query::create()
		->select('inventory_center_id, inventory_center_name')
		->from('ProductsInventoryCenters')
		->orderBy('inventory_center_name');
		
		$Result = $Qcenters->execute(array(), Doctrine::HYDRATE_ARRAY);
		return $Result;
	}
	
	public function getSelectedInventoryCenter(){
		$invCenterID = 0;
		if (Session::exists('addressCheck') === true && sysConfig::get('EXTENSION_INVENTORY_CENTERS_MUST_BE_IN_ZONE') == 'True'){			
			$addressCheck = Session::get('addressCheck');
			if (isset($addressCheck['systemSelected'])){
				$invCenterID = $addressCheck['systemSelected'];
			}
			if (sysConfig::get('EXTENSION_INVENTORY_CENTERS_ALLOW_MANUAL_ZONE') == 'True'){
				if (isset($addressCheck['customerSelected'])){
					$invCenterID = $addressCheck['customerSelected'];
				}
			}

			if (isset($addressCheck['checkoutSelected'])){
				$invCenterID = $addressCheck['checkoutSelected'];
			}
		}
		return $invCenterID;
	}

	public function getInventoryCenters($centerId = null){
		$Qcenter = Doctrine_Query::create()
		->from('ProductsInventoryCenters');

		if (is_null($centerId) === false){
			$Qcenter->where('inventory_center_id = ?', $centerId);
		}

		$Result = $Qcenter->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		return $Result;
	}

	public function ParseReservationInfo(&$return, &$resInfo){
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') == 'True'){
			$pickup = $this->getInventoryCenters($resInfo['inventory_center_pickup']);
			if(isset($pickup[0]) && $pickup[0]['inventory_center_name'] != ''){
				$return .= '<br /><small><i> - ' .  'Inventory Center pickup zone: ' . $pickup[0]['inventory_center_name'] . '</i></small>';
			}
		}

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_DROPOFF') == 'True'){
			$dropoff = $this->getInventoryCenters($resInfo['inventory_center_dropoff']);
			if(isset($dropoff[0]) && $dropoff[0]['inventory_center_name'] != ''){
				$return .= '<br /><small><i> - ' .  'Inventory Center dropoff zone: ' . $dropoff[0]['inventory_center_name']  . '</i></small>';
			}
		}
	}

	public function ReservationProcessAddToOrder(&$reservationInfo){
		global $appExtension;

		if ($appExtension->isCatalog()){
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') == 'True'){
				$reservationInfo['reservationInfo']['inventory_center_pickup'] = $reservationInfo['OrdersProductsReservation'][0]['inventory_center_pickup'];
			}

			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_DROPOFF') == 'True'){
				$reservationInfo['reservationInfo']['inventory_center_dropoff'] = $reservationInfo['OrdersProductsReservation'][0]['inventory_center_dropoff'];
			}
		}
	}

	public function ReservationProcessAddToCart(&$reservationInfo){
		global $messageStack;

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') == 'True'){
			$reservationInfo['inventory_center_pickup'] = $_POST['pickup'];
		}

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_DROPOFF') == 'True'){
			$reservationInfo['inventory_center_dropoff'] = $_POST['dropoff'];
		}
		if(sysConfig::get('EXTENSION_INVENTORY_CENTERS_SINGLE_INVENTORY_PER_ORDER') == 'True'){
			global $ShoppingCart;

			if (!is_object($ShoppingCart) || !is_array($reservationInfo) || ($reservationInfo['start_date'] == '' && $reservationInfo['end_date'] == '')) return;
			$notgood = false;
			foreach($ShoppingCart->getProducts() as $cartProduct){
				if ($cartProduct->hasInfo('reservationInfo') === true){
					$reservationInfo1 = $cartProduct->getInfo('reservationInfo');
					if($reservationInfo1['inventory_center_pickup'] != $reservationInfo['inventory_center_pickup']){
						$notgood = true;
						break;
					}
				}
			}
			if($notgood){
				$messageStack->addSession('pageStack', 'You may only order from one destination at a time. If you need to order from 2 destinations please place this order, then place a second order for the second destination.', 'error');
				tep_redirect(getenv("HTTP_REFERER"));
				itwExit();
			}
		}
	}

	public function BeforeProcessUpdateCart(&$pInfo){
		global $messageStack;
		$reservationInfo = $pInfo['reservationInfo'];
		if(sysConfig::get('EXTENSION_INVENTORY_CENTERS_SINGLE_INVENTORY_PER_ORDER') == 'True'){
			global $ShoppingCart;

			if (!is_object($ShoppingCart) || !is_array($reservationInfo) || ($reservationInfo['start_date'] == '' && $reservationInfo['end_date'] == '')) return;
			$notgood = false;
			foreach($ShoppingCart->getProducts() as $cartProduct){
				if ($cartProduct->hasInfo('reservationInfo') === true){
					$reservationInfo1 = $cartProduct->getInfo('reservationInfo');
					if($reservationInfo1['inventory_center_pickup'] != $reservationInfo['inventory_center_pickup']){
						$notgood = true;
						break;
					}
				}
			}
			if($notgood){
				$messageStack->addSession('pageStack', 'You may only order from one destination at a time. If you need to order from 2 destinations please place this order, then place a second order for the second destination.', 'error');
				tep_redirect(getenv("HTTP_REFERER"));
				itwExit();
			}
		}
	}

	public function ReservationOnInsertOrderedProduct(&$Reservation, &$cartProduct){
		$resInfo = $cartProduct->getInfo('reservationInfo');
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') == 'True'){
			$Reservation->inventory_center_pickup = $resInfo['inventory_center_pickup'];
		}

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_DROPOFF') == 'True'){
			$Reservation->inventory_center_dropoff = $resInfo['inventory_center_dropoff'];
		}
	}

	public function Extension_payPerRentalsOrderClassQueryFillProductArray(&$mainReservation, &$product){
		if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') == 'True')
			$product['reservationInfo']['inventory_center_pickup'] = $mainReservation['inventory_center_pickup'];
		if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_DROPOFF') == 'True')
			$product['reservationInfo']['inventory_center_dropoff'] = $mainReservation['inventory_center_dropoff'];
	}
	
	public function ReservationAppendOrderedProductsString(&$products_ordered, &$cartProduct){
		$resInfo = $cartProduct->getInfo('reservationInfo');
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') == 'True'){
			$pickup = $this->getInventoryCenters($resInfo['inventory_center_pickup']);
			$products_ordered .= "\n\t" . 'Inventory Center pickup zone: ' . $pickup[0]['inventory_center_name'] . '</i></small>';
		}
		
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_DROPOFF') == 'True'){
			$dropoff = $this->getInventoryCenters($resInfo['inventory_center_dropoff']);
			$products_ordered .= "\n\t" . 'Inventory Center dropoff zone: ' . $dropoff[0]['inventory_center_name']  . '</i></small>';
		}
	}

	public function ReservationCheckQueryAfterExecute(&$Result, $settings, &$returnVal){
		if ($returnVal <= 0){
			if (isset($settings['cartProduct'])){
				$resInfo = $settings['cartProduct']->getInfo('reservationInfo');
				$qtyCheck = $settings['cartProduct']->getQuantity();
			}else{
				$qtyCheck = $settings['quantity'];
			}
			$returnVal = 1;
			if ($settings['item_type'] == 'barcode'){

				$Qcheck = Doctrine_Query::create()
				->select('barcode_id')
				->from('ProductsInventoryBarcodes pib')
				->leftJoin('pib.ProductsInventoryBarcodesToInventoryCenters pibc')
				->where('pib.barcode_id = ?', $settings['item_id']);
				
				if (isset($settings['inventory_center_pickup'])){
					$Qcheck->andWhere('pibc.inventory_center_id = ?', $settings['inventory_center_pickup']);
				}
				
				$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Result){
					$returnVal = 0;
				}
			}elseif ($settings['item_type'] == 'quantity'){
				$Qcheck = Doctrine_Query::create()
				->from('ProductsInventoryQuantity')
				->where('quantity_id = ?', $settings['item_id']);
				
				if (isset($settings['inventory_center_pickup'])){
					$Qcheck->andWhere('inventory_center_id = ?', $settings['inventory_center_pickup']);
				}
				
				$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Result){
					if ($Result[0]['available'] > $qtyCheck){
						$returnVal = 0;
					}
				}
			}
		}
	}

	public function ProductsInventoryReportsDefaultAddFilterOptions(&$searchForm){
		$inventoryField = htmlBase::newElement('selectbox')
		            ->setName('centerId')
		            ->setLabel('Inventory Center: ')
		            ->setLabelPosition('before');
	   	$QInventory = Doctrine_Query::create()
				     ->from('ProductsInventoryCenters')
				     ->orderBy('inventory_center_name')
				     ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$inventoryField->addOption('0','Any');
		if (count($QInventory) > 0){
			foreach($QInventory as $iInfo){
				$inventoryField->addOption($iInfo['inventory_center_id'],$iInfo['inventory_center_name']);
			}
		}
		$searchForm->append($inventoryField);
	}

	public function ProductInventoryReportsListingQueryBeforeExecute(&$Qproducts){
		global $appExtension;

		$isMultiStore = $appExtension->isInstalled('multiStore') && $appExtension->isEnabled('multiStore');
		if ($this->stockMethod != 'Store'){
			$Qproducts->leftJoin('pib.ProductsInventoryBarcodesToInventoryCenters b2c')
					  ->leftJoin('b2c.ProductsInventoryCenters');
			if (isset($_GET['centerId']) && $_GET['centerId'] != 0){
				$Qproducts->andWhere('b2c.inventory_center_id = ?', $_GET['centerId']);
			}
		}		
	}
}
?>