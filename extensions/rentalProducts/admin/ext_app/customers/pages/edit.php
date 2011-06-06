<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class rentalProducts_admin_customers_edit extends Extension_rentalProducts {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvent('AdminCustomerEditBuildTabs', null, $this);
	}
	
	public function AdminCustomerEditBuildTabs($Customer, &$tabsObj){
		global $currencies, $cID;
		$pageTabsDir = sysConfig::getDirFsCatalog() . 'extensions/rentalProducts/admin/ext_app/customers/page_tabs/';
		ob_start();
		include($pageTabsDir . 'history.php');
		$tab1 = ob_get_contents();
		ob_end_clean();
	
		ob_start();
		include($pageTabsDir . 'pending.php');
		$tab2 = ob_get_contents();
		ob_end_clean();
	
		ob_start();
		include($pageTabsDir . 'out.php');
		$tab3 = ob_get_contents();
		ob_end_clean();

		$tabsObj->addTabHeader('rentalProductsTab1', array('text' => sysLanguage::get('TAB_RENTAL_PRODUCTS_HISTORY')))
		->addTabPage('rentalProductsTab1', array('text' => $tab1))
		->addTabHeader('rentalProductsTab2', array('text' => sysLanguage::get('TAB_RENTAL_PRODUCTS_PENDING')))
		->addTabPage('rentalProductsTab2', array('text' => $tab2))
		->addTabHeader('rentalProductsTab3', array('text' => sysLanguage::get('TAB_RENTAL_PRODUCTS_OUT')))
		->addTabPage('rentalProductsTab3', array('text' => $tab3));
	}
}
?>