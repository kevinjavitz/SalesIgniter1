<?php
	$polygon = serialize($_POST['poly_point']);
	$centerName = $_POST['inventory_center_name'];
	$centerAddress = $_POST['inventory_center_address'];
	$scenterAddress = $_POST['inventory_center_specific_address'];
   	$centerDetails = $_POST['inventory_center_details'];
	$shortDetails = $_POST['inventory_center_short_details'];
	$image = $_POST['inventory_center_image'];
	$centerCom = $_POST['inventory_center_comission'];
	$minRentalDays = $_POST['inventory_center_min_rental_days'];
	$delivery_instructions = $_POST['inventory_center_delivery_instructions'];
	$provider = $_POST['provider'];

	$continent = $_POST['continent'];
	$country = $_POST['country'];
	$state = $_POST['state'];
	$city = $_POST['city'];
	$sortOrder = $_POST['inventory_center_sort_order'];

	$ProductsInventoryCenters = Doctrine_Core::getTable('ProductsInventoryCenters');
	if (isset($_GET['cID'])){
		$InventoryCenter = $ProductsInventoryCenters->find((int)$_GET['cID']);
	}else{
		$InventoryCenter = new ProductsInventoryCenters();
	}

	require(sysConfig::getDirFsCatalog() . 'includes/classes/json.php');
	 $json = new Services_JSON();
	 $pointCoordinates = array(
              'lng' => 'false',
              'lat' => 'false'
          );

     $addressStr = str_replace("\r\n", " ",stripslashes (htmlspecialchars($centerAddress)));
	 $addressStr = str_replace(' ', '+', $addressStr);
     $address = 'http://maps.google.com/maps/geo?q=' . $addressStr  . '&key=' . Session::get('google_key') . '&output=json';
     $page = file_get_contents($address);

     if (tep_not_null($page)){
        $addressArr = $json->decode($page);
        if (isset($addressArr->Placemark)){
            $point = $addressArr->Placemark[0]->Point->coordinates;
        }
        if (is_array($point)){
            $pointCoordinates['lng'] = $point[0];
            $pointCoordinates['lat'] = $point[1];
        }
     }

	$InventoryCenter->inventory_center_name = $centerName;
	$InventoryCenter->inventory_center_address = $centerAddress;
	$InventoryCenter->inventory_center_address_point = serialize($pointCoordinates);
	$InventoryCenter->inventory_center_specific_address = $scenterAddress;
	$InventoryCenter->inventory_center_details = $centerDetails;
	$InventoryCenter->inventory_center_short_details = $shortDetails;
	$InventoryCenter->inventory_center_image = $image;

	$InventoryCenter->inventory_center_comission = $centerCom;
	$InventoryCenter->inventory_center_min_rental_days = $minRentalDays;
	$InventoryCenter->inventory_center_customer = $provider;
	$InventoryCenter->inventory_center_delivery_instructions = $delivery_instructions;

	$InventoryCenter->inventory_center_continent = $continent;
	$InventoryCenter->inventory_center_country = $country;
	$InventoryCenter->inventory_center_state = $state;
	$InventoryCenter->inventory_center_city = $city;
	$InventoryCenter->inventory_center_sort_order = $sortOrder;

	$InventoryCenter->gmaps_polygon = $polygon;

	if (is_array($_POST['inventory_center_shipping'])){
		$InventoryCenter->inventory_center_shipping = implode(',', $_POST['inventory_center_shipping']);
	}else{
		$InventoryCenter->inventory_center_shipping = $_POST['inventory_center_shipping'];
	}

	EventManager::notify('InventoryCentersBeforeSave', &$InventoryCenter);

	$InventoryCenter->save();
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $InventoryCenter->inventory_center_id, null, 'default'), 'redirect');
?>