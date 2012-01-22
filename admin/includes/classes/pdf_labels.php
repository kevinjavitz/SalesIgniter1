<?php
require_once('../tcpdf/config/tcpdf_config.php');
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

class PDF_Labels
{

	/**
	 * @var TCPDF
	 */
	private $pdf;

	/**
	 * @var array
	 */
	private $startLocation = array(
		'row' => 1,
		'col' => 1
	);

	/**
	 * @var array
	 */
	private $layoutInfo = array();

	function PDF_Labels() {
		$this->setStartDate(date('Y-m-d'));
		$this->setEndDate(date('Y-m-d'));
		$this->setFilter('All');
		$this->labels = array();
	}

	function setStartDate($val) { $this->startDate = $val; }

	function setEndDate($val) { $this->endDate = $val; }

	function setFilter($val) { $this->filter = $val; }

	function setLabelsType($val) { $this->labelsType = $val; }

	function setLabelLocation($val) { $this->labelLocation = $val; }

	function setInventoryCenter($val) { $this->invCenter = $val; }

	public function setStartLocation($row, $col) {
		$this->startLocation = array(
			'row' => $row,
			'col' => $col
		);
	}

	function getRentedQueueQuery($settings = null) {
		$query = Doctrine_Query::create()
		->select('ab.*, co.*, z.*, rq.customers_queue_id, rq.shipment_date as date_shipped, concat(c.customers_firstname, " ", c.customers_lastname) as customers_name, p.products_id, pd.products_name, "rental" as products_type, c.customers_id, co.countries_id, ib.barcode_id, ib.barcode')
			->from('RentedQueue rq')
			->leftJoin('rq.ProductsInventoryBarcodes ib')
			->leftJoin('rq.Customers c')
			->leftJoin('c.AddressBook ab')
			->leftJoin('ab.Countries co')
			->leftJoin('ab.Zones z')
			->leftJoin('rq.Products p')
			->leftJoin('p.ProductsDescription pd')
			->where('pd.language_id = ?', Session::get('languages_id'))
			->andWhere('ab.address_book_id = c.customers_delivery_address_id or c.customers_delivery_address_id is null')
			->orderBy('rq.shipment_date asc, pd.products_name asc');

		if (isset($settings['startDate']) && isset($settings['endDate'])){
			$query->andWhere('rq.shipment_date between "' . $settings['startDate'] . ' 00:00:00" and "' . $settings['endDate'] . ' 23:59:59"');
		}

		if (isset($settings['queueId'])){
			$query->andWhere('rq.customers_queue_id = ?', $settings['queueId']);
		}

		return $this->getRentedProducts($settings);
		return $query;
	}

