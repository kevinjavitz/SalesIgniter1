<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class upsLabels_catalog_account_history_info extends Extension_upsLabels {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
			'AccountHistoryAfterTracking'
		), null, $this);
	}



	public function  AccountHistoryAfterTracking($order_id){
		$QOrders = Doctrine_Query::create()
			->from('Orders o')
			->andWhere('o.orders_id = ?', $order_id)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		echo '<br/><b><a target="_blank" href="'.itw_app_link('action=showReturnLabel&track='.$QOrders[0]['ups_track_num2'],'account','default').'">Print UPS return label</a></b>';
	}
	

}
?>