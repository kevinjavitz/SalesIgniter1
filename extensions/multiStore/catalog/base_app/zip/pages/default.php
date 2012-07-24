<?php
/*
	Multistore Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

 global $messageStack;
$zip = isset($_GET['zip'])?$_GET['zip']:null;

if (!empty($zip)) {
	$zip =(int)ltrim(rtrim(urldecode($zip)));
    $multi_store = $appExtension->getExtension('multiStore');    
    $store = $multi_store->getClosestStoreByZip($zip);
    if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_ZIPCODES_SHIPPING') == 'True'){
        $module = OrderShippingModules::getModule('zonereservation');
	    $zipExists = false;
		foreach($module->getMethods() as $methodId => $mInfo){

			if(in_array($zip, $mInfo['zipcodesArr']) && $mInfo['status'] == 'True'){
				$zipExists = true;
				break;
			}
		}
    }

} else {
    $store = null;
}
    
if (empty($store)) {
    $url = itw_app_link('', 'index','default');
	//$messageStack->addSession('pageStack', 'No store was found for the given zip. You were redirected to our main store.', 'success');
} else {
	if(!isset($zipExists) || isset($zipExists) && $zipExists == true){
		//Session::set('zipClient'.$store->stores_id, ltrim(rtrim(urldecode($zip))));
        $url = 'http://'.$store->stores_domain;
		$messageStack->addSession('pageStack', 'Thank you the closest store to you is '.$store->stores_name.', we have redirected you to this store\'s homepage', 'success');
	}else{
		$url = 'http://'.$store->stores_domain;
		$messageStack->addSession('pageStack', 'We do not serve the zip code area you are in', 'success');
	}
}

if(!empty($store) /*&& Session::get('current_store_id') == $store->stores_id*/){
	if(!isset($zipExists) || isset($zipExists) && $zipExists == true){
		//Session::set('zipClient'.$store->stores_id, ltrim(rtrim(urldecode($zip))));
		$url .= '/multiStore/zip/default.php?action=selectZip&zipClient='.ltrim(rtrim(urldecode($zip)));
	}else{
		$messageStack->addSession('pageStack', 'We do not serve the zip code area you are in', 'success');
		$url .= '/products/all.php';
	}
}else{
	//$messageStack->addSession('pageStack','We do not serve that location. Please choose a different one');
	//$url = '';
}



tep_redirect($url);

//EventManager::attachActionResponse($url, 'redirect');
