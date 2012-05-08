<?php
$totalShippingCost = -1;
$shippingmodulesInfo = '';
EventManager::notify('OrderTotalShippingProcess', &$totalShippingCost, &$shippingmodulesInfo);
$totals = '';
$totalPrice = $ShoppingCart->showTotal();
if(isset($_POST['pickup_time'])){
	$pickupTime = $_POST['pickup_time'];
	$multiStore = $appExtension->getExtension('multiStore');
	if ($multiStore !== false && $multiStore->isEnabled() === true){
		$QTimeFees = Doctrine_Query::create()
			->from('StoresTimeFees')
			->where('stores_id = ?', Session::get('current_store_id'))
			->andWhere('timefees_id = ?', $pickupTime)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}else{
		$QTimeFees = Doctrine_Query::create()
		->from('PayPerRentalTimeFees')
		->where('timefees_id = ?', $pickupTime)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}

	$totalPrice += $QTimeFees[0]['timefees_fee'];
	$totals .= '<b>Pickup Time - '.$QTimeFees[0]['timefees_name'].': '.$currencies->format($QTimeFees[0]['timefees_fee']).'</b><br/>';
	Session::set('pickupFees_time', $pickupTime);
	Session::set('pickupFees_fee', $QTimeFees[0]['timefees_fee']);
	Session::set('pickupFees_name', $QTimeFees[0]['timefees_name']);
}
if(isset($_POST['delivery_time'])){
	$deliveryTime  = $_POST['delivery_time'];
	$multiStore = $appExtension->getExtension('multiStore');
	if ($multiStore !== false && $multiStore->isEnabled() === true){
		$QTimeFees = Doctrine_Query::create()
			->from('StoresTimeFees')
			->where('stores_id = ?', Session::get('current_store_id'))
			->andWhere('timefees_id = ?', $deliveryTime)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}else{
		$QTimeFees = Doctrine_Query::create()
		->from('PayPerRentalTimeFees')
		->where('timefees_id = ?', $deliveryTime)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}
	$totalPrice += $QTimeFees[0]['timefees_fee'];
	$totals .= '<b>Delivery Time - '.$QTimeFees[0]['timefees_name'].': '.$currencies->format($QTimeFees[0]['timefees_fee']).'</b><br/>';
	Session::set('deliveryFees_time', $deliveryTime);
	Session::set('deliveryFees_fee', $QTimeFees[0]['timefees_fee']);
	Session::set('deliveryFees_name', $QTimeFees[0]['timefees_name']);
}

if(isset($_POST['extrafees_time']) && $_POST['extrafees_time'] > 0){
	$extrafeesTime  = $_POST['extrafees_time'];
	$multiStore = $appExtension->getExtension('multiStore');
	if ($multiStore !== false && $multiStore->isEnabled() === true){
		$QTimeFees = Doctrine_Query::create()
			->from('StoresExtraFees')
			->where('timefees_id = ?', $extrafeesTime)
			->andWhere('stores_id = ?', Session::get('current_store_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}else{
		$QTimeFees = Doctrine_Query::create()
		->from('PayPerRentalExtraFees')
		->where('timefees_id = ?', $extrafeesTime)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}
	if(isset($QTimeFees[0])){
		$totalPrice += $QTimeFees[0]['timefees_fee'];
		$totals .= '<b>Extra Fee - '.$QTimeFees[0]['timefees_name'].': '.$currencies->format($QTimeFees[0]['timefees_fee']).'</b><br/>';
		Session::set('extraFees_time', $extrafeesTime);
		Session::set('extraFees_fee', $QTimeFees[0]['timefees_fee']);
		Session::set('extraFees_name', $QTimeFees[0]['timefees_name']);
	}
}elseif(isset($_POST['extrafees_time']) && $_POST['extrafees_time'] <= 0){
	Session::remove('extraFees_time');
}
	$multiStore = $appExtension->getExtension('multiStore');
	if ($multiStore !== false && $multiStore->isEnabled() === true){
		$QExtraFees = Doctrine_Query::create()
			->from('StoresExtraFees')
			->where('timefees_mandatory = ?', '1')
			->andWhere('stores_id = ?', Session::get('current_store_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}else{
		$QExtraFees = Doctrine_Query::create()
		->from('PayPerRentalExtraFees')
		->where('timefees_mandatory = ?', '1')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}
if(count($QExtraFees) > 0){
	foreach($QExtraFees as $extraFee){
		if($extraFee['timefees_hours'] == 0){
			$totalPrice += $extraFee['timefees_fee'];
			$totals .= '<b>'.$extraFee['timefees_name'].': '.$currencies->format($extraFee['timefees_fee']).'</b><br/>';
			continue;
		}
		foreach($ShoppingCart->getProducts() as $cartProduct) {
			$pID_string = $cartProduct->getIdString();
			$purchaseType = $cartProduct->getPurchaseType();
			$purchaseQuantity = $cartProduct->getQuantity();
			if($purchaseType == 'reservation' && $cartProduct->hasInfo('reservationInfo')){
				$pInfo = $cartProduct->getInfo('reservationInfo');
				$startDate = $pInfo['start_date'];
				$diffHours = floor((strtotime($startDate) - time())/3600);
				if($diffHours < $extraFee['timefees_hours']){
					$totalPrice += $extraFee['timefees_fee'];
					$totals .= '<b>'.$extraFee['timefees_name'].': '.$currencies->format($extraFee['timefees_fee']).'</b><br/>';
					break;
				}
			}
		}
	}
}



if ($totalShippingCost >= 0){
	$totals .= '<b>'.$shippingmodulesInfo.': '.$currencies->format($totalShippingCost).'</b><br/>';
	//$totalPrice -= $totalShippingCost;
}
$totals .= '<b>'. sysLanguage::get('SUB_TITLE_SUB_TOTAL').'&nbsp;'. $currencies->format($totalPrice).'</b>';
$html ='<div class="main" style="text-align:right;"><span class="smallText" style="float:left;"></span>'. $totals.'</div><div style="clear:both;"></div>';

	EventManager::attachActionResponse(array(
		'success' => true,
		'html' => $html
	), 'json');
?>