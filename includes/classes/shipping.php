<?php
/*
$Id: shipping.php,v 1.23 2003/06/29 11:22:05 hpdl Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/

class shipping {
	private $installedModules,
	$selectedModule,
	$moduleClasses;

	// class constructor
	function shipping($module = '') {
		if (defined('MODULE_SHIPPING_INSTALLED') && tep_not_null(MODULE_SHIPPING_INSTALLED)){
			$this->installedModules = explode(';', MODULE_SHIPPING_INSTALLED);

			$include_modules = array();
			$useSingle = false;
			if (tep_not_null($module)){
				$moduleName = substr($module['id'], 0, strpos($module['id'], '_'));
				if (in_array($moduleName . '.php', $this->installedModules)){
					$useSingle = true;
				}
			}

			if ($useSingle === true){
				$include_modules[] = array(
				'class' => $moduleName,
				'file'  => $moduleName . '.php'
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
				if (!class_exists($include_modules[$i]['class'])){
					sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages_phar/' . Session::get('language') . '/osc/modules/shipping/' . substr($include_modules[$i]['file'], 0, -4) . '.xml');
					include(DIR_FS_CATALOG . DIR_WS_MODULES . 'shipping/' . $include_modules[$i]['file']);
				}

				$this->moduleClasses[$include_modules[$i]['class']] = new $include_modules[$i]['class'];
			}
		}
	}

	function getDropMenuArray(){
		$dropMenuArray = array(
		array('id' => '', 'text' => 'Please Select A Shipping Method')
		);
		foreach($this->moduleClasses as $moduleName => $moduleClass){
			$dropMenuArray[] = array(
			'id'   => $moduleClass->code,
			'text' => $moduleClass->title
			);
		}
		return $dropMenuArray;
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
				die('No shipping module specified and no module specifically loaded');
			}
		}

		if ($this->moduleIsLoaded($moduleName)){
			return $this->moduleClasses[$moduleName];
		}else{
			die('Module Not Loaded :: ' . $moduleName);
		}
		return false;
	}

		public static function addMissingConfig($moduleName){
			sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages_phar/' . Session::get('language') . '/osc/modules/shipping/' . $moduleName . '.xml');

		include(sysConfig::get('DIR_FS_CATALOG') . sysConfig::get('DIR_WS_MODULES') . 'shipping/' . $moduleName . '.php');

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

	function unloadModule($moduleName){
		unset($this->moduleClasses[$moduleName]);
	}

	function quote($method = '', $module = '') {
		global $total_weight, $shipping_weight, $shipping_quoted, $shipping_num_boxes;

		$quotes_array = array();
		if ($this->modulesAreInstalled() === true) {
			$this->calculateWeight();

			$include_quotes = array();
			foreach($this->moduleClasses as $moduleName => $moduleClass){
				if ($this->moduleIsEnabled($moduleName) === true){
					if (!tep_not_null($module) || (tep_not_null($module) && $module == $moduleName)){
						$quotes = $moduleClass->quote($method);
						if (is_array($quotes)) $quotes_array[] = $quotes;
					}
				}
			}
		}
		return $quotes_array;
	}

	function calculateWeight(){
		global $ShoppingCart, $total_weight, $shipping_weight, $shipping_quoted, $shipping_num_boxes;
		$shipping_quoted = '';
		$shipping_num_boxes = 1;
		$shipping_weight = $ShoppingCart->showWeight();

		if (SHIPPING_BOX_WEIGHT >= $shipping_weight*SHIPPING_BOX_PADDING/100) {
			$shipping_weight = $shipping_weight+SHIPPING_BOX_WEIGHT;
		} else {
			$shipping_weight = $shipping_weight + ($shipping_weight*SHIPPING_BOX_PADDING/100);
		}

		if ($shipping_weight > SHIPPING_MAX_WEIGHT) { // Split into many boxes
			$shipping_num_boxes = ceil($shipping_weight/SHIPPING_MAX_WEIGHT);
			$shipping_weight = $shipping_weight/$shipping_num_boxes;
		}
	}

	function cheapest() {
		if ($this->modulesAreInstalled() === true) {
			$rates = array();
			foreach($this->moduleClasses as $moduleName => $moduleClass){
				if ($this->moduleIsEnabled($moduleName) && $moduleName != 'reservation'){
					$quotes = $moduleClass->quotes;
					for($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++){
						if (isset($quotes['methods'][$i]['cost']) && $quotes['methods'][$i]['cost'] != '') {
							$rates[] = array(
								'id'     => $quotes['id'] . '_' . $quotes['methods'][$i]['id'],
								'module' => $quotes['id'],
								'method' => $quotes['methods'][$i]['id'],
								'title'  => $quotes['module'] . ' (' . $quotes['methods'][$i]['title'] . ')',
								'cost'   => $quotes['methods'][$i]['cost']
							);
						}
					}
				}
			}

			$cheapest = false;
			for ($i=0, $n=sizeof($rates); $i<$n; $i++) {
				if (is_array($cheapest)) {
					if ($rates[$i]['cost'] < $cheapest['cost']) {
						$cheapest = $rates[$i];
					}
				} else {
					$cheapest = $rates[$i];
				}
			}
			return $cheapest;
		}
	}
}
?>