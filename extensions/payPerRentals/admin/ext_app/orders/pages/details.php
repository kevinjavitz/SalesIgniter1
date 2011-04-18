<?php
/*
	Pay Per Rentals Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class payPerRentals_admin_orders_details extends Extension_payPerRentals {

	public function __construct(){
		parent::__construct();
	}

	public function load(){
		global $appExtension;
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
			'OrderDetailsTabPaneInsideComments',

		), null, $this);
	}

	public function OrderDetailsTabPaneInsideComments(&$orderInfo, &$tabContent){
		$tabContent .= sysLanguage::get('TEXT_RENTAL_NOTES').'<br/>'.tep_draw_textarea_field('rental_notes', 'soft', '60', '5', $orderInfo['rental_notes']);
	}


}
?>