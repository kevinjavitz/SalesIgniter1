<?php
/*
	Multi Stores Extension Version 1.1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_multiStore extends ExtensionBase {

	private $storesArray = array();

	private $storeInfoCache = array();

	public function __construct() {
		parent::__construct('multiStore');
	}

	public function init(){
		global $App, $appExtension, $Template;
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'EmailEventSetAllowedVars',
			'OrderQueryBeforeExecute',
			'MetaTagsFetchPageQueryBeforeExecute',
			'ProductInventoryBarcodeHasInventoryQueryBeforeExecute',
			'ProductInventoryBarcodeGetInventoryItemsQueryBeforeExecute',
			'ProductInventoryBarcodeGetInventoryItemsArrayPopulate',
			'OrdersProductsReservationListingBeforeExecuteUtilities',
			'MetaTagsFetchDefaultsQueryBeforeExecute',
			'BoxMarketingAddLink',
			'OrderBeforeSendEmail',
			'OrderSingleLoad'
		), null, $this);
		
		if ($appExtension->isCatalog()){
			EventManager::attachEvents(array(
				'CategoryQueryBeforeExecute',
				'ModuleConfigReaderModuleConfigLoad',
				'CustomerQueryBeforeExecute',
				'ProductQueryBeforeExecute',
				'ProductListingQueryBeforeExecute',
				'SpecialQueryBeforeExecute',
				'FeaturedQueryBeforeExecute',
				'CheckoutProcessPostProcess',
				'SetTemplateName',
				'SeoUrlsInit',
				'CheckoutAddNewCustomer',
				'ScrollerFeaturedQueryBeforeExecute',
				'ProductQueryAfterExecute',
				'ReviewsQueryBeforeExecute'
			), null, $this);
		}
		
		if ($appExtension->isAdmin()){
			EventManager::attachEvents(array(
				'BoxConfigurationAddLink',
				'AdminHeaderRightAddContent',
				'AdminInventoryCentersListingQueryBeforeExecute',
				'ProductInventoryReportsListingQueryBeforeExecute',
				'MetaTagsAdminEditAddTabContents',
				'MetaTagsAdminSaveQueryBeforeExecute',
				'NewCustomerAccountBeforeExecute',
				'CustomerInfoAddTableContainer',
				'AdminOrdersListingBeforeExecute',
                'AdminOrdersListingBeforeExecuteReportConsumption',
                'AdminOrdersListingBeforeExecuteReportConsumptionBarcodes',
				'AdminProductListingTemplateQueryBeforeExecute',
				'ProductInventoryReportsListingQueryBeforeExecute'
			), null, $this);
			$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.dropdownchecklist.js');
			$App->addStylesheetFile('ext/jQuery/themes/smoothness/ui.dropdownchecklist.css');
			$App->addJavascriptFile('extensions/multiStore/javascript/main.js');
		}

			if ($App->getAppName() == 'customers'){
				EventManager::attachEvents(array(
						'CustomersListingQueryBeforeExecute'
					), null, $this);
			}

			if ($App->getAppName() == 'orders'){
				EventManager::attachEvents(array(
					'OrdersListingAddGridHeader',
					'OrdersListingAddGridBody'
				), null, $this);
			}

			if ($App->getAppName() == 'products'){
				EventManager::attachEvents(array(
					'AdminProductListingQueryBeforeExecute'
				), null, $this);
			}

			if ($App->getAppName() == 'categories'){

				EventManager::attachEvents(array(
						'CategoryListingQueryBeforeExecute'
				), null, $this);
			}

		$this->loadStoreInfo();
	}

	public function loadStoreInfo() {
		global $App;
		if ($App->getEnv() == 'admin'){
			$this->loadStoreInfoAdmin();
		}
		else {
			$this->loadStoreInfoCatalog();
		}
	}

	private function loadStoreInfoAdmin(){
		if (Session::exists('login_id')){
			$Qadmin = Doctrine_Query::create()
				->from('Admin')
				->where('admin_id = ?', Session::get('login_id'))
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$Qstore = Doctrine_Query::create()
				->from('Stores')
				->execute(array(), Doctrine::HYDRATE_ARRAY);
			$adminsStores = array();
			foreach($Qstore as $iStore){
				$adminsStores[] = $iStore['stores_id'];
			}
			$this->storeInfo = $Qstore[0];
			if (Session::exists('login_groups_id') && Session::get('login_groups_id') == '1'){
				Session::set('admin_allowed_stores', $adminsStores);
			}else{
				Session::set('admin_allowed_stores', explode(',', $Qadmin[0]['admins_stores']));
			}
			if (Session::exists('admin_showing_stores') === false || Session::get('admin_showing_stores') == '' || sizeof(Session::get('admin_showing_stores')) == 0){
				Session::set('admin_showing_stores',  explode(',', $Qadmin[0]['admins_stores']));
			}



			if (isset($_GET['stores_id'])){
				$validStores = array();
				foreach($_GET['stores_id'] as $storeId){
					if ($storeId == 'all') continue;
					if (in_array($storeId, Session::get('admin_allowed_stores')) === false) continue;
					$validStores[] = $storeId;
				}
				Session::set('admin_showing_stores', $validStores);
				tep_redirect(itw_app_link(tep_get_all_get_params(array('action', 'stores_id'))));
			}
		}else{
			$Qstore = Doctrine_Query::create()
				->from('Stores')
				->where('stores_id = ?', 1)
				->execute(array(), Doctrine::HYDRATE_ARRAY);
			$this->storeInfo = $Qstore[0];
		}

		Session::set('tplDir', 'fallback');

		sysLanguage::set('HEAD_TITLE_TAG_DEFAULT', $this->storeInfo['stores_name']);

		sysConfig::set('HTTP_COOKIE_DOMAIN', $this->storeInfo['stores_domain']);
		sysConfig::set('HTTPS_COOKIE_DOMAIN', $this->storeInfo['stores_ssl_domain']);
	}

	private function loadStoreInfoCatalog(){
		global $App;
		if ((getenv('HTTPS') == 'on' && Session::exists('current_store_id')) || isset($_GET['forceStoreId'])) {
			$checkId = Session::get('current_store_id');
			if (isset($_GET['forceStoreId'])){
				$checkId = $_GET['forceStoreId'];
			}
			$Qstore = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc('select * from stores where stores_id = "' . $checkId . '"');
		}
		else {
			$domainCheck = array($_SERVER['HTTP_HOST']);
			if (substr($_SERVER['HTTP_HOST'], 0, 4) != 'www.'){
				$domainCheck[] = 'www.' . $_SERVER['HTTP_HOST'];
			}
			else {
				$domainCheck[] = substr($_SERVER['HTTP_HOST'], 4);
			}

			if (getenv('HTTPS') == 'on'){
				$checkCol = 'stores_ssl_domain';
			}
			else {
				$checkCol = 'stores_domain';
			}
			$Qstore = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc('select * from stores where ' . $checkCol . ' IN("' . implode('", "', $domainCheck) . '")');
		}
		$this->storeInfo = $Qstore[0];
		Session::set('tplDir', $this->storeInfo['stores_template']);

		if (!Session::exists('current_store_id') || (Session::get('current_store_id') != $this->storeInfo['stores_id'])){
			//if (getenv('HTTPS') != 'on'){
				Session::set('current_store_id', $this->storeInfo['stores_id']);
			//}
		}
		else {
			$Qconfig = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc('select configuration_key, configuration_value from stores_configuration where stores_id = "' . Session::get('current_store_id') . '"');
			if (sizeof($Qconfig) > 0){
				foreach($Qconfig as $cInfo){
					sysConfig::set($cInfo['configuration_key'], $cInfo['configuration_value']);
				}
			}
		}

		define('HEAD_TITLE_TAG_DEFAULT', $this->storeInfo['stores_name']);
		if ($App->getEnv() == 'catalog'){
			sysConfig::set('HTTP_SERVER', 'http://' . $this->storeInfo['stores_domain']);
			sysConfig::set('HTTPS_SERVER', 'https://' . $this->storeInfo['stores_ssl_domain']);
			$defaultCurrency = $this->storeInfo['default_currency'];
			Session::set('mainCurrencyStore'.$this->storeInfo['stores_id'], $defaultCurrency);
			if (Session::exists('currencyStore'.$this->storeInfo['stores_id']) === false || isset($_GET['currency']) ) {
				if (isset($_GET['currency'])) {
					if (!$currency = tep_currency_exists($_GET['currency'])) $currency = $defaultCurrency;
				} else {
					$currency = $defaultCurrency;
				}
				Session::set('currency', $currency);
				Session::set('currencyStore'.$this->storeInfo['stores_id'], $currency);
			}else{
				Session::set('currency', Session::get('currencyStore'.$this->storeInfo['stores_id']));
			}

			$defaultLanguage = $this->storeInfo['default_language'];
			Session::set('mainLanguageStore'.$this->storeInfo['stores_id'], $defaultLanguage);
			if (Session::exists('languageStore'.$this->storeInfo['stores_id']) === false || isset($_GET['language']) ) {
				if (isset($_GET['language'])) {
					if (!$language = tep_language_exists($_GET['language'])) $language = $defaultLanguage;
				} else {
					$language = $defaultLanguage;
				}
				Session::set('language', $language);
				Session::set('languageStore'.$this->storeInfo['stores_id'], $language);
			}else{
				Session::set('language', Session::get('languageStore'.$this->storeInfo['stores_id']));
				$language = Session::get('languageStore'.$this->storeInfo['stores_id']);
			}
			sysLanguage::init($language);

			if(sysConfig::get('EXTENSION_MULTI_STORE_REDIRECT_BY_COUNTRY') == 'True'){
				include(sysConfig::getDirFsCatalog().'extensions/multiStore/geoip/geoip.php');
				$storeCountries = explode(',', $this->storeInfo['stores_countries']);

				if(file_exists(sysConfig::getDirFsCatalog().'extensions/multiStore/geoip/GeoIP.dat')){
					$gi = geoip_open(sysConfig::getDirFsCatalog().'extensions/multiStore/geoip/GeoIP.dat',GEOIP_STANDARD);
					$visitorCountry = geoip_country_name_by_addr($gi, $_SERVER['REMOTE_ADDR']);
					geoip_close($gi);
				}else{
					$visitorCountry = 'United States';
				}


				if(!in_array($visitorCountry, $storeCountries)){
					$Qstoresr = Doctrine_Manager::getInstance()
						->getCurrentConnection()
						->fetchAssoc('select * from stores where FIND_IN_SET("'.$visitorCountry.'", stores_countries) > 0');
					$redirectStoreInfo = false;
					if (sizeof($Qstoresr) > 0){
						$redirectStoreInfo = $Qstoresr[0];
					}else{
						$Qstoresr = Doctrine_Manager::getInstance()
							->getCurrentConnection()
							->fetchAssoc('select * from stores where is_default = 1');
						$redirectStoreInfo = $Qstoresr[0];
					}
					if($redirectStoreInfo !== false){
						if($this->storeInfo['stores_domain'] != $redirectStoreInfo['stores_domain']){
							tep_redirect('http://' . $redirectStoreInfo['stores_domain']);
						}
					}
				}
			}
		}
		sysConfig::set('HTTP_COOKIE_DOMAIN', $this->storeInfo['stores_domain']);
		sysConfig::set('HTTPS_COOKIE_DOMAIN', $this->storeInfo['stores_ssl_domain']);
		if($App->getAppPage() == 'default' && $App->getAppName() == 'index'){
			if($this->storeInfo['home_redirect_store_info'] == '1'){
				tep_redirect(itw_app_link('appExt=multiStore&store_id='.$this->storeInfo['stores_id'],'show_store','default'));
			}
		}
	}

	public function EmailEventSetAllowedVars(&$allowedVars){
		$allowedVars['store_name'] = $this->storeInfo['stores_name'];
		$allowedVars['store_owner'] = $this->storeInfo['stores_owner'];
		$allowedVars['store_owner_email'] = $this->storeInfo['stores_email'];
		$allowedVars['store_url'] = 'http://' . $this->storeInfo['stores_domain'];
	}

	public function ReviewsQueryBeforeExecute(&$Qreviews){
		$Qreviews->leftJoin('p.ProductsToStores p2s')
		->andWhere('p2s.stores_id = ?', Session::get('current_store_id'));
	}

	public function AdminProductListingQueryBeforeExecute(&$Qproducts){
		if(Session::exists('admin_showing_stores')){
			$Qproducts->leftJoin('p.ProductsToStores p2s')
				->andWhere('FIND_IN_SET(p2s.stores_id,"'.implode(',',Session::get('admin_showing_stores')).'") > 0 OR p2s.stores_id is null' );
		}
	}
	public function OrderBeforeSendEmail(&$order, &$emailEvent, &$products_ordered, &$sendVariables){
		if(isset($this->storeInfo['stores_email']) && !empty($this->storeInfo['stores_email'])){
			$sendVariables['emails'][] = array(
				'email' => $this->storeInfo['stores_email'],
				'name'  => $this->storeInfo['stores_owner']
			);
		}
	}

	public function AdminProductListingTemplateQueryBeforeExecute(&$Qproducts){
		$storesArr = array();
		foreach($this->getStoresArray() as $arr){
			$storesArr[] = $arr['stores_id'];
		}

		$Qproducts->leftJoin('p.ProductsToStores p2s')
		->andWhereIn('p2s.stores_id', $storesArr);

	}

	function pc_array_power_set($array) {
		// initialize by adding the empty set
		$results = array(array( ));

		foreach ($array as $element)
			foreach ($results as $combination)
				array_push($results, array_merge(array($element), $combination));

		return $results;
	}

	public function AdminInventoryCentersListingQueryBeforeExecute(&$Qcenter) {
		if (Session::exists('admin_showing_stores')){
			$power_set = $this->pc_array_power_set(Session::get('admin_showing_stores'));
			$string = '';
		    foreach($power_set as $set){
				if(count($set) > 0){
					$val = implode(';', array_reverse($set));
					$string .= 'inventory_center_stores = "'.$val.'" OR ';
				}
		    }
			$string = substr($string, 0, strlen($string) -3);
			$Qcenter->andWhere($string);
		}
	}

	public function CategoryListingQueryBeforeExecute(&$Qcategories) {
		if(Session::exists('admin_showing_stores')){
			$Qcategories
			->leftJoin('c.CategoriesToStores c2s')
			->andWhere('FIND_IN_SET(c2s.stores_id,"'.implode(',',Session::get('admin_showing_stores')).'") > 0 OR c2s.stores_id is null' );
			//->where('c2s.stores_id', Session::get('admin_showing_stores'));
		}
	}

	public function AdminOrdersListingBeforeExecute(&$Qorders) {
		$Qorders
			->leftJoin('o.OrdersToStores order2store')
			->leftJoin('order2store.Stores store')
			->addSelect('store.stores_name, order2store.stores_id')
			->whereIn('order2store.stores_id', Session::get('admin_showing_stores'));
	}

    public function AdminOrdersListingBeforeExecuteReportConsumptionBarcodes(&$Qorders) {
        $Qorders
            ->leftJoin('pib.ProductsInventoryBarcodesToStores pibs')
            ->leftJoin('pibs.Stores s')
            ->whereIn('pibs.inventory_store_id', Session::get('admin_showing_stores'));
    }

    public function AdminOrdersListingBeforeExecuteReportConsumption(&$Qorders) {
        $Qorders
            ->leftJoin('o.OrdersToStores ots')
            ->leftJoin('ots.Stores s')
            ->whereIn('ots.stores_id', Session::get('admin_showing_stores'));
    }

	public function SeoUrlsInit(&$seoUrl){
		$seoUrl->base_url = 'http://' . $this->storeInfo['stores_domain'] . sysConfig::getDirWsCatalog('NONSSL');
		$seoUrl->base_url_ssl = 'https://' . $this->storeInfo['stores_ssl_domain'] . sysConfig::getDirWsCatalog('SSL');
	}

	public function getStoresArray($storesId = false, $nofilter = false) {
		global $appExtension;
		if ($storesId !== false){
			if (!isset($this->storeInfoCache[$storesId])){
				$Qstores = Doctrine_Query::create()
					->from('Stores')
					->where('stores_id = ?', $storesId)
					->execute();
				$this->storeInfoCache[$storesId] = $Qstores[0];
			}
			return $this->storeInfoCache[$storesId];
		}else{
			if (empty($this->storesArray)){
				$Qstores = Doctrine_Query::create()
					->from('Stores')
					->orderBy('stores_name');

				if ($appExtension->isAdmin() === true && $nofilter === false){
					$Qstores->whereIn('stores_id', Session::get('admin_allowed_stores'));
				}

				$this->storesArray = $Qstores->execute();
			}
		}
		return $this->storesArray;
	}

	public function AdminHeaderRightAddContent(){
		$Result = $this->getStoresArray();
		if ($Result){
			$form = htmlBase::newElement('form')
			->attr('name', 'storeSelector')
			->attr('method', 'get')
			->attr('action', itw_app_link(tep_get_all_get_params(array('action', 'stores_id'))));

			$selectBox = htmlBase::newElement('selectbox')
				->setName('stores_id[]')
				->attr('id', 'storeSelect')
				->attr('multiple', 'multiple')/*
			->attr('onchange', 'this.form.submit()')*/
			;

			$selectBox->addOption('all', 'All Allowed Stores', false);

			foreach($Result as $sInfo){
				if(in_array($sInfo['stores_id'], Session::get('admin_allowed_stores'))) {
                    $selectBox->addOption(
                        $sInfo['stores_id'],
                        $sInfo['stores_name'],
                        (in_array($sInfo['stores_id'], Session::get('admin_showing_stores')) ? true : false)
                    );
                }
			}
			$form->append($selectBox);
			$form->append(htmlBase::newElement('button')->setText('GO')->setType('submit'));
			return '<span style="vertical-align:middle;">Showing Store(s): </span>' . $form->draw();
		}
		return '';
	}
	
	public function SetTemplateName(){
		Session::set('tplDir', $this->storeInfo['stores_template']);
	}
	
	public function BoxConfigurationAddLink(&$contents){
		$contents['children'][] = array(
			'link' => false,
			'text' => $this->getExtensionName(),
			'children' => array(
				array(
					'link'       => itw_app_link('appExt=multiStore','manage','default','SSL'),
					'text'       => 'Setup Stores'
				)
			)
		);
	}
	
	public function CategoryQueryBeforeExecute(&$categoryQuery){
		$categoryQuery->leftJoin('c.CategoriesToStores c2s')
		->andWhere('c2s.stores_id = ?', $this->storeInfo['stores_id']);
	}
	
	public function CustomerQueryBeforeExecute(&$customerQuery){
		$customerQuery->leftJoin('c.CustomersToStores c2s')
		->andWhere('c2s.stores_id = '. $this->storeInfo['stores_id'].' OR c2s.stores_id is null');
	}
	
	public function OrderQueryBeforeExecute(&$orderQuery){
		global $appExtension;
		if ($appExtension->isAdmin()){
			$orderQuery->leftJoin('o.OrdersToStores o2s');
		}else{
			$orderQuery->leftJoin('o.OrdersToStores o2s');
			//->andWhere('o2s.stores_id = ?', $this->storeInfo['stores_id']);
		}
	}
	
	public function ProductQueryBeforeExecute(&$productQuery){
		$productQuery->leftJoin('p.ProductsToStores p2s')
		->andWhere('p2s.stores_id = ?', $this->storeInfo['stores_id']);
	}
	
	public function ProductListingQueryBeforeExecute(&$productQuery){
		$productQuery->leftJoin('p.ProductsToStores p2s')
		->andWhere('p2s.stores_id = ?', $this->storeInfo['stores_id']);
	}
	
	public function FeaturedQueryBeforeExecute(&$productQuery){
		$productQuery->leftJoin('p.ProductsToStores p2s')
		->andWhere('p2s.stores_id = ?', $this->storeInfo['stores_id']);
	}

	public function ScrollerFeaturedQueryBeforeExecute(&$productQuery){
		$productQuery->leftJoin('p.ProductsToStores p2s')
		->andWhere('p2s.stores_id = ?', $this->storeInfo['stores_id']);
	}
	
	public function SpecialQueryBeforeExecute(&$productQuery){
		$productQuery->leftJoin('p.SpecialsToStores s2s')
		->andWhere('s2s.stores_id = ?', $this->storeInfo['stores_id']);
	}
	
	public function CheckoutProcessPostProcess(&$order){
		$OrdersToStores = new OrdersToStores();
		$OrdersToStores->orders_id = $order->newOrder['orderID'];
		$OrdersToStores->stores_id = $this->getStoreId();
		$OrdersToStores->save();
	}

	public function OrdersListingAddGridHeader(&$gridHeaders){
		$gridHeaders[] = array(
			'text' => 'Store Name'
		);
	}
	
	public function OrdersListingAddGridBody(&$order, &$gridBody){
		$gridBody[] = array(
			'text'   => (isset($order['OrdersToStores']) ? $order['OrdersToStores']['Stores']['stores_name'] : 'N/A'),
			'align'  => 'center'
		);
	}
	
	public function OrderSingleLoad(&$orderClass, $Order){
		$orderClass->info['store_id'] = $Order['OrdersToStores']['stores_id'];
	}
	
	public function getStoreId(){
		return $this->storeInfo['stores_id'];
	}

	public function getStoreName(){
		return $this->storeInfo['stores_name'];
	}

	public function getStoreEmail(){
		return $this->storeInfo['stores_email'];
	}

	public function getStoreDomain(){
		return $this->storeInfo['stores_domain'];
	}

	public function getStoreSslDomain(){
		return $this->storeInfo['stores_ssl_domain'];
	}

	public function getStoreTemplate(){
		return $this->storeInfo['stores_template'];
	}

	public function ProductInventoryReportsListingQueryBeforeExecute(&$Qproducts){
		global $appExtension;
		$isInventory = $appExtension->isInstalled('inventoryCenters') && $appExtension->isEnabled('inventoryCenters');
		$extInventoryCenters = $appExtension->getExtension('inventoryCenters');
		if ($isInventory && $extInventoryCenters->stockMethod == 'Store'){
			if (!Session::exists('admin_showing_stores')){
				$Qproducts->leftJoin('pib.ProductsInventoryBarcodesToStores b2s')
					->leftJoin('b2s.Stores s')
					->andWhereIn('s.stores_id', Session::get('admin_showing_stores'));
			}
		}elseif (Session::exists('admin_showing_stores')){
				$Qproducts->leftJoin('pib.ProductsInventoryBarcodesToStores b2s')
					->leftJoin('b2s.Stores s')
					->andWhereIn('s.stores_id', Session::get('admin_showing_stores'));
		}


	}
	
	public function CustomersListingQueryBeforeExecute(&$Qcustomers){
		$Qcustomers
			->leftJoin('c.CustomersToStores c2s')
			->andwhere('FIND_IN_SET(stores_id,"'. implode(',',Session::get('admin_showing_stores')).'") > 0 OR stores_id is null');
	}
	
	public function MetaTagsAdminEditAddTabContents(&$layout){
		$Result = $this->getStoresArray();
		if ($Result){
			$selectBox = htmlBase::newElement('selectbox')
				->setName('meta_stores_id')
				->attr('jslink', itw_app_link(tep_get_all_get_params(array('action', 'meta_stores_id'))));

			if (isset($_GET['meta_stores_id'])){
				$selectBox->selectOptionByValue($_GET['meta_stores_id']);
			}

			$selectBox->addOption('all', 'All Stores');
			foreach($Result as $sInfo){
				$selectBox->addOption($sInfo['stores_id'], $sInfo['stores_name']);
			}
			$layout = '<br><table cellpadding="4" cellspacing="4"><tr><td>Store:</td><td>' . $selectBox->draw() . '</td></tr></table><hr>' . $layout;
		}
	}
	
	public function ModuleConfigReaderModuleConfigLoad($cfgKey, $moduleCode, $moduleType, &$configVal) {

		$Query = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select ' .
				'configuration_value' .
				' from ' .
				'stores_modules_configuration' .
				' where ' .
				'module_code = "' . $moduleCode . '"' .
				' and ' .
				'module_type = "' . $moduleType . '"' .
				' and ' .
				'configuration_key = "' . $cfgKey . '"' .
				' and ' .
				'store_id = "' . $this->storeInfo['stores_id'] . '"');
		if (sizeof($Query) > 0){
			foreach($Query as $Result){
				$configVal = $Result['configuration_value'];
			}
		}
	}

	public function MetaTagsCheckStores(){
		global $langs;
		$Result = $this->getStoresArray();
		if ($Result){
			foreach($Result as $sInfo){
				foreach ($langs as $langid => $val) {
					$Metatags = Doctrine::getTable('MetaTags')->findByStoresIdAndLanguageId($sInfo['stores_id'], $langid);
					if(!$Metatags){

						$Metatags = Doctrine::getTable('MetaTags');
						$Metatags = $Metatags->create();
						$Metatags->language_id	= intVal($langid);
						$Metatags->stores_id	= intVal($sInfo['stores_id']);
						$Metatags->save();
					}
				}
			}
		}
	}
	
	public function ProductInventoryBarcodeGetInventoryItemsQueryBeforeExecute($invData, &$Qcheck){
		global $Editor, $appExtension;
		$Qcheck->leftJoin('ib.ProductsInventoryBarcodesToStores ib2s')
			->leftJoin('ib2s.Stores');
		if ($appExtension->isAdmin()){
			if (is_object($Editor)){
				$Qcheck->andWhere('ib2s.inventory_store_id = ?', $Editor->getData('store_id'));
			}else{
				$Qcheck->andWhereIn('ib2s.inventory_store_id', Session::get('admin_showing_stores'));
			}
		}else{
			$isInventory = $appExtension->isInstalled('inventoryCenters') && $appExtension->isEnabled('inventoryCenters');
			$extInventoryCenters = $appExtension->getExtension('inventoryCenters');
			if ($isInventory && $extInventoryCenters->stockMethod == 'Store'){
				$Qcheck->andWhere('ib2s.inventory_store_id = ?', Session::get('current_store_id'));
			}
		}

	}

	public function MetaTagsAdminSaveQueryBeforeExecute(&$Metatags){
		$Metatags->stores_id = (($_POST['meta_stores_id'] == 'all') ? 0 : intVal($_POST['meta_stores_id']));
	}
	
	public function ProductInventoryBarcodeHasInventoryQueryBeforeExecute($invData, &$Qcheck){
		global $appExtension, $Editor;
		if ($invData['use_store_inventory'] == '1'){
			$Qcheck->leftJoin('ib.ProductsInventoryBarcodesToStores b2s')
				->leftJoin('b2s.Stores');
			if ($appExtension->isAdmin()){
				if (is_object($Editor)){
					$Qcheck->andWhere('b2s.inventory_store_id = ?', $Editor->getData('store_id'));
				}else{
					$Qcheck->andWhereIn('b2s.inventory_store_id', Session::get('admin_showing_stores'));
				}
			}else{
				$Qcheck->andWhere('b2s.inventory_store_id = ?', Session::get('current_store_id'));
			}
		}
	}


	public function MetaTagsFetchPageQueryBeforeExecute(&$MetatagsQuery){
		global $appExtension;
		if($appExtension->isAdmin()){
			$this->MetaTagsCheckStores();
			$MetatagsQuery->andWhere('stores_id = ?', (($_GET['meta_stores_id'] == 'all') ? 0 : intVal($_GET['meta_stores_id'])));
		} else {
			$MetatagsQuery->andWhere('stores_id = ?', ((Session::get('current_store_id') == 'all') ? 0 : intVal(Session::get('current_store_id'))));
		}
	}

	public function ProductInventoryBarcodeGetInventoryItemsArrayPopulate($bInfo, &$barcodeArr){
		$barcodeArr['store_id'] = $bInfo['ProductsInventoryBarcodesToStores']['inventory_store_id'];
	}
	public function CustomerInfoAddTableContainer($Customer){
		$storeDrop = htmlBase::newElement('selectbox')
			->setName('customers_store_id')
			->selectOptionByValue($Customer->CustomersToStores->stores_id);
		foreach($this->getStoresArray() as $sInfo){
			$storeDrop->addOption($sInfo['stores_id'], $sInfo['stores_name']);
		}

		return '<div class="main" style="margin-top:.5em;font-weight:bold;">Customers Store</div><div class="ui-widget ui-widget-content ui-corner-all" style="padding:.5em;">'.$storeDrop->draw().'</div>';
	}
	
	public function OrdersProductsReservationListingBeforeExecuteUtilities(&$Qorders){
		global $Editor, $appExtension;
		$Qorders->leftJoin('ib.ProductsInventoryBarcodesToStores b2s')
			->leftJoin('b2s.Stores');
		if ($appExtension->isAdmin()){
			if (is_object($Editor)){
				$Qorders->andWhere('b2s.inventory_store_id = ?', $Editor->getData('store_id'));
			}else{
				$Qorders->andWhereIn('b2s.inventory_store_id', Session::get('admin_showing_stores'));
			}
		}else{
			$Qorders->andWhere('b2s.inventory_store_id = ?', Session::get('current_store_id'));
		}

	}
	
	public function NewCustomerAccountBeforeExecute(&$customerId){

        $Qexiting = Doctrine::getTable('CustomersToStores')->findOneByCustomersId($customerId);
        if ($Qexiting){
            $Qexiting->delete();
        }
        $CustomerToStore = new CustomersToStores();
        $CustomerToStore->customers_id = $customerId;
        $CustomerToStore->stores_id = $_POST['customers_store_id'];
        $CustomerToStore->save();
	}

	public function ProductQueryAfterExecute(&$productInfo){
		$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from stores_pricing where products_id = "' . $productInfo['products_id'] .'" and stores_id = "'.Session::get('current_store_id').'"');
		if(isset($ResultSet[0])){
			$productInfo['products_price'] = $ResultSet[0]['products_price'];
			$productInfo['products_price_used']= $ResultSet[0]['products_price_used'];
			$productInfo['products_price_stream']= $ResultSet[0]['products_price_stream'];
			$productInfo['products_price_download']= $ResultSet[0]['products_price_download'];
			$productInfo['typeArr'] = explode(',', $ResultSet[0]['products_type']);
			$productInfo['allow_overbooking'] = explode(',', $ResultSet[0]['allow_overbooking']);
		}
	}

	public function MetaTagsFetchDefaultsQueryBeforeExecute(&$MetatagsQuery){
		global $appExtension;
		if($appExtension->isAdmin()){
			$MetatagsQuery->andWhere('stores_id = ?', (($_GET['meta_stores_id'] == 'all') ? 0 : intVal($_GET['meta_stores_id'])));
		} else {
			$MetatagsQuery->andWhere('stores_id = ?', ((Session::get('current_store_id') == 'all') ? 0 : intVal(Session::get('current_store_id'))));
		}
	}

	public function BoxMarketingAddLink(&$contents){
		if (sysPermissions::adminAccessAllowed('statistics', 'salesReport') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'statistics', 'salesReport', 'SSL'),
				'text' => 'Sales Report'
			);
		}
	}

	public function CheckoutAddNewCustomer($customerId){
		$CustomerToStore = new CustomersToStores();
		$CustomerToStore->customers_id = $customerId;
		$CustomerToStore->stores_id = Session::get('current_store_id');
		$CustomerToStore->save();
	}

	protected function _calculateDistance($point1, $point2) {
	    $radius      = 3958;      // Earth's radius (miles)
	    $pi          = 3.1415926;
	    $deg_per_rad = 57.29578;  // Number of degrees/radian (for conversion)

	    $distance = ($radius * $pi * sqrt(
			($point1['lat'] - $point2['lat'])
			* ($point1['lat'] - $point2['lat'])
			+ cos($point1['lat'] / $deg_per_rad)  // Convert these to
			* cos($point2['lat'] / $deg_per_rad)  // radians for cos()
			* ($point1['long'] - $point2['long'])
			* ($point1['long'] - $point2['long'])
		) / 180);

	    return $distance;  // Returned using the units used for $radius.	    
	}
	
	public function getClosestStoreByZip($zip) {
	    if (empty($zip)) {
		return null;
	    }
	    
	    //Find location by zip
	    $location = Doctrine::getTable('Zips')->findOneByZip($zip);
	    if (empty($location)) {
		return null;
	    }
	    $from_point = array('lat'=>$location->latitude, 'long'=>$location->longitude);
	    
	    
	    //Calculate distance from each store to the given zip and find store with minimal distance
	    $min_distance = null;
	    $closest_store = null;
	    $stores = Doctrine::getTable('Stores')->findAll();
	    
	    foreach ($stores as $current_store) {
		if (!empty($current_store->stores_zip)) {
		    $store_location = Doctrine::getTable('Zips')->findOneByZip($current_store->stores_zip);
		    if (!empty($store_location)) {
			$to_point = array('lat'=>$store_location->latitude, 'long'=>$store_location->longitude);
			$distance = $this->_calculateDistance($from_point, $to_point);
		
			
			if ($min_distance===null || $distance<$min_distance) {
			    $min_distance = $distance;
			    $closest_store = $current_store;
			}
		    }
		}	
	    }
	    
	    return $closest_store;
	}
}
?>