	public function getRentedProducts($settings = null) {
		$query = Doctrine_Query::create()
		->select('ab.*, co.*, z.*, rq.customers_queue_id, rq.shipment_date, concat(c.customers_firstname, " ", c.customers_lastname) as customers_name, p.products_id, pd.products_name, c.customers_id, co.countries_id, ib.barcode_id, ib.barcode')
			->from('RentedQueue rq')
			->leftJoin('rq.ProductsInventoryBarcodes ib')
			->leftJoin('rq.Customers c')
			->leftJoin('c.AddressBook ab')
			->leftJoin('ab.Countries co')
			->leftJoin('ab.Zones z')
			->leftJoin('rq.Products p')
			->leftJoin('p.ProductsDescription pd')
			->where('pd.language_id = ?', Session::get('languages_id'))
			->andWhere('ab.address_book_id = c.customers_delivery_address_id or c.customers_delivery_address_id is null')
			->orderBy('rq.shipment_date asc, pd.products_name asc');

		if (isset($settings['startDate']) && isset($settings['endDate'])){
			$query->andWhere('rq.shipment_date between "' . $settings['startDate'] . ' 00:00:00" and "' . $settings['endDate'] . ' 23:59:59"');
		}

		if (isset($settings['queueId'])){
			$query->andWhere('rq.customers_queue_id = ?', $settings['queueId']);
		}

		$Result = $query->execute();

		$dataArray = array();
		if ($Result->count() > 0){
			$idx = 0;
			foreach($Result->toArray(true) as $Queue){
				if (!empty($Queue['Products'])){
					$descInfo = $Queue['Products']['ProductsDescription'][Session::get('languages_id')];
					$Address = $Queue['Customers']['AddressBook'][0];

					$dataArray[$idx] = array(
						'customers_name'   => $Queue['customers_name'],
						'addressFormatted' => tep_address_format($Address['Countries']['address_format_id'], $Address, true, '', '<br />', 'short'),
						'products_name'    => $descInfo['products_name'],
						'products_type'    => 'rental',
						'date_sent'        => $Queue['shipment_date'],
						'checkbox_value'   => 'rental_' . $Queue['customers_queue_id']
					);

					if(sysConfig::get('EXTENSION_CUSTOM_FIELDS_SHOW_DOWNLOAD_FILE_COLUMN') == 'True'){
						$Qfields = Doctrine_Query::create()
							->select('group_id')
							->from('ProductsCustomFieldsGroupsToProducts')
							->where('product_id = ?', $Queue['Products']['products_id'])
							->execute(array(), Doctrine::HYDRATE_ARRAY);

						$QfieldsProduct = Doctrine_Query::create()
							->from('ProductsCustomFields f')
							->leftJoin('f.ProductsCustomFieldsDescription fd')
							->leftJoin('f.ProductsCustomFieldsToGroups f2g')
							->where('f2g.group_id = ?', $Qfields[0]['group_id'])
							->andWhere('f.input_type = ?', 'upload')
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						if(isset($QfieldsProduct[0])){
							$Qvalue = Doctrine_Query::create()
								->select('value')
								->from('ProductsCustomFieldsToProducts')
								->where('product_id = ?', $Queue['Products']['products_id'])
								->andWhere('field_id = ?', $QfieldsProduct[0]['field_id'])
								->fetchOne();
							if($Qvalue){
								$dataArray[$idx]['filename'] = stripslashes($Qvalue['value']);
							}
						}
					}

					if (isset($Queue['ProductsInventoryBarcodes']) && !empty($Queue['ProductsInventoryBarcodes'])){
						$dataArray[$idx]['barcode_id'] = $Queue['ProductsInventoryBarcodes']['barcode_id'];
						$dataArray[$idx]['barcode'] = $Queue['ProductsInventoryBarcodes']['barcode'];
						$dataArray[$idx]['barcode_type'] = 'Code128Auto';
					}

					if (isset($Queue['ProductsInventoryQuantity']) && !empty($Queue['ProductsInventoryQuantity'])){
						$dataArray[$idx]['quantity_id'] = $Queue['ProductsInventoryQuantity']['quantity_id'];
					}
				}
				$idx++;
			}
		}

		return $dataArray;
	}

	function getPayPerRentalQuery($settings = null) {
		$query = Doctrine_Query::create()
		->select('ib.barcode_id, ib.barcode, oa.*, opr.orders_products_reservations_id as reservation_id, opr.date_shipped as date_shipped, opr.barcode_id, opr.quantity_id, oa.entry_name as customers_name, op.products_name, op.products_id, "reservation" as products_type, o.customers_id, p.products_id, pd.products_description')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsReservation opr')
			->leftJoin('opr.ProductsInventoryBarcodes ib')
			->leftJoin('op.Products p')
			->leftJoin('p.ProductsDescription pd')
			->leftJoin('o.OrdersAddresses oa')
			->where('oa.address_type = ?', 'delivery')
			->orderBy('opr.date_shipped asc, op.products_name asc');

		if (isset($settings['startDate']) && isset($settings['endDate'])){
			$query->andWhere('opr.date_shipped BETWEEN CAST("' . $settings['startDate'] . '" as DATE) AND CAST("' . $settings['endDate'] . '" as DATE)');
		}

		if (isset($settings['bookingId'])){
			$query->andWhere('opr.orders_products_reservations_id = ?', $settings['bookingId']);
		}
		else {
			$query->andWhere('opr.parent_id is null');
		}

		EventManager::notify('OrdersListingBeforeExecute', &$query);

		return $this->getPayPerRentals($settings);
		return $query;
	}

