<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of new
 *
 * @author Stephen
 */
class payPerRentals_admin_orderCreator_estimates_new extends Extension_payPerRentals {

	public function __construct(){
		parent::__construct();
	}

	public function load(){
		global $App;
		if ($this->isEnabled() === false) return;

		EventManager::attachEvents(array(
			'OrderCreatorAddProductToEmail'
		), null, $this);

		$App->addJavascriptFile('ext/jQuery/external/datepick/jquery.datepick.js');
		$App->addStylesheetFile('ext/jQuery/external/datepick/css/jquery.datepick.css');

		$App->addJavascriptFile('ext/jQuery/external/fullcalendar/fullcalendar.min.js');
		$App->addStylesheetFile('ext/jQuery/external/fullcalendar/fullcalendar.css');
		$App->addStylesheetFile('ext/jQuery/external/fullcalendar/extra.css');
	}

	public function OrderCreatorAddProductToEmail($opInfo, &$products_ordered){
		global $currencies;
		if (isset($opInfo->OrdersProductsReservation)){
			$lastOrderId = -1;
			foreach($opInfo->OrdersProductsReservation as $rInfo){
				if($rInfo->orders_products_id != $lastOrderId){
					if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'False'){
						$products_ordered .= 'Reservation Info' .
							"\n\t" . 'Start Date: ' . strftime(sysLanguage::getDateFormat('long'), strtotime($rInfo->start_date)) .
							"\n\t" . 'End Date: ' . strftime(sysLanguage::getDateFormat('long'), strtotime($rInfo->end_date))
							;
					}else{
						$products_ordered .= 'Reservation Info' .
							"\n\t" . 'Event Date: ' . strftime(sysLanguage::getDateFormat('long'), strtotime($rInfo->event_date)) .
							"\n\t" . 'Event Name: ' . $rInfo->event_name
							;
						if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
							$products_ordered .= "\n\t" . 'Event Gate: ' . $rInfo->event_gate;
						}
					}

					if (!empty($rInfo->shipping_method_title)){
						$products_ordered .= "\n\t" . 'Shipping Method: ' . $rInfo->shipping_method_title . ' (' . $currencies->format($rInfo->shipping_cost) . ')';
					}
					$products_ordered .= "\n\t" . 'Insurance: ' . $currencies->format($rInfo->insurance);
					$products_ordered .= "\n";
					$lastOrderId = $rInfo->orders_products_id;
				}
			}
		}
	}
}
?>