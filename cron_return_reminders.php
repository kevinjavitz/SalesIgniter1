<?php
 require('includes/application_top.php');
$pprExt = $appExtension->getExtension('payPerRentals');
	if ($pprExt !== false && $pprExt->isEnabled() === true){
		if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_ENABLE_REMINDERS') == 'True'){
			$day_before_due = (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_REMINDERS_DAYS');
			$date_before_due = date('Y-m-d H:i:s', strtotime('+ '.$day_before_due . ' days', strtotime(date('Y-m-d'))));
			$ReservationQuery = Doctrine_Query::create()
					->from('Orders o')
					->leftJoin('o.Customers c')
					->leftJoin('o.OrdersAddresses oa')
					->leftJoin('o.OrdersProducts op')
					->leftJoin('op.OrdersProductsReservation opr')
					//->where('opr.orders_products_reservations_id = ?', $bID)
					->where('opr.end_date <= ?', $date_before_due)
					->andWhere('opr.end_date >= ?', date('Y-m-d H:i:s'))
					->andWhere('opr.rental_state = ?', 'out')
					->andWhere('oa.address_type = ?', 'customer')
					->orderBy('c.customers_id')
					->andWhere('parent_id IS NULL');
			        //todo email for late not returned items--auto_send_return_rentals does that
					$Reservation = $ReservationQuery->execute();
					$rented_list = array();
					foreach($Reservation as $oInfo){
						$list_per_order = array();
						foreach($oInfo->OrdersProducts as $opInfo){
							foreach($opInfo->OrdersProductsReservation as $oprInfo){
								$list_per_order[] = 'Product name: '.$opInfo['products_name'] .' due on:'. strftime(sysLanguage::getDateFormat('long'),strtotime($oprInfo['end_date']));
							}
						}
						if(isset($rented_list[$oInfo['customers_id']]['list'])){
							$rented_list[$oInfo['customers_id']]['list'] = array_merge($rented_list[$oInfo['customers_id']]['list'], $list_per_order);
						}else{
							$rented_list[$oInfo['customers_id']]['list'] = array();
							$rented_list[$oInfo['customers_id']]['list'] = array_merge($rented_list[$oInfo['customers_id']]['list'], $list_per_order);
						}
						$rented_list[$oInfo['customers_id']]['firstname'] = $oInfo->OrdersAddresses['customer']->entry_name;
						$rented_list[$oInfo['customers_id']]['email_address'] = $oInfo->customers_email_address;
					}

					//print_r($rented_list);
					//echo implode('<br/>',$rented['list']).'--'. $rented['email_address'].'--'.$rented['firstname'];

					foreach($rented_list as $rented){
						$emailEvent = new emailEvent('return_reminder', $oInfo->Customers->language_id);

						$emailEvent->setVar('firstname', $rented['firstname']);
						$emailEvent->setVar('email_address', $rented['email_address']);
						$emailEvent->setVar('rented_list', implode('<br/>',$rented['list']));

						$emailEvent->sendEmail(array(
						'email' => $rented['email_address'],
						'name'  => $rented['firstname']
						));

					}



		}
	}
  require('includes/application_bottom.php');
?>