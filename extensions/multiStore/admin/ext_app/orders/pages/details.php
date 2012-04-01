<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class multiStore_admin_orders_details extends Extension_multiStore {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'OrderInfoAddBlock'
		), null, $this);
	}

	public function OrderInfoAddBlock($orderId){
		$storeData = '';

		$QOrdersStore = Doctrine_Query::create()
		->from('Stores s')
		->leftJoin('s.OrdersToStores os')
		->where('os.orders_id = ?', $orderId)
		->fetchOne();

		if($QOrdersStore){
			$storeData = sysLanguage::get('STORE_NAME'). $QOrdersStore->stores_name;
		}

		return
				'<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' .
				'<table cellpadding="3" cellspacing="0">' .
				'<tr>' .
				'<td>'.$storeData.'</td>' .
				'</tr>' .
				'</table>';
	}

}
?>