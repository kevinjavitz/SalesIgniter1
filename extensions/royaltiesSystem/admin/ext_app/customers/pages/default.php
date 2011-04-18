<?php
/*
	Royalties System Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/



class royaltiesSystem_admin_customers_default extends Extension_royaltiesSystem {

	public function __construct(){
		parent::__construct();
	}

	public function load(){
		if ($this->enabled === false) return;
		EventManager::attachEvents(array(
			'AdminCustomerListingAddHeader',
			'AdminCustomerListingAddBody'
		), null, $this);
	}

	public function AdminCustomerListingAddHeader(&$tableGridHeader){
		$tableGridHeader[] = array(
			'text' => 'Content Provider'
		);
	}
	
	public function AdminCustomerListingAddBody($Customer, &$tableGridBody){
		if ($Customer['is_content_provider'] == 1){
			$iconType = 'circleCheck';
			$setFlag = '0';
		}else{
			$iconType = 'circleClose';
			$setFlag = '1';
		}
		$allGetParms = tep_get_all_get_params(array('app', 'appPage', 'action', 'flag', 'cID'));
		
		$icon = htmlBase::newElement('icon')
		->setType($iconType)
		->setHref(itw_app_link($allGetParms . 'action=setProviderFlag&flag=' . $setFlag . '&cID=' . $Customer['customers_id'], 'customers', 'default'));
		
		$tableGridBody[] = array(
			'align' => 'center',
			'text' => $icon->draw()
		);
	}
}


?>