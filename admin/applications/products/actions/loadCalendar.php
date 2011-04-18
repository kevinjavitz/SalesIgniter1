<?php
	$month = $_GET['month'];
	$year = $_GET['year'];
	$productId = $_GET['products_id'];
	$purchaseType = $_GET['purchase_type'];
	$trackMethod = 'barcode';
	$barcodeId = (isset($_GET['barcode_id']) ? $_GET['barcode_id'] : false);
	if (isset($_GET['stores_id'])){
		$storeId = (int)$_GET['stores_id'];
	}

	if (isset($_GET['inventory_center_id'])){
		$inventoryCenterId = (int)$_GET['inventory_center_id'];
	}

	$calHtml = htmlBase::newElement('calendar')->attr('purchase_type', $purchaseType)->dateNow($month, $year)->draw();
	//phpQuery::$debug = 2;
	$calDom = phpQuery::newDocument($calHtml);
	foreach(pq('.htmlcal-day', $calDom) as $idx => $calDay){
		$el = pq($calDay);
		if ($el->hasClass('htmlcal-day-unselectable') === false){
			$eventsTable = htmlBase::newElement('table')->setCellPadding(2)->setCellSpacing(0);

			$day = $el->attr('day');
			$queryDate = date('Y-m-d', mktime(0,0,0,$month,$day,$year));
			$available = 0;
			$reserved = 0;
			$out = 0;
			if ($trackMethod == 'barcode'){
				if ($purchaseType == 'reservation'){
					$Qbarcodes = Doctrine_Query::create()
					->select('i.inventory_id, b.barcode_id, b.barcode')
					->from('ProductsInventory i')
					->leftJoin('i.ProductsInventoryBarcodes b')
					->where('i.products_id = ?', $productId)
					->andWhere('i.type = ?', $purchaseType)
					->andWhere('i.track_method = ?', $trackMethod);
					if ($barcodeId !== false){
						$Qbarcodes->andWhere('b.barcode_id = ?', $barcodeId);
					}
					if (isset($storeId)){
						$Qbarcodes->leftJoin('b.ProductsInventoryBarcodesToStores b2s')
						->andWhere('b2s.inventory_store_id = ?', $storeId);
					}
					if (isset($inventoryCenterId)){
						$Qbarcodes->leftJoin('b.ProductsInventoryBarcodesToInventoryCenters b2c')
						->andWhere('b2c.inventory_center_id = ?', $inventoryCenterId);
					}
					$Result = $Qbarcodes->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if ($Result){
						$eventsTable->addHeaderRow(array(
							'columns' => array(
								array('addCls' => 'smallText', 'text' => 'Customer'),
								array('addCls' => 'smallText', 'text' => 'Qty'),
								array('addCls' => 'smallText', 'text' => 'Dates'),
								array('addCls' => 'smallText', 'text' => 'Barcode'),
								array('addCls' => 'smallText', 'text' => 'Order')
							)
						));
						$available = sizeof($Result[0]['ProductsInventoryBarcodes']);
						foreach($Result[0]['ProductsInventoryBarcodes'] as $bInfo){
							$Qbookings = Doctrine_Query::create()
							->select('op.barcode_id, opr.rental_state, o.customers_id, o.orders_id, opr.start_date, opr.end_date, opr.shipping_days')
							->from('Orders o')
							->leftJoin('o.OrdersProducts op')
							->leftJoin('op.OrdersProductsReservation opr')
							->where('CAST("' . $queryDate . '" as DATE) BETWEEN opr.start_date AND opr.end_date')
							->andWhere('op.barcode_id = ?', $bInfo['barcode_id'])
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							if ($Qbookings){
								foreach($Qbookings as $rbInfo){
									$firstname = 'Unknown';
									$lastname = '';
									
									$Qcustomer = Doctrine_Query::create()
									->select('customers_firstname, customers_lastname')
									->from('Customers')
									->where('customers_id = ?', $rbInfo['customers_id'])
									->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
									if ($Qcustomer){
										$firstname = $Qcustomer[0]['customers_firstname'];
										$lastname = $Qcustomer[0]['customers_lastname'];
									}

									$eventsTable->addBodyRow(array(
										'columns' => array(
											array('addCls' => 'smallText', 'text' => $firstname . ' ' . $lastname),
											array('addCls' => 'smallText', 'text' => '1'),
											array('addCls' => 'smallText', 'text' => tep_date_short($rbInfo['OrdersProducts'][0]['OrdersProductsReservation'][0]['start_date']) . ' - ' . tep_date_short($rbInfo['OrdersProducts'][0]['OrdersProductsReservation'][0]['end_date'])),
											array('addCls' => 'smallText', 'text' => $bInfo['OrdersProducts'][0]['barcode']),
											array('addCls' => 'smallText', 'text' => '<a href="' . itw_app_link('orders_id=' . $rbInfo['orders_id'], 'orders', 'details') . '" style="text-decoration:underline;">View</a>')
										)
									));
									if ($rbInfo['OrdersProducts'][0]['OrdersProductsReservation'][0]['rental_state'] == 'reserved'){
										$reserved++;
										$available--;
									}elseif ($rbInfo['OrdersProducts'][0]['OrdersProductsReservation'][0]['rental_state'] == 'out'){
										$out++;
										$available--;
									}elseif ($rbInfo['OrdersProducts'][0]['OrdersProductsReservation'][0]['rental_state'] == 'broken'){
										$broken++;
										$available--;
									}
								}
							}
						}
					}

					$el->append('<div><table cellspacing="0" cellpadding="1" border="0" class="htmlcal-inventory-info">' .
						'<tr>' .
							'<td>Avail: </td>' .
							'<td>' . $available . '</td>' .
						'</tr>' .
						'<tr>' .
							'<td>Res: </td>' .
							'<td>' . $reserved . '</td>' .
						'</tr>' .
						'<tr>' .
							'<td>Out: </td>' .
							'<td>' . $out . '</td>' .
						'</tr>' .
					'</table></div>');
				}else{
					$Qbarcodes = Doctrine_Query::create()
					->select('i.inventory_id, b.barcode_id, b.barcode, b.status')
					->from('ProductsInventory i')
					->leftJoin('i.ProductsInventoryBarcodes b')
					->where('i.products_id = ?', $productId)
					->andWhere('i.type = ?', $purchaseType)
					->andWhere('i.track_method = ?', $trackMethod);
					if ($barcodeId !== false){
						$Qbarcodes->andWhere('barcode_id = ?', $barcodeId);
					}
					if (isset($storeId)){
						$Qbarcodes->leftJoin('b.ProductsInventoryBarcodesToStores b2s')
						->andWhere('b2s.stores_id = ?', $storeId);
					}
					if (isset($inventoryCenterId)){
						$Qbarcodes->leftJoin('b.ProductsInventoryBarcodesToInventoryCenters b2c')
						->andWhere('b2c.inventory_center_id = ?', $inventoryCenterId);
					}
					$Result = $Qbarcodes->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if ($Result){
						$eventsTable->addHeaderRow(array(
							'columns' => array(
								array('addCls' => 'smallText', 'text' => 'Customer'),
								array('addCls' => 'smallText', 'text' => 'Qty'),
								array('addCls' => 'smallText', 'text' => 'Shipped Date'),
								array('addCls' => 'smallText', 'text' => 'Barcode')
							)
						));
						$available = sizeof($Result[0]['ProductsInventoryBarcodes']);
						foreach($Result[0]['ProductsInventoryBarcodes'] as $bInfo){
							$QrentedProducts = Doctrine_Query::create()
							->select('*')
							->from('RentedProducts')
							->where('products_barcode = ?', $bInfo['barcode_id'])
							->andWhere('CAST("' . $queryDate . '" as DATE) BETWEEN shipment_date AND return_date')
							->andWhere('products_id = ?', $productId)
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							if ($QrentedProducts){
								foreach($QrentedProducts as $pInfo){
									$out++;
									$Qcustomer = Doctrine_Query::create()
									->select('customers_firstname, customers_lastname')
									->from('Customers')
									->where('customers_id = ?', $pInfo['customers_id'])
									->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

									$eventsTable->addBodyRow(array(
										'columns' => array(
											array('addCls' => 'smallText', 'text' => $Qcustomer[0]['customers_firstname'] . ' ' . $Qcustomer[0]['customers_lastname']),
											array('addCls' => 'smallText', 'text' => '1'),
											array('addCls' => 'smallText', 'text' => tep_date_short($pInfo['shipment_date'])),
											array('addCls' => 'smallText', 'text' => $bInfo['barcode']),
											array('addCls' => 'smallText', 'text' => '')
										)
									));
								}
							}
						}
					}
					
					$el->append('<div><table cellspacing="0" cellpadding="1" border="0" class="htmlcal-inventory-info">' .
						'<tr>' .
							'<td>Avail: </td>' .
							'<td>' . $available . '</td>' .
						'</tr>' .
						'<tr>' .
							'<td>Out: </td>' .
							'<td>' . $out . '</td>' .
						'</tr>' .
					'</table></div>');
				}
			}else{
			}

			if ($reserved > 0 || $out > 0){
				$el->addClass('date_has_popup')->append('<div class="events">' .
					'<ul>' .
						'<li>' .
							$eventsTable->draw() .
						'</li>' .
					'</ul>' .
				'</div>');
			}
		}
	}
	$newHtml = $calDom->htmlOuter();
	EventManager::attachActionResponse($newHtml, 'html');
?>