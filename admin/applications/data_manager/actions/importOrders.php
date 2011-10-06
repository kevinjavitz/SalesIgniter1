<?php
    /* New Tabbed Logging - BEGIN */
	require('includes/classes/data_populate/export.php');
	$dataExport = new dataExport();
	$uploaded = false;
	if (isset($_FILES['usrfl'])){
		$upload = new upload('usrfl');
		$upload->set_extensions(array('txt', 'xls', 'csv', 'tsv'));
		$upload->set_destination($dataExport->tempDir);
		if ($upload->parse() && $upload->save()) {
			$uploaded = true;
			$showLogInfo = true;
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

	function getLogTabs(){
		global $logArray;
		$html = '';
		if (sizeof($logArray['error']) > 0){
			$html .= '<li><a href="#tab1">Errors</a></li>';
		}
		if (sizeof($logArray['update']) > 0){
			$html .= '<li><a href="#tab2">Updates</a></li>';
		}
		if (sizeof($logArray['new']) > 0){
			$html .= '<li><a href="#tab3">New Entries</a></li>';
		}
		return $html;
	}

	function getLogDivs(){
		global $logArray;
		$html = '';
		if (sizeof($logArray['error']) > 0){
			$html .= '<div id="tab1">' . logSection('error', $logArray['error']) . '</div>';
		}
		if (sizeof($logArray['update']) > 0){
			$html .= '<div id="tab2">' . logSection('update', $logArray['update']) . '</div>';
		}
		if (sizeof($logArray['new']) > 0){
			$html .= '<div id="tab3">' . logSection('new', $logArray['new']) . '</div>';
		}
		$html .= '</div>';
		return $html;
	}

	function infoTable($columns, $info){
		$html = '<table cellpadding="2" cellspacing="0" border="0">';
		for ($i=0; $i<sizeof($info); $i++){
			$col=1;
			$html .= '<tr>';
			foreach($info[$i] as $text => $val){
				$html .= '<td class="main" valign="top"><b>' . $text . '</b></td><td class="main" valign="top">' . $val . '</td>';
				$col++;
				if ($col > $columns){
					$col=1;
					$html .= '</tr><tr>';
				}
			}
			$html .= '</tr><tr><td colspan="' . $columns . '"><br></td></tr>';
		}
		$html .= '</table>';
		return $html;
	}

	function logSection($divID, $lArr){
		$html = '';
		foreach($lArr as $type => $eInfo){
			$html .= '<div style="margin:5px;">TYPE: ' . $type . '</div><a class="expandHref" href="Javascript:void(0)" onclick="showHideDivs(\'#' . $divID . '_' . $type . '\');">Click Here To Expand</a><div id="' . $divID . '_' . $type . '" style="margin:10px;display:none;">';
			if ($type == 'product_barcode' || $type == 'product_description'){
				$html .= infoTable(1, $eInfo);
			}else{
				$html .= infoTable(2, $eInfo);
			}
			$html .= '</div>';
		}
		return $html;
	}
	/* New Tabbed Logging - END*/
	class dataImportHeaderIterator extends ArrayIterator {
		public function current(){
			return str_replace('"', '', parent::current());
		}
	}
	
	class dataImportLineIterator extends ArrayIterator {
		public function current(){
			return ltrim(rtrim(parent::current()));
		}
	}
	
	class dataImportColumnIterator extends ArrayIterator {
		public function current(){
		}
		
		public function offsetClean($offset, $replace_quotes){
			if (parent::offsetExists($offset) === false) return;
			
			if (function_exists('ini_get')){
				$currentVal = parent::offsetGet($offset);
				if (ini_get('magic_quotes_runtime') == 1){
					if (substr($currentVal, -1) == '"'){
						$currentVal = substr($currentVal, 2, strlen($currentVal)-4);
					}
					$currentVal = str_replace('\"\"', "&#34", $currentVal);
					if ($replace_quotes){
						$currentVal = str_replace('\"', "&#34", $currentVal);
						$currentVal = str_replace("\'", "&#39", $currentVal);
					}
				}else{
					if (substr($currentVal,-1) == '"'){
						$currentVal = substr($currentVal, 1, strlen($currentVal)-2);
					}
					$currentVal = str_replace('""', "&#34", $currentVal);
					if ($replace_quotes){
						$currentVal = str_replace('"', "&#34", $currentVal);
						$currentVal = str_replace("'", "&#39", $currentVal);
					}
				}
				parent::offsetSet($offset, $currentVal);
			}
		}
	}
	
	function tep_get_uploaded_file($filename){
		if (isset($_FILES[$filename])){
			$uploaded_file = array(
				'name' => $_FILES[$filename]['name'],
				'type' => $_FILES[$filename]['type'],
				'size' => $_FILES[$filename]['size'],
				'tmp_name' => $_FILES[$filename]['tmp_name']
			);
		}else{
			$uploaded_file = array(
				'name' => $GLOBALS[$filename . '_name'],
				'type' => $GLOBALS[$filename . '_type'],
				'size' => $GLOBALS[$filename . '_size'],
				'tmp_name' => $GLOBALS[$filename]
			);
		}
		return $uploaded_file;
	}
	
	// the $filename parameter is an array with the following elements:
	// name, type, size, tmp_name
	function tep_copy_uploaded_file($filename, $target){
		if (substr($target, -1) != '/') $target .= '/';
		$target .= $filename['name'];
		move_uploaded_file($filename['tmp_name'], $target);
	}

	if ((isset($localfile) && $localfile) || $uploaded === true){
		if ($uploaded === true){
			$fileName = $upload->filename;
			
			$messageStack->addSession('pageStack', '<p>File uploaded.<br />Temporary filename: ' . $upload->tmp_filename . '<br />User filename: ' . $fileName . '<br />Size: ' . $upload->file_size . '<br /></p>', 'success');
		}elseif (isset($localfile) && $localfile){
			$file = tep_get_uploaded_file('usrfl');
			if (is_uploaded_file($file['tmp_name'])) {
				tep_copy_uploaded_file($file, sysConfig::get('DIR_FS_DOCUMENT_ROOT') . $tempdir);
			}
			
			$fileName = $localfile;
			$messageStack->addSession('pageStack', '<p>File uploaded.<br />Filename: ' . $fileName . '</p>', 'success');
		}
		
		$originalContents = file($dataExport->tempDir . $fileName);

		// now we string the entire thing together in case there were carriage returns in the data
		$fileString = '';
		foreach($originalContents as $fileLine){
			$fileString .= $fileLine;
		}
		
		// now newreaded has the entire file together without the carriage returns.
		// if for some reason excel put qoutes around our EOREOR, remove them then split into rows
		$fileString = str_replace('"EOREOR"', 'EOREOR', $fileString);
		$fileContent = explode($separator . 'EOREOR', $fileString);
		
		// Now we'll populate the filelayout based on the header row.
		$fileHeaders = explode($separator, $fileContent[0]); // explode the first row, it will be our filelayout
		unset($fileContent[0]); //  we don't want to process the headers with the data
		
		$fileHeaderObj = new ArrayObject($fileHeaders);
		$fileHeaderObj->setIteratorClass('dataImportHeaderIterator');
		$fileHeaderIterator = $fileHeaderObj->getIterator();
		
		$fileArrObj = new ArrayObject($fileContent);
		$fileArrObj->setIteratorClass('dataImportLineIterator');
		$lineIterator = $fileArrObj->getIterator();
		
		while($lineIterator->valid()){
			$currentLine = $lineIterator->current();

			// blow it into an array, splitting on the tabs
			$columns = explode($separator, $currentLine);
			$columnsObj = new ArrayObject($columns);
			$columnsObj->setIteratorClass('dataImportColumnIterator');
			$columnIterator = $columnsObj->getIterator();
			
			$items = array();
			while($fileHeaderIterator->valid()){
				$i = $fileHeaderIterator->key();
				if ($columnIterator->offsetExists($i) === false){
					$columnIterator->offsetSet($i, '');
				}else{
					$columnIterator->offsetClean($i, $replace_quotes);
				}
				$items[$fileHeaderIterator->current()] = trim($columnIterator->offsetGet($i));
				$fileHeaderIterator->next();
			}
			$fileHeaderIterator->rewind();
			$Orders = new Orders();


			if (!isset($items['v_orders_customers_email_address']) || strlen($items['v_orders_customers_email_address']) <= 0 || $items['v_orders_customers_email_address'] == ''){
				$lineIterator->next();
				continue;
			}

			$emailAddress = $items['v_orders_customers_email_address'];

			$QCustomers = Doctrine_Query::create()
			->from('Customers')
			->where('customers_email_address = ?', $emailAddress)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if(count($QCustomers) > 0){
				$customers_id = $QCustomers[0]['customers_id'];
			}else{
				$firstname = 'None';
				$lastname = 'None';
				if(isset($items['v_orders_customers_name'])){
					$name = explode(' ', $items['v_orders_customers_name']);
					$firstname = $name[0];
					$lastname = $name[1];
				}
				$Customers = new Customers();
				$Customers->customers_email_address = $emailAddress;
				$Customers->language_id = Session::get('languages_id');
				$Customers->customers_firstname = $firstname;
				$Customers->customers_lastname = $lastname;
				$Customers->customers_telephone = (isset($items['v_orders_customers_telephone'])?$items['v_orders_customers_telephone']:'');
				$Customers->save();
				$customers_id = $Customers->customers_id;
				$AddressBook = new AddressBook();
				$AddressBook->customers_id = $customers_id;
				$firstname = 'None';
				$lastname = 'None';
				$name = 'None';
				if(isset($items['v_orders_billing_name'])){
					$name = explode(' ', $items['v_orders_billing_name']);
					$firstname = $name[0];
					$lastname = $name[1];
				}
				$AddressBook->entry_firstname = $firstname;
				$AddressBook->entry_lastname = $lastname;
				$AddressBook->entry_street_address = (isset($items['v_orders_billing_address'])?$items['v_orders_billing_address']:'');
				$AddressBook->entry_city = (isset($items['v_orders_billing_city'])?$items['v_orders_billing_city']:'');
				$AddressBook->entry_state = (isset($items['v_orders_billing_state'])?$items['v_orders_billing_state']:'');
				$AddressBook->entry_postcode = (isset($items['v_orders_billing_postcode'])?$items['v_orders_billing_postcode']:'');

				$OrdersAddress = new OrdersAddresses();
				$OrdersAddress->orders_id = $Orders->orders_id;
				$OrdersAddress->entry_name = $name;
				$OrdersAddress->entry_company = (isset($items['v_orders_customers_company'])?$items['v_orders_customers_company']:'');
				$OrdersAddress->entry_street_address = (isset($items['v_orders_billing_address'])?$items['v_orders_billing_address']:'');
				$OrdersAddress->entry_city = (isset($items['v_orders_billing_city'])?$items['v_orders_billing_city']:'');
				$OrdersAddress->entry_state = (isset($items['v_orders_billing_state'])?$items['v_orders_billing_state']:'');
				$OrdersAddress->entry_country = (isset($items['v_orders_billing_country'])?$items['v_orders_billing_country']:'');
				$OrdersAddress->entry_postcode = (isset($items['v_orders_billing_postcode'])?$items['v_orders_billing_postcode']:'');
				$OrdersAddress->address_type = 'billing';


				$Qcountry = Doctrine_Query::create()
				->from('Countries')
				->where('countries_name = ?', (isset($items['v_orders_billing_country'])?$items['v_orders_billing_country']:''))
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				if(count($Qcountry) > 0){
					$AddressBook->entry_country_id = $Qcountry[0]['countries_id'];
					$format_id = $Qcountry[0]['address_format_id'];
				}else{
					$AddressBook->entry_country_id = '0';
					$format_id = '1';
				}
				$OrdersAddress->entry_format_id = $format_id;
				$OrdersAddress->save();

				$OrdersAddress = new OrdersAddresses();
				$OrdersAddress->orders_id = $Orders->orders_id;
				$OrdersAddress->entry_name = $name;
				$OrdersAddress->entry_company = (isset($items['v_orders_customers_company'])?$items['v_orders_customers_company']:'');
				$OrdersAddress->entry_street_address = (isset($items['v_orders_billing_address'])?$items['v_orders_billing_address']:'');
				$OrdersAddress->entry_city = (isset($items['v_orders_billing_city'])?$items['v_orders_billing_city']:'');
				$OrdersAddress->entry_state = (isset($items['v_orders_billing_state'])?$items['v_orders_billing_state']:'');
				$OrdersAddress->entry_country = (isset($items['v_orders_billing_country'])?$items['v_orders_billing_country']:'');
				$OrdersAddress->entry_postcode = (isset($items['v_orders_billing_postcode'])?$items['v_orders_billing_postcode']:'');
				$OrdersAddress->address_type = 'customer';
				$OrdersAddress->entry_format_id = $format_id;
				$OrdersAddress->save();

				$QZones = Doctrine_Query::create()
				->from('Zones')
				->where('zone_country_id = ?', (isset($Qcountry[0]['countries_id']) ? $Qcountry[0]['countries_id']:'0'))
				->andWhere('zone_name = ?', (isset($items['v_orders_billing_state'])?$items['v_orders_billing_state']:'0'))
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				if(count($QZones) > 0){
					$AddressBook->entry_zone_id = $QZones[0]['zone_id'];
				}else{
					$AddressBook->entry_zone_id = '0';
				}
				$AddressBook->save();
				$Customers->customers_default_address_id = $AddressBook->address_book_id;
				if((isset($items['v_orders_billing_address'])?$items['v_orders_billing_address']:'') != (isset($items['v_orders_shipping_address'])?$items['v_orders_shipping_address']:'')){
					$AddressBook = new AddressBook();
					$AddressBook->customers_id = $customers_id;
					$firstname = 'None';
					$lastname = 'None';
					$name = 'None';
					if(isset($items['v_orders_shipping_name'])){
						$name = explode(' ', $items['v_orders_shipping_name']);
						$firstname = $name[0];
						$lastname = $name[1];
					}
					$AddressBook->entry_firstname = $firstname;
					$AddressBook->entry_lastname = $lastname;
					$AddressBook->entry_street_address = (isset($items['v_orders_shipping_address'])?$items['v_orders_shipping_address']:'');
					$AddressBook->entry_city = (isset($items['v_orders_shipping_city'])?$items['v_orders_shipping_city']:'');
					$AddressBook->entry_state = (isset($items['v_orders_shipping_state'])?$items['v_orders_shipping_state']:'');
					$AddressBook->entry_postcode = (isset($items['v_orders_shipping_postcode'])?$items['v_orders_shipping_postcode']:'');

					$OrdersAddress = new OrdersAddresses();
					$OrdersAddress->orders_id = $Orders->orders_id;
					$OrdersAddress->entry_name = $name;
					$OrdersAddress->entry_company = (isset($items['v_orders_customers_company'])?$items['v_orders_customers_company']:'');
					$OrdersAddress->entry_street_address = (isset($items['v_orders_shipping_address'])?$items['v_orders_shipping_address']:'');
					$OrdersAddress->entry_city = (isset($items['v_orders_shipping_city'])?$items['v_orders_shipping_city']:'');
					$OrdersAddress->entry_state = (isset($items['v_orders_shipping_state'])?$items['v_orders_shipping_state']:'');
					$OrdersAddress->entry_country = (isset($items['v_orders_shipping_country'])?$items['v_orders_shipping_country']:'');
					$OrdersAddress->entry_postcode = (isset($items['v_orders_shipping_postcode'])?$items['v_orders_shipping_postcode']:'');
					$OrdersAddress->address_type = 'delivery';

					$Qcountry = Doctrine_Query::create()
						->from('Countries')
						->where('countries_name = ?', (isset($items['v_orders_shipping_country'])?$items['v_orders_shipping_country']:''))
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					if(count($Qcountry) > 0){
						$AddressBook->entry_country_id = $Qcountry[0]['countries_id'];
						$format_id = $Qcountry[0]['address_format_id'];
					}else{
						$AddressBook->entry_country_id = '0';
						$format_id = '1';
					}
					$OrdersAddress->entry_format_id = $format_id;
					$OrdersAddress->save();

					$OrdersAddress = new OrdersAddresses();
					$OrdersAddress->orders_id = $Orders->orders_id;
					$OrdersAddress->entry_name = $name;
					$OrdersAddress->entry_company = (isset($items['v_orders_customers_company'])?$items['v_orders_customers_company']:'');
					$OrdersAddress->entry_street_address = (isset($items['v_orders_shipping_address'])?$items['v_orders_shipping_address']:'');
					$OrdersAddress->entry_city = (isset($items['v_orders_shipping_city'])?$items['v_orders_shipping_city']:'');
					$OrdersAddress->entry_state = (isset($items['v_orders_shipping_state'])?$items['v_orders_shipping_state']:'');
					$OrdersAddress->entry_country = (isset($items['v_orders_shipping_country'])?$items['v_orders_shipping_country']:'');
					$OrdersAddress->entry_postcode = (isset($items['v_orders_shipping_postcode'])?$items['v_orders_shipping_postcode']:'');
					$OrdersAddress->address_type = 'pickup';
					$OrdersAddress->entry_format_id = $format_id;
					$OrdersAddress->save();


					$QZones = Doctrine_Query::create()
						->from('Zones')
						->where('zone_country_id = ?', (isset($Qcountry[0]['countries_id']) ? $Qcountry[0]['countries_id']:'0'))
						->andWhere('zone_name = ?', (isset($items['v_orders_shipping_state'])?$items['v_orders_shipping_state']:'0'))
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					if(count($QZones) > 0){
						$AddressBook->entry_zone_id = $QZones[0]['zone_id'];
					}else{
						$AddressBook->entry_zone_id = '0';
					}
					$AddressBook->save();
					$Customers->customers_delivery_address_id = $AddressBook->address_book_id;
				}else{
					$OrdersAddress = new OrdersAddresses();
					$OrdersAddress->orders_id = $Orders->orders_id;
					$OrdersAddress->entry_name = $name;
					$OrdersAddress->entry_company = (isset($items['v_orders_customers_company'])?$items['v_orders_customers_company']:'');
					$OrdersAddress->entry_street_address = (isset($items['v_orders_billing_address'])?$items['v_orders_billing_address']:'');
					$OrdersAddress->entry_city = (isset($items['v_orders_billing_city'])?$items['v_orders_billing_city']:'');
					$OrdersAddress->entry_state = (isset($items['v_orders_billing_state'])?$items['v_orders_billing_state']:'');
					$OrdersAddress->entry_country = (isset($items['v_orders_billing_country'])?$items['v_orders_billing_country']:'');
					$OrdersAddress->entry_postcode = (isset($items['v_orders_billing_postcode'])?$items['v_orders_billing_postcode']:'');
					$OrdersAddress->address_type = 'delivery';
					$Qcountry = Doctrine_Query::create()
						->from('Countries')
						->where('countries_name = ?', (isset($items['v_orders_billing_country'])?$items['v_orders_billing_country']:''))
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					if(count($Qcountry) > 0){
						$format_id = $Qcountry[0]['address_format_id'];
					}else{
						$format_id = '1';
					}
					$OrdersAddress->entry_format_id = $format_id;
					$OrdersAddress->save();

					$OrdersAddress = new OrdersAddresses();
					$OrdersAddress->orders_id = $Orders->orders_id;
					$OrdersAddress->entry_name = $name;
					$OrdersAddress->entry_company = (isset($items['v_orders_customers_company'])?$items['v_orders_customers_company']:'');
					$OrdersAddress->entry_street_address = (isset($items['v_orders_billing_address'])?$items['v_orders_billing_address']:'');
					$OrdersAddress->entry_city = (isset($items['v_orders_billing_city'])?$items['v_orders_billing_city']:'');
					$OrdersAddress->entry_state = (isset($items['v_orders_billing_state'])?$items['v_orders_billing_state']:'');
					$OrdersAddress->entry_country = (isset($items['v_orders_billing_country'])?$items['v_orders_billing_country']:'');
					$OrdersAddress->entry_postcode = (isset($items['v_orders_billing_postcode'])?$items['v_orders_billing_postcode']:'');
					$OrdersAddress->address_type = 'pickup';
					$Qcountry = Doctrine_Query::create()
						->from('Countries')
						->where('countries_name = ?', (isset($items['v_orders_billing_country'])?$items['v_orders_billing_country']:''))
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					if(count($Qcountry) > 0){
						$format_id = $Qcountry[0]['address_format_id'];
					}else{
						$format_id = '1';
					}
					$OrdersAddress->entry_format_id = $format_id;
					$OrdersAddress->save();

				}
				$Customers->save();
				$customersInfo = new CustomersInfo();
				$customersInfo->customers_info_id = $customers_id;
				$customersInfo->customers_info_number_of_logons = 0;
				$customersInfo->customers_info_date_account_created = date('Y-m-d H:i:s');
				$customersInfo->global_product_notifications = 0;
				$customersInfo->save();
			}

			$Orders->customers_id = $customers_id;
			$Orders->currency = Session::get('currency');
			$Orders->currency_value = '1.00';

			if(isset($items['v_orders_date_purchased'])){
				$Orders->date_purchased = $items['v_orders_date_purchased'];
			}

			if(isset($items['v_orders_shipping_method'])){
				$Orders->shipping_module = $items['v_orders_shipping_method'];
			}

			if(isset($items['v_orders_payment_method'])){
				$Orders->payment_module = $items['v_orders_payment_method'];
			}


			$Orders->customers_telephone = (isset($items['v_orders_customers_telephone'])?$items['v_orders_customers_telephone']:'');
			$Orders->save();
			if(isset($items['v_orders_status'])){
				$QStatus = Doctrine_Query::create()
				->from('OrdersStatusDescription')
				->where('orders_status_name = ?', $items['v_orders_status'])
				->andWhere('language_id = ?', Session::get('languages_id'))
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				if(count($QStatus) > 0){
					$Orders->orders_status = $QStatus[0]['orders_status_id'];
					$status_id = $QStatus[0]['orders_status_id'];
				}else{
					$Status = new OrdersStatus();
					$Status->save();
					$StatusDesc = new OrdersStatusDescription();
					$StatusDesc->orders_status_id = $Status->orders_status_id;
					$StatusDesc->language_id = Session::get('languages_id');
					$StatusDesc->orders_status_name = $items['v_orders_status'];
					$StatusDesc->save();
					$status_id = $Status->orders_status_id;
				}
				$Orders->save();
				$OrdersStatusHistory = new OrdersStatusHistory();
				$OrdersStatusHistory->orders_id = $Orders->orders_id;
				$OrdersStatusHistory->orders_status_id = $status_id;
				$OrdersStatusHistory->date_added = date('Y-m-d H:i:s');
				$OrdersStatusHistory->save();
			}

			if(isset($items['v_orders_total']) && !empty($items['v_orders_total'])){
				$OrdersTotal = new OrdersTotal();
				$OrdersTotal->orders_id = $Orders->orders_id;
				$OrdersTotal->title = 'Total:';
				$OrdersTotal->value = $items['v_orders_total'];
				$OrdersTotal->text = '<b>'.$currencies->format($items['v_orders_total']).'</b>';
				$OrdersTotal->module_type = 'total';
				$OrdersTotal->save();
			}

			if(isset($items['v_orders_subtotal']) && !empty($items['v_orders_subtotal'])){
				$OrdersTotal = new OrdersTotal();
				$OrdersTotal->orders_id = $Orders->orders_id;
				$OrdersTotal->title = 'Sub-Total:';
				$OrdersTotal->value = $items['v_orders_subtotal'];
				$OrdersTotal->text = $currencies->format($items['v_orders_subtotal']);
				$OrdersTotal->module_type = 'subtotal';
				$OrdersTotal->save();
			}

			if(isset($items['v_orders_tax']) && !empty($items['v_orders_tax'])){
				$OrdersTotal = new OrdersTotal();
				$OrdersTotal->orders_id = $Orders->orders_id;
				$OrdersTotal->title = 'Tax:';
				$OrdersTotal->value = $items['v_orders_tax'];
				$OrdersTotal->text = $currencies->format($items['v_orders_tax']);
				$OrdersTotal->module_type = 'tax';
				$OrdersTotal->save();
			}

			if(isset($items['v_orders_shipping_price']) && !empty($items['v_orders_shipping_price'])){
				$OrdersTotal = new OrdersTotal();
				$OrdersTotal->orders_id = $Orders->orders_id;
				$OrdersTotal->title = 'Shipping:';
				$OrdersTotal->value = $items['v_orders_shipping_price'];
				$OrdersTotal->text = $currencies->format($items['v_orders_shipping_price']);
				$OrdersTotal->module_type = 'shipping';
				$OrdersTotal->save();
			}





			$i = 0;
			while(true){
				if(isset($items['v_orders_products_model_'.$i])){
					$QProducts = Doctrine_Query::create()
					->from('Products')
					->where('products_model = ?', $items['v_orders_products_model_'.$i])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if(count($QProducts) > 0){
						$products_id = $QProducts[0]['products_id'];
					}else{
						continue;
					}
					$OrderProducts = new OrdersProducts();
					$OrderProducts->orders_id = $Orders->orders_id;
					$OrderProducts->products_id = $products_id;
					$OrderProducts->products_model = $items['v_orders_products_model_'.$i];
					$OrderProducts->products_name = (isset($items['v_orders_products_name_'.$i])?$items['v_orders_products_name_'.$i]:'');
					$OrderProducts->products_price = (isset($items['v_orders_products_price_'.$i])?$items['v_orders_products_price_'.$i]:'');
					$OrderProducts->final_price = (isset($items['v_orders_products_finalprice_'.$i])?$items['v_orders_products_finalprice_'.$i]:'');
					$OrderProducts->products_quantity = (isset($items['v_orders_products_qty_'.$i])?$items['v_orders_products_qty_'.$i]:'');
					$OrderProducts->products_tax = (isset($items['v_orders_products_tax_'.$i])?$items['v_orders_products_tax_'.$i]:'');
					$OrderProducts->purchase_type = (isset($items['v_orders_products_purchasetype_'.$i])?$items['v_orders_products_purchasetype_'.$i]:'');
					$OrderProducts->save();
					if(isset($items['v_orders_products_purchasetype_'.$i]) && $items['v_orders_products_purchasetype_'.$i] == 'reservation'){
						$OrderProductReservation = new OrdersProductsReservation();
						$OrderProductReservation->orders_products_id =  $OrderProducts->orders_products_id;
						$OrderProductReservation->start_date = (isset($items['v_orders_products_start_date_'.$i])?$items['v_orders_products_start_date_'.$i]:'');
						$OrderProductReservation->end_date = (isset($items['v_orders_products_end_date_'.$i])?$items['v_orders_products_end_date_'.$i]:'');
						$OrderProductReservation->rental_state = 'reserved';
						$OrderProductReservation->semester_name = (isset($items['v_orders_products_semester_name_'.$i])?$items['v_orders_products_semester_name_'.$i]:'');
						$OrderProductReservation->insurance = (isset($items['v_orders_products_insurance_'.$i])?$items['v_orders_products_insurance_'.$i]:'');
						$OrderProductReservation->shipping_method_title = (isset($items['v_orders_products_shipping_method_title_'.$i])?$items['v_orders_products_shipping_method_title_'.$i]:'');
						$OrderProductReservation->shipping_cost = (isset($items['v_orders_products_shipping_cost_'.$i])?$items['v_orders_products_shipping_cost_'.$i]:'');
						$OrderProductReservation->shipping_days_before = (isset($items['v_orders_products_shipping_days_before_'.$i])?$items['v_orders_products_shipping_days_before_'.$i]:'');
						$OrderProductReservation->shipping_days_after = (isset($items['v_orders_products_shipping_days_after_'.$i])?$items['v_orders_products_shipping_days_after_'.$i]:'');
						if(isset($items['v_orders_products_barcode_'.$i])){
							$OrderProductReservation->track_method = 'barcode';
							$Qbarcode = Doctrine_Query::create()
							->from('ProductsInventoryBarcodes')
							->where('barcode = ?', $items['v_orders_products_barcode_'.$i])
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							if(count($Qbarcode) > 0){
								$OrderProductReservation->barcode_id = $Qbarcode[0]['barcode_id'];
							}

						}else{
							$OrderProductReservation->track_method = 'quantity';
						}
						$OrderProductReservation->save();
					}
				}else{
					break;
				}
			}

			$ordersLogArr = array(
				'ID:'              => $Orders->orders_id
			);

			logNew('order', $ordersLogArr);

			$Orders->free();

			$lineIterator->next();
		}
	}
?>