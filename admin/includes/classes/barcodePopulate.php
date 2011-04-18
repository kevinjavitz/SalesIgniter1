<?php
//require('includes/classes/data_populate/export.php');

class barcodePopulate{

	function barcodePopulate(){
		$this->version = '1.0';
		$this->languageID = Session::get('languages_id');
		$this->colSeparator = "\t";
		$this->endOfRow = 'EOREOR' . "\n";
		$this->tempDir = sysConfig::getDirFsCatalog() . 'temp/';
	}

	function buildFile(){
		$this->fileHeaders = array(
			'v_products_model',
			'v_barcode',
			'v_inventory_store_center',
			'v_purchase_type',
			'v_barcode_status',
			'v_quantity_available',
			'v_quantity_broken',
			'v_quantity_out',
			'v_quantity_purchased',
			'v_quantity_reserved',
			'v_use_center',
			'v_comments'
		);
	}
	  /*TODO
	   * change variable names and move to app
	  */
	function getQuery($productModel = false, $productsID = false, $barcode = false){
		global $appExtension;
		$multiStore = $appExtension->getExtension('multiStore');
		$invext = $appExtension->getExtension('inventoryCenters');
		$multiStoreEnabled = ($multiStore !== false && $multiStore->isEnabled() === true);
		$invEnabled = ($invext !== false && $invext->isEnabled() === true);
		$stockMethod = '';
		if ($invEnabled){
			$stockMethod = $invext->stockMethod;
		}

		$isStore = 0;
		if ($stockMethod == 'Store' && $multiStoreEnabled === true){
			$isStore = 1;
			$stores = $multiStore->getStoresArray();
			foreach($stores as $sInfo){
				$inventoryCenterArray[$sInfo['stores_id']] = $sInfo['stores_name'];
			}
		}else{
			if ($stockMethod == 'Zone'){
				$isStore = 2;
				$QinventoryCenters = Doctrine_Query::create()
							->select('inventory_center_id, inventory_center_name')
							->from('ProductsInventoryCenters')
							->orderBy('inventory_center_name')
							->execute();
				if ($QinventoryCenters->count() > 0){
					foreach($QinventoryCenters->toArray() as $cInfo){
						$inventoryCenterArray[$cInfo['inventory_center_id']] = $cInfo['inventory_center_name'];
					}
				}
			}
		}

		$mydata = array();
		$countData = 0;
		$isExit = false;
		
		$QproductsAll = Doctrine_Query::create()
						->select('products_id')
						->from('Products')
						->andWhere('products_id > ?', '0')
						->orderBy('products_id')
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);		
		foreach($QproductsAll as $pInfo){
			$productsArr[] = $pInfo['products_id'];
		}		

		$QPurchaseTypes = Doctrine_Query::create()
				->from('ProductsInventory')
				->where('controller = ?','normal')
				->andWhereIn('products_id', $productsArr)
				->orderBy('products_id')
				->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

