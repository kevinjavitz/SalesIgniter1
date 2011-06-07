<?php
require_once('../tcpdf/config/tcpdf_config.php');
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

class PDF_Labels {

	function PDF_Labels(){
		$this->setStartDate(date('Y-m-d'));
		$this->setEndDate(date('Y-m-d'));
		$this->setFilter('All');
		$this->labels = array();
	}

	function setStartDate($val){ $this->startDate = $val; }
	function setEndDate($val){ $this->endDate = $val; }
	function setFilter($val){ $this->filter = $val; }
	function setLabelsType($val){ $this->labelsType = $val; }
	function setLabelLocation($val){ $this->labelLocation = $val; }
	function setInventoryCenter($val){ $this->invCenter = $val; }

	function getRentedQueueQuery($settings = null){
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
		->andWhere('ab.address_book_id = c.customers_default_address_id')
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
	
	public function getRentedProducts($settings = null){
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
		->andWhere('ab.address_book_id = c.customers_default_address_id')
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

					if (isset($Queue['ProductsInventoryBarcodes']) && !empty($Queue['ProductsInventoryBarcodes'])){
						$dataArray[$idx]['barcode_id'] = $Queue['ProductsInventoryBarcodes']['barcode_id'];
						$dataArray[$idx]['barcode'] = $Queue['ProductsInventoryBarcodes']['barcode'];
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

	function getPayPerRentalQuery($settings = null){
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
		}else{
			$query->andWhere('opr.parent_id is null');
		}

		return $this->getPayPerRentals($settings);
		return $query;
	}

	public function getPayPerRentals($settings = null){
		$query = Doctrine_Query::create()
		->from('Orders o')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('op.OrdersProductsReservation opr')
		->leftJoin('opr.ProductsInventoryBarcodes ib')
		->leftJoin('o.OrdersAddresses oa')
		->where('oa.address_type = ?', 'delivery')
		->orderBy('opr.date_shipped asc, op.products_name asc');

		if (isset($settings['startDate']) && isset($settings['endDate'])){
			$query->andWhere('opr.date_shipped BETWEEN CAST("' . $settings['startDate'] . '" as DATE) AND CAST("' . $settings['endDate'] . '" as DATE)');
		}

		if (isset($settings['bookingId'])){
			$query->andWhere('opr.orders_products_reservations_id = ?', $settings['bookingId']);
		}else{
			$query->andWhere('opr.parent_id is null');
		}

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

						if (isset($resInfo['ProductsInventoryBarcodes']) && !empty($resInfo['ProductsInventoryBarcodes'])){
							$dataArray[$idx]['barcode_id'] = $resInfo['ProductsInventoryBarcodes']['barcode_id'];
							$dataArray[$idx]['barcode'] = $resInfo['ProductsInventoryBarcodes']['barcode'];
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

	function runListingQuery(){
		global $appExtension;
		$queries['rental'] = $this->getRentedQueueQuery(array(
			'startDate' => $this->startDate,
			'endDate' => $this->endDate
		));

		$queries['onetime'] = $this->getPayPerRentalQuery(array(
			'startDate' => $this->startDate,
			'endDate' => $this->endDate
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
							}elseif (!empty($itemData['quantity_id'])){
								$Qcheck->from('ProductsInventoryQuantity')
								->where('quantity_id = ?', $itemData['quantity_id']);
							}
							$Qcheck->andWhere('inventory_center_id = ?', $this->invCenter);

							$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						}

						if ($Result && $Result[0]['total'] <= 0){
							continue;
						}
					}else{
						$QinventoryCenter = Doctrine_Query::create()
						->select('ic.inventory_center_name')
						->from('ProductsInventoryCenters ic');

						if (isset($itemData['barcode_id']) || isset($itemData['quantity_id'])){
							if (isset($itemData['barcode_id'])){
								$QinventoryCenter->leftJoin('ic.ProductsInventoryBarcodesToInventoryCenters b2c')
								->where('b2c.barcode_id = ?', $itemData['barcode_id']);
							}else{
								$QinventoryCenter->leftJoin('ic.ProductsInventoryQuantity iq')
								->where('iq.quantity_id = ?', $itemData['quantity_id']);
							}

							$Result = $QinventoryCenter->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							if ($Result){
								$inventoryCenterName = $Result[0]['inventory_center_name'];
							}else{
								$inventoryCenterName = 'None';
							}
						}else{
							$inventoryCenterName = 'Package Product';
						}
					}
				}

				$this->listingData[] = array(
					'customers_name'   => $itemData['customers_name'],
					'addressFormatted' => $itemData['addressFormatted'],
					'products_name'    => $itemData['products_name'],
					'products_type'    => $itemData['products_type'],
					'date_sent'        => $itemData['date_sent'],
					'barcode'          => (isset($itemData['barcode']) ? $itemData['barcode'] : 'Quantity Tracking'),
					'inventory_center' => $inventoryCenterName,
					'checkbox_value'   => $itemData['checkbox_value']
				);
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
				}else{
					$barcodeNum = 'Quantity Tracking';
				}

				$this->listingData[$index] = array(
					'customers_name'   => $rental['customers_name'],
					'addressFormatted' => '',
					'products_name'    => '',
					'products_type'    => $rental['products_type'],
					'date_sent'        => (isset($rental['shipment_date']) ? $rental['shipment_date'] : $rental['date_shipped']),
					'barcode'          => $barcodeNum,
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

	function parseListingData($type = 'html'){
		global $typeNames;
		if (isset($this->listingData) && sizeof($this->listingData) > 0){
			switch($type){
				case 'json':
					$returnArray = array();
					foreach($this->listingData as $listingInfo){
						$returnArray[] = array(
							$listingInfo['customers_name'],
							$listingInfo['addressFormatted'],
							htmlspecialchars(stripslashes($listingInfo['products_name']), ENT_QUOTES),
							$listingInfo['barcode'],
							$listingInfo['inventory_center'],
							$typeNames[$listingInfo['products_type']],
							tep_date_short($listingInfo['date_sent']),
							$listingInfo['checkbox_value']
						);
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
		}else{
			$return = '"No Listings To Display"';
		}
		return $return;
	}

	function getProductTypeName($type){
		switch($type){
			case 'R':
				return 'Rental Queue';
				break;
			case 'O':
				return 'Reservation';
				break;
		}
	}

	function loadProductBarcodes($id, $singleBarcode = false){
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
					'barcode_id'           => $barcode['barcode_id'],
					'products_description' => $product->getDescription(),
					'customers_address'    => false
				);
			}
		}
	}

	function loadBarcodes($pID, $barcodes){
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
					'barcode_id'           => $Qbarcode[0]['barcode_id'],
					'products_description' => $product->getDescription(),
					'customers_address'    => false
				);
			}
		}
	}

	function loadLabelInfo($id, $type, $singleBarcode = false){
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

	function buildHTML(){
		return $this->buildOutput('html');
	}

	function buildPDF(){
		return $this->buildOutput('pdf');
	}

	function buildOutput($type = 'html'){
		$this->tmpType = $type;
		if ($type == 'pdf'){
			$this->pdf = new TCPDF('P', 'in', array('8.53', '11.03'), true);
			$this->pdf->SetCreator('osCommerce Rental Script');
			$this->pdf->SetAuthor('Kevin Javitz');
			$this->pdf->SetTitle('Rental Product Labels');
			$this->pdf->SetSubject('Rental Product Labels');
			if ($this->labelsType == '5160'){
				$this->pdf->SetMargins(0.18, 0.49, 0.2);
			} elseif ($this->labelsType == '5164'){
				$this->pdf->SetMargins(0.15, 0.49, 0.15);
			} else{
				$this->pdf->SetMargins(0.18, 0.49, 0.2);
			}
			$this->pdf->SetCellPadding(.075);
			$this->pdf->setPrintHeader(false);
			$this->pdf->setPrintFooter(false);
			$this->pdf->SetAutoPageBreak(TRUE, .51);
			$this->pdf->setImageScale(1);
			//$this->pdf->setLanguageArray($l);
			$this->pdf->AliasNbPages();
			$this->pdf->AddPage();
			$this->pdf->SetFont("helvetica", "", 11);
		}else{
			$topPadding = 0.49;
			$bottomPadding = 0.51;
			$leftPadding = ($this->labelsType == '5160' ? 0.18 : 0.15);
			$rightPadding = ($this->labelsType == '5160' ? 0.18 : 0.15);
			$this->htmlOutput = '<html>' .
			'<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>' .
			'<body topmargin="0" leftmargin="0" style="font-family:halvetica;font-size:11pt;">' .
			'<div style="padding-top:' . $topPadding . 'in;padding-left:' . $leftPadding . 'in;padding-right:' . $rightPadding . 'in;padding-bottom:' . $bottomPadding . 'in;width:8.53in;height:11.03in;">' . "\n";
		}

		$function = '';
		switch($this->labelsType){
			case '5160': // Avery 5160 Shipping Labels
			case 'ship_html': // Shipping Labels HTML Version
			$maxCol = 1;
			$function = 'buildLabel_5160';
			$pageCells = 30;
			break;
			case '5164': // Avery 5164 Product Info Labels
			case 'pinfo_html': // Product Info Labels HTML Version
			$maxCol = 0;
			$function = 'buildLabel_5164';
			$pageCells = 6;
			break;
			case 'barcodes':
				$maxCol = 1;
				$function = 'buildLabel_Barcodes';
				$this->buildLabel_5164($this->labels[0], 1, true);
				$pageCells = 30;
				break;
			default:
				die('PDF Error: Unknown Label Sheet Type');
				break;
		}

		$col = 0;
		if (isset($this->labelLocation) && tep_not_null($this->labelLocation)){
			$n = $pageCells;
		}else{
			$n = sizeof($this->labels);
		}
		for($i=0; $i<$n; $i++){
			if ($col > $maxCol){
				$col = 0;
				$newLine = 1;
			}else{
				$col++;
				$newLine = 0;
			}
			$lInfo = $this->labels[$i];
			if (isset($this->labelLocation) && tep_not_null($this->labelLocation)){
				if ($i != $this->labelLocation){
					$lInfo = array();
				}else{
					$lInfo = $this->labels[0];
				}
			}
			$this->$function($lInfo, $newLine);
		}

		if ($type == 'pdf'){
			$this->pdf->lastPage();
			$this->pdf->Output("example_017.pdf", "I");
		}else{
			$this->htmlOutput .= '</div>' . "\n" .
			'</body>' . "\n" .
			'</html>' . "\n";
			return $this->htmlOutput;
		}
	}

	function buildLabel_5160($lInfo, $newLine){
		$labelContent = array(
		substr($lInfo['products_name'], 0, 13) . ' - ' . $lInfo['barcode']
		);

		if ($lInfo['customers_address'] !== false){
			$labelContent = tep_address_format($lInfo['customers_address']['address_format_id'], $lInfo['customers_address'],'','','','short');
		}

		if ($this->tmpType == 'pdf'){
			$this->pdf->MultiCell(2.63, 1, implode('<br>', $labelContent), 0, 'L', 0, $newLine, 0, 0, true, 0, true);
			if ($newLine == 0){
				$this->pdf->Cell(0.13, 1, '');
			}
		}else{
			$this->htmlOutput .= '<div style="width:2.63in;height:1in;position:relative;float:left;">' . "\n" .
			'<div style="padding:0.075in;">' . "\n" .
			implode('<br>', $labelContent) . "\n" .
			'</div>' . "\n" .
			'</div>' . "\n";
			if ($newLine == 1){
				$this->htmlOutput .= '<div style="clear:both;"></div>' . "\n";
			}else{
				$this->htmlOutput .= '<div style="width:0.13in;height:1in;position:relative;float:left;"></div>' . "\n";
			}
		}
	}

	function buildLabel_5164($lInfo, $newLine, $hideBarcode = false){
		$labelContent = array();
		if (tep_not_null($lInfo['products_name'])){
			$labelContent[] = '<b>' . $lInfo['products_name'] . '</b>';
		}

		if (tep_not_null($lInfo['products_id'])){
			$Qcheck = Doctrine_Query::create()
			->from('ProductsCustomFields f')
			->leftJoin('f.ProductsCustomFieldsDescription fd')
			->leftJoin('f.ProductsCustomFieldsToProducts f2p')
			->where('f2p.product_id = ?', $lInfo['products_id'])
			->andWhere('fd.language_id = ?', Session::get('languages_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qcheck){
				foreach($Qcheck as $fInfo){
					if ($fInfo['show_on_labels'] == '1'){
						$maxChars = ($fInfo['labels_max_chars'] > 0 ? $fInfo['labels_max_chars'] : 150);
						if (strlen($fInfo['ProductsCustomFieldsToProducts']['value']) > $maxChars){
							$labelContent[] = '<b>' . $fInfo['ProductsCustomFieldsDescription'][Session::get('languages_id')]['field_name'] . ':</b> ' . substr($fInfo['ProductsCustomFieldsToProducts']['value'], 0, $maxChars) . '...';
						}else{
							$labelContent[] = '<b>' . $fInfo['ProductsCustomFieldsDescription'][Session::get('languages_id')]['field_name'] . ':</b> ' . $fInfo['ProductsCustomFieldsToProducts']['value'];
						}
					}
				}
			}
		}
		
		if (tep_not_null($lInfo['products_description'])){
			$labelContent[] = '<b>Description:</b> ' . (strlen($lInfo['products_description']) > 350 ? substr($lInfo['products_description'], 0, 350) . '...' : $lInfo['products_description']);
		}

		if (tep_not_null($lInfo['barcode']) && $hideBarcode === false){
			$labelContent[] = '<b>Barcode:</b> ' . $lInfo['barcode'];
			if (tep_not_null($lInfo['barcode_id'])){
				$labelContent[] = '<img src="' . tep_href_link('showBarcode_' . $lInfo['barcode_id'] . '.png', Session::getSessionName() . '=' . Session::getSessionId()) . '">';
			}else{
				$labelContent[] = 'Image Not Available';
			}
		}

		if ($this->tmpType == 'pdf'){
			$this->pdf->MultiCell(4, 3.34, implode('<br>', $labelContent), 0, 'L', 0, $newLine, '', '', true, 0, true);
			if ($newLine == 0){
				$this->pdf->Cell(.2, 3.34, '');
			}
		}else{
			$this->htmlOutput .= '<div style="width:4in;height:3.34in;position:relative;float:left;">' . "\n" .
			'<div style="padding:0.075in;">' . "\n" .
			implode('<br>', $labelContent) . "\n" .
			'</div>' . "\n" .
			'</div>' . "\n";
			if ($newLine == 1){
				$this->htmlOutput .= '<div style="clear:both;"></div>' . "\n";
			}else{
				$this->htmlOutput .= '<div style="width:0.2in;height:3.34in;position:relative;float:left;"></div>' . "\n";
			}
		}
	}

	function buildLabel_Barcodes($lInfo, $newLine){
		$labelContent = array(
		$lInfo['barcode']
		);

		if (tep_not_null($lInfo['barcode_id'])){
			$labelContent[] = '<img src="' . tep_href_link('showBarcode_' . $lInfo['barcode_id'] . '.png', Session::getSessionName() . '=' . Session::getSessionId()) . '">';
		}else{
			$labelContent[] = 'Image Not Available';
		}

		if ($this->tmpType == 'pdf'){
			$this->pdf->MultiCell(2.63, 1, implode('<br>', $labelContent), 0, 'L', 0, $newLine, '', '', true, 0, true);
			if ($newLine == 0){
				$this->pdf->Cell(0.13, 1, '');
			}
		}else{
			$this->htmlOutput .= '<div style="width:2.63in;height:1in;position:relative;float:left;">' . "\n" .
			'<div style="padding:0.075in;">' . "\n" .
			implode('<br>', $labelContent) . "\n" .
			'</div>' . "\n" .
			'</div>' . "\n";
			if ($newLine == 1){
				$this->htmlOutput .= '<div style="clear:both;"></div>' . "\n";
			}else{
				$this->htmlOutput .= '<div style="width:0.13in;height:1in;position:relative;float:left;"></div>' . "\n";
			}
		}
	}
}
?>