	public function getPayPerRentals($settings = null) {
		$query = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsReservation opr')
			->leftJoin('opr.ProductsInventoryBarcodes ib')
			->leftJoin('o.OrdersAddresses oa')
			->where('oa.address_type = ?', 'delivery')
			->orderBy('opr.date_shipped asc, op.products_name asc');

		/*if (isset($settings['startDate']) && isset($settings['endDate'])){
			$query->andWhere('opr.date_shipped BETWEEN CAST("' . $settings['startDate'] . '" as DATE) AND CAST("' . $settings['endDate'] . '" as DATE)');
		} */

		if (isset($settings['bookingId'])){
			$query->andWhere('opr.orders_products_reservations_id = ?', $settings['bookingId']);
		}
		else {
			$query->andWhere('opr.parent_id is null');
		}

		EventManager::notify('OrdersListingBeforeExecute', &$query);

		$Result = $query->execute();

		$dataArray = array();
		if ($Result->count() > 0){
			$idx = 0;
			foreach($Result->toArray(true) as $Order){
				foreach($Order['OrdersProducts'] as $OrderProduct){
					if (!empty($OrderProduct['OrdersProductsReservation'])){
						$resInfo = $OrderProduct['OrdersProductsReservation'][0];
						$Address = $Order['OrdersAddresses']['delivery'];

						$dataArray[$idx] = array(
							'customers_name'   => $Address['entry_name'],
							'addressFormatted' => tep_address_format($Address['entry_format_id'], $Address, true, '', '<br />', 'short'),
							'products_name'    => $OrderProduct['products_name'],
							'products_type'    => 'reservation',
							'date_sent'        => $resInfo['date_shipped'],
							'checkbox_value'   => 'reservation_' . $resInfo['orders_products_reservations_id']
						);

						if(sysConfig::get('EXTENSION_CUSTOM_FIELDS_SHOW_DOWNLOAD_FILE_COLUMN') == 'True'){
							$Qfields = Doctrine_Query::create()
								->select('group_id')
								->from('ProductsCustomFieldsGroupsToProducts')
								->where('product_id = ?', $OrderProduct['products_id'])
								->execute(array(), Doctrine::HYDRATE_ARRAY);
							$QfieldsProduct = Doctrine_Query::create()
								->from('ProductsCustomFields f')
								->leftJoin('f.ProductsCustomFieldsDescription fd')
								->leftJoin('f.ProductsCustomFieldsToGroups f2g')
								->where('f2g.group_id = ?', $Qfields[0]['group_id'])
								->andWhere('f.input_type = ?', 'upload')
								->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							if(isset($QfieldsProduct[0])){
								$Qvalue = Doctrine_Query::create()
									->select('value')
									->from('ProductsCustomFieldsToProducts')
									->where('product_id = ?', $OrderProduct['products_id'])
									->andWhere('field_id = ?', $QfieldsProduct[0]['field_id'])
									->fetchOne();
								if($Qvalue){
									$dataArray[$idx]['filename'] = stripslashes($Qvalue['value']);
								}
							}
						}


						if (isset($resInfo['ProductsInventoryBarcodes']) && !empty($resInfo['ProductsInventoryBarcodes'])){
							$dataArray[$idx]['barcode_id'] = $resInfo['ProductsInventoryBarcodes']['barcode_id'];
							$dataArray[$idx]['barcode'] = $resInfo['ProductsInventoryBarcodes']['barcode'];
							$dataArray[$idx]['barcode_type'] = 'Code128Auto';
						}

						if (isset($resInfo['ProductsInventoryQuantity']) && !empty($resInfo['ProductsInventoryQuantity'])){
							$dataArray[$idx]['quantity_id'] = $resInfo['ProductsInventoryQuantity']['quantity_id'];
						}
					}
					$idx++;
				}
			}
		}
		return $dataArray;
	}

