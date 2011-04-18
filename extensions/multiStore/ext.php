<?php
/*
	Multi Stores Extension Version 1.1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_multiStore extends ExtensionBase {

	public function __construct(){
		parent::__construct('multiStore');
	}

	public function init(){
		global $App, $appExtension, $Template;
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'EmailEventSetAllowedVars',
			'OrderQueryBeforeExecute',
			'OrderSingleLoad'
		), null, $this);
		
		if ($appExtension->isCatalog()){
			EventManager::attachEvents(array(
				'CategoryQueryBeforeExecute',
				'CustomerQueryBeforeExecute',
				'ProductQueryBeforeExecute',
				'ProductListingQueryBeforeExecute',
				'SpecialQueryBeforeExecute',
				'FeaturedQueryBeforeExecute',
				'CheckoutProcessPostProcess',
				'SetTemplateName',
				'SeoUrlsInit',
				'ReviewsQueryBeforeExecute'
			), null, $this);
		}
		
		if ($appExtension->isAdmin()){
			if(Session::exists('current_store_id')){
				$this->adminStoreId = Session::get('current_store_id');
			}
			if (isset($_GET['stores_id']) && is_numeric($_GET['stores_id']) && $_GET['stores_id'] > 0){
				$this->adminStoreId = $_GET['stores_id'];
				Session::set('current_store_id', $_GET['stores_id']);
				Session::remove('all_stores');
			}else{
				Session::set('current_store_id', '1');
				Session::set('all_stores','true');
			}
			EventManager::attachEvents(array(
				'BoxConfigurationAddLink',
				'AdminHeaderRightAddContent',
				'ProductInventoryReportsListingQueryBeforeExecute'
			), null, $this);
			
			if ($App->getAppName() == 'orders'){
				EventManager::attachEvents(array(
					'AdminOrdersListingBeforeExecute',
					'OrdersListingAddGridHeader',
					'OrdersListingAddGridBody'
				), null, $this);
			}
			
			if ($App->getAppName() == 'products' && isset($this->adminStoreId)){
				EventManager::attachEvents(array(
					'AdminProductListingQueryBeforeExecute'
				), null, $this);
			}
			
			if ($App->getAppName() == 'categories'){
				if (isset($this->adminStoreId)){
					EventManager::attachEvents(array(
						'CategoryListingQueryBeforeExecute'
					), null, $this);
				}
			}
		}
		
		$this->loadStoreInfo();
	}
	
	public function loadStoreInfo(){
		global $App;
		if (isset($this->adminStoreId)){
			$Qstore = Doctrine_Query::create()
			->from('Stores')
			->where('stores_id = ?', $this->adminStoreId)
			->execute(array(), Doctrine::HYDRATE_ARRAY);
			$this->storeInfo = $Qstore[0];
		}elseif (getenv('HTTPS') == 'on' && Session::exists('current_store_id')){
			$Qstore = Doctrine_Query::create()
			->from('Stores')
			->where('stores_id = ?', Session::get('current_store_id'))
			->execute(array(), Doctrine::HYDRATE_ARRAY);
			$this->storeInfo = $Qstore[0];
		}else{
			$domainCheck = array($_SERVER['HTTP_HOST']);
			if (substr($_SERVER['HTTP_HOST'], 0, 4) != 'www.'){
				$domainCheck[] = 'www.' . $_SERVER['HTTP_HOST'];
			}else{
				$domainCheck[] = substr($_SERVER['HTTP_HOST'], 4);
			}
		
			if (getenv('HTTPS') == 'on'){
				$Qstore = Doctrine_Query::create()
				->from('Stores')
				->whereIn('stores_ssl_domain', $domainCheck)
				->execute(array(), Doctrine::HYDRATE_ARRAY);
			}else{
				$Qstore = Doctrine_Query::create()
				->from('Stores')
				->whereIn('stores_domain', $domainCheck)
				->execute(array(), Doctrine::HYDRATE_ARRAY);
			}
			$this->storeInfo = $Qstore[0];
		}
		
		if (getenv('HTTPS') != 'on'){
			Session::set('current_store_id', $this->storeInfo['stores_id']);
		}
		if(Session::exists('current_store_id')){
			$Qconfig = Doctrine_Query::create()
			->select('configuration_key, configuration_value')
			->from('StoresConfiguration')
			->where('stores_id = ?', Session::get('current_store_id'))
			->execute();
		}
		if (isset($Qconfig) && $Qconfig->count() > 0){
			foreach($Qconfig->toArray() as $cInfo){
				sysConfig::set($cInfo['configuration_key'], $cInfo['configuration_value']);
			}
		}
		
		define('HEAD_TITLE_TAG_DEFAULT', $this->storeInfo['stores_name']);
		if ($App->getEnv() == 'catalog'){
			sysConfig::set('HTTP_SERVER', 'http://' . $this->storeInfo['stores_domain']);
			sysConfig::set('HTTPS_SERVER', 'https://' . $this->storeInfo['stores_ssl_domain']);
		}
		sysConfig::set('HTTP_COOKIE_DOMAIN', $this->storeInfo['stores_domain']);
		sysConfig::set('HTTPS_COOKIE_DOMAIN', $this->storeInfo['stores_ssl_domain']);
	}

/* Auto Upgrade ( Version 1.0 to 1.1 ) --BEGIN-- */
	public function EmailEventSetAllowedVars(&$allowedVars){
		$allowedVars['store_name'] = $this->storeInfo['stores_name'];
		$allowedVars['store_owner'] = $this->storeInfo['stores_owner'];
		$allowedVars['store_owner_email'] = $this->storeInfo['stores_email'];
		$allowedVars['store_url'] = 'http://' . $this->storeInfo['stores_domain'];
	}
