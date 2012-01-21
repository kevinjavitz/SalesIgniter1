<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	abstract class PaymentModuleBase {
		private $configData = array();
		private $xmlData = null;
		private $enabled = false;
		private $visible = 'Both';
		private $code = null;
		private $title = 'No Title Set';
		private $description = 'No Description Set';
		private $sortOrder = 0;
		private $check = null;
		private $paymentZone = null;
		private $checkoutMethod = null;
		private $paymentError = null;
		private $orderStatus = 0;
		private $formUrl = null;
		private $installed = false;
		private $errorMessage = null;
		
		public function init($code){
			$this->code = $code;
			
			$moduleDir = sysConfig::getDirFsCatalog() . 'includes/modules/orderPaymentModules/' . $code . '/';
			$this->xmlData = simplexml_load_file(
				$moduleDir . 'data/info.xml',
				'SimpleXMLElement',
				LIBXML_NOCDATA
			);
				
			$info = $this->xmlData;
			
			$Qmodules = Doctrine_Query::create()
			->from('Modules m')
			->leftJoin('m.ModulesConfiguration mc')
			->where('m.modules_type = ?', 'order_payment')
			->andWhere('m.modules_code = ?', $code)
			->orderBy('mc.sort_order')
			->execute();
			if ($Qmodules->count() > 0){
				$this->moduleData = $Qmodules->toArray(true);
				$this->configData = $this->moduleData[0]['ModulesConfiguration'];
				$this->installed = true;

				sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/modules/orderPaymentModules/' . $code . '/language_defines/global.xml');
				if (file_exists(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/includes/modules/orderPaymentModules/' . $code . '/global.xml')){
					sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/includes/modules/orderPaymentModules/' . $code . '/global.xml');
				}
				
				if (is_dir(sysConfig::getDirFsCatalog() . 'includes/modules/orderPaymentModules/' . $code . '/Doctrine/')){
					Doctrine_Core::loadModels(sysConfig::getDirFsCatalog() . 'includes/modules/orderPaymentModules/' . $code . '/Doctrine/', Doctrine_Core::MODEL_LOADING_AGGRESSIVE);
				}
			
				$this->title = $this->getTitle();
				$this->description = sysLanguage::get((string) $info->description_key);
				
				if (array_key_exists((string) $info->status_key, $this->configData)){
					$this->enabled = ($this->configData[(string) $info->status_key]['configuration_value'] == 'True' ? true : false);
				}

				if (array_key_exists((string) $info->visible_key, $this->configData)){
					$this->visible = $this->configData[(string) $info->visible_key]['configuration_value'];
				}
				
				if (array_key_exists((string) $info->sort_key, $this->configData)){
					$this->sortOrder = (int) $this->configData[(string) $info->sort_key]['configuration_value'];
				}
				
				if (array_key_exists((string) $info->zone_key, $this->configData)){
					$this->paymentZone = (int) $this->configData[(string) $info->zone_key]['configuration_value'];
				}
				
				if (array_key_exists((string) $info->checkout_method_key, $this->configData)){
					$this->checkoutMethod = (int) $this->configData[(string) $info->checkout_method_key]['configuration_value'];
				}
				
				if (array_key_exists((string) $info->order_status_key, $this->configData)){
					$this->orderStatus = ((int) $this->configData[(string) $info->order_status_key]['configuration_value'] ? (int) $this->configData[(string) $info->order_status_key]['configuration_value'] : 1);
				}
				
				$this->updateStatus();
			}
		}

		public function check(){
			return $this->isInstalled();
		}
		
		public function isEnabled(){
			global $App;
			$enabled = false;
			if($this->visible == 'Both'){
				$enabled = true;
			}else{
				if($App->getEnv() == 'catalog'){
					if($this->visible == 'Catalog'){
						$enabled = true;
					}
				}else{
					if($this->visible == 'Admin'){
						$enabled = true;
					}
				}
			}

			return ($this->enabled && $enabled);
		}
		
		public function isInstalled(){
			return ($this->installed === true);
		}
		
		public function isFromExtension(){
			return false;
		}
		
		public function getExtensionName(){
			return false;
		}
		
		public function getConfig(){
			return $this->configData;
		}
		
		public function getConfigData($key = null){
			if (is_null($key) === true){
				return $this->configData;
			}else{
				if (array_key_exists($key, $this->configData)){
					return $this->configData[$key]['configuration_value'];
				}
				return null;
			}
		}
		
		public function getCode(){
			return $this->code;
		}
		
		public function setTitle($val){
			$this->title = $val;
		}
		
		public function getTitle(){
			return $this->title;
		}
		
		public function setDescription($val){
			$this->description = $val;
		}
		
		public function getDescription(){
			return $this->description;
		}

		public function getSortOrder(){
			return $this->sortOrder;
		}
		
		public function getStatus(){
			return $this->enabled;
		}
		
		public function hasError(){
			return (is_null($this->paymentError) === false);
		}
		
		public function setFormUrl($val){
			$this->formUrl = $val;
		}
		
		public function getFormUrl(){
			return $this->formUrl;
		}
		
		public function hasFormUrl(){
			return (is_null($this->formUrl) === false);
		}

		public function updateStatus(){
			global $order, $onePageCheckout;
			if (is_object($order) && $this->enabled === true && $this->paymentZone > 0){
				$userAccount = &Session::getReference('userAccount');
				$billingAddress = $userAccount->plugins['addressBook']->getAddress('billing');

				$check_flag = false;
				$Qcheck = Doctrine_Query::create()
				->from('GeoZones g')
				->leftJoin('g.ZonesToGeoZones ztg')
				->where('g.geo_zone_id = ?', $this->paymentZone)
				->andWhere('ztg.zone_country_id = ?', $billingAddress['entry_country_id'])
				->orderBy('ztg.zone_id')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qcheck){
					foreach($Qcheck as $zInfo){
						foreach($zInfo['ZonesToGeoZones'] as $iInfo){
							if ($iInfo['zone_id'] < 1){
								$check_flag = true;
								break;
							} elseif ($iInfo['zone_id'] == $billingAddress['entry_zone_id']){
								$check_flag = true;
								break;
							}
						}
					}
				}

				if ($check_flag == false){
					$this->enabled = false;
				}
			}
		
			if (isset($onePageCheckout) && is_object($onePageCheckout)){
				if ($this->enabled === true && $this->checkoutMethod != 'Both'){
					if ($onePageCheckout->isMembershipCheckout() === true && $this->checkoutMethod == 'Normal'){
						$this->enabled = false;
					}

					if ($onePageCheckout->isMembershipCheckout() === false && $this->checkoutMethod == 'Membership'){
						$this->enabled = false;
					}
				}
			}
		}

		public function javascriptValidation(){
			return '';
		}

		public function onSelect(){
			return array(
				'id'     => $this->code,
				'module' => $this->title
			);
		}

		/*
		 * Process the response from the gateway
		 */
		private function onResponse($response){
		}
		
		/*
		 * On successful response from the gateway
		 */
		private function onSuccess($info){
		}

		/*
		 * On failure response from the gateway
		 */
		private function onFail($info){
		}

		public function sendPaymentRequest($requestData){
			return true;
		}
		
		public function processPayment($orderID = null, $amount = null){
			return false;
		}

		public function refundPayment($requestData){
			return false;
		}

		public function processPaymentCron($orderID){
			return false;
		}

		public function afterOrderProcess(){
			return false;
		}

		public function afterOrderProcessCron(){
			return false;
		}

		public function validatePost(){
			return true;
		}
		
		public function hasHiddenFields(){
			return false;
		}
		
		public function getHiddenFields(){
			return '';
		}

		public function beforeRentalProcess(){
			return false;
		}

		public function afterRentalProcess(){
			return false;
		}
		
		public function onInstall(){
			return false;
		}
		
		public function logToCollection(&$CollectionObj){
			$this->logUseCollection = true;
			$this->Collection = $CollectionObj;
		}
		
		public function getErrorMessage(){
			return $this->errorMessage;
		}
		
		public function setErrorMessage($val){
			$this->errorMessage = $val;
		}
		
		public function logPayment($info){
			global $order;

			$Order = Doctrine_Core::getTable('Orders')->findOneByOrdersId((isset($info['orderID']) ? $info['orderID'] : $order->newOrder['orderID']));
			$newHistory =& $Order->OrdersStatusHistory;
			$idx = $newHistory->count();
			$Order->OrdersStatusHistory[$idx]->orders_status_id = $this->orderStatus;
			$Order->orders_status = $this->orderStatus;
			$Order->save();

			$newStatus = new OrdersPaymentsHistory();
			$newStatus->orders_id = (isset($info['orderID']) ? $info['orderID'] : $order->newOrder['orderID']);
			$newStatus->payment_module = $this->code;
			$newStatus->payment_method = $this->title;
			$newStatus->payment_amount = $info['amount'];
			$newStatus->success = (int) $info['success'];
			$newStatus->can_reuse = (int) (isset($info['can_reuse'])?$info['can_reuse']:0);
			
			if (isset($info['message'])){
				$newStatus->gateway_message = $info['message'];
			}
			
			if (isset($info['cardDetails'])){
				$newStatus->card_details = cc_encrypt(serialize($info['cardDetails']));
			}
			
			if (isset($this->logUseCollection) && $this->logUseCollection === true){
				$this->Collection->OrdersPaymentsHistory->add($newStatus);
			}else{
				$newStatus->save();
			}
		}
	}
?>