	function runListingQuery() {
		global $appExtension;
		$queries['rental'] = $this->getRentedQueueQuery(array(
			'startDate' => $this->startDate,
			'endDate'   => $this->endDate
		));

		$queries['onetime'] = $this->getPayPerRentalQuery(array(
			'startDate' => $this->startDate,
			'endDate'   => $this->endDate
		));

		$sqlResources = array();
		switch($this->filter){
			case 'all':
				//$sqlResources[] = $queries['rental']->execute();
				$sqlResources[] = $queries['rental'];
				if ($appExtension->isEnabled('payPerRentals')){
					//$sqlResources[] = $queries['onetime']->execute();
					$sqlResources[] = $queries['onetime'];
				}
				break;
			case 'onetime':
			case 'rental':
				//$sqlResources[] = $queries[$this->filter]->execute();
				$sqlResources[] = $queries[$this->filter];
				break;
		}

		foreach($sqlResources as $pTypeData){
			foreach($pTypeData as $itemData){
				$inventoryCenterName = 'None';
				if ($appExtension->isEnabled('inventoryCenters')){
					if (isset($this->invCenter) && $this->invCenter != ''){
						$QinventoryCenter = Doctrine_Query::create()
							->select('inventory_center_name')
							->from('ProductsInventoryCenters')
							->where('inventory_center_id = ?', $this->invCenter)
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						$inventoryCenterName = $QinventoryCenter[0]['inventory_center_name'];

						$Result = false;
						if (!empty($itemData['barcode_id']) || !empty($itemData['quantity_id'])){
							$Qcheck = Doctrine_Query::create()
								->select('count(*) as total');
							if (!empty($itemData['barcode_id'])){
								$Qcheck->from('ProductsInventoryBarcodesToInventoryCenters')
									->where('barcode_id = ?', $itemData['barcode_id']);
							}
							elseif (!empty($itemData['quantity_id'])) {
								$Qcheck->from('ProductsInventoryQuantity')
									->where('quantity_id = ?', $itemData['quantity_id']);
							}
							$Qcheck->andWhere('inventory_center_id = ?', $this->invCenter);

							$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						}

						if ($Result && $Result[0]['total'] <= 0){
							continue;
						}
					}
					else {
						$QinventoryCenter = Doctrine_Query::create()
							->select('ic.inventory_center_name')
							->from('ProductsInventoryCenters ic');

						if (isset($itemData['barcode_id']) || isset($itemData['quantity_id'])){
							if (isset($itemData['barcode_id'])){
								$QinventoryCenter->leftJoin('ic.ProductsInventoryBarcodesToInventoryCenters b2c')
									->where('b2c.barcode_id = ?', $itemData['barcode_id']);
							}
							else {
								$QinventoryCenter->leftJoin('ic.ProductsInventoryQuantity iq')
									->where('iq.quantity_id = ?', $itemData['quantity_id']);
							}

							$Result = $QinventoryCenter->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							if ($Result){
								$inventoryCenterName = $Result[0]['inventory_center_name'];
							}
							else {
								$inventoryCenterName = 'None';
							}
						}
						else {
							$inventoryCenterName = 'Package Product';
						}
					}
				}
				$myArray = array(
					'customers_name'   => $itemData['customers_name'],
					'addressFormatted' => $itemData['addressFormatted'],
					'products_name'    => $itemData['products_name'],
					'products_type'    => $itemData['products_type'],
					'date_sent'        => $itemData['date_sent'],
					'barcode'          => (isset($itemData['barcode']) ? $itemData['barcode'] : 'Quantity Tracking'),
					'barcode_type'     => (isset($itemData['type']) ? $itemData['type'] : 'Code128Auto'),
					'inventory_center' => $inventoryCenterName
				);
				if(sysConfig::get('EXTENSION_CUSTOM_FIELDS_SHOW_DOWNLOAD_FILE_COLUMN') == 'True'){
					$myArray['filename'] = $itemData['filename'];
				}
				$myArray['checkbox_value'] = $itemData['checkbox_value'];
				$this->listingData[] = $myArray;
			}
		}
		/*
		$index = 0;
		foreach($sqlResources as $Qrental){
			foreach($Qrental->toArray(true) as $rental){
				//print_r($rental);
				$inventoryCenterName = 'None';
				if ($appExtension->isEnabled('inventoryCenters')){
					if (isset($this->invCenter) && $this->invCenter != ''){
						$QinventoryCenter = Doctrine_Query::create()
						->select('inventory_center_name')
						->from('ProductsInventoryCenters')
						->where('inventory_center_id = ?', $this->invCenter)
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						$inventoryCenterName = $QinventoryCenter[0]['inventory_center_name'];

						unset($Qcheck);
						if (tep_not_null($rental['barcode_id'])){
							$Qcheck = Doctrine_Query::create()
							->select('count(*) as total')
							->from('ProductsInventoryBarcodesToInventoryCenters')
							->where('barcode_id = ?', $rental['barcode_id'])
							->andWhere('inventory_center_id = ?', $this->invCenter)
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						}elseif (tep_not_null($rental['quantity_id'])){
							$Qcheck = Doctrine_Query::create()
							->select('count(*) as total')
							->from('ProductsInventoryQuantity')
							->where('quantity_id = ?', $rental['quantity_id'])
							->andWhere('inventory_center_id = ?', $this->invCenter)
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						}
						if ($Qcheck && $Qcheck[0]['total'] <= 0){
							continue;
						}
					}else{
						if (isset($rental['ProductsInventoryBarcodes'])){
							$QinventoryCenter = Doctrine_Query::create()
							->select('ic.inventory_center_name')
							->from('ProductsInventoryCenters ic')
							->leftJoin('ic.ProductsInventoryBarcodesToInventoryCenters b2c')
							->where('b2c.barcode_id = ?', $rental['ProductsInventoryBarcodes']['barcode_id'])
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						}elseif (isset($rental['ProductsInventoryQuantity'])){
							$QinventoryCenter = Doctrine_Query::create()
							->select('ic.inventory_center_name')
							->from('ProductsInventoryCenters ic')
							->leftJoin('ic.ProductsInventoryQuantity iq')
							->where('iq.quantity_id = ?', $rental['ProductsInventoryQuantity']['quantity_id'])
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						}else{
							$QinventoryCenter = array(array('inventory_center_name' => 'Package Product'));
						}
						if ($QinventoryCenter){
							$inventoryCenterName = $QinventoryCenter[0]['inventory_center_name'];
						}
					}
				}

				if (isset($rental['ProductsInventoryBarcodes'])){
					$Qbarcode = Doctrine_Query::create()
					->select('barcode')
					->from('ProductsInventoryBarcodes')
					->where('barcode_id = ?', $rental['ProductsInventoryBarcodes']['barcode_id'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					$barcodeNum = $Qbarcode[0]['barcode'];
					$barcodeType = 'Code128Auto';
				}else{
					$barcodeNum = 'Quantity Tracking';
					$barcodeType = '';
				}

				$this->listingData[$index] = array(
					'customers_name'   => $rental['customers_name'],
					'addressFormatted' => '',
					'products_name'    => '',
					'products_type'    => $rental['products_type'],
					'date_sent'        => (isset($rental['shipment_date']) ? $rental['shipment_date'] : $rental['date_shipped']),
					'barcode'          => $barcodeNum,
					'barcode_type'     => $barcodeType,
					'inventory_center' => $inventoryCenterName,
					'checkbox_value'   => ''
				);

				if (isset($rental['reservation_id'])){
					$Address = $rental['OrdersAddresses']['delivery'];
					$this->listingData[$index]['addressFormatted'] = tep_address_format($Address['entry_format_id'], $Address, true, '', '<br />');
					$this->listingData[$index]['products_name'] = $rental['OrdersProducts'][0]['products_name'];
					$this->listingData[$index]['checkbox_value'] = 'reservation_' . $rental['reservation_id'];
				}else{
					$Address = $rental['Customers']['AddressBook'][0];
					$this->listingData[$index]['addressFormatted'] = tep_address_format($Address['Countries']['address_format_id'], $Address, true, '', '<br />');
					$this->listingData[$index]['products_name'] = $rental['Products']['ProductsDescription'][Session::get('languages_id')]['products_name'];
					$this->listingData[$index]['checkbox_value'] = 'rental_' . $rental['customers_queue_id'];
				}

				$index++;
			}
		}
		*/
	}

