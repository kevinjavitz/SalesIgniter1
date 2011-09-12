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
    $multi_store = $appExtension->getExtension('multiStore');    
    $store = $multi_store->getClosestStoreByZip($zip);        
} else {
    $store = null;
}
    
if (empty($store)) {
    //Store not found
    $url = itw_app_link('', 'index','default');
	$messageStack->addSession('pageStack', 'No store was found for the given zip. You were redirected to our main store.', 'success');
} else {
    $url = 'http://'.$store->stores_domain;
	$messageStack->addSession('pageStack', 'Thank you the closest store to you is '.$store->stores_name.', we have redirected you to this store\'s homepage', 'success');
}


$url .= '/products/all.php';

EventManager::attachActionResponse($url, 'redirect');