		if (count($QPurchaseTypes) > 0){
			foreach($QPurchaseTypes as $qpur){

				$track_method = $qpur['track_method'];
				$iID = $qpur['inventory_id'];
				if (isset($qpur['use_center'])){
					$usecenter = $qpur['use_center'];
				}else{
					$usecenter = 0;
				}
				$purtype = $qpur['type'];

				$Qprod = Doctrine_Query::create()
						->select('products_model')
						->from('Products p')
						->where('products_id = ?',$qpur['products_id'])
						->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

				if (count($Qprod) > 0){
					$products_model = $Qprod[0]['products_model'];
				}else{
					$products_model = '';
				}
				              
				if ($track_method == 'quantity'){
					$QInv = Doctrine_Query::create()
							->from('ProductsInventoryQuantity')
							->where('inventory_id = ?', $iID)
							->andWhere('available > 0 OR qty_out > 0 OR broken > 0 OR purchased > 0 OR reserved > 0');
					
					if ($usecenter == 1){
						if ($isStore == 1){
							$QInv->andWhere('inventory_store_id > 0');
						}else if ($isStore == 2){
							$QInv->andWhere('inventory_center_id > 0');
						}
					}
					      
					$QInv = $QInv->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

					if (count($QInv) > 0){
						foreach($QInv as $qi){
							$countData++;
							if (isset($_POST['start_num']) && (!empty($_POST['start_num']) || $_POST['start_num'] == 0)){
								if ($countData < $_POST['start_num']) continue;
							}
							if (isset($_POST['num_items']) && !empty($_POST['num_items'])){
								if ($countData >= ((int)$_POST['start_num'] + $_POST['num_items'])){
									$isExit = true;
									break;
								}
							}
							$pr['v_products_model'] = $products_model;
							$pr['v_quantity_available'] = $qi['available'];
							$pr['v_quantity_out'] = $qi['qty_out'];
							$pr['v_quantity_broken'] = $qi['broken'];
							$pr['v_quantity_purchased'] = $qi['purchased'];
							$pr['v_quantity_reserved'] = $qi['reserved'];
							$pr['v_purchase_type'] = $purtype;
							$pr['v_use_center'] = $usecenter;

							if ($usecenter == 1){
								if ($isStore == 1){
									$pr['v_inventory_store_center'] = $inventoryCenterArray[$qi['inventory_store_id']];
								}else if ($isStore == 2){
									$pr['v_inventory_store_center'] = $inventoryCenterArray[$qi['inventory_center_id']];
								}else{
									$pr['v_inventory_store_center'] = '';
								}
							}

							$pr['v_barcode'] = '';
							$pr['v_barcode_status'] = '';
							//get comments for inventory_quantity

							$Qcom = Doctrine_Query::create()
										->select('comments')
										->from('ProductsInventoryQuantityComments')
										->where('quantity_id = ?',$qi['quantity_id'])
										->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
							if (count($Qcom) > 0){
								$pr['v_comments'] = $Qcom[0]['comments'];
							}else{
								$pr['v_comments'] = '';
							}
							$mydata[] = $pr;
						}
					}

				}else{					
					$QInv1 = Doctrine_Query::create()
							->from('ProductsInventoryBarcodes')
							->where('inventory_id = ?', $iID)
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					if (count($QInv1) > 0){
						foreach($QInv1 as $qi){
							$countData++;
							if (isset($_POST['start_num']) && (!empty($_POST['start_num']) || $_POST['start_num'] == 0)){
								if ($countData < $_POST['start_num']) continue;
							}
							if (isset($_POST['num_items']) && !empty($_POST['num_items'])){
								if ($countData >= ((int)$_POST['start_num'] + $_POST['num_items'])){
									$isExit = true;
									break;
								}
							}
							$pr['v_products_model'] = $products_model;
							$pr['v_quantity_available'] = '';
							$pr['v_quantity_out'] = '';
							$pr['v_quantity_broken'] = '';
							$pr['v_quantity_purchased'] = '';
							$pr['v_quantity_reserved'] = '';
							$pr['v_purchase_type'] = $purtype;
							$pr['v_use_center'] = $usecenter;
							$Qcom = Doctrine_Query::create()
										->select('comments')
										->from('ProductsInventoryBarcodesComments')
										->where('barcode_id = ?',$qi['barcode_id'])
										->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
							if ($Qcom){
								$pr['v_comments'] = $Qcom[0]['comments'];
							}else{
								$pr['v_comments'] = '';
							}

							if ($usecenter == 1){
								if ($isStore == 1){
									$Qbar = Doctrine_Query::create()
										->from('ProductsInventoryBarcodesToStores')
										->where('barcode_id = ?',$qi['barcode_id'])
										->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

									if (count($Qbar) > 0){
										$pr['v_inventory_store_center'] = $inventoryCenterArray[$Qbar[0]['inventory_store_id']];
									}
								}
								else if($isStore == 2){
									$Qbar = Doctrine_Query::create()
										->from('ProductsInventoryBarcodesToInventoryCenters')
										->where('barcode_id = ?',$qi['barcode_id'])
										->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
									if (count($Qbar) > 0){
										$pr['v_inventory_store_center'] = $inventoryCenterArray[$Qbar[0]['inventory_center_id']];
									}
								}
								else{
									$pr['v_inventory_store_center'] = '';

								}
							}
							$pr['v_barcode'] = $qi['barcode'];
							$pr['v_barcode_status'] = $qi['status'];
							$mydata[] = $pr;
						}
					}

				}
				if ($isExit){
					break;
				}

			}

		}
		