	function parseListingData($type = 'html') {
		global $typeNames;
		if (isset($this->listingData) && sizeof($this->listingData) > 0){
			switch($type){
				case 'json':
					$returnArray = array();
					foreach($this->listingData as $listingInfo){
						$myArray = array(
							$listingInfo['customers_name'],
							$listingInfo['addressFormatted'],
							htmlspecialchars(stripslashes($listingInfo['products_name']), ENT_QUOTES),
							$listingInfo['barcode'],
							$listingInfo['inventory_center'],
							$typeNames[$listingInfo['products_type']],
							tep_date_short($listingInfo['date_sent'])
						);

						if(sysConfig::get('EXTENSION_CUSTOM_FIELDS_SHOW_DOWNLOAD_FILE_COLUMN') == 'True'){
							$myArray[] = '<a href="'.sysConfig::getDirWsCatalog().'images/'.$listingInfo['filename'].'">'.$listingInfo['filename'].'</a>';
						}
						$myArray[] = $listingInfo['checkbox_value'];
						$returnArray[] = $myArray;
						/*$returnArray[] = '[' .
						'"' . $listingInfo['customers_name'] . '", ' .
						'"' . $listingInfo['addressFormatted'] . '", ' .
						'"' . htmlspecialchars(stripslashes($listingInfo['products_name']), ENT_QUOTES) . '", ' .
						'"' . $listingInfo['barcode'] . '", ' .
						'"' . $listingInfo['inventory_center'] . '", ' .
						'"' . $typeNames[$listingInfo['products_type']] . '", ' .
						'"' . tep_date_short($listingInfo['date_sent']) . '", ' .
						'"' . $listingInfo['checkbox_value'] . '"' .
						']';*/
					}
					//$return = '[' . implode(',', $returnArray) . ']';
					$return = $returnArray;
					break;
				case 'html':
					$return = '"HTML Parsing Not Available At This Time"';
					break;
			}
		}
		else {
			$return = '"No Listings To Display"';
		}
		return $return;
	}

	function getProductTypeName($type) {
		switch($type){
			case 'R':
				return 'Rental Queue';
				break;
			case 'O':
				return 'Reservation';
				break;
		}
	}

	function loadProductBarcodes($id, $singleBarcode = false) {
		$product = new product($id);

		$Qinventory = Doctrine_Query::create()
		->select('i.inventory_id, ib.barcode, ib.barcode_id')
			->from('ProductsInventory i')
			->leftJoin('i.ProductsInventoryBarcodes ib')
			->where('i.products_id = ?', $id);

		if ($singleBarcode !== false && $singleBarcode > 0){
			$Qinventory->andWhere('barcode_id = ?', $singleBarcode);
		}

		$inventory = $Qinventory->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($inventory){
			foreach($inventory[0]['ProductsInventoryBarcodes'] as $barcode){
				$this->labels[] = array(
					'products_name'        => $product->getName(),
					'barcode'              => $barcode['barcode'],
					'barcode_type'         => 'Code128Auto',
					'barcode_id'           => $barcode['barcode_id'],
					'products_description' => $product->getDescription(),
					'customers_address'    => false
				);
			}
		}
	}

