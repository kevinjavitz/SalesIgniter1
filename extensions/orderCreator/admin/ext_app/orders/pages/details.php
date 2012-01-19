<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class orderCreator_admin_orders_details extends Extension_orderCreator {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'OrderInfoAddBlock'
		), null, $this);
	}

	public function OrderInfoAddBlock($orderId){
		$adminData = '';

		$QOrders = Doctrine_Query::create()
		->from('Orders')
		->where('orders_id = ?', $orderId)
		->fetchOne();

		$QAdmin = Doctrine_Query::create()
		->from('Admin')
		->where('admin_id = ?', $QOrders->admin_id)
		->fetchOne();


		if($QAdmin){
			$adminData = sysLanguage::get('ADMIN_NAME'). $QAdmin->admin_firstname. ' '.$QAdmin->admin_lastname;


			return
				'<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' .
				'<table cellpadding="3" cellspacing="0">' .
				'<tr>' .
				'<td>'.$adminData.'</td>' .
				'</tr>' .
				'</table>';
		}else{
			return '';
		}

	}

}
?>