		return $mydata;
	}

	function export(){
		if (empty($this->fileHeaders)){
			$this->buildFile();
		}
		$query = $this->getQuery();
		$dataExporter = new dataExport();
		$dataExporter->setHeaders($this->fileHeaders)
		->setExportData($query)
		->setColSeparator($this->colSeparator)
		->setEndOfRow($this->endOfRow)
		->process()
		->output();
	}

	function importFile($fileName){
		global $appExtension;
		$fileString = file($this->tempDir . $fileName);
		$new_fileString = '';
		foreach($fileString as $string){
			$new_fileString .= $string;
		}
		$new_fileString = str_replace('"EOREOR"', 'EOREOR', $new_fileString);
		$fileString = explode($this->colSeparator . 'EOREOR', $new_fileString);

		$multiStore = $appExtension->getExtension('multiStore');
		$invext = $appExtension->getExtension('inventoryCenters');
		$multiStoreEnabled = ($multiStore !== false && $multiStore->isEnabled() === true);
		$invEnabled = ($invext !== false && $invext->isEnabled() === true);

		if($invEnabled){			
			$stockMethod = $invext->stockMethod;
		}

		$isStore = 0;
		if ($stockMethod == 'Store' && $multiStoreEnabled === true){
			$isStore = 1;
			$stores = $multiStore->getStoresArray();
			foreach($stores as $sInfo){
				$inventoryCenterArray[$sInfo['stores_name']] = $sInfo['stores_id'];

			}
		}else{
			if ($stockMethod == 'Zone'){
				$isStore = 2;
				$QinventoryCenters = Doctrine_Query::create()
				->select('inventory_center_id, inventory_center_name')
				->from('ProductsInventoryCenters')
				->orderBy('inventory_center_name')
				->execute();
				if ($QinventoryCenters->count() > 0){
					foreach($QinventoryCenters->toArray() as $cInfo){
						$inventoryCenterArray[$cInfo['inventory_center_name']] = $cInfo['inventory_center_id'];

					}
				}
			}
		}

		// Now we'll populate the filelayout based on the header row.
		$headerArray = explode($this->colSeparator, $fileString[0]); // explode the first row, it will be our filelayout
		$colcount = 0;
		$this->fileLayout = array();
		foreach($headerArray as $headerCol){
			$headerCol = str_replace( '"', '', $headerCol);
			$this->fileLayout[$headerCol] = $colcount++;
		}
		unset($fileString[0]);

		$foundID = array();
		foreach($fileString as $lineNumber => $line){
			$cols = $this->cleanupValues(explode($this->colSeparator, $line));

			$productsModel = isset($cols[$this->fileLayout['v_products_model']])?ltrim(rtrim($cols[$this->fileLayout['v_products_model']])):'';

			$qty_tmp = isset($cols[$this->fileLayout['v_quantity_available']])?ltrim(rtrim($cols[$this->fileLayout['v_quantity_available']])):'';
			if (empty($qty_tmp)){
				$qty_available = 0;
			}else{
				$qty_available = (int)ltrim(rtrim($cols[$this->fileLayout['v_quantity_available']]));
			}

			$qty_tmp = isset($cols[$this->fileLayout['v_quantity_broken']])?ltrim(rtrim($cols[$this->fileLayout['v_quantity_broken']])):'';
			if (empty($qty_tmp)){
				$qty_broken = 0;
			}else{
				$qty_broken = (int)ltrim(rtrim($cols[$this->fileLayout['v_quantity_broken']]));
			}

			$qty_tmp = isset($cols[$this->fileLayout['v_quantity_out']]) ? ltrim(rtrim($cols[$this->fileLayout['v_quantity_out']])):'';
			if (empty($qty_tmp)){
				$qty_out = 0;
			}else{
				$qty_out = (int)ltrim(rtrim($cols[$this->fileLayout['v_quantity_out']]));
			}

			$qty_tmp = isset($cols[$this->fileLayout['v_quantity_reserved']])?ltrim(rtrim($cols[$this->fileLayout['v_quantity_reserved']])):'';
			if (empty($qty_tmp)){
				$qty_reserved = 0;
			}else{
				$qty_reserved = (int)ltrim(rtrim($cols[$this->fileLayout['v_quantity_reserved']]));
			}

			$qty_tmp = isset($cols[$this->fileLayout['v_quantity_purchased']])?ltrim(rtrim($cols[$this->fileLayout['v_quantity_purchased']])):'';
			if (empty($qty_tmp)){
				$qty_purchased = 0;
			}else{
				$qty_purchased = (int)ltrim(rtrim($cols[$this->fileLayout['v_quantity_purchased']]));
			}
			
			$barcodeNumber = isset($cols[$this->fileLayout['v_barcode']])?ltrim(rtrim($cols[$this->fileLayout['v_barcode']])):'';
			$purchaseType = isset($cols[$this->fileLayout['v_purchase_type']])?ltrim(rtrim($cols[$this->fileLayout['v_purchase_type']])):'';
			$comments = isset($cols[$this->fileLayout['v_comments']])?ltrim(rtrim($cols[$this->fileLayout['v_comments']])):'';

			$useCenter = isset($cols[$this->fileLayout['v_use_center']])?ltrim(rtrim($cols[$this->fileLayout['v_use_center']])):'';
			if (empty($useCenter)){
				$useCenter = 0;
			}
			$barcodeStatus = isset($cols[$this->fileLayout['v_barcode_status']])?ltrim(rtrim($cols[$this->fileLayout['v_barcode_status']])):'';

			$inventoryCenter = isset($cols[$this->fileLayout['v_inventory_store_center']])?ltrim(rtrim($cols[$this->fileLayout['v_inventory_store_center']])):'';
			if (empty($inventoryCenter)){
				$inventoryCenter = 0;
			}

			$controller = 'normal';
			//echo 'barcode:'. $barcodeNumber. ' barcode_status: '. $barcodeStatus;
			//itwExit();
			$commonLog = array(
				'Products ID'      => 'Unknown',
				'Products Model'   => $productsModel,
				'Barcode'          => $barcodeNumber,
				'Inventory Center' => $inventoryCenter,
				'Purchase Type'    => $purchaseType,
				'Comments'         => $comments
			);

			if (empty($productsModel)){
				logError('product_barcode', array_merge($commonLog, array(
					'Message' => 'Line #' . $lineNumber . ': Model field is empty, No Action Taken'
				)));
				continue;
			}

			if (!isset($foundID[$productsModel])){
				$Qproduct = Doctrine_Query::create()
							->select('products_id')
							->from('Products')
							->where('products_model = ?', $productsModel)
							->execute();

				if($Qproduct){
					$productID = $Qproduct[0]['products_id'];
					$foundID[$productsModel] = $productID;
				}else{
					logError('product_barcode', array_merge($commonLog, array(
					'Message' => 'Model field doesnt exists, No Action Taken'
					)));
				continue;
				}
			}else{
				$productID = $foundID[$productsModel];
			}
			 
			$inventory_store_center_id = 0;
			if($useCenter){
				if(isset($inventoryCenterArray[$inventoryCenter])){
					$inventory_store_center_id = $inventoryCenterArray[$inventoryCenter];
				}else{
					$inventory_store_center_id = 0;
					$useCenter = 0;
					logError('inventory_center', array_merge($commonLog, array(
						'Products ID' => $productID,
						'Message'     => 'No inventory entry exists for this product. Assuming no inventory center.'
					)));
				}
			}

			if (empty($barcodeNumber)){
				if (($qty_available > 0) || ($qty_broken > 0) || ($qty_out > 0) || ($qty_purchased > 0) || ($qty_reserved > 0)){
					$track_method = 'quantity';

					$Qinv = Doctrine_Query::create()
											->from('ProductsInventory')
											->where('products_id = ?',$productID)
											->andWhere('track_method = ?', $track_method)
											->andWhere('type = ?',$purchaseType)
											->andWhere('controller = ?', $controller)
											->execute();
					$ist = true;
					if ($Qinv){
						foreach($Qinv as $qi){
							$inventory_id = $qi->inventory_id;
							if ($useCenter){
								$qi->use_center = $useCenter;
							}
							$qi->save();
							$ist = false;
						}
					}

					if($ist){
						$myInv = new ProductsInventory();
						$myInv->track_method = $track_method;
						$myInv->type = $purchaseType;
						$myInv->controller = $controller;
						$myInv->products_id = $productID;
						if ($useCenter){
							$myInv->use_center = $useCenter;
						}
						$myInv->save();
						$inventory_id = $myInv->inventory_id;
						logNew('product_quantity', array_merge($commonLog, array(
						'Products ID'      => $productID,
						'Message'          => 'Updating existing Inventory'
						)));
					}else{
						logUpdate('product_quantity', array_merge($commonLog, array(
						'Products ID'      => $productID,
						'Message'          => 'Updating existing Inventory'
						)));
					}

					$Qinvq1 = Doctrine_Query::create()
									->leftJoin('ProductsInventoryQuantity')
									->where('inventory_id = ?',$inventory_id);
					if($isStore == 1){
						$Qinvq1->andWhere('inventory_store_id = ?', $inventory_store_center_id);
					}else if($isStore == 2){
						$Qinvq1->andWhere('inventory_center_id = ?', $inventory_store_center_id);
					}

					$Qinvq1 = $Qinvq1->execute();
					$ist = true;
					if ($Qinvq1){
						foreach($Qinvq1 as $qi){
							$quantity_id = $qi->quantity_id;
							$qi->available = $qty_available;
							$qi->qty_out = $qty_out;
							$qi->broken = $qty_broken;
							$qi->purchased = $qty_purchased;
							$qi->reserved = $qty_reserved;
							$qi->save();
							$ist = false;
						}
					}

					if($ist){
						$myInvq = new ProductsInventoryQuantity();
						$myInvq->available = $qty_available;
						$myInvq->qty_out = $qty_out;
						$myInvq->broken = $qty_broken;
						$myInvq->purchased = $qty_purchased;
						$myInvq->inventory_id = $inventory_id;
						$myInvq->reserved = $qty_reserved;
						$myInvq->save();
						$quantity_id = $myInvq->quantity_id;
						logNew('product_quantity', array_merge($commonLog, array(
						'Products ID'      => $productID,
						'Message'          => 'Updating existing Inventory'
						)));
					}else{
						logUpdate('product_quantity', array_merge($commonLog, array(
						'Products ID'      => $productID,
						'Message'          => 'Updating existing Inventory quantity'
						)));
					}

					$Qinvc = Doctrine_Query::create()
									->from('ProductsInventoryQuantityComments')
									->where('quantity_id = ?',$quantity_id)
									->execute();

					$ist = true;
					if ($Qinvc){
						foreach($Qinvc as $qi){
							$qi->comments = $comments;
							$qi->save();
							$ist = false;
						}
					}

					if($ist){
						if(!empty($comments)){
							$myInvc = new ProductsInventoryQuantityComments();
							$myInvc->comments = $comments;
							$myInvc->save();
							logNew('product_quantity', array_merge($commonLog, array(
							'Products ID'      => $productID,
							'Message'          => 'Updating existing Comments'
							)));
						}
					}else{
						logUpdate('product_quantity', array_merge($commonLog, array(
						'Products ID'      => $productID,
						'Message'          => 'Updating existing Comments'
						)));
					}

				}else{
					logError('product_quantity', array_merge($commonLog, array(
						'Products ID' => $productID,
						'Message'     => 'No quantity neither barcode exists for this product. No action taken.'
					)));
					continue;
				}
			}else{
				if(empty($barcodeStatus)){
					logError('product_barcode', array_merge($commonLog, array(
						'Products ID' => $productID,
						'Message'     => 'No status exists for this products barcode. No action taken.'
					)));
					continue;
				}

				$track_method = 'barcode';

				$Qinv = Doctrine_Query::create()
						->from('ProductsInventory')
						->where('products_id = ?',$productID)
						->andWhere('track_method = ?', $track_method)
						->andWhere('type = ?',$purchaseType)
						->andWhere('controller = ?', $controller)
						->execute();

				$ist = true;
				if ($Qinv){
					foreach($Qinv as $qi){
						$inventory_id = $qi->inventory_id;
						if ($useCenter){
							$qi->use_center = $useCenter;
						}
						$qi->save();
						$ist = false;
					}
				}

				if ($ist){
						$myInv = new ProductsInventory();
						$myInv->track_method = $track_method;
						$myInv->type = $purchaseType;
						$myInv->controller = $controller;
						$myInv->products_id = $productID;
						if ($useCenter){
							$myInv->use_center = $useCenter;
						}
						$myInv->save();
						$inventory_id = $myInv->inventory_id;
						logNew('product_barcode', array_merge($commonLog, array(
						'Products ID'      => $productID,
						'Message'          => 'Updating existing Inventory'
						)));
				}else{
						logUpdate('product_barcode', array_merge($commonLog, array(
						'Products ID'      => $productID,
						'Message'          => 'Updating existing Inventory'
						)));
				}

				$Qinvq = Doctrine_Query::create()
							->from('ProductsInventoryBarcodes')
							->where('inventory_id = ?',$inventory_id)
							->andWhere('barcode = ?',$barcodeNumber);
				$Qinvq = $Qinvq->execute();

				$ist = true;
				if ($Qinvq){
					foreach($Qinvq as $qi){
						$barcode_id = $qi->barcode_id;
						$qi->status = $barcodeStatus;
						$qi->save();
						$ist = false;
					}
				}

				if($ist){
					$myInvq = new ProductsInventoryBarcodes();
					$myInvq->barcode = $barcodeNumber;
					$myInvq->inventory_id = $inventory_id;
					$myInvq->status = $barcodeStatus;
					$myInvq->save();
					$barcode_id = $myInvq->barcode_id;
					logNew('product_barcode', array_merge($commonLog, array(
					'Products ID'      => $productID,
					'Message'          => 'Updating existing Inventory'
					)));
				}else{
					logUpdate('product_barcode', array_merge($commonLog, array(
					'Products ID'      => $productID,
					'Message'          => 'Updating existing Inventory'
					)));
				}

				if($isStore == 1){
					$Qinvbs = Doctrine_Query::create()
								->from('ProductsInventoryBarcodesToStores')
								->where('barcode_id = ?',$barcode_id)
								->execute();

					$ist = true;
					if ($Qinvbs){
						foreach($Qinvbs as $qi){
							$qi->inventory_store_id = $inventory_store_center_id;
							$qi->save();
							$ist = false;
						}
					}

					if($ist){
						$myInvbs = new ProductsInventoryBarcodesToStores();
						$myInvbs->barcode_id = $barcode_id;
						$myInvbs->inventory_store_id = $inventory_store_center_id;
						$myInvbs->save();
						logNew('product_barcode', array_merge($commonLog, array(
						'Products ID'      => $productID,
						'Message'          => 'Updating existing Inventory'
						)));
					}else{
						logUpdate('product_barcode', array_merge($commonLog, array(
						'Products ID'      => $productID,
						'Message'          => 'Updating existing Inventory'
						)));
					}

				}else if($isStore == 2){
							$Qinvbc = Doctrine_Query::create()
									->from('ProductsInventoryBarcodesToInventoryCenters')
									->where('barcode_id = ?',$barcode_id)
									->execute();

							$ist = true;
							if ($Qinvbc){
								foreach($Qinvbc as $qi){
									$qi->inventory_center_id = $inventory_store_center_id;
									$qi->save();
									$ist = false;
								}
							}

							if($ist){
								$myInvbc = new ProductsInventoryBarcodesToInventoryCenters();
								$myInvbc->barcode_id = $barcode_id;
								$myInvbc->inventory_center_id = $inventory_store_center_id;
								$myInvbc->save();
								logNew('product_barcode', array_merge($commonLog, array(
								'Products ID'      => $productID,
								'Message'          => 'Updating existing Inventory'
							)));
							}else{
								logUpdate('product_barcode', array_merge($commonLog, array(
								'Products ID'      => $productID,
								'Message'          => 'Updating existing Inventory'
								)));
							}
				}

				$Qinvc = Doctrine_Query::create()
							->from('ProductsInventoryBarcodesComments')
							->where('barcode_id = ?',$barcode_id)
							->execute();

				$ist = true;
				if ($Qinvc){
					foreach($Qinvc as $qi){
						$qi->comments = $comments;
						$qi->save();
						$ist = false;
					}
				}

				if($ist){
					if(!empty($comments)){
						$myInvc = new ProductsInventoryBarcodesComments();
						$myInvc->comments = $comments;
						$myInvc->save();
						logNew('product_barcode', array_merge($commonLog, array(
						'Products ID'      => $productID,
						'Message'          => 'Updating existing Comments'
						)));
					}
				}else{
					logUpdate('product_barcode', array_merge($commonLog, array(
					'Products ID'      => $productID,
					'Message'          => 'Updating existing Comments'
					)));
				}
			}
		}
	}

	function splitFile($fileName){
		$infp = fopen($this->tempDir . $fileName, "r");

		//toprow has the field headers
		$toprow = fgets($infp,32768);

		$filecount = 1;

		logSplit('split_result', array(
			'Message:' => sprintf(sysLanguage::get('TEXT_INFO_CREATING_SPLIT'), $filecount)
		));

		$tmpfname = $this->tempDir . "BP_Split" . $filecount . ".txt";
		$fp = fopen($tmpfname, "w+");
		fwrite($fp, $toprow);

		$linecount = 0;
		$line = fgets($infp,32768);
		while ($line){
			// walking the entire file one row at a time
			// but a line is not necessarily a complete row, we need to split on rows that have "EOREOR" at the end
			$line = str_replace('"EOREOR"', 'EOREOR', $line);
			fwrite($fp, $line);
			if (strpos($line, 'EOREOR')){
				// we found the end of a line of data, store it
				$linecount++; // increment our line counter
				if ($linecount >= $this->maxRecords){
					logSplit('split_result', array(
						'Message:' => sprintf(sysLanguage::get('TEXT_INFO_ADDED_RECORDS'), $linecount)
					));
					$linecount = 0; // reset our line counter
					// close the existing file and open another;
					fclose($fp);
					// increment filecount
					$filecount++;
					logSplit('split_result', array(
						'Message:' => sprintf(sysLanguage::get('TEXT_INFO_CREATING_SPLIT'), $filecount)
					));
					$tmpfname = $this->tempDir . "BP_Split" . $filecount . ".txt";
					//Open next file name
					$fp = fopen($tmpfname, "w+");
					fwrite($fp, $toprow);
				}
			}
			$line=fgets($infp,32768);
		}

		logSplit('split_result', array(
			'Message:' => 'Added ' . $linecount . ' records and closing file...'
		));
		fclose($fp);
		fclose($infp);
		logSplit('split_result', array(
			'Message:' => sysLanguage::get('TEXT_INFO_SPLIT_FILE')
		));
	}

	function cleanupValues($arr){
		/*foreach($arr as $key => $value){
			if (function_exists('ini_get')) {
				if (substr($value,-1) == '"'){
					if (ini_get('magic_quotes_runtime') == 1){
						$arr[$key] = substr($value, 2, strlen($value)-4);
					} else {
						$arr[$key] = substr($value, 1, strlen($value)-2);
					}
				}

				$arr[$key] = str_replace('""', "&#34", $arr[$key]);

				if ($this->replaceQuotes === true){
					$arr[$key] = str_replace('"', "&#34", $arr[$key]);
					$arr[$key] = str_replace("'", "&#39", $arr[$key]);
				}
			}
		}           */
		return $arr;
	}
}

