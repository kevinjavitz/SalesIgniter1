<?php
	foreach ($ShoppingCart->getProducts() as $cartProduct){
		if ($cartProduct->hasInfo('reservationInfo')){
			$pInfo = $cartProduct->getInfo();
			$pID = $cartProduct->getIdString();
			if ($pInfo['reservationInfo']['start_date'] == $_GET['start_date'] && $pInfo['reservationInfo']['end_date'] == $_GET['end_date'] && $pID == $_GET['pID']){

				if( isset($pInfo['reservationInfo']['insurance']) && $pInfo['reservationInfo']['insurance'] > 0){
					$pInfo['reservationInfo']['insurance'] = 0;
				}else{
					$payPerRentals = Doctrine_Query::create()
							         ->select('insurance')
									->from('ProductsPayPerRental')
									->where('products_id = ?', $pID)
									->fetchOne();
					$pInfo['reservationInfo']['insurance'] = $payPerRentals->insurance;//getInsurance from db
				}
				$ShoppingCart->updateProduct($cartProduct->getUniqID(), $pInfo);
			}
		}
	}
	
	EventManager::attachActionResponse(itw_app_link(null,'checkout','default',$request_type), 'redirect');
?>