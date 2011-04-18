<?php
/*
	$Id: payment.php,v 1.37 2003/06/09 22:26:32 hpdl Exp $

	osCommerce, Open Source E-Commerce Solutions
	http://www.oscommerce.com

	Copyright (c) 2003 osCommerce

	Released under the GNU General Public License
*/

class payment {
	private $installedModules, $selectedModule, $moduleClasses;

	// class constructor
	public function __construct($module = '') {
		if (sysConfig::exists('MODULE_PAYMENT_INSTALLED') && sysConfig::isNotEmpty('MODULE_PAYMENT_INSTALLED')){
			$this->installedModules = sysConfig::explode('MODULE_PAYMENT_INSTALLED', ';');

			$include_modules = array();
			if (tep_not_null($module) && in_array($module . '.php', $this->installedModules)){
				$this->selectedModule = $module;

				$include_modules[] = array(
					'class' => $module,
					'file'  => $module . '.php'
				);
			} else {
				foreach($this->installedModules as $moduleFileName){
					$include_modules[] = array(
						'class' => substr($moduleFileName, 0, strrpos($moduleFileName, '.')),
						'file'  => $moduleFileName
					);
				}
			}

			for ($i=0, $n=sizeof($include_modules); $i<$n; $i++) {
   				sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages_phar/' . Session::get('language') . '/osc/admin/modules/payment/' . substr($include_modules[$i]['file'], 0, -4) . '.xml');
				include(sysConfig::get('DIR_FS_CATALOG') . sysConfig::get('DIR_WS_MODULES') . 'payment/' . $include_modules[$i]['file']);

				$this->moduleClasses[$include_modules[$i]['class']] = new $include_modules[$i]['class'];
				if ($this->moduleClasses[$include_modules[$i]['class']]->enabled === true){
					$this->updateStatus($this->moduleClasses[$include_modules[$i]['class']]);
				}
			}

			// if there is only one payment method, select it as default because in
			// checkout_confirmation.php the $payment variable is being assigned the
			// $_POST['payment'] value which will be empty (no radio button selection possible)
			if ($this->getTotalEnabled() == 1 && $this->moduleIsLoaded(Session::get('payment')) === false){
				Session::set('payment', $include_modules[0]['class']);
			}

			if (tep_not_null($module) && $this->moduleHasFormUrl($module)){
				$this->form_action_url = $this->getModuleFormUrl($module);
			}
		}
	}
	
	public function updateStatus(&$module){
		global $order, $onePageCheckout;
		if (is_object($order) && $module->enabled === true && $module->paymentZone > 0){
			$userAccount = &Session::getReference('userAccount');
			$billingAddress = $userAccount->plugins['addressBook']->getAddress('billing');

			$check_flag = false;
			$Qcheck = dataAccess::setQuery('select zone_id from {geo_zones} where geo_zone_id = {module_zone} and zone_country_id = {country_id} order by zone_id');
			$Qcheck->setTable('{geo_zones}', TABLE_ZONES_TO_GEO_ZONES);
			$Qcheck->setValue('{module_zone}', $module->paymentZone);
			$Qcheck->setValue('{country_id}', $billingAddress['entry_country_id']);
			while ($Qcheck->next() !== false){
				if ($Qcheck->getVal('zone_id') < 1){
					$check_flag = true;
					break;
				} elseif ($Qcheck->getVal('zone_id') == $billingAddress['entry_zone_id']){
					$check_flag = true;
					break;
				}
			}

			if ($check_flag == false){
				$module->enabled = false;
			}
		}
		
		if (isset($onePageCheckout) && is_object($onePageCheckout)){
			if ($module->enabled === true){
				if ($onePageCheckout->isMembershipCheckout() === true && $module->checkoutMethod == 'Normal'){
					$module->enabled = false;
				}

				if ($onePageCheckout->isNormalCheckout() === true && $module->checkoutMethod == 'Membership'){
					$module->enabled = false;
				}
			}
		}
	}

