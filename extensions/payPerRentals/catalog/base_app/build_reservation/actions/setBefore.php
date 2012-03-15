<?php
	$html = '';
	$html2  =  '';
 	$nr = 0;
	$goodDates = '';
	$events_date = '';
	$selectedDates = array();
	if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_ENABLE_TIME') == 'True'){

			$hours = $_POST['hstart'];
			$minutes = 0;
			$houre = $_POST['hend'];
			$minutee = 0;
			if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_TIME_INCREMENT') == '1/2'){
				$st = (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_START_TIME');
				$hours = $st + ($_POST['hstart'] - $st)/2;
				if(($_POST['hstart'] - $st)%2 == 1){
					$minutes = 30;
				}else{
					$minutes = 0;
				}

				$st = (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_START_TIME');
				$houre = $st + ($_POST['hend'] - $st)/2;
				if(($_POST['hend'] - $st)%2 == 1){
					$minutee = 30;
				}else{
					$minutee = 0;
				}

			}
    }else{
            $hours = 0;
			$minutes = 0;
			$houre = 0;
			$minutee = 0;
    }

    if(isset($_POST['dstart']) && isset($_POST['dend']) && !empty($_POST['dstart']) && !empty($_POST['dend'])){
			$_POST['dstart'] = strtotime($_POST['dstart']);
			$_POST['dend'] = strtotime($_POST['dend']);
			$start_date = date("Y-m-d H:i:s",mktime($hours,$minutes,0,date("m",$_POST['dstart']), date("d",$_POST['dstart']), date("Y",$_POST['dstart'])));
			$end_date = date("Y-m-d H:i:s",mktime($houre,$minutee,0,date("m",$_POST['dend']), date("d",$_POST['dend']), date("Y",$_POST['dend'])));
			$todayPadding = date("Y-m-d", strtotime("+" . ((int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_PADDING')) . " days"));
			$startPadding = date("Y-m-d",mktime(0,0,0,date("m",$_POST['dstart']), date("d",$_POST['dstart']), date("Y",$_POST['dstart'])));
			$invExt = false;
			if ($appExtension->isInstalled('inventoryCenters') && $appExtension->isEnabled('inventoryCenters')){
				$invExt = $appExtension->getExtension('inventoryCenters');
			}
			$inventoryPickupMinDays = 0;
			if (isset($_POST['pickup']) && ($_POST['pickup'] != 'select') && $invExt){
				$invCenter = $invExt->getInventoryCenters($_POST['pickup']);
				$inventoryPickupMinDays = (int)$invCenter[0]['inventory_center_min_rental_days'];
			}
			$min_end_date = date("Y-m-d",mktime(0,0,0,date("m",$_POST['dstart']), date("d",$_POST['dstart']) + $inventoryPickupMinDays , date("Y",$_POST['dstart'])));
			$end_date_compare = date("Y-m-d",mktime(0,0,0,date("m",$_POST['dend']), date("d",$_POST['dend']), date("Y",$_POST['dend'])));
			if (strtotime($startPadding) < strtotime($todayPadding) || strtotime($end_date_compare) < strtotime($min_end_date)){
				$messageStack->addSession('pageStack','Dates were not selected because miniumum rental days was not selected','error');
				EventManager::attachActionResponse( itw_app_link(tep_get_all_get_params()), 'redirect');
			}

			Session::set('isppr_date_start', $start_date);
			Session::set('isppr_date_end', $end_date);

			if (isset($_POST['hstart'])){
				Session::set('isppr_hour_starts', $_POST['hstart']);
			}
			if (isset($_POST['hend'])){
				Session::set('isppr_hour_ends', $_POST['hend']);
			}
			if (isset($_POST['pickup']) &&($_POST['pickup'] != 'select')){
				Session::set('isppr_inventory_pickup', $_POST['pickup']);
			}else{
				Session::remove('isppr_inventory_pickup');
			}

			Session::set('isppr_selected', true);
			if (isset($_POST['dropoff']) && $_POST['dropoff'] != 0 && $_POST['dropoff'] != 'select'){
				Session::set('isppr_inventory_dropoff', $_POST['dropoff']);
			}else{
				if (isset($_POST['pickup']) &&($_POST['pickup'] != 'select')){
					Session::set('isppr_inventory_dropoff', $_POST['pickup']);
				}else{
					Session::remove('isppr_inventory_pickup');
				}
			}
	        if(isset($_POST['qty']) && !empty($_POST['qty']) && is_numeric($_POST['qty'])){
	            Session::set('isppr_product_qty', $_POST['qty']);
	        }else{
		        Session::set('isppr_product_qty', 1);
	        }
	}else if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
		if(isset($_POST['event']) && $_POST['event'] != 0){
			//transform event date in start_date - end_date =start_date+1day
			$event_duration = 1;//in days
			$Qevent = Doctrine_Query::create()
				->from('PayPerRentalEvents')
				->where('events_id = ?', $_POST['event'])
				->fetchOne();

			$start_date = strtotime($Qevent->events_date);
			$starting_date = date("Y-m-d H:i:s", mktime(date("h",$start_date),date("i",$start_date), date("s",$start_date), date("m",$start_date), date("d",$start_date), date("Y",$start_date)));
			$ending_date = date("Y-m-d H:i:s", mktime(date("h",$start_date),date("i",$start_date), date("s",$start_date), date("m",$start_date), date("d",$start_date)+$event_duration, date("Y",$start_date)));
			Session::set('isppr_date_start', $starting_date);
			Session::set('isppr_event_date', $starting_date);
			Session::set('isppr_event_name', $Qevent->events_name);
			Session::set('isppr_date_end', $ending_date);
			Session::set('isppr_selected', true);

			if(isset($_POST['multiple_dates'])){
				Session::set('isppr_event_multiple_dates', $_POST['multiple_dates']);
			}else{
				if(Session::exists('isppr_event') && Session::get('isppr_event') != $_POST['event']){
					Session::remove('isppr_event_multiple_dates');
				}

			}
			Session::set('isppr_event', $_POST['event']);
		}

		if(isset($_POST['gate']) && isset($Qevent)){
			if($_POST['gate'] == 0){
				$_POST['gate'] = $Qevent->default_gate;
			}
			$Qgate = Doctrine_Query::create()
			->from('PayPerRentalGates')
			->where('gates_id = ?', $_POST['gate'])
			->fetchOne();

			Session::set('isppr_gate', $_POST['gate']);
			Session::set('isppr_event_gate', $Qgate->gate_name);
		}

		if(isset($_POST['qty']) && !empty($_POST['qty']) && is_numeric($_POST['qty'])){
			Session::set('isppr_product_qty', $_POST['qty']);
		}else{
			Session::set('isppr_product_qty', 1);
		}
	}
	else {
		if(!isset($_POST['isZip'])){
			Session::set('isppr_selected', false);
		}
	}

if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_SHIP') == 'True'){
	if (isset($_POST['ship_method']) && $_POST['ship_method'] != '0' && $_POST['ship_method'] != 'null'){
		Session::set('isppr_shipping_method', $_POST['ship_method']);

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
			$module = OrderShippingModules::getModule('zonereservation');
		}
		else {

			if (isset($_POST['zipCode']) && !empty($_POST['zipCode'])){
				$postcode = $_POST['zipCode'];
				Session::set('isppr_shipping_zip', $_POST['zipCode']);
				$shippingAddressArray = array(
					'entry_street_address' => (isset($_POST['street_address']) && !empty($_POST['street_address']))
						? $_POST['street_address'] : '',
					'entry_postcode' => $postcode,
					'entry_city' => (isset($_POST['city']) && !empty($_POST['city'])) ? $_POST['city'] : '',
					'entry_state' => (isset($_POST['state']) && ($_POST['state'] != 'undefined')) ? $_POST['state']
						: '',
					'entry_country_id' => (isset($_POST['country']) && !empty($_POST['country'])) ? $_POST['country']
						: sysConfig::get('STORE_COUNTRY'),
					'entry_zone_id' => (isset($_POST['state']) && ($_POST['state'] != 'undefined')) ? $_POST['state']
						: ''
				);
				$addressBook =& $userAccount->plugins['addressBook'];
				$addressBook->addAddressEntry('delivery', $shippingAddressArray);

			}
			$module = OrderShippingModules::getModule('upsreservation');
		}
		if (isset($module) && is_object($module)){
			$quotes = $module->quote();
			for($i = 0, $n = sizeof($quotes['methods']); $i < $n; $i++){
				if ($quotes['methods'][$i]['id'] == $_POST['ship_method']){
					Session::set('isppr_shipping_days_before', $quotes['methods'][$i]['days_before']);
					Session::set('isppr_shipping_days_after', $quotes['methods'][$i]['days_after']);
					Session::set('isppr_shipping_cost', $quotes['methods'][$i]['cost']);
					break;
				}
			}
		}
		Session::set('isppr_selected', true);
	}
	else {
		if(!isset($_POST['isZip']))
		Session::set('isppr_selected', false);
	}
}
else {
	Session::set('isppr_shipping_days_before', 0);
	Session::set('isppr_shipping_days_after', 0);
	Session::set('isppr_shipping_cost', 0);
}

	if(!isset($_POST['rType']) && !isset($_POST['fromInfobox'])){
			if(isset($_POST['cPath']) && ($_POST['cPath'] != '-1')){
				$redirectLink = itw_app_link(null, 'index', $_POST['cPath']);
				$redirectCat = $_POST['cPath'];
			}else if(isset($_GET['cPath']) && ($_POST['cPath'] != '-1')){
				$redirectLink = itw_app_link(null, 'index', $_GET['cPath']);
				$redirectCat = $_GET['cPath'];
			}else{
				if (isset($_POST['url']) && (strpos($_POST['url'],'index/default') < 0) && $_POST['cPath'] != '-1' && $_GET['cPath'] != '-1'){
					$redirectLink = $_POST['url'];
				}else{ 
					$redirectLink = itw_app_link(null,'products','all');
				}
		    }
			if(isset($redirectLink)){
				if(sysConfig::get('EXTENSION_INVENTORY_CENTERS_INTERMEDIARY_PAGE') == 'True'){
					Session::set('redirectLinkBefore', $redirectLink);
					if(isset($redirectCat)){
						Session::set('redirectCategoryBefore', $redirectCat);
					}
					EventManager::attachActionResponse(itw_app_link('appExt=inventoryCenters','show_inventory','list_select'), 'redirect');
				}else{
					EventManager::attachActionResponse($redirectLink, 'redirect');
				}
		    }
	}else{
		if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
			//get all modules padding days and add to current day if is bigger than start date exclude
			$Qevent = Doctrine_Query::create()
				->from('PayPerRentalEvents')
				->where('events_id = ?', $_POST['event'])
				->fetchOne();
			$events_date = date('m/d/Y', strtotime($Qevent->events_date));

			if($Qevent->events_days>1){
				$myDates = '';
				if(Session::exists('isppr_event_multiple_dates')){
					$datesArr = Session::get('isppr_event_multiple_dates');
					$selectedDates = $datesArr;
					if(isset($datesArr[0]) && !empty($datesArr[0])){
						$myDates = '<div class="mydates">';
						foreach($datesArr as $iDate){
							$myDates .= '<input type="hidden" name="multiple_dates[]" value="'.$iDate.'">';
						}
						$myDates .= '</div>';
					}
				}
				$html2 = '<div style="position:relative"><div class="allCalendar"><div class="myTextCalendar" style="color:red;background-color:#ffffff;width:200px;padding:10px;padding-top:5px;padding-bottom:5px;">Please click the dates you want to reserve. Then click the Done Selecting Dates button, and then chose your gate (optional) and click view rentals</div><div class="myCalendar"></div> </div><div class="calDone">Choose Dates</div><span class="closeCal ui-icon ui-icon-closethick"></span></div>'.$myDates;
				$startTimePadding = strtotime($Qevent->events_date);
				$endTimePadding = strtotime('+' . $Qevent->events_days . ' days', $startTimePadding);
				$booked = array();
				while ($startTimePadding <= $endTimePadding) {
					$dateFormatted = date('Y-n-j', $startTimePadding);
					$booked[] = $dateFormatted;
					$startTimePadding += 60 * 60 * 24;
				}
				$goodDates =  $booked;
			}
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'False'){
				if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
					$module = OrderShippingModules::getModule('zonereservation');
			}
			else {
				if (isset($_POST['zipCode']) && !empty($_POST['zipCode'])){
					$postcode = $_POST['zipCode'];

					$shippingAddressArray = array(
						'entry_street_address' => (isset($_POST['street_address']) && !empty($_POST['street_address']))
							? $_POST['street_address'] : '',
						'entry_postcode' => $postcode,
						'entry_city' => (isset($_POST['city']) && !empty($_POST['city'])) ? $_POST['city'] : '',
						'entry_state' => (isset($_POST['state']) && ($_POST['state'] != 'undefined')) ? $_POST['state']
							: '',
						'entry_country_id' => (isset($_POST['country']) && !empty($_POST['country']))
							? $_POST['country'] : sysConfig::get('STORE_COUNTRY'),
						'entry_zone_id' => (isset($_POST['state']) && ($_POST['state'] != 'undefined'))
							? $_POST['state'] : ''
					);
					$addressBook =& $userAccount->plugins['addressBook'];
					$addressBook->addAddressEntry('delivery', $shippingAddressArray);
					//global $current_product_weight;
					//$current_product_weight = $product->getWeight()* $_GET['qty'];
					//OrderShippingModules::calculateWeight();
					$module = OrderShippingModules::getModule('upsreservation');
				}
			}
			$shippingArr = explode(',', $Qevent->shipping);


				$html.='<option value="0">Select Level of Service</option>';
				$nr = 0;
				if(isset($module) && is_object($module)){
					$quotes = $module->quote();
					for($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++){
						$days = $quotes['methods'][$i]['days_before'];
						$next_day = mktime(0,0,0,date("m"),date("d")+$days,date("Y"));
						if ($next_day < strtotime($starting_date) && in_array($quotes['methods'][$i]['id'], $shippingArr)){
							if(Session::exists('isppr_shipping_method') && Session::get('isppr_shipping_method') == $quotes['methods'][$i]['id']){
								$html.='<option selected="selected" value="' . $quotes['methods'][$i]['id'] . '">' . $quotes['methods'][$i]['title'] . ' ('.$currencies->format($quotes['methods'][$i]['cost']).')</option>';
							}else{
								$html.='<option value="' . $quotes['methods'][$i]['id'] . '">' . $quotes['methods'][$i]['title'] . ' ('.$currencies->format($quotes['methods'][$i]['cost']).')</option>';
							}
							$nr++;
						}
					}
				}
			}else{
				$Qevent = Doctrine_Query::create()
				->from('PayPerRentalEvents')
				->where('events_id = ?', $_POST['event'])
				->fetchOne();
				$gatesArr = explode(',', $Qevent->gates);
				$html .= '<option value="0">Autoselect Gate</option>';
				$nr = 0;

				$QGate =Doctrine_Query::create()
				->from('PayPerRentalGates')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				$defaultGate = $Qevent->default_gate;
				foreach($QGate as $iGate){
					if(in_array($iGate['gates_id'], $gatesArr)){
						if(Session::exists('isppr_gate') && Session::get('isppr_gate') == $iGate['gates_id']){
							$defaultGate = $iGate['gates_id'];
							break;
						}
					}
				}

				foreach($QGate as $iGate){
					if(in_array($iGate['gates_id'], $gatesArr)){
						if($iGate['gates_id'] != $defaultGate){
							$html .= '<option value="' . $iGate['gates_id'] . '">' . $iGate['gate_name'] .'</option>';
						}else{
							$html .= '<option value="' . $iGate['gates_id'] . '" selected="selected">' . $iGate['gate_name'] .'</option>';
						}
					}
					$nr++;

				}
			}

			EventManager::attachActionResponse(array(
				'success' => true,
				'nr'	=> $nr,
				'data'     => $html,
				'calendar' => $html2,
				'events_date' => $events_date,
				'selectedDates' => $selectedDates,
				'goodDates' => $goodDates
			), 'json');
		}
	else {

		if (isset($_POST['zipCode']) && !empty($_POST['zipCode'])){
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
				$module = OrderShippingModules::getModule('zonereservation');
			}
			else {
				$postcode = $_POST['zipCode'];

				$shippingAddressArray = array(
					'entry_street_address' => (isset($_POST['street_address']) && !empty($_POST['street_address']))
						? $_POST['street_address'] : '',
					'entry_postcode' => $postcode,
					'entry_city' => (isset($_POST['city']) && !empty($_POST['city'])) ? $_POST['city'] : '',
					'entry_state' => (isset($_POST['state']) && ($_POST['state'] != 'undefined')) ? $_POST['state']
						: '',
					'entry_country_id' => (isset($_POST['country']) && !empty($_POST['country'])) ? $_POST['country']
						: sysConfig::get('STORE_COUNTRY'),
					'entry_zone_id' => (isset($_POST['state']) && ($_POST['state'] != 'undefined')) ? $_POST['state']
						: ''
				);
				$addressBook =& $userAccount->plugins['addressBook'];
				$addressBook->addAddressEntry('delivery', $shippingAddressArray);
				//global $current_product_weight;
				//$current_product_weight = $product->getWeight()* $_GET['qty'];
				//OrderShippingModules::calculateWeight();
				$module = OrderShippingModules::getModule('upsreservation');
			}
			$html .= '<option value="0">Select Method</option>';
			$nr = 0;
			if (isset($module) && is_object($module)){
				$quotes = $module->quote();
				for($i = 0, $n = sizeof($quotes['methods']); $i < $n; $i++){

					if (Session::exists('isppr_shipping_method') && Session::get('isppr_shipping_method') == $quotes['methods'][$i]['id']){
						$html .= '<option selected="selected" value="' . $quotes['methods'][$i]['id'] . '">' . $quotes['methods'][$i]['title'] /*. ' (' . $currencies->format($quotes['methods'][$i]['cost']) . ')' */ . '</option>';
					}
					else {
						$html .= '<option value="' . $quotes['methods'][$i]['id'] . '">' . $quotes['methods'][$i]['title'] /*. ' (' . $currencies->format($quotes['methods'][$i]['cost']) . ')' */. '</option>';
					}
					$nr++;
				}
			}
			EventManager::attachActionResponse(array(
					'success' => true,
					'data' => $html,
					'calendar' => $html2,
					'selectedDates' => $selectedDates,
					'goodDates' => $goodDates
				), 'json');
		}
		else if (sysConfig::get('EXTENSION_INVENTORY_CENTERS_USE_LOCATION') == 'True'){
			if (isset($_POST['pickup']) &&($_POST['pickup'] != 'select')){
				//echo 'll';
				    Session::set('isppr_continent', '');
					Session::set('isppr_country', '');
					Session::set('isppr_state', '');
					Session::set('isppr_city', '');
					Session::remove('isppr_inventory_pickup');

					$inventory_centers = $appExtension->getExtension('inventoryCenters');
					$invInfo = $inventory_centers->getInventoryCenters((int)$_POST['pickup']);
					$invInfo = $invInfo[0];
					//print_r($invInfo);
				   // echo $invInfo['inventory_center_state'].'---'.$invInfo['inventory_center_country'];
					/*Select in PPRBox*/
					Session::set('isppr_city', $invInfo['inventory_center_city']);
					Session::set('isppr_state', $invInfo['inventory_center_state']);
					Session::set('isppr_country', $invInfo['inventory_center_country']);
					Session::set('isppr_continent', $invInfo['inventory_center_continent']);

					$_POST['isInv'] = 1;
			}
			//here I return the filtered contry-state-etc..the same has to be done in infoboxutil
			if(!isset($_POST['isInv'])){
				if(isset($_POST['isContinent']) && $_POST['isContinent'] > 0){
					switch($_POST['isContinent']){
						case '1':
							Session::set('isppr_continent', isset($_POST['continent']) && $_POST['continent'] != 'select'?$_POST['continent']:'');
							Session::set('isppr_country', '');
							Session::set('isppr_state', '');
							Session::set('isppr_city', '');
							break;
						case '2':
							Session::set('isppr_continent', isset($_POST['continent']) && $_POST['continent'] != 'select'?$_POST['continent']:'');
							Session::set('isppr_country', isset($_POST['country']) && $_POST['country'] != 'select'?$_POST['country']:'');
							Session::set('isppr_state', '');
							Session::set('isppr_city', '');
							break;
						case '3':
							Session::set('isppr_continent', isset($_POST['continent']) && $_POST['continent'] != 'select'?$_POST['continent']:'');
							Session::set('isppr_country', isset($_POST['country']) && $_POST['country'] != 'select'?$_POST['country']:'');
							Session::set('isppr_state', isset($_POST['state']) && $_POST['state'] != 'select'?$_POST['state']:'');
							Session::set('isppr_city', '');
							break;
						case '4':
							Session::set('isppr_continent', isset($_POST['continent']) && $_POST['continent'] != 'select'?$_POST['continent']:'');
							Session::set('isppr_country', isset($_POST['country']) && $_POST['country'] != 'select'?$_POST['country']:'');
							Session::set('isppr_state', isset($_POST['state']) && $_POST['state'] != 'select'?$_POST['state']:'');
							Session::set('isppr_city', isset($_POST['city']) && $_POST['city'] != 'select'?$_POST['city']:'');
							break;
					}
					if(isset($_POST['city']) && $_POST['city'] != 'select' && isset($_POST['state']) && $_POST['state'] != 'select' && isset($_POST['country']) && $_POST['country'] != 'select' && isset($_POST['continent']) && $_POST['continent'] != 'select'){
						Session::set('isppr_selected', true);
					}else{
						Session::remove('isppr_inventory_pickup');
					}

				}/*else
				if(isset($_POST['continent']) && $_POST['continent'] != 'select'){
					Session::set('isppr_continent', $_POST['continent']);

					if(isset($_POST['country']) && $_POST['country'] != 'select'){
						Session::set('isppr_country', $_POST['country']);
						if(isset($_POST['state']) && $_POST['state'] != 'select'){
							Session::set('isppr_state', $_POST['state']);
							if(isset($_POST['city']) && $_POST['city'] != 'select'){
								Session::set('isppr_city', $_POST['city']);
								Session::set('isppr_selected', true);
							}else{
								Session::set('isppr_city', '');
								Session::remove('isppr_selected');
								Session::remove('isppr_inventory_pickup');
							}
						}else{
							Session::set('isppr_state', '');
							Session::set('isppr_city', '');
							Session::remove('isppr_inventory_pickup');
						}
					}else{
						Session::set('isppr_country', '');
						Session::set('isppr_state', '');
						Session::set('isppr_city', '');
						Session::remove('isppr_inventory_pickup');
					}

				}*/else{
					Session::set('isppr_continent', '');
					Session::set('isppr_country', '');
					Session::set('isppr_state', '');
					Session::set('isppr_city', '');
					Session::remove('isppr_inventory_pickup');
				}

			}

			if (isset($_POST['hasHeaders']) && $_POST['hasHeaders'] == false){
				$isHome = false;
			}else{
				$isHome = true;
			}
			if(!isset($_POST['fromInfobox'])){
				$html = ReservationUtilities::inventoryCenterAddon($isHome, true, false, false)->draw();
				EventManager::attachActionResponse(array(
					'success' => true,
					'data'     => $html,
					'calendar' => $html2,
					'events_date' => $events_date,
					'selectedDates' =>$selectedDates,
					'goodDates' => $goodDates
				), 'json');
			}else{
				if(isset($_POST['cPath']) && ($_POST['cPath'] != '-1')){
					$redirectLink = itw_app_link(null, 'index', $_POST['cPath']);
					$redirectCat = $_POST['cPath'];
				}else if(isset($_GET['cPath']) && ($_POST['cPath'] != '-1')){
					$redirectLink = itw_app_link(null, 'index', $_GET['cPath']);
					$redirectCat = $_GET['cPath'];
				}else{
					if (isset($_POST['url']) && (strpos($_POST['url'],'index/default') < 0) && $_POST['cPath'] != '-1' && $_GET['cPath'] != '-1'){
						$redirectLink = $_POST['url'];
					}else{
						$redirectLink = itw_app_link(null,'products','all');
					}
				}
				if(isset($redirectLink)){
					if(sysConfig::get('EXTENSION_INVENTORY_CENTERS_INTERMEDIARY_PAGE') == 'True'){
						Session::set('redirectLinkBefore', $redirectLink);
						if(isset($redirectCat)){
							Session::set('redirectCategoryBefore', $redirectCat);
						}
						EventManager::attachActionResponse(itw_app_link('appExt=inventoryCenters','show_inventory','list_select'), 'redirect');
					}else{
						EventManager::attachActionResponse($redirectLink, 'redirect');
					}
				}
			}
		}
	}
	}
 
?>