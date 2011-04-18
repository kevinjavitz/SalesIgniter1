<?php
	$html = '';
 	$nr = 0;
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
			if (isset($_POST['pickup']) && ($_POST['pickup'] != 'undefined') && $invExt){
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
			if (isset($_POST['pickup']) &&($_POST['pickup'] != 'undefined')){
				Session::set('isppr_inventory_pickup', $_POST['pickup']);
			}else{
				Session::remove('isppr_inventory_pickup');
			}

			Session::set('isppr_selected', true);
			if (isset($_POST['dropoff']) && $_POST['dropoff'] != 0 && $_POST['dropoff'] != 'undefined'){
				Session::set('isppr_inventory_dropoff', $_POST['dropoff']);
			}else{
				if (isset($_POST['pickup']) &&($_POST['pickup'] != 'undefined')){
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
			Session::set('isppr_event', $_POST['event']);
		}
	}else{
		Session::set('isppr_selected', false);
	}

	if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_SHIP') == 'True'){
			if(isset($_POST['ship_method']) && $_POST['ship_method'] != '0'){
				
				Session::set('isppr_shipping_method', $_POST['ship_method']);

				if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
					$module = OrderShippingModules::getModule('zonereservation');
				} else{
					$module = OrderShippingModules::getModule('upsreservation');
				}
				if(isset($module) && is_object($module)){
					$quotes = $module->quote();

					for($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++){
						if($quotes['methods'][$i]['id'] == $_POST['ship_method']){
							Session::set('isppr_shipping_days_before', $quotes['methods'][$i]['days_before']);
							Session::set('isppr_shipping_days_after', $quotes['methods'][$i]['days_after']);
							Session::set('isppr_shipping_cost', $quotes['methods'][$i]['cost']);
							break;
						}
					}
				}
				Session::set('isppr_selected', true);
		}else{
			Session::set('isppr_selected', false);
		}
	}else{
		Session::set('isppr_shipping_days_before', 0);
		Session::set('isppr_shipping_days_after', 0);
		Session::set('isppr_shipping_cost', 0);
	}

	if(!isset($_POST['rType'])){
			if(isset($_POST['cPath']) && ($_POST['cPath'] != '-1')){
				EventManager::attachActionResponse(itw_app_link('cPath=' . $_POST['cPath'], 'index', 'default'), 'redirect');
			}else if(isset($_GET['cPath']) && ($_POST['cPath'] != '-1')){
				EventManager::attachActionResponse(itw_app_link('cPath=' . $_GET['cPath'], 'index', 'default'), 'redirect');
			}else{
				if (isset($_POST['url']) && (strpos($_POST['url'],'index/default') < 0) && $_POST['cPath'] != '-1' && $_GET['cPath'] != '-1'){
					EventManager::attachActionResponse($_POST['url'], 'redirect');
				}else{ 
					EventManager::attachActionResponse(itw_app_link(null,'products','all'), 'redirect');
				}
		    }
	}else{
		if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
			//get all modules padding days and add to current day if is bigger than start date exclude

			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
				$module = OrderShippingModules::getModule('zonereservation');
			} else{
				$module = OrderShippingModules::getModule('upsreservation');
			}
			$Qevent = Doctrine_Query::create()
				->from('PayPerRentalEvents')
				->where('events_id = ?', $_POST['event'])
				->fetchOne();
			$shippingArr = explode(',', $Qevent->shipping);


			$html.='<option value="0">Select Level of Service</option>';
			$nr = 0;
			if(isset($module) && is_object($module)){
				$quotes = $module->quote();
				for($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++){
					$days = $quotes['methods'][$i]['days_before'];
					$next_day = mktime(0,0,0,date("m"),date("d")+$days,date("Y"));
					if ($next_day < strtotime($starting_date) && in_array($quotes['methods'][$i]['id'], $shippingArr)){
						$html.='<option value="' . $quotes['methods'][$i]['id'] . '">' . $quotes['methods'][$i]['title'] . ' ('.$currencies->format($quotes['methods'][$i]['cost']).')</option>';
						$nr++;
					}
				}
			}

			EventManager::attachActionResponse(array(
				'success' => true,
				'nr'	=> $nr,
				'data'     => $html
			), 'json');
		}
	}
 
?>