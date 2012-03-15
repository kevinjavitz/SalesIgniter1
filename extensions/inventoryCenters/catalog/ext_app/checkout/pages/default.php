<?php
	class inventoryCenters_catalog_checkout_default extends Extension_inventoryCenters {
		
		public function __construct(){
			global $App;
			parent::__construct();

			if ($App->getAppName() != 'checkout' || ($App->getAppName() == 'checkout' && $App->getPageName() != 'default')){
				$this->enabled = false;
			}
		}
	
		public function load(){
			if ($this->enabled === false) return;

			EventManager::attachEvents(array(
				'CheckoutShippingMethodsAfterQuoteMethod',
				'CheckoutSetShippingStatus',
				'CheckoutShippingMethodsAfterTitle'
			), null, $this);
		}
		
		private function getShippingMethods($centerId){
			$Qmethods = Doctrine_Query::create()
			->select('inventory_center_shipping')
			->from('ProductsInventoryCenters')
			->where('inventory_center_id = ?', $centerId)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qmethods){
				return explode(',', $Qmethods[0]['inventory_center_shipping']);
			}
			return null;
		}
		
		public function CheckoutShippingMethodsAfterQuoteMethod(&$quotes){
			global $ShoppingCart;
			$show_method = true;
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') == 'True' && sysConfig::get('EXTENSION_INVENTORY_CENTERS_SHIPPING_PER_INVENTORY') == 'True'){
				$show_method = false;
				foreach($ShoppingCart->getProducts() as $cartProduct){
					if ($cartProduct->hasInfo('reservationInfo') === true){
						$reservationInfo = $cartProduct->getInfo('reservationInfo');

						$shippingMethods = $this->getShippingMethods($reservationInfo['inventory_center_pickup']);
						if ($shippingMethods){
							for($i=0, $n=sizeof($shippingMethods); $i<$n; $i++){
								if ($quotes['id'] == $shippingMethods[$i]){
									$show_method = true;								
								}
							}
						}
					}
				}
			}
			return $show_method;
		}

	public function CheckoutShippingMethodsAfterTitle(&$quotes){
		global $ShoppingCart;
		if(sysConfig::get('EXTENSION_INVENTORY_CENTERS_SHOW_DELIVERY_INSTRUCTIONS_ON_CHECKOUT') == 'True'){
			if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') == 'True' && sysConfig::get('EXTENSION_INVENTORY_CENTERS_SHIPPING_PER_INVENTORY') == 'True'){
				$deliveryInstructions = '';
				foreach($ShoppingCart->getProducts() as $cartProduct){
					if ($cartProduct->hasInfo('reservationInfo') === true){
						$reservationInfo = $cartProduct->getInfo('reservationInfo');
						$shippingMethods = $this->getShippingMethods($reservationInfo['inventory_center_pickup']);
						if ($shippingMethods){
							for($i=0, $n=sizeof($shippingMethods); $i<$n; $i++){
								if ($quotes['id'] == $shippingMethods[$i]){
									$deliveryInstructions = $reservationInfo['inventory_center_pickup'];
								}
							}
						}
					}
				}
			}
            return  //'<a onclick="popupWindow(\''.urlencode(itw_app_link('appExt=inventoryCenters&dialog=true&delv='.$deliveryInstructions,'show_inventory','delivery','SSL')).'\',\'400\',\'300\');return false;\'> (click for details) </a>';
			'<a href="' . itw_app_link('appExt=inventoryCenters', 'show_inventory', 'delivery', 'SSL') . '" onclick="popupWindow(\'' . itw_app_link('appExt=inventoryCenters&dialog=true&delv='.$deliveryInstructions, 'show_inventory', 'delivery', 'SSL') . '\',\'400\',\'300\');return false;">'. '(click for details) </a>';
		}
		return '';
	}

		public function CheckoutSetShippingStatus(){
			global $ShoppingCart, $onePageCheckout;
			if ($this->enabled === false) return;

			if (sysConfig::get('EXTENSION_INVENTORY_CENTERS_SHIPPING_PER_INVENTORY') == 'True'){
				if(Session::exists('onlyReservations')){
					$onlyReservations = Session::get('onlyReservations');
				}else{
					$onlyReservations = true;
				}
				foreach($ShoppingCart->getProducts() as $cartProduct){
					if ($cartProduct->hasInfo('reservationInfo') === false){
						$onlyReservations = false;
					}else{
						$reservationInfo = $cartProduct->getInfo('reservationInfo');
						if(isset($reservationInfo['inventory_center_pickup'])){
							$shippingMethods = $this->getShippingMethods($reservationInfo['inventory_center_pickup']);
							if ($shippingMethods && ($ShoppingCart->showWeight() > 0)){
								$onePageCheckout->onePage['info']['shipping'] = array();
								$onePageCheckout->onePage['shippingEnabled'] = true;
								break;
							}else{
								$onePageCheckout->onePage['info']['shipping'] = false;
								$onePageCheckout->onePage['shippingEnabled'] = false;
							}
						}
					}
				}
				Session::set('onlyReservations', $onlyReservations);
			}else{
				//$onePageCheckout->onePage['info']['shipping'] = false;
				//$onePageCheckout->onePage['shippingEnabled'] = false;
			}
		}
	}
?>