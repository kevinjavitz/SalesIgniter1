<?php
/*Special Modification*/
	$html = '';
 	$nr = 0;

    if(isset($_POST['name'])){
	    $nameArr = explode(' ', $_POST['name']);
	    if(isset($nameArr[0])){
			Session::set('isppr_firstname', $nameArr[0]);
	    }
	    if(isset($nameArr[1])){
			Session::set('isppr_lastname', $nameArr[1]);
	    }
	    Session::set('isppr_name', $_POST['name']);
    }
	if(isset($_POST['email'])){
		Session::set('isppr_email', $_POST['email']);
	}
	if(isset($_POST['phone'])){
		Session::set('isppr_phone', $_POST['phone']);
	}

	if(isset($_POST['comment'])){
		Session::set('isppr_comment', $_POST['comment']);
	}

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
			Session::set('isppr_event', $_POST['event']);
		}

	}else{

		Session::set('isppr_selected', false);
	}

	if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_SHIP') == 'True'){
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
	}else{
		Session::set('isppr_shipping_days_before', 0);
		Session::set('isppr_shipping_days_after', 0);
		Session::set('isppr_shipping_cost', 0);
	}

	if(!isset($_POST['rType']) && !isset($_POST['fromInfobox'])){
			/*if(isset($_POST['cPath']) && ($_POST['cPath'] != '-1')){
				EventManager::attachActionResponse(itw_app_link('cPath=' . $_POST['cPath'], 'index', 'default'), 'redirect');
			}else if(isset($_GET['cPath']) && ($_POST['cPath'] != '-1')){
				EventManager::attachActionResponse(itw_app_link('cPath=' . $_GET['cPath'], 'index', 'default'), 'redirect');
			}else{
				if (isset($_POST['url']) && (strpos($_POST['url'],'index/default') < 0) && $_POST['cPath'] != '-1' && $_GET['cPath'] != '-1'){
					EventManager::attachActionResponse($_POST['url'], 'redirect');
				}else{
					EventManager::attachActionResponse(itw_app_link(null,'products','all'), 'redirect');
				}
		    } */
		tep_redirect(itw_app_link(null,'products','all'));
		itwExit();

		if(Session::exists('isppr_selected') && Session::get('isppr_selected') == true){
			$start_date = '';
			$end_date = '';
			$event_date = '';
			$event_name = '';
			$pickup = '';
			$dropoff = '';

			$QProducts = Doctrine_Query::create()
			->from('Products')
			->limit(1)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if(isset($QProducts[0])){
				$prodId = $QProducts[0]['products_id'];
			}else{
				EventManager::attachActionResponse(itw_app_link(null,'products','all'), 'redirect');
			}

			$product = new product($prodId);
			$purchaseType = $product->getPurchaseType('reservation');

			$pageForm = htmlBase::newElement('div');
			if (Session::exists('isppr_date_start')){
				$start_date = Session::get('isppr_date_start');
			}
			if (Session::exists('isppr_date_end')){
				$end_date = Session::get('isppr_date_end');
			}
			if (Session::exists('isppr_event_date')){
				$event_date = Session::get('isppr_event_date');
			}
			if (Session::exists('isppr_event_name')){
				$event_name = Session::get('isppr_event_name');
			}
			if (Session::exists('isppr_inventory_pickup')){
				$pickup = Session::get('isppr_inventory_pickup');
			}else{
				//check the inventory center for this one...if multiple output a text to show...select specific//use continent, city for comparison
			}
			if (Session::exists('isppr_inventory_dropoff')){
				$dropoff = Session::get('isppr_inventory_dropoff');
			}
			if (Session::exists('isppr_product_qty')){
				$qtyVal = (int)Session::get('isppr_product_qty');
			}else{
				$qtyVal = 1;
			}

			if ($purchaseType->hasInventory()){
				if(Session::exists('isppr_shipping_cost')){
					$ship_cost = (float)Session::get('isppr_shipping_cost');
				}else{
					$payPerRentalButton->disable();
				}
				$price = $purchaseType->getReservationPrice($start_date, $end_date);
				$pricing = $currencies->format($qtyVal*$price['price'] + $ship_cost);

				$pageForm =  htmlBase::newElement('div');

				if (isset($start_date)) {
					$htmlStartDate = htmlBase::newElement('input')
						->setType('hidden')
						->setName('start_date')
						->setValue($start_date);
				}
				if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
					$htmlEventDate = htmlBase::newElement('input')
						->setType('hidden')
						->setName('event_date')
						->setValue($event_date);
					$htmlEventName = htmlBase::newElement('input')
						->setType('hidden')
						->setName('event_name')
						->setValue($event_name);
				}
				if (isset($pickup)) {
					$htmlPickup = htmlBase::newElement('input')
						->setType('hidden')
						->setName('pickup')
						->setValue($pickup);
				}
				if (isset($dropoff)) {
					$htmlDropoff = htmlBase::newElement('input')
						->setType('hidden')
						->setName('dropoff')
						->setValue($dropoff);
				}
				$htmlRentalQty = htmlBase::newElement('input')
					->setType('hidden')
					->setName('rental_qty')
					->setValue($qtyVal);
				$htmlProductsId = htmlBase::newElement('input')
					->setType('hidden')
					->setName('products_id')
					->setValue($prodId);
				if (isset($end_date)) {
					$htmlEndDate = htmlBase::newElement('input')
						->setType('hidden')
						->setName('end_date')
						->setValue($end_date);
				}

				if (isset($htmlStartDate)) {
					$pageForm->append($htmlStartDate);
				}
				if (isset($htmlEndDate)) {
					$pageForm->append($htmlEndDate);
				}
				if (isset($htmlPickup)) {
					$pageForm->append($htmlPickup);
				}
				if (isset($htmlDropoff)) {
					$pageForm->append($htmlDropoff);
				}
				$pageForm->append($htmlRentalQty);
				$pageForm->append($htmlProductsId);

				if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
					$pageForm->append($htmlEventDate)
						->append($htmlEventName);
				}

				if (Session::exists('isppr_shipping_method')){
					$htmlShippingDays = htmlBase::newElement('input')
						->setType('hidden')
						->setName('rental_shipping')
						->setValue("zonereservation_" . Session::get('isppr_shipping_method'));
					$pageForm->append($htmlShippingDays);
				}




		?>
		<form action="<?php echo itw_app_link('appExt=payPerRentals&products_id=' . $prodId, 'build_reservation', 'default');?>" method="post" name="theform">
		<?php echo $pageForm->draw();?>
		<input type="hidden" name="add_reservation_product">
		</form>
<script type="text/javascript">document.theform.submit();</script>
			<?php
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
		}else{
			//echo 'kk';
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
				if(isset($_POST['continent']) && $_POST['continent'] != 'select' && isset($_POST['isContinent']) && $_POST['isContinent'] == 'true'){
					Session::set('isppr_continent', $_POST['continent']);
					Session::set('isppr_country', '');
					Session::set('isppr_state', '');
					Session::set('isppr_city', '');
					Session::remove('isppr_inventory_pickup');
				}else
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

				}else{
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
					'data'     => $html
				), 'json');
			}else{
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
			}
		}
	}
 
?>