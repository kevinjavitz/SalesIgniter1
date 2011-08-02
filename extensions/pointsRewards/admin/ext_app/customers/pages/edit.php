<?php
/*
	Pay Per Rentals Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class pointsRewards_admin_customers_edit extends Extension_pointsRewards {

	public function __construct(){
		parent::__construct();
	}

	public function load(){
		if ($this->enabled === false) return;

		EventManager::attachEvent('AdminCustomerEditBuildTabs', null, $this);
	}

	public function AdminCustomerEditBuildTabs($Customer, &$tabsObj){
		global $currencies, $cID;
		$pageTabsDir = sysConfig::getDirFsCatalog() . 'extensions/pointsRewards/admin/ext_app/customers/page_tabs/';
		
		ob_start();
		include($pageTabsDir . 'history.php');
		$tab = ob_get_contents();
		ob_end_clean();


		$tabsObj->addTabHeader('pointsRewardsTab', array('text' => sysLanguage::get('TAB_POINTS_REWARDS_HISTORY')))
		->addTabPage('pointsRewardsTab', array('text' => $tab))		;
	}
}
?>