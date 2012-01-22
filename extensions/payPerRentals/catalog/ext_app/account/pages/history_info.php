<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class payPerRentals_catalog_account_history_info extends Extension_payPerRentals {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
			'OrderInfoProductTableHeading',
			'OrderInfoProductTableBody'
		), null, $this);
	}

	public function  OrderInfoProductTableHeading($oID){
		echo '<td class="smallText" align="right"><b>'. sysLanguage::get('HEADING_TRACKING_NUMBER') .'</b></td>';
	}

	public function  OrderInfoProductTableBody($OrderProduct, $trackingCompanies){
		/*Here there is a problem with reservations created from Inventory Report*/
		if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_TRACKING_HISTORY_INFO') == 'True'){
			$Qreservations = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsReservation opr')
			->where('opr.orders_products_id=?', $OrderProduct->getIdString())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			foreach($Qreservations as $iReservation){
				echo '            <td class="main" align="right" valign="top"><a href="' . (isset($trackingCompanies[strtolower($iReservation['OrdersProducts'][0]['OrdersProductsReservation'][0]['tracking_type'])]['url'])?($trackingCompanies[strtolower($iReservation['OrdersProducts'][0]['OrdersProductsReservation'][0]['tracking_type'])]['url'] . $iReservation['OrdersProducts'][0]['OrdersProductsReservation'][0]['tracking_number']):'#')  . '">'.(isset($trackingCompanies[strtolower($iReservation['OrdersProducts'][0]['OrdersProductsReservation'][0]['tracking_type'])]['url'])?'Track Order':'Not Shipped').'</a></td>' . "\n";
				break;
			}
			if(count($Qreservations) <= 0){
				echo '            <td class="main" align="right" valign="top">'  . '</td>' . "\n";
			}
		}
	}
	

}
?>