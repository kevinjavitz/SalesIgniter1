<?php
/*
$Id: order_total.php,v 1.4 2003/02/11 00:04:53 hpdl Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/

class order_total {
	public $installedModules,
	$moduleClasses;

	// class constructor
	function order_total() {
		if (defined('MODULE_ORDER_TOTAL_INSTALLED') && tep_not_null(MODULE_ORDER_TOTAL_INSTALLED)) {
			$this->installedModules = explode(';', MODULE_ORDER_TOTAL_INSTALLED);

			$include_modules = array();
			foreach($this->installedModules as $moduleFileName){
				$include_modules[] = array(
				'class' => substr($moduleFileName, 0, strrpos($moduleFileName, '.')),
				'file'  => $moduleFileName
				);
			}

			for ($i=0, $n=sizeof($include_modules); $i<$n; $i++) {
				if (!file_exists(sysConfig::getDirFsCatalog() . DIR_WS_MODULES . 'order_total/' . $include_modules[$i]['file'])){
					$extDir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
					$found = false;
					foreach($extDir as $fileObj){
						if ($fileObj->isDot() || $fileObj->isFile()) continue;
						
						$extModuleDir = $fileObj->getPathname() . '/order_total/modules/';
						$extModuleLangDir = $fileObj->getPathname() . '/order_total/language_defines/';
						if (is_dir($extModuleDir)){
							$extModuleDir = new DirectoryIterator($extModuleDir);
							foreach($extModuleDir as $extModule){
								if ($extModule->isDot() || $extModule->isDir()) continue;

								$className = $extModule->getBasename('.php');
								if (!class_exists($className)){
									$found = true;
									require($extModule->getPathname());
								}
							}
						}
					}
					if ($found === false){
						/* @TODO: Make this report using messageStack rather than killing the script */
						die('Order Total Module Not Found: ' . $include_modules[$i]['file']);
					}
				}else{
					sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages_phar/' . Session::get('language') . '/osc/modules/order_total/' . substr($include_modules[$i]['file'], 0, -4) . '.xml');
					include(DIR_FS_CATALOG . DIR_WS_MODULES . 'order_total/' . $include_modules[$i]['file']);
				}

				$this->moduleClasses[$include_modules[$i]['class']] = new $include_modules[$i]['class'];
			}
		}
	}

	function modulesAreInstalled(){
		return (is_array($this->moduleClasses) ? true : false);
	}

	function moduleIsLoaded($moduleName){
		if (isset($this->moduleClasses[$moduleName]) && is_object($this->moduleClasses[$moduleName])){
			return true;
		}
		return false;
	}

	function moduleIsEnabled($moduleName){
		if ($this->moduleIsLoaded($moduleName)){
			if ($this->moduleClasses[$moduleName]->enabled === true){
				return true;
			}
		}
		return false;
	}

	function getModule($moduleName = false){
		if ($moduleName === false){
			if (isset($this->selectedModule)){
				$moduleName = $this->selectedModule;
			}else{
				die('No order total module specified and no module specifically loaded');
			}
		}

		if ($this->moduleIsLoaded($moduleName)){
			return $this->moduleClasses[$moduleName];
		}else{
			//die('Module Not Loaded :: ' . $moduleName);
		}
		return false;
	}

	function getModuleClasses(){
		return $this->moduleClasses;
	}

	function unloadModule($moduleName){
		unset($this->moduleClasses[$moduleName]);
	}

	function process() {
		$orderTotalArray = array();
		if ($this->modulesAreInstalled() === true) {
			foreach($this->moduleClasses as $moduleName => $moduleClass){
				if ($this->moduleIsEnabled($moduleName) === true){
					$moduleClass->process();
					$moduleOutput = $moduleClass->output;
					for($i=0, $n=sizeof($moduleOutput); $i<$n; $i++){
						if (tep_not_null($moduleOutput[$i]['title']) && tep_not_null($moduleOutput[$i]['text'])){
							$orderTotalArray[] = array(
							'code'       => $moduleClass->code,
							'title'      => $moduleOutput[$i]['title'],
							'text'       => $moduleOutput[$i]['text'],
							'value'      => $moduleOutput[$i]['value'],
							'sort_order' => $moduleClass->sort_order
							);
						}
					}
				}
			}
		}
		return $orderTotalArray;
	}

	function output($type = 'html') {
		if ($type == 'json'){
			$outputString = array();
			if ($this->modulesAreInstalled() === true) {
				foreach($this->moduleClasses as $moduleName => $moduleClass){
					if ($this->moduleIsEnabled($moduleName) === true){
						$moduleOutput = $moduleClass->output;
						for($i=0, $n=sizeof($moduleOutput); $i<$n; $i++){
							$outputString[] = array(
								$moduleOutput[$i]['title'] . (isset($moduleOutput[$i]['help']) ? ' (<a href=\"' . $moduleOutput[$i]['help'] . '\" onclick=\"popupWindow(\'' . $moduleOutput[$i]['help'] . '\',\'300\',\'300\');return false;\">?</a>)' : ''),
								$moduleOutput[$i]['text']
							);
						}
					}
				}
			}
		}else{
			$outputString = '';
			if ($this->modulesAreInstalled() === true) {
				foreach($this->moduleClasses as $moduleName => $moduleClass){
					if ($this->moduleIsEnabled($moduleName) === true){
						$moduleOutput = $moduleClass->output;
						for($i=0, $n=sizeof($moduleOutput); $i<$n; $i++){
							$outputString .= '<tr>
	                           <td align="right" class="main">' . $moduleOutput[$i]['title'] . '</td>
	                           <td align="right" class="main">' . $moduleOutput[$i]['text'] . '</td>
	                          </tr>';
						}
					}
				}
			}
		}
		return $outputString;
	}

	// ############ Added CCGV Contribution ##########
	//
	// This function is called in checkout payment after display of payment methods. It actually calls
	// two credit class functions.
	//
	// use_credit_amount() is normally a checkbox used to decide whether the credit amount should be applied to reduce
	// the order total. Whether this is a Gift Voucher, or discount coupon or reward points etc.
	//
	// The second function called is credit_selection(). This in the credit classes already made is usually a redeem box.
	// for entering a Gift Voucher number. Note credit classes can decide whether this part is displayed depending on
	// E.g. a setting in the admin section.
	//
	function credit_selection() {
		$selectionString = '';
		$html = '';
		if ($this->modulesAreInstalled() === true) {
			foreach($this->moduleClasses as $moduleName => $moduleClass){
				if ($this->moduleIsEnabled($moduleName) === true && isset($moduleClass->credit_class) && $moduleClass->credit_class === true){
					if ($selectionString == '') $selectionString = $moduleClass->credit_selection();
					if ($selectionString != ''){
						$selectionString = '<tr colspan="4">
                           <td colspan="4" width="100%">' .  tep_draw_separator('pixel_trans.gif', '100%', '10') . '</td>
                          </tr>
                          <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
                           <td class="main">' .  tep_draw_separator('pixel_trans.gif', '10', '1') . '<b>' . $moduleClass->header . '</b>' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>
                           ' . $selectionString . '
                          </tr>';
					}
				}
			}

			if ($selectionString != '') {
				$html = '<tr>
                   <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                     <td class="main"><b>' . sysLanguage::get('TABLE_HEADING_CREDIT') . '</b></td>
                    </tr>
                   </table></td>
                  </tr>
                  <tr>
                   <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
                    <tr class="infoBoxContents">
                     <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                      <tr>
                       <td width="10">' .  tep_draw_separator('pixel_trans.gif', '10', '1') .'</td>
                       <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                        ' . $selectionString . '
                       </table></td>
                       <td width="10">' .  tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>
                      </tr>
                     </table></td>
                    </tr>
                   </table></td>
                  </tr>
                  <tr>
                   <td width="100%">' .  tep_draw_separator('pixel_trans.gif', '100%', '10') . '</td>
                  </tr>';
			}
		}
		return $html;
	}

	// #################### Begin Added CGV JONYO ######################
	function sub_credit_selection(){
		$selectionString = '';
		$useCreditString = '';
		if ($this->modulesAreInstalled() === true) {
			foreach($this->moduleClasses as $moduleName => $moduleClass){
				if ($this->moduleIsEnabled($moduleName) === true && isset($moduleClass->credit_class) && $moduleClass->credit_class === true){
					$useCreditString = $moduleClass->use_credit_amount_sub();
					if ($selectionString == '') $selectionString = $moduleClass->credit_selection();
					if ($useCreditString != '' || $selectionString != ''){
						$selectionString = '<tr>' .
						'<td width="10">' .  tep_draw_separator('pixel_trans.gif', '10', '1') .'</td>' .
						'<td colspan=2><table border="0" cellpadding="2" cellspacing="0" width="100%">' .
						'<tr class="moduleRow">' . "\n" .
						'<td width="10">' .  tep_draw_separator('pixel_trans.gif', '10', '1') .'</td>' .
						'<td class="main">' . $useCreditString . '</td>' .
						'<td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>' .
						'</tr>' .
						'</table></td>' .
						'<td width="10">' .  tep_draw_separator('pixel_trans.gif', '10', '1') .'</td>' .
						'</tr>';
					}
				}
			}
		}
		return $selectionString;
	}

	// update_credit_account is called in checkout process on a per product basis. It's purpose
	// is to decide whether each product in the cart should add something to a credit account.
	// e.g. for the Gift Voucher it checks whether the product is a Gift voucher and then adds the amount
	// to the Gift Voucher account.
	// Another use would be to check if the product would give reward points and add these to the points/reward account.
	//
	function update_credit_account($cartProduct) {
		if ($this->modulesAreInstalled() === true) {
			foreach($this->moduleClasses as $moduleName => $moduleClass){
				if ($this->moduleIsEnabled($moduleName) === true && (isset($moduleClass->credit_class) && $moduleClass->credit_class)){
					$moduleClass->update_credit_account($cartProduct);
				}
			}
		}
	}

	// This function is called in checkout confirmation.
	// It's main use is for credit classes that use the credit_selection() method. This is usually for
	// entering redeem codes(Gift Vouchers/Discount Coupons). This function is used to validate these codes.
	// If they are valid then the necessary actions are taken, if not valid we are returned to checkout payment
	// with an error
	//
	function collect_posts() {
		if ($this->modulesAreInstalled() === true) {
			foreach($this->moduleClasses as $moduleName => $moduleClass){
				if ($this->moduleIsEnabled($moduleName) === true && (isset($moduleClass->credit_class) && $moduleClass->credit_class)){
					$post_var = 'c' . $moduleClass->code;
					if (array_key_exists($post_var, $_POST)){
						Session::set('post_var', $_POST[$post_var]);
					}
					$moduleClass->collect_posts();
				}
			}
		}
	}

	// pre_confirmation_check is called on checkout confirmation. It's function is to decide whether the
	// credits available are greater than the order total. If they are then a variable (credit_covers) is set to
	// true. This is used to bypass the payment method. In other words if the Gift Voucher is more than the order
	// total, we don't want to go to paypal etc.
	//
	function pre_confirmation_check() {
		// #################### Begin Added CGV JONYO ######################
		global $order;
		// #################### End Added CGV JONYO ######################
		if ($this->modulesAreInstalled() === true) {
			$total_deductions  = 0;
			$order_total = $order->info['total'];
			foreach($this->moduleClasses as $moduleName => $moduleClass){
				$order_total = $this->get_order_total_main($moduleName, $order_total);
				if ($this->moduleIsEnabled($moduleName) === true && (isset($moduleClass->credit_class) && $moduleClass->credit_class)){
					$total_deductions = $total_deductions + $moduleClass->pre_confirmation_check($order_total);
					$order_total = $order_total - $moduleClass->pre_confirmation_check($order_total);
				}
			}

			// #################### Begin Added CGV JONYO ######################
			$gv_payment_amount = 0;

			$otGv = $this->getModule('ot_gv');
			$giftVoucherAmount = $otGv->getCustomerGvAmount();
			if ($giftVoucherAmount){
				$gv_payment_amount += $giftVoucherAmount;
			}

			if ($order->info['total'] - $gv_payment_amount <= 0 ) {
				Session::set('credit_covers', true);
			} else{
				Session::remove('credit_covers');
			}
			// #################### End Added CGV JONYO ######################
		}
	}

	// this function is called in checkout process. it tests whether a decision was made at checkout payment to use
	// the credit amount be applied aginst the order. If so some action is taken. E.g. for a Gift voucher the account
	// is reduced the order total amount.
	//
	function apply_credit() {
		if ($this->modulesAreInstalled() === true) {
			foreach($this->moduleClasses as $moduleName => $moduleClass){
				if ($this->moduleIsEnabled($moduleName) === true && isset($moduleClass->credit_class) && $moduleClass->credit_class === true){
					$moduleClass->apply_credit();
				}
			}
		}
	}

	// Called in checkout process to clear session variables created by each credit class module.
	//
	function clear_posts() {
		if ($this->modulesAreInstalled() === true) {
			foreach($this->moduleClasses as $moduleName => $moduleClass){
				if ($this->moduleIsEnabled($moduleName) === true && isset($moduleClass->credit_class) && $moduleClass->credit_class){
					Session::remove('c' . $moduleClass->code);
				}
			}
		}
	}

	// Called at various times. This function calulates the total value of the order that the
	// credit will be appled aginst. This varies depending on whether the credit class applies
	// to shipping & tax
	//
	function get_order_total_main($class, $order_total) {
		//if ($GLOBALS[$class]->include_tax == 'false') $order_total=$order_total-$order->info['tax'];
		//if ($GLOBALS[$class]->include_shipping == 'false') $order_total=$order_total-$order->info['shipping_cost'];
		return $order_total;
	}
	// ############ End Added CCGV Contribution ##########
	
	
	public static function includeModuleFiles($moduleName, $extName = ''){
		$catalogDir = sysConfig::getDirFsCatalog();
		if (!empty($extName)){
			$moduleFile = $catalogDir . 'extensions/' . $extName . '/order_total/modules/' . $moduleName . '.php';
		}else{
			$moduleFile = $catalogDir . sysConfig::get('DIR_WS_MODULES') . 'order_total/' . $moduleName . '.php';
			$langFile = true;
		}
		
		if (!file_exists($moduleFile)){
			return false;
		}
		
		if (isset($langFile)){
			sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages_phar/' . Session::get('language') . '/osc/modules/order_total/' . $moduleName . '.xml');
		}
		require($moduleFile);
		
		return true;
	}
	
	public static function installModule($moduleName, $extName = ''){
		if (Session::exists('login_id') === false) return;
		
		if (self::includeModuleFiles($moduleName, $extName)){
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
	}
	
	public static function uninstallModule($moduleName, $extName = ''){
		global $appExtension;
		if (self::includeModuleFiles($moduleName, $extName)){
			$module = new $moduleName();
			$moduleKeys = $module->keys();
		
			Doctrine_Query::create()
			->delete('Configuration')
			->whereIn('configuration_key', $moduleKeys)
			->execute();
		}
	}
	/*public static function installModule($moduleName, $extName = ''){
		if (Session::exists('login_id') === false) return;
		
		if (self::includeModuleFiles($moduleName, $extName)){
			$module = new $moduleName();
			$dataArray = $module->install();

			if (is_array($dataArray)){
				$Configuration = dataAccess::getTable('Configuration');
				$records = array();
				$insertKey = 0;
				foreach($dataArray as $key => $cInfo){
					$Qinsert = $Configuration->create();
					$Qinsert->configuration_group_id = 6;
					if (!is_array($cInfo)){
						switch($key){
							case 'sortOrderKey':
								$Qinsert->configuration_title = 'Sort order of display';
								$Qinsert->configuration_value = '0';
								$Qinsert->configuration_description = 'Sort order of display. Lowest is displayed first.';
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
	}
	
	public static function uninstallModule($moduleName, $extName = ''){
		global $appExtension;
		if (self::includeModuleFiles($moduleName, $extName)){
			$module = new $moduleName();
			
			dataAccess::getTable('Configuration')
			->findAll('configuration_key IN("' . implode('","', $module->keys()) . '")')
			->delete();
		}
	}*/
}
?>