	// class methods
	public function getDropMenuArray(){
		$dropMenuArray = array(
			array(
				'id' => '',
				'text' => 'Please Select A Payment Method'
			)
		);
		foreach($this->moduleClasses as $moduleName => $moduleClass){
			$dropMenuArray[] = array(
				'id'   => $moduleClass->code,
				'text' => $moduleClass->title
			);
		}
		return $dropMenuArray;
	}

	public function getTotalEnabled(){
		$enabledTotal = 0;
		foreach($this->moduleClasses as $moduleName => $moduleClass){
			if ($moduleClass->enabled === true){
				$enabledTotal++;
			}
		}
		return $enabledTotal;
	}

	public function modulesAreInstalled(){
		return (is_array($this->moduleClasses) ? true : false);
	}

	public function moduleIsLoaded($moduleName){
		if (isset($this->moduleClasses[$moduleName]) && is_object($this->moduleClasses[$moduleName])){
			return true;
		}
		return false;
	}

	public function moduleIsEnabled($moduleName){
		if ($this->moduleIsLoaded($moduleName)){
			if ($this->moduleClasses[$moduleName]->enabled === true){
				return true;
			}
		}
		return false;
	}

	public function getModule($moduleName = false){
		if ($moduleName === false){
			if (isset($this->selectedModule)){
				$moduleName = $this->selectedModule;
			}else{
				die('No module specified and no module specifically loaded');
			}
		}

		if ($this->moduleIsLoaded($moduleName)){
			return $this->moduleClasses[$moduleName];
		}else{
			die('Module Not Loaded :: ' . $moduleName);
		}
		return false;
	}

	public function unloadModule($moduleName){
		unset($this->moduleClasses[$moduleName]);
	}

	public function moduleHasFormUrl($moduleName){
		if ($this->moduleIsEnabled($moduleName) && isset($this->moduleClasses[$moduleName]->form_action_url)){
			return true;
		}
		return false;
	}

	public function getModuleFormUrl($moduleName){
		if ($this->moduleHasFormUrl($moduleName)){
			return $this->moduleClasses[$moduleName]->form_action_url;
		}
		return false;
	}

	public function update_status() {
		if ($this->moduleIsEnabled($this->selectedModule)){
			if (method_exists($this->moduleClasses[$this->selectedModule], 'update_status')){
				$this->moduleClasses[$this->selectedModule]->update_status();
			}
		}
	}

