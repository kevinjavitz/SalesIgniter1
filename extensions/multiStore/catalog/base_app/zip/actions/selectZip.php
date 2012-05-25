<?php
$zip = (int)ltrim(rtrim(urldecode($_POST['zipClient'])));
$zipExists = false;
	$module = OrderShippingModules::getModule('zonereservation');

	foreach($module->getMethods() as $methodId => $mInfo){
		if(in_array($zip, $mInfo['zipcodesArr']) && $mInfo['status'] == 'True'){
			$zipExists = true;
			break;
		}
	}

$error = 'Area is not served';
$redirect = '';
if($zipExists){
	$multi_store = $appExtension->getExtension('multiStore');
	$store = $multi_store->getClosestStoreByZip($zip);
	Session::set('zipClient'.$store->stores_id, ltrim(rtrim(urldecode($zip))));
	if($store->stores_id != Session::get('current_store_id')){
		$redirect = 'http://'.$store->stores_domain.'/products/all.php';
		$messageStack->addSession('pageStack', 'Thank you the closest store to you is '.$store->stores_name.', we have redirected you to this store', 'success');
	}
	$error = '';
}
EventManager::attachActionResponse(array(
		'success' => true,
		'error' => $error,
		'redirect' => $redirect
	), 'json');
?>