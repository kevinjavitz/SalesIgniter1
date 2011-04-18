<?php
	$polygon = serialize($_POST['poly_point']);
	$centerName = $_POST['inventory_center_name'];
	$centerAddress = $_POST['inventory_center_address'];
	$scenterAddress = $_POST['inventory_center_specific_address'];
   	$centerDetails = $_POST['inventory_center_details'];
	$centerCom = $_POST['inventory_center_comission'];
	$minRentalDays = $_POST['inventory_center_min_rental_days'];
	$delivery_instructions = $_POST['inventory_center_delivery_instructions'];
	$provider = $_POST['provider'];

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
     $address = 'http://maps.google.com/maps/geo?q=' . $addressStr  . '&key=' . sysConfig::get('EXTENSION_INVENTORY_CENTERS_GOOGLE_MAPS_API_KEY') . '&output=json';
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
	$InventoryCenter->inventory_center_comission = $centerCom;
	$InventoryCenter->inventory_center_min_rental_days = $minRentalDays;
	$InventoryCenter->inventory_center_customer = $provider;
	$InventoryCenter->inventory_center_delivery_instructions = $delivery_instructions;
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