	// #################### Begin Added CGV JONYO ######################
	public function javascript_validation($coversAll) {
		//added the $coversAll to be able to pass whether or not the voucher will cover the whole
		//price or not.  If it does, then let checkout proceed when just it is passed.
		$js = '';
		if ($this->modulesAreInstalled() === true){
			$addThis = '';
			if ($coversAll) {
				$addThis = 'if (document.checkout_payment.cot_gv.checked) {
                                payment_value=cot_gv;
                            } else ';
			}

			$js = '<script language="javascript"><!-- ' . "\n" .
			'function check_form() {' . "\n" .
			'  var error = 0;' . "\n" .
			'  var error_message = "' . sysLanguage::get('JS_ERROR') . '";' . "\n" .
			'  var payment_value = null;' . "\n" .$addThis . //added by jonyo, yo
			'  if (document.checkout_payment.payment.length) {' . "\n" .
			'      for (var i=0; i<document.checkout_payment.payment.length; i++) {' . "\n" .
			'          if (document.checkout_payment.payment[i].checked) {' . "\n" .
			'              payment_value = document.checkout_payment.payment[i].value;' . "\n" .
			'          }' . "\n" .
			'      }' . "\n" .
			'  } else if (document.checkout_payment.payment.checked) {' . "\n" .
			'      payment_value = document.checkout_payment.payment.value;' . "\n" .
			'  } else if (document.checkout_payment.payment.value) {' . "\n" .
			'      payment_value = document.checkout_payment.payment.value;' . "\n" .
			'  }' . "\n\n";

			foreach($this->moduleClasses as $moduleName => $moduleClass){
				if ($this->moduleIsEnabled($moduleName) === true){
					$js .= $moduleClass->javascript_validation();
				}
			}

			// ############ Added CCGV Contribution ##########
			//        $js .= "\n" . '  if (payment_value == null) {' . "\n" .
			$js .= "\n" . '  if (payment_value == null && submitter != 1) {' . "\n" . // CCGV Contribution
			// ############ End Added CCGV Contribution ##########
			'    error_message = error_message + "' . sysLanguage::get('JS_ERROR_NO_PAYMENT_MODULE_SELECTED') . '";' . "\n" .
			'    error = 1;' . "\n" .
			'  }' . "\n\n" .
			// ############ Added CCGV Contribution ##########
			//  ICW CREDIT CLASS Gift Voucher System Line below amended
			//               '  if (error == 1) {' . "\n" .
			'  if (error == 1 && submitter != 1) {' . "\n" .
			// ############ End Added CCGV Contribution ##########
			'    alert(error_message);' . "\n" .
			'    return false;' . "\n" .
			'  } else {' . "\n" .
			'    return true;' . "\n" .
			'  }' . "\n" .
			'}' . "\n" .
			'//--></script>' . "\n";
		}
		return $js;
	}

	public function selection(){
		$selection_array = array();
		if ($this->modulesAreInstalled() === true){
			foreach($this->moduleClasses as $moduleName => $moduleClass){
				if ($this->moduleIsEnabled($moduleName) === true){
					$selection = $this->getModule($moduleName)->selection();
					if (is_array($selection)){
						$selection_array[] = $selection;
					}
				}
			}
		}
		return $selection_array;
	}

	public function confirmation(){
		if ($this->moduleIsEnabled($this->selectedModule) === true) {
			return $this->moduleClasses[$this->selectedModule]->confirmation();
		}
	}

	public function process_button(){
		if ($this->moduleIsEnabled($this->selectedModule) === true) {
			return $this->moduleClasses[$this->selectedModule]->process_button();
		}
	}

	public function process_rental_button($planid){
		if ($this->moduleIsEnabled($this->selectedModule) === true) {
			return $this->moduleClasses[$this->selectedModule]->process_rental_button($planid);
		}
	}

	public function process_recurring_button(){
		if ($this->moduleIsEnabled($this->selectedModule) === true) {
			return $this->moduleClasses[$this->selectedModule]->process_recurring_button();
		}
	}

	public function before_rental_process(){
		if ($this->moduleIsEnabled($this->selectedModule) === true) {
			return $this->moduleClasses[$this->selectedModule]->before_rental_process();
		}
	}

	public function before_process(){
		if ($this->moduleIsEnabled($this->selectedModule) === true) {
			return $this->moduleClasses[$this->selectedModule]->before_process();
		}
	}

	public function after_process(){
		if ($this->moduleIsEnabled($this->selectedModule) === true) {
			return $this->moduleClasses[$this->selectedModule]->after_process();
		}
	}

	public function get_error() {
		if ($this->moduleIsEnabled($this->selectedModule) === true) {
			return $this->moduleClasses[$this->selectedModule]->get_error();
		}
	}

	public function pre_confirmation_check() {
		// ############ Added CCGV Contribution ##########
		global $paymentModules;
		// ############ End Added CCGV Contribution ##########
		if ($this->moduleIsEnabled($this->selectedModule) === true){
			$module = $this->getModule();
			// ############ Added CCGV Contribution ##########
			if ($this->check_credit_covers() === true){ //  ICW CREDIT CLASS Gift Voucher System
				$module->enabled = false; //ICW CREDIT CLASS Gift Voucher System
				$module->enabled = false; //ICW CREDIT CLASS Gift Voucher System
				$this->unloadModule($this->selectedModule); //ICW CREDIT CLASS Gift Voucher System
				unset($module);
				$paymentModules = ''; //ICW CREDIT CLASS Gift Voucher System
			} else { //ICW CREDIT CLASS Gift Voucher System
				// ############ End Added CCGV Contribution ##########
				$module->pre_confirmation_check();
				// ############ Added CCGV Contribution ##########
			}
			// ############ End Added CCGV Contribution ##########
		}
	}

	public static function getMonthDropMenuArr(){
		$expires_month = array();
		for ($i = 1; $i < 13; $i++){
			$expires_month[] = array(
				'id'   => sprintf('%02d', $i),
				'text' => strftime('%B', mktime(0, 0, 0, $i, 1, 2000))
			);
		}
		return $expires_month;
	}
	
	public static function getYearDropMenuArr(){
		$expires_year = array();
		$today = getdate();
		for ($i = $today['year']; $i < $today['year'] + 10; $i++){
			$expires_year[] = array(
				'id'   => strftime('%y', mktime(0, 0, 0, 1, 1, $i)),
				'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}
		return $expires_year;
	}
	
	public static function getCreditCardOwnerField(){
		$input = htmlBase::newElement('input')->setName('cardOwner')->setId('cardOwner');
		
		$userAccount = self::getUserAccount();
		$paymentInfo = self::getPaymentInfo();
		
		$addressBook =& $userAccount->plugins['addressBook'];

		if (isset($paymentInfo['cardDetails']['cardOwner'])){
			$input->val($paymentInfo['cardDetails']['cardOwner']);
		}else{
			$billingAddress = $addressBook->getAddress('billing');
			if ($billingAddress !== false){
				$input->val($billingAddress['entry_firstname'] . ' ' . $billingAddress['entry_lastname']);
			}
		}
		return $input->draw();
	}
	
	public static function getCreditCardNumber(){
		$input = htmlBase::newElement('input')->setName('cardNumber')->setId('cardNumber');
		
		$paymentInfo = self::getPaymentInfo();
		if (isset($paymentInfo['cardDetails']['cardNumber'])){
			$input->val($paymentInfo['cardDetails']['cardNumber']);
		}
		return $input->draw();
	}
	
	public static function getCreditCardExpMonthField(){
		$input = htmlBase::newElement('selectbox')->setName('cardExpMonth')->setId('cardExpMonth');
		
		foreach(self::getMonthDropMenuArr() as $mInfo){
			$input->addOption($mInfo['id'], $mInfo['text']);
		}
		
		$paymentInfo = self::getPaymentInfo();
		if (isset($paymentInfo['cardDetails']['cardExpMonth'])){
			$input->selectOptionByValue($paymentInfo['cardDetails']['cardExpMonth']);
		}
		return $input->draw();
	}
	
	public static function getCreditCardExpYearField(){
		$input = htmlBase::newElement('selectbox')->setName('cardExpYear')->setId('cardExpYear');
		
		foreach(self::getYearDropMenuArr() as $yInfo){
			$input->addOption($yInfo['id'], $yInfo['text']);
		}
		
		$paymentInfo = self::getPaymentInfo();
		if (isset($paymentInfo['cardDetails']['cardExpYear'])){
			$input->selectOptionByValue($paymentInfo['cardDetails']['cardExpYear']);
		}
		return $input->draw();
	}
	
	public static function getCreditCardCvvField(){
		$input = htmlBase::newElement('input')->setName('cardCvvNumber')->setId('cardCvvNumber');
		
		$paymentInfo = self::getPaymentInfo();
		if (isset($paymentInfo['cardDetails']['cardCvvNumber'])){
			$input->val($paymentInfo['cardDetails']['cardCvvNumber']);
		}
		return $input->attr('size', 5)->attr('maxlength', 4)->draw();
	}
	
	public static function validateCreditCard($arr, $useCvv = false){
		include(sysConfig::get('DIR_WS_CLASSES') . 'cc_validation.php');
		$validator = new cc_validation();
		if ($useCvv === true){
			$result = $validator->validate(
				$arr['cardNumber'],
				$arr['cardExpMonth'],
				$arr['cardExpYear'],
				$arr['cardCvvNumber'],
				(isset($arr['cardType']) ? $arr['cardType'] : '')
			);
		}else{
			$result = $validator->validate_normal(
				$arr['cardNumber'],
				$arr['cardExpMonth'],
				$arr['cardExpYear']
			);
		}
		
		$error = '';
		if ($result !== true){
			switch ($result) {
				case -1:
					$error = sprintf(sysLanguage::get('TEXT_CCVAL_ERROR_UNKNOWN_CARD'), substr($validator->cc_number, 0, 4));
					break;
				case -2:
				case -3:
				case -4:
					$error = sysLanguage::get('TEXT_CCVAL_ERROR_INVALID_DATE');
					break;
				case -5:
					$error = sysLanguage::get('TEXT_CCVAL_ERROR_CARD_TYPE_MISMATCH');
					break;
				case -6:
					$error = sysLanguage::get('TEXT_CCVAL_ERROR_CVV_LENGTH');
					break;
				case false:
					$error = sysLanguage::get('TEXT_CCVAL_ERROR_INVALID_NUMBER');
					break;
			}
		}
		
		return array(
			'error'     => $error,
			'validator' => $validator
		);
	}
	
	public static function &getPaymentInfo(){
		global $onePageCheckout;
		if (isset($onePageCheckout) && is_object($onePageCheckout)){
			$paymentInfo = $onePageCheckout->onePage['info']['payment'];
		}elseif (Session::exists('pointOfSale') === true){
			$pointOfSale = &Session::getReference('pointOfSale');
			if (isset($pointOfSale->order['info']['payment'])){
				$paymentInfo = $pointOfSale->order['info']['payment'];
			}else{
				$paymentInfo = array();
			}
		}
		return $paymentInfo;
	}

	public static function &getUserAccount(){
		global $onePageCheckout, $membershipUpdate;
		if (isset($onePageCheckout) && is_object($onePageCheckout)){
			$userAccount = &$onePageCheckout->getUserAccount();
		}elseif (isset($membershipUpdate) && is_object($membershipUpdate)){
			$userAccount = &$membershipUpdate->getUserAccount();
		}elseif (Session::exists('pointOfSale') === true){
			$pointOfSale = &Session::getReference('pointOfSale');
			$userAccount = &$pointOfSale->getUserAccount();
		}
		return $userAccount;
	}

	public static function installModule($moduleName){
		if (Session::exists('login_id') === false) return;
		if (!file_exists(sysConfig::getDirFsCatalog() . sysConfig::get('DIR_WS_MODULES') . 'payment/' . $moduleName . '.php')) return;
		
   		sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages_phar/' . Session::get('language') . '/osc/admin/modules/payment/' . $moduleName . '.xml');
		include(sysConfig::getDirFsCatalog() . sysConfig::get('DIR_WS_MODULES') . 'payment/' . $moduleName . '.php');

		$module = new $moduleName();
		$dataArray = $module->install();

		if (is_array($dataArray)){
			foreach($dataArray as $key => $cInfo){
				$Qinsert = new Configuration();
				$Qinsert->configuration_group_id = 6;
				if (!is_array($cInfo)){
					switch($key){
						case 'sortOrderKey':
							$Qinsert->configuration_title = 'Sort order of display';
							$Qinsert->configuration_value = '0';
							$Qinsert->configuration_description = 'Sort order of display. Lowest is displayed first.';
							break;
						case 'paymentZoneKey':
							$Qinsert->configuration_title = 'Payment Zone';
							$Qinsert->configuration_value = '0';
							$Qinsert->configuration_description = 'If a zone is selected, only enable this payment method for that zone.';
							$Qinsert->use_function = 'tep_get_zone_class_title';
							$Qinsert->set_function = 'tep_cfg_pull_down_zone_classes(';
							break;
						case 'orderStatusKey':
							$Qinsert->configuration_title = 'Set Order Status';
							$Qinsert->configuration_value = '0';
							$Qinsert->configuration_description = 'Set the status of orders made with this payment module to this value';
							$Qinsert->use_function = 'tep_get_order_status_name';
							$Qinsert->set_function = 'tep_cfg_pull_down_order_statuses(';
							break;
						case 'checkoutMethodKey':
							$Qinsert->configuration_title = 'Accept For';
							$Qinsert->configuration_value = '0';
							$Qinsert->configuration_description = 'Allow this payment module to be used for (Rental membership signup, Normal checkout or Both )';
							$Qinsert->set_function = 'tep_cfg_select_option(array(\'Membership\',\'Normal\',\'Both\'),';
							break;
						default:
							die('Unknown Key: ' . $key . '::' . $cInfo);
							break;
					}
					$Qinsert->configuration_key = $cInfo;
				}else{
					foreach($cInfo as $k => $v){
						$Qinsert->$k = $v;
					}
				}
				$Qinsert->save();
			}
		}
	}
	
	public static function uninstallModule($moduleName){
		global $appExtension;
   		sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages_phar/' . Session::get('language') . '/osc/admin/modules/payment/' . $moduleName . '.xml');
		include(sysConfig::get('DIR_FS_CATALOG') . sysConfig::get('DIR_WS_MODULES') . 'payment/' . $moduleName . '.php');

		$module = new $moduleName();
		$moduleKeys = $module->keys();
		
		Doctrine_Query::create()
		->delete('Configuration')
		->whereIn('configuration_key', $moduleKeys)
		->execute();
	}
	
	public static function addMissingConfig($moduleName){
   		sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages_phar/' . Session::get('language') . '/osc/admin/modules/payment/' . $moduleName . '.xml');
		include(sysConfig::get('DIR_FS_CATALOG') . sysConfig::get('DIR_WS_MODULES') . 'payment/' . $moduleName . '.php');

		$module = new $moduleName();
		foreach($module->install() as $configKey => $configSettings){
			$Qcheck = Doctrine_Query::create()
			->select('configuration_id')
			->from('Configuration');
			if (is_array($configSettings)){
				$Qcheck->where('configuration_key = ?', $configSettings['configuration_key']);
			}else{
				$Qcheck->where('configuration_key = ?', $configSettings);
			}
			$Qcheck->execute();
			if ($Qcheck->count() <= 0){
				if (is_array($configSettings)){
					$newConfig = new Configuration();
					$newConfig->configuration_key = (string) $configSettings['configuration_key'];
					$newConfig->configuration_title = (string) $configSettings['configuration_title'];
					$newConfig->configuration_value = (string) $configSettings['configuration_value'];
					$newConfig->configuration_description = (string) $configSettings['configuration_description'];

					if (isset($configSettings['use_function'])){
						$newConfig->use_function = (string) $configSettings['use_function'];
					}

					if (isset($configSettings['set_function'])){
						$newConfig->set_function = (string) $configSettings['set_function'];
					}
					
					$newConfig->configuration_group_id = (int) 6;
					$newConfig->sort_order = (int) 0;
					$newConfig->save();
				}else{
					die('Preset Keys Are Not Supported At This Time.');
				}
			}
			$Qcheck->free();
		}
	}

	// ############ Added CCGV Contribution ##########
	// check credit covers was setup to test whether credit covers is set in other parts of the code
	public function check_credit_covers() {
		return Session::get('credit_covers');
	}
	// ############ End Added CCGV Contribution ##########
}
?>
