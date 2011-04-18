<?php
	$Qreservations = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.OrdersProductsReservation opr')
	->leftJoin('opr.ProductsInventoryBarcodes ib')
	->where('opr.rental_state = ?', 'returned')
	->andWhere('o.customers_id = ?', $cID)
	->orderBy('opr.date_returned DESC')
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
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_PRODUCTS_COST')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_SHIPPED')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_RECEIVED')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_STATUS'))
		)
	));

	if (!$Qreservations){
		$htmlTable->addBodyRow(array(
			'columns' => array(
				array('colspan' => 7, 'addCls' => 'ui-widget-content', 'align' => 'center', 'css' => array('border-top' => 0), 'text' => sysLanguage::get('TEXT_INFO_NO_ONETIME_RENTAL_HISTORY'))
			)
		));
	}else{
		foreach($Qreservations as $rInfo){
			foreach($rInfo['OrdersProducts'] as $opInfo){
				$orderedProduct = $opInfo;
				$resInfo = $orderedProduct['OrdersProductsReservation'][0];

				$barcodeNum = 'Quantity Tracking';
				if (is_null($resInfo['barcode_id']) === false){
					$barcodeNum = $resInfo['ProductsInventoryBarcodes']['barcode'];
				}

				$htmlTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0), 'text' => $resInfo['orders_products_reservations_id']),
						array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $barcodeNum),
						array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $orderedProduct['products_name']),
						array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $currencies->format($orderedProduct['final_price'])),
						array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => date('Y-m-d', strtotime($resInfo['date_shipped']))),
						array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => date('Y-m-d', strtotime($resInfo['date_returned']))),
						array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => ($resInfo['broken'] ? sysLanguage::get('TEXT_RECEIVED_BROKEN') : sysLanguage::get('TEXT_RECEIVED_OK')))
					)
				));
			}
		}
	}
	echo $htmlTable->draw();
?>