	function loadBarcodes($pID, $barcodes) {
		$product = new product($pID);
		foreach($barcodes as $barcodeId){
			$Qbarcode = Doctrine_Query::create()
			->select('barcode, barcode_id')
				->from('ProductsInventoryBarcodes')
				->where('barcode_id = ?', $barcodeId)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qbarcode){
				$this->labels[] = array(
					'products_name'        => $product->getName(),
					'barcode'              => $Qbarcode[0]['barcode'],
					'barcode_type'         => 'Code128Auto',
					'barcode_id'           => $Qbarcode[0]['barcode_id'],
					'products_description' => strip_tags($product->getDescription()),
					'customers_address'    => false
				);
			}
		}
	}

	function loadLabelInfo($id, $type, $singleBarcode = false) {
		switch($type){
			case 'R':
				$Qorder = $this->getRentedQueueQuery(array(
					'queueId' => $id
				));

				$order = $Qorder->execute()->toArray(true);
				$oInfo = $order[0];

				$Product = $oInfo['Products'];
				$ProductDescription = $Product['ProductsDescription'][Session::get('languages_id')];
				$ProductsInventoryBarcodes = $oInfo['ProductsInventoryBarcodes'];
				$Customer = $oInfo['Customers'];
				$CustomerAddress = $Customer['AddressBook'][0];
				//todo here i have to select the delivery default
				$this->labels[] = array(
					'products_id'          => $Product['products_id'],
					'products_name'        => $ProductDescription['products_name'],
					'barcode_id'           => $ProductsInventoryBarcodes['barcode_id'],
					'barcode'              => $ProductsInventoryBarcodes['barcode'],
					'barcode_type'         => 'Code128Auto',
					'products_description' => stripslashes(strip_tags($ProductDescription['products_description'])),
					'customers_address'    => $CustomerAddress
				);
				break;
			case 'O':
				$Qorder = $this->getPayPerRentalQuery(array(
					'bookingId' => $id
				));
				$order = $Qorder->execute()->toArray(true);
				$oInfo = $order[0];

				$this->labels[] = array(
					'products_id'          => $oInfo['OrdersProducts']['products_id'],
					'products_name'        => $oInfo['OrdersProducts']['products_name'],
					'barcode_id'           => $oInfo['ProductsInventoryBarcodes']['barcode_id'],
					'barcode'              => $oInfo['ProductsInventoryBarcodes']['barcode'],
					'barcode_type'         => 'Code128Auto',
					'products_description' => stripslashes(strip_tags($oInfo['OrdersProducts']['Products']['ProductsDescription'][Session::get('languages_id')]['products_description'])),
					'customers_address'    => $oInfo['OrdersProducts']['Orders']['OrdersAddresses']['delivery']
				);
				break;
			case 'barcode':
				$this->loadProductBarcodes($id, $singleBarcode);
				return true;
				break;
		}
	}

	public function setData($data) {
		$this->labels = $data;
	}

	function buildHTML() {
		return $this->buildOutput('pdf');
	}

	function buildPDF() {
		return $this->buildOutput('pdf');
	}

	function buildOutput() {
		$this->tmpType = 'pdf';

		$printerMargin = 0;

		$this->layoutInfo = array(
			'leftMargin'   => .15625,
			'topMargin'    => .5,
			'rightMargin'  => .15625,
			'labelPadding' => .125
		);

		if ($printerMargin > $this->layoutInfo['leftMargin']){
			$this->layoutInfo['labelPadding'] = ($printerMargin - $this->layoutInfo['leftMargin']);
			$this->layoutInfo['leftMargin'] = 0;
		}
		else {
			$this->layoutInfo['leftMargin'] -= $printerMargin;
		}

		if ($printerMargin > $this->layoutInfo['topMargin']){
			$this->layoutInfo['topMargin'] = 0;
		}
		else {
			$this->layoutInfo['topMargin'] -= $printerMargin;
		}

		if ($printerMargin > $this->layoutInfo['rightMargin']){
			$this->layoutInfo['rightMargin'] = 0;
		}
		else {
			$this->layoutInfo['rightMargin'] -= $printerMargin;
		}

		$this->layoutInfo['labelPage'] = false;
		if ($this->labelsType == '5160' || $this->labelsType == '8160-s' || $this->labelsType == '8160-b' || $this->labelsType == 'barcodes'){
			$this->layoutInfo['labelPage'] = '8160';
			$this->layoutInfo['RowsPerPage'] = 10;
			$this->layoutInfo['ColsPerRow'] = 3;
			$this->layoutInfo['labelHeight'] = 1;
			$this->layoutInfo['labelWidth'] = 2.625;
			$this->layoutInfo['labelSpacerWidth'] = .15625;

			$this->layoutInfo['barcodeMaxWidth'] = $this->layoutInfo['labelWidth'] - ($this->layoutInfo['labelPadding'] * 2);
			$this->layoutInfo['barcodeMaxHeight'] = $this->layoutInfo['labelHeight'] - ($this->layoutInfo['labelPadding'] * 2);
			$this->layoutInfo['barcodeMaxHeight'] -= .125; //Allow For Text

			if ($this->labelsType == '5160' || $this->labelsType == '8160-s'){
				$buildfunction = 'buildLabel_Address';
			}elseif ($this->labelsType == 'barcodes' || $this->labelsType == '8160-b'){
				$buildfunction = 'buildLabel_Barcodes';
			}
		}
		elseif ($this->labelsType == '5164' || $this->labelsType == '8164') {
			$this->layoutInfo['labelPage'] = '8164';
			$this->layoutInfo['RowsPerPage'] = 3;
			$this->layoutInfo['ColsPerRow'] = 2;
			$this->layoutInfo['labelHeight'] = 3.3125;
			$this->layoutInfo['labelWidth'] = 4;
			$this->layoutInfo['labelSpacerWidth'] = .1875;

			$this->layoutInfo['barcodeMaxWidth'] = $this->layoutInfo['labelWidth'] - ($this->layoutInfo['labelPadding'] * 2);
			$this->layoutInfo['barcodeMaxHeight'] = 1;
			$this->layoutInfo['barcodeMaxHeight'] -= .125; //Allow For Text

			$buildfunction = 'buildLabel_ProductInfo';
		}

		if ($this->layoutInfo['labelPage'] !== false){
			$this->pdf = new TCPDF('P', 'in', array('8.5', '11.2'), true);
			$this->pdf->SetCreator('osCommerce Rental Script');
			$this->pdf->SetAuthor('Kevin Javitz');
			$this->pdf->SetTitle('Rental Product Labels');
			$this->pdf->SetSubject('Rental Product Labels');
			$this->pdf->setViewerPreferences(array(
				'PrintScaling' => 'None'
			));

			$this->pdf->SetMargins(
				$this->layoutInfo['leftMargin'],
				$this->layoutInfo['topMargin'],
				$this->layoutInfo['rightMargin'],
				true
			);
			$this->pdf->SetCellPadding($this->layoutInfo['labelPadding']);
			$this->pdf->setPrintHeader(false);
			$this->pdf->setPrintFooter(false);
			$this->pdf->SetAutoPageBreak(TRUE, .51);
			$this->pdf->setImageScale(1);
			//$this->pdf->setLanguageArray($l);
			$this->pdf->AliasNbPages();
			$this->pdf->AddPage();
			$this->pdf->SetFont("helvetica", "", 11);

			$CurPage = 1;
			$CurrentRow = 1;
			$CurrentCol = 1;
			$labelCnt = 0;
			$lastLabel = sizeof($this->labels);
			while($CurrentRow <= $this->layoutInfo['RowsPerPage']){
				if (!isset($this->labels[$labelCnt])){
					break;
				}

				if ($CurPage == 1){
					if ($CurrentRow < $this->startLocation['row']){
						$blankRow = true;
					}
					else {
						$blankRow = false;
					}
				}
				else {
					$blankRow = false;
				}

				while($CurrentCol <= $this->layoutInfo['ColsPerRow']){
					if ($CurPage == 1){
						if ($blankRow === true || $CurrentCol < $this->startLocation['col']){
							$blankCol = true;
						}
						else {
							//Reset to 1 so that after first output it will continue without skipping columns
							$this->startLocation['col'] = 1;
							$blankCol = false;
						}
					}
					else {
						$blankCol = false;
					}

					$newLine = ($CurrentCol == $this->layoutInfo['ColsPerRow'] ? 1 : 0);

					if ($blankCol === true){
						$lInfo = array();
					}
					else {
						$lInfo = $this->labels[$labelCnt];
						$labelCnt++;
					}
					$this->$buildfunction($lInfo, $newLine);

					$CurrentCol++;
				}

				$CurrentCol = 1;
				$CurrentRow++;
				if ($CurrentRow == $this->layoutInfo['RowsPerPage']){
					if ($lastLabel > $labelCnt){
						$CurrentRow = 1;
						$CurPage++;
					}
				}
			}

			$this->pdf->lastPage();
			$this->pdf->Output("labelSheet.pdf", "I");
		}else{
			die('PDF Error: Unknown Label Sheet Type (' . $this->labelsType . ')');
		}
	}

	private function buildLabel_ProductInfo($labelInfo, $newLine) {
		$labelContent = array();
		if (tep_not_null($labelInfo['products_name'])){
			$labelContent[] = '<b>' . $labelInfo['products_name'] . '</b>';
		}

		if (tep_not_null($labelInfo['products_description'])){
			$labelContent[] = '<b>Description:</b> ' . (strlen($labelInfo['products_description']) > 350 ? substr($labelInfo['products_description'], 0, 350) . '...' : $labelInfo['products_description']);
		}

		if (tep_not_null($labelInfo['barcode'])){
			$labelContent[] = '<b>Barcode:</b> ' . $labelInfo['barcode'];
			if (tep_not_null($labelInfo['barcode'])){
				$labelContent[] = $this->getTcpdfBarcode($labelInfo);
			}
			else {
				$labelContent[] = 'Image Not Available';
			}
		}

		$this->pdf->MultiCell($this->layoutInfo['labelWidth'], $this->layoutInfo['labelHeight'], implode('<br>', $labelContent), 0, 'L', 0, $newLine, '', '', true, 0, true, true, 1, 'M', true);
		if ($newLine == 0){
			$this->pdf->Cell($this->layoutInfo['labelSpacerWidth'], $this->layoutInfo['labelHeight'], '');
		}
	}

	private function buildLabel_Address($lInfo, $newLine) {
		$labelContent = array(
			substr($lInfo['products_name'], 0, 13) . ' - ' . $lInfo['barcode']
		);

		if ($lInfo['customers_address'] !== false){
			$labelContent[] = tep_address_format(tep_get_address_format_id($lInfo['customers_address']['entry_country_id']), $lInfo['customers_address']);
		}

		$this->pdf->MultiCell($this->layoutInfo['labelWidth'], $this->layoutInfo['labelHeight'], strip_tags(str_replace('&nbsp;', ' ', implode("\n", $labelContent))), 0, 'L', 0, $newLine, '', '', true, 0, false, true, $this->layoutInfo['labelHeight'], 'M', true);
		if ($newLine == 0){
			$this->pdf->Cell($this->layoutInfo['labelSpacerWidth'], $this->layoutInfo['labelHeight'], '');
		}
	}

	private function buildLabel_Barcodes($lInfo, $newLine) {
		$labelContent = array();
		if (tep_not_null($lInfo['barcode'])){

			if (tep_not_null($lInfo['barcode_type'])){
				$labelContent[] = $this->getTcpdfBarcode($lInfo);
			}
			else {
				$labelContent[] = 'Image Not Available';
			}
		}
		$this->pdf->MultiCell($this->layoutInfo['labelWidth'], $this->layoutInfo['labelHeight'], implode("\n", $labelContent), 0, 'C', 0, $newLine, '', '', true, 0, true, true, $this->layoutInfo['labelHeight'], 'M', true);
		if ($newLine == 0){
			$this->pdf->Cell($this->layoutInfo['labelSpacerWidth'], $this->layoutInfo['labelHeight'], '');
		}
	}

	private function getTcpdfBarcode($bInfo){
		$style = array(
			'position'     => '',
			'align'        => 'L',
			'stretch'      => false,
			'fitwidth'     => false,
			'cellfitalign' => '',
			'border'       => false,
			'hpadding'     => '0',
			'vpadding'     => '0',
			'fgcolor'      => array(0, 0, 0),
			'bgcolor'      => false, //array(255,255,255),
			'text'         => true,
			'font'         => 'helvetica',
			'fontsize'     => 8,
			'stretchtext'  => false
		);

		$styleQR = array(
			'border'        => 0,
			'vpadding'      => '0',
			'hpadding'      => '0',
			'fgcolor'       => array(0, 0, 0),
			'bgcolor'       => false, //array(255,255,255)
			'module_width'  => 1, // width of a single module in points
			'module_height' => 1 // height of a single module in points
		);

		switch($bInfo['barcode_type']){
			case 'Code39':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'C39', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code39CS':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'C39+', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code128Auto':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'C128', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code128A':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'C128A', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code128B':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'C128B', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code128C':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'C128C', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code2of5':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'S25', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'UpcA':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'UPCA', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'UpcE':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'UPCE', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Ean8':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'EAN8', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Ean13':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'EAN13', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Codabar':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'CODABAR', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Postnet':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'POSTNET', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code39LibR':
				break;
			case 'Code39LibL':
				break;
			case 'CodabarLibR':
				break;
			case 'CodabarLibL':
				break;
			case 'Code128Ean':
				break;
			case 'Itf14':
				break;
			case 'Planet':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'PLANET', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Pdf417':
				break;
			case 'QRCode':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'QRCODE', '', '', 1, 1, $styleQR, 'N'));
				$barcodeStr = '<tcpdf method="write2DBarcode" params="' . $params . '" />';
				break;
			case 'IMail':
				break;
		}
		return $barcodeStr;
	}
}

?>