<?php
	$Barcode = Doctrine_Core::getTable('ProductsInventoryBarcodes')->findOneByBarcodeId((int)$_GET['bID']);
	if ($Barcode){
		if ($Barcode->status == 'O'){
			$response = array(
				'success' => true,
				'errorMsg' => sysLanguage::get('TEXT_BARCODE_OUT')
			);
		}elseif ($Barcode->status == 'P'){
			$response = array(
				'success' => true,
				'errorMsg' => sysLanguage::get('TEXT_BARCODE_PURCHASED')
			);
		}else{
			$Qproducts = Doctrine_Query::create()
			->from('ProductsInventoryBarcodes pib')
			->leftJoin('pib.OrdersProductsReservation opr')
			->where('pib.barcode_id=?', $_GET['bID'])
			->andWhere('opr.start_date >= ?', date('Y-m-d'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if(count($Qproducts) > 0){
				$response = array(
					'success' => true,
					'errorMsg' => sysLanguage::get('TEXT_FUTURE_RESERVATION')
				);
			}else{
				$Barcode->delete();
				$response = array('success' => true);
			}
		}
	}else{
		$response = array('success' => false);
	}
	EventManager::attachActionResponse($response, 'json');
?>