/* Auto Upgrade ( Version 1.0 to 1.1 ) --END-- */

	public function ReviewsQueryBeforeExecute(&$Qreviews){
		$Qreviews->leftJoin('p.ProductsToStores p2s')
		->andWhere('p2s.stores_id = ?', Session::get('current_store_id'));
	}
	
	public function AdminProductListingQueryBeforeExecute(&$Qproducts){
		$Qproducts->leftJoin('p.ProductsToStores p2s')
		->andWhere('p2s.stores_id = ?', $this->adminStoreId);
	}
	
	public function CategoryListingQueryBeforeExecute(&$Qcategories){
		$Qcategories->leftJoin('c.CategoriesToStores c2s')
		->andWhere('c2s.stores_id = ?', $this->adminStoreId);
	}
	
	public function AdminOrdersListingBeforeExecute(&$Qorders){
		$Qorders->leftJoin('o.OrdersToStores order2store')
		->leftJoin('order2store.Stores store')
		->addSelect('store.stores_name, order2store.stores_id');
		if (isset($this->adminStoreId)){
			$Qorders->andWhere('order2store.stores_id = ?', $this->adminStoreId);
		}
	}
	
	public function SeoUrlsInit(&$seoUrl){
		$seoUrl->base_url = 'http://' . $this->storeInfo['stores_domain'] . sysConfig::getDirWsCatalog('NONSSL');
		$seoUrl->base_url_ssl = 'https://' . $this->storeInfo['stores_ssl_domain'] . sysConfig::getDirWsCatalog('SSL');
	}
	
	public function getStoresArray(){
		$Qstores = Doctrine_Query::create()
		->select('stores_id, stores_name')
		->from('Stores')
		->orderBy('stores_name');
		
		$Result = $Qstores->execute(array(), Doctrine::HYDRATE_ARRAY);
		return $Result;
	}
	
	public function AdminHeaderRightAddContent(){
		$Result = $this->getStoresArray();
		if ($Result){
			$form = htmlBase::newElement('form')
			->attr('name', 'storeSelector')
			->attr('method', 'get')
			->attr('action', itw_app_link(tep_get_all_get_params(array('action', 'stores_id'))));

			/*
			$params = array();
			$stuff = parse_str(tep_get_all_get_params(array('action', 'app', 'appPage', 'stores_id')), &$params);
			if (!empty($params)){
				foreach($params as $k => $v){
					$form->append(htmlBase::newElement('input')->setType('hidden')->setName($k)->val($v));
				}
			}
			*/
			
			$selectBox = htmlBase::newElement('selectbox')
			->setName('stores_id')
			->attr('onchange', 'this.form.submit()');
			
			if (isset($this->adminStoreId)){
				$selectBox->selectOptionByValue($this->adminStoreId);
			}
			
			$selectBox->addOption('all', 'All Stores');
			foreach($Result as $sInfo){
				$selectBox->addOption($sInfo['stores_id'], $sInfo['stores_name']);
			}
			$form->append($selectBox);
			return 'Store: ' . $form->draw();
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
		->andWhere('c2s.stores_id = ?', $this->storeInfo['stores_id']);
	}
	
	public function OrderQueryBeforeExecute(&$orderQuery){
		global $appExtension;
		if ($appExtension->isAdmin()){
			$orderQuery->leftJoin('o.OrdersToStores o2s');
		}else{
			$orderQuery->leftJoin('o.OrdersToStores o2s')
			->andWhere('o2s.stores_id = ?', $this->storeInfo['stores_id']);
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
		if($isInventory && $extInventoryCenters->stockMethod == 'Store' ){
			if(!Session::exists('all_stores')){
				$Qproducts->leftJoin('pib.ProductsInventoryBarcodesToStores b2s')
					  ->leftJoin('b2s.Stores s')
					  ->andWhere('s.stores_id = ?', (int)Session::get('current_store_id'));
			}
		}
	}
}
?>