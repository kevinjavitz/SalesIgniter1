<?php
/*
	Pay Per Rentals Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class giftCertificates_admin_customers_edit extends Extension_giftCertificates {

	public function __construct(){
		parent::__construct();
	}

	public function load(){
		if ($this->isEnabled() === false) return;

		EventManager::attachEvent('AdminCustomerEditBuildTabs', null, $this);
	}

	public function AdminCustomerEditBuildTabs($Customer, &$tabsObj){
		global $currencies, $cID, $typeNames;
        $purchaseTypeNames = $typeNames;
        $purchaseTypeNames['global'] = 'All Purchase Types';
		$pageTabsDir = sysConfig::getDirFsCatalog() . 'extensions/giftCertificates/admin/ext_app/customers/page_tabs/';
		
		ob_start();
		include($pageTabsDir . 'history.php');
		$tab = ob_get_contents();
		ob_end_clean();


		$tabsObj->addTabHeader('giftCertificatesTab', array('text' => sysLanguage::get('TAB_GIFT_CERTIFICATES_TRANSACTIONS_HISTORY')))
		->addTabPage('giftCertificatesTab', array('text' => $tab))		;
	}
}
?>