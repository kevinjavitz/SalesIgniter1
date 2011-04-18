<?php
	$Qreservations = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.OrdersProductsReservation opr')
	->leftJoin('opr.ProductsInventoryBarcodes ib')
	->where('opr.rental_state = ?', 'reserved')
	->andWhere('opr.parent_id IS NULL')
	->andWhere('o.customers_id = ?', $cID)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$htmlTable = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0)
	->addClass('ui-widget')
	->css(array(
		'width' => '100%'
	));

	$htmlTable->addHeaderRow(array(
		'columns' => array(
			array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_ID')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_BARCODE')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_PRODUCTS_NAME')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_START_DATE')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_END_DATE')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_RENTAL_STATUS'))
		)
	));

	if (!$Qreservations){
		$htmlTable->addBodyRow(array(
			'columns' => array(
				array('colspan' => 6, 'addCls' => 'ui-widget-content', 'align' => 'center', 'css' => array('border-top' => 0), 'text' => sysLanguage::get('TEXT_INFO_NO_ONETIME_RENTAL_PENDING'))
			)
		));
	}else{
		foreach($Qreservations as $rInfo){
			foreach($rInfo['OrdersProducts'] as $opInfo){
				$orderedProduct = $opInfo;
				$resInfo = $orderedProduct['OrdersProductsReservation'][0];

				$Qcheck = Doctrine_Query::create()
				->select('barcode_id')
				->from('OrdersProductsReservation opr')
				->leftJoin('opr.ProductsInventoryBarcodes ib')
				->where('parent_id = ?', $resInfo['orders_products_reservations_id'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qcheck){
					$barcodeNum = array();
					foreach($Qcheck as $rcInfo){
						$barcodeNum[] = $rcInfo['ProductsInventoryBarcodes']['barcode'];
					}

					if (sizeof($barcodeNum) > 0){
						$barcodeNum = implode(', ', $barcodeNum);
					}else{
						$barcodeNum = 'N/A';
					}
				}else{
					$barcodeNum = 'Quantity Tracking';
					if (is_null($resInfo['barcode_id']) === false){
						$barcodeNum = $resInfo['ProductsInventoryBarcodes']['barcode'];
					}
				}

				$htmlTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0), 'text' => $resInfo['orders_products_reservations_id']),
						array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $barcodeNum),
						array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $orderedProduct['products_name']),
						array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => date('Y-m-d', strtotime($resInfo['start_date']))),
						array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => date('Y-m-d', strtotime($resInfo['end_date']))),
						array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $resInfo['rental_state'])
					)
				));
			}
		}
	}
	echo $htmlTable->draw();
?>