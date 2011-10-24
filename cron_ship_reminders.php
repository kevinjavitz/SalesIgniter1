<?php
 require('includes/application_top.php');
$pprExt = $appExtension->getExtension('payPerRentals');
$invExt = $appExtension->getExtension('inventoryCenters');

	if ($pprExt !== false && $pprExt->isEnabled() === true && $invExt !== false && $invExt->isEnabled() === true){
		if(sysConfig::get('EXTENSION_INVENTORY_CENTERS_ENABLE_REMINDERS') == 'True'){
			$day_before_due = (int)sysConfig::get('EXTENSION_INVENTORY_CENTERS_REMINDERS_SHIPPING');
			$date_before_due = date('Y-m-d H:i:s', strtotime('+ '.$day_before_due . ' days', strtotime(date('Y-m-d'))));
			$ReservationQuery = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.Customers c')
			->leftJoin('o.OrdersAddresses oa')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsReservation opr')
			//->where('opr.orders_products_reservations_id = ?', $bID)
			->where('DATE_SUB(opr.start_date, INTERVAL opr.shipping_days_before DAY) <= ?', $date_before_due)
			//->andWhere('DATE_SUB(opr.start_date, INTERVAL opr.shipping_days_before DAY) >= ?', date('Y-m-d H:i:s'))
			//->andWhere('opr.rental_state = ?', 'out')

			->andWhere('parent_id IS NULL');
			//todo email for late not returned items--auto_send_return_rentals does that

			EventManager::notify('OrdersListingBeforeExecute', &$ReservationQuery);

			$Reservation = $ReservationQuery->execute();

			//print_r($Reservation->toArray(true));

			$rented_list = array();
			foreach($Reservation as $oInfo){
				$list_per_order = array();
				$list = array();
				foreach($oInfo->OrdersProducts as $opInfo){
					foreach($opInfo->OrdersProductsReservation as $oprInfo){
						$barcode_id = $oprInfo['barcode_id'];
						$QInvCenters = Doctrine_Query::create()
						->select('ic.inventory_center_customer')
						->from('ProductsInventoryCenters ic')
						->leftJoin('ic.ProductsInventoryBarcodesToInventoryCenters bic')
						->where('bic.barcode_id = ?', $barcode_id)
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						$QProviderEmail = Doctrine_Query::create()
						->select('customers_email_address')
						->from('Customers')
						->where('customers_id = ?', $QInvCenters[0]['inventory_center_customer'])
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);


						$list[$QProviderEmail[0]['customers_email_address']][] = 'Product name: '.$opInfo['products_name']. '. Shipping due on '.strftime(sysLanguage::getDateFormat('long'),strtotime('-'.$oprInfo['shipping_days_before'].' days',strtotime($oprInfo['start_date']))).'<br/>';


						//print_r($QInvCenters);
						//print_r($QProviderEmail);
					}
				}
				foreach($list as $email=>$val){
					$rented_list[$email]['list'] .= 'Order ID: '. $oInfo->orders_id.'<br/>'.implode('<br/>',$val);

					if(file_exists(sysConfig::getDirFsCatalog(). 'extensions/upsLabels/tracking/'.$oInfo->ups_track_num.'.png')){
						$rented_list[$email]['attach'][] = 'extensions/upsLabels/tracking/'.$oInfo->ups_track_num.'.png';
					}else{
						$file = (itw_admin_app_link('appExt=upsLabels&action=shipOrdersAuto&oID=' . $oInfo->orders_id, 'ship_ups', 'default'));
						if(!empty($file)){
							$ch=curl_init();
							curl_setopt($ch,CURLOPT_URL, $file);
							curl_exec($ch);
							curl_close($ch);
						}

						$QNewOrder = Doctrine_Query::create()
						->from('Orders o')
						->andWhere('o.orders_id = ?', $oInfo->orders_id)
						->fetchOne();
						if(file_exists(sysConfig::getDirFsCatalog(). 'extensions/upsLabels/tracking/'.$QNewOrder->ups_track_num.'.png')){
							$rented_list[$email]['attach'][] = 'extensions/upsLabels/tracking/'.$QNewOrder->ups_track_num.'.png';
						}
					}
				}
			}

			foreach($rented_list as $email => $rented){
				$emailEvent = new emailEvent('ship_reminder', $oInfo->Customers->language_id);
				$QFirstname = Doctrine_Query::create()
				->select('customers_firstname')
				->from('Customers')
				->where('customers_email_address = ?', $email)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

            	$emailEvent->setVar('firstname', $QFirstname[0]['customers_firstname']);
				$emailEvent->setVar('rented_list', $rented['list']);
				$sendVariables = array();
				$sendVariables['email'] = $email;
				$sendVariables['name'] = $QFirstname[0]['customers_firstname'];
				$sendVariables['attach'] = $rented['attach'];
            	$emailEvent->sendEmail($sendVariables);
			}
		}
	}
  require('includes/application_bottom.php');
?>