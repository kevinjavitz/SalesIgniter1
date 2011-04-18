<?php
	if (isset($_POST['pickup_postcode'])){
		//$onePageCheckout->setCheckoutAddress('setPickUp');
		$addressId = 'pickup';
	}elseif (isset($_POST['use'])){
		if ($_POST['use'] == 'shipping'){
			$addressId = 'delivery';
		}elseif ($_POST['use'] == 'billing'){
			$addressId = 'billing';
		}
	}
    //here checks if pickup address is in center a config option I think is needed
	$centerID = $userAccount->plugins['addressBook']->getAddressInventoryCenter($addressId);

	EventManager::attachActionResponse(array(
		'success' => true,
		'isInventoryCenterEnabled' => ((sysConfig::get('EXTENSION_INVENTORY_CENTERS_ENABLED') == 'True') ? true : false),
		'inService' => ($centerID === false ? false : true),
		'areaID' => $centerID
	), 'json');
?>