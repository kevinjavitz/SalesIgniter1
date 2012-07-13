<?php
$isRemove = false;
foreach ($ShoppingCart->getProducts() as $cartProduct){
		if ($cartProduct->hasInfo('reservationInfo')){
			$pInfo = $cartProduct->getInfo();
			$pID = $cartProduct->getIdString();
			if (isset($_POST['insure_all_products']) || (isset($_POST['insure_product']) && array_search($pID, $_POST['insure_product']) !== false)){
				$payPerRentals = Doctrine_Query::create()
						         ->select('insurance')
				   				 ->from('ProductsPayPerRental')
								 ->where('products_id = ?', $pID)
								 ->fetchOne();

				/*if (!isset($pInfo['reservationInfo']['insurance']) || (isset($pInfo['reservationInfo']['insurance']) && $pInfo['reservationInfo']['insurance'] == 0)){
					$pInfo['reservationInfo']['insurance'] = $payPerRentals->insurance;//getInsurance from db
					$isRemove = true;
				}else{
					$pInfo['reservationInfo']['insurance'] = 0;
					$isRemove = false;
				}
				$ShoppingCart->updateProduct($cartProduct->getUniqID(), $pInfo);*/

                $pInfo['reservationInfo']['insurance'] = $payPerRentals->insurance;//getInsurance from db
                $isRemove = true;
            }else{
                $pInfo['reservationInfo']['insurance'] = 0;
                $isRemove = false;
            }

            $ShoppingCart->updateProduct($cartProduct->getUniqID(), $pInfo);
		}
	}

    ob_start();
	require(sysConfig::getDirFsCatalog() . 'applications/checkout/pages/cart.php');
	$pageHtml = ob_get_contents();
	ob_end_clean();

	EventManager::attachActionResponse(array(
		'success' => true,
		'pageHtml' => $pageHtml,
		'isRemove' => $isRemove
	), 'json');

?>