$logArray = array(
	'new'    => array(),
	'update' => array(),
	'error'  => array()
);

function logNew($type, $lInfo){
	global $logArray;
	$logArray['new'][$type][] = $lInfo;
}

function logUpdate($type, $lInfo){
	global $logArray;
	$logArray['update'][$type][] = $lInfo;
}

function logError($type, $lInfo){
	global $logArray;
	$logArray['error'][$type][] = $lInfo;
}

function logSplit($type, $lInfo){
	global $logArray;
	$logArray['split'][$type][] = $lInfo;
}

function getLogTabs(){
	global $logArray;
	$html = '';
	if (isset($logArray['error']) && sizeof($logArray['error']) > 0){
		$html .= '<li><a href="#tab1">Errors</a></li>';
	}
	if (isset($logArray['update']) && sizeof($logArray['update']) > 0){
		$html .= '<li><a href="#tab2">Updates</a></li>';
	}
	if (isset($logArray['new']) && sizeof($logArray['new']) > 0){
		$html .= '<li><a href="#tab3">New Entries</a></li>';
	}
	if (isset($logArray['split']) && sizeof($logArray['split']) > 0){
		$html .= '<li><a href="#tab4">Split Results</a></li>';
	}
	return $html;
}

function getLogDivs(){
	global $logArray;
	$html = '';
	if (isset($logArray['error']) && sizeof($logArray['error']) > 0){
		$html .= '<div id="tab1">' . logSection('error', $logArray['error']) . '</div>';
	}
	if (isset($logArray['update']) && sizeof($logArray['update']) > 0){
		$html .= '<div id="tab2">' . logSection('update', $logArray['update']) . '</div>';
	}
	if (isset($logArray['new']) && sizeof($logArray['new']) > 0){
		$html .= '<div id="tab3">' . logSection('new', $logArray['new']) . '</div>';
	}
	if (isset($logArray['split']) && sizeof($logArray['split']) > 0){
		$html .= '<div id="tab4">' . logSection('split', $logArray['split']) . '</div>';
	}
	$html .= '</div>';
	return $html;
}

function infoTable($info, &$tableGrid){
	for ($i=0; $i<sizeof($info); $i++){
		$columns = array();
		foreach($info[$i] as $text => $val){
			$columns[] = array(
				'text' => $val
			);
		}
		$tableGrid->addBodyRow(array(
			'columns' => $columns
		));
	}
}

function logSection($divID, $lArr){
	$html = '';
	foreach($lArr as $type => $eInfo){
		$html .= '<div style="margin:5px;">TYPE: ' . $type . '</div><a class="expandHref" href="Javascript:void(0)" onclick="showHideDivs(\'#' . $divID . '_' . $type . '\');">Click Here To Expand</a><div id="' . $divID . '_' . $type . '" style="margin:10px;display:none;">';
		
		$gridHeaders = array();
		foreach($eInfo[0] as $text => $val){
			$gridHeaders[] = array(
				'text' => $text
			);
		}
		$tableGrid = htmlBase::newElement('grid');
		$tableGrid->addHeaderRow(array(
			'columns' => $gridHeaders
		));
		infoTable($eInfo, $tableGrid);
		$html .= $tableGrid->draw();
		$html .= '</div>';
	}
	return $html;
}
?>