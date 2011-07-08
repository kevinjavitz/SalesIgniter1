<?php
/*
	Pay Per Rentals Version 1
	Product Purchase Type: Reservation

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PurchaseType_package extends PurchaseTypeAbstract {
	public $typeLong = 'package';
	public $typeName;
	public $typeShow;

	private $enabledShipping = array();

	public function __construct($ProductCls, $forceEnable = false){

		$productInfo = $ProductCls->productInfo;
		$this->enabled = ($forceEnable === true ? true : (in_array($this->typeLong, $productInfo['typeArr'])));
		$this->typeName = sysLanguage::get('PURCHASE_TYPE_PACKAGE_NAME');
		$this->typeShow = sysLanguage::get('PURCHASE_TYPE_PACKAGE_SHOW');

		if ($this->enabled === true){
			$this->productInfo = array(
				'id'          => $productInfo['products_id'],
				'price'       => $productInfo['products_price'],
				'overbooking' => $productInfo['products_onetime_overbooking'],
				'taxRate'     => $productInfo['taxRate']
			);

			if (isset($productInfo['Specials']) && !empty($productInfo['Specials'])){
				$this->productInfo['special_price'] = $productInfo['Specials']['specials_new_products_price'];
			}

			EventManager::notify('PurchaseTypeAfterSetup', &$this->productInfo);

			$this->inventoryCls = new ProductInventory(
				$this->productInfo['id'],
				$this->typeLong,
				$productInfo['products_inventory_controller']
			);

		}
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

	public function hasInventory(){

		if ($this->enabled === false) return false;

		$QPackageProducts = Doctrine_Query::create()
		->from('ProductsPackages pp')
		->leftJoin('pp.Products p')
		->leftJoin('p.ProductsDescription pd')
		->where('pp.parent_id = ?', $this->productInfo['id'])
		->andWhere('pd.language_id = ?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$has = true;
		if(count($QPackageProducts) > 0){
			foreach($QPackageProducts as $package){
				$pClass = new product($package['products_id']);

				$purchaseType = $pClass->getPurchaseType($package['purchase_type']);
				//print_r($purchaseType);
				$has = $has && $purchaseType->hasInventory();

			}
		}else{
			$has = false;
		}

		return $has;

	}

	public function updateStock($orderId, $orderProductId, &$cartProduct){
	}

	public function processRemoveFromCart(){
		global $ShoppingCart;
	}

	public function processAddToOrderOrCart($resInfo, &$pInfo){
		global $App, $ShoppingCart;

	}

	public function processAddToOrder(&$pInfo){
		//$this->processAddToOrderOrCart($infoArray, $pInfo);

		EventManager::notify('ReservationProcessAddToOrder', &$pInfo);
	}

	public function processAddToCart(&$pInfo){

		//$this->processAddToOrderOrCart($reservationInfo, $pInfo);

		EventManager::notify('ReservationProcessAddToCart', &$pInfo['reservationInfo']);
	}

	public function getPrice(){
		return false;
	}

	public function displayPrice(){
			global $currencies, $appExtension;
			if ($this->enabled === false || is_null($this->inventoryCls)) return null;
			if (isset($this->productInfo['special_price'])){
				$extSpecials = $appExtension->getExtension('specials');
				$display = $currencies->display_price($this->productInfo['price'], $this->productInfo['taxRate']);
				$extSpecials->ProductNewPriceBeforeDisplay($this->productInfo['special_price'], $display);
				return $display;
			}else{
				return $currencies->display_price($this->productInfo['price'], $this->productInfo['taxRate']);
			}
	}

	public function canUseSpecial(){
		return false;
	}

	public function onInsertOrderedProduct($cartProduct, $orderId, &$orderedProduct, &$products_ordered){
		global $currencies, $onePageCheckout, $appExtension;

	}


	public function getPurchaseHtml($key){
		global $currencies;
		$return = null;
		switch($key){
			case 'product_info':

				$button = htmlBase::newElement('button')
				->setType('submit')
				->setName('buy_' . $this->typeLong . '_package')
				->setText(sysLanguage::get('TEXT_BUTTON_BUY'));

				$pClass = new product($this->productInfo['id']);
				if ($this->hasInventory() === false || $pClass->isNotAvailable() ){
					$button->disable();
					if($pClass->isNotAvailable()){
						$button->setText(sysLanguage::get('TEXT_AVAILABLE').': '. strftime(sysLanguage::getDateFormat('short'), strtotime($pClass->getAvailableDate())));
					}
				}

				$content = htmlBase::newElement('span')
				->css(array(
					'font-size' => '1.5em',
					'font-weight' => 'bold'
				))
				->html($this->displayPrice());

				$return = array(
					'form_action'   => itw_app_link(tep_get_all_get_params(array('action'))),
					'purchase_type' => $this->typeLong,
					'allowQty'      => true,
					'header'        =>  sysLanguage::get('BUY_PACKAGE'),
					'content'       => $content->draw(),
					'button'        => $button
				);


				break;
		}
		return $return;
	}

}
?>