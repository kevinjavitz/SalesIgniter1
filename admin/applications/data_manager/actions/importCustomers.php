<?php
    /* New Tabbed Logging - BEGIN */
	require('includes/classes/data_populate/export.php');
	$dataExport = new dataExport();
	$uploaded = false;
	if (isset($_FILES['usrfl'])){
		$upload = new upload('usrfl');
		$upload->set_extensions(array('txt', 'xls', 'csv'));
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

			if (!isset($items['v_customers_email_address']) || strlen($items['v_customers_email_address']) <= 0 || $items['v_customers_email_address'] == ''){
				$lineIterator->next();
				continue;
			}

			$emailAddress = $items['v_customers_email_address'];

			$Customers = Doctrine_Query::create()
			->from('Customers')
			->where('customers_email_address = ?', $emailAddress)
			->fetchOne();


			if($Customers){
				$customers_id = $Customers->customers_id;
				$Customers->customers_firstname = (isset($items['v_customers_firstname'])?$items['v_customers_firstname']:'');
				$Customers->customers_lastname = (isset($items['v_customers_lastname'])?$items['v_customers_lastname']:'');
				$Customers->customers_telephone = (isset($items['v_customers_telephone'])?$items['v_customers_telephone']:'');
				$Customers->customers_dob = (isset($items['v_customers_dob'])?$items['v_customers_dob']:'');
				$Customers->customers_gender = (isset($items['v_customers_gender'])?$items['v_customers_gender']:'');
				$Customers->customers_newsletter = (isset($items['v_customers_newsletter'])?$items['v_customers_newsletter']:'');
				$Customers->customers_fax = (isset($items['v_customers_fax'])?$items['v_customers_fax']:'');
				$isNew = false;
			}else{

				$Customers = new Customers();
				$Customers->customers_email_address = $emailAddress;
				$Customers->language_id = Session::get('languages_id');
				$Customers->customers_firstname = (isset($items['v_customers_firstname'])?$items['v_customers_firstname']:'');
				$Customers->customers_lastname = (isset($items['v_customers_lastname'])?$items['v_customers_lastname']:'');
				$Customers->customers_telephone = (isset($items['v_customers_telephone'])?$items['v_customers_telephone']:'');
				$Customers->customers_dob = (isset($items['v_customers_dob'])?$items['v_customers_dob']:'');
				$Customers->customers_gender = (isset($items['v_customers_gender'])?$items['v_customers_gender']:'');
				$Customers->customers_newsletter = (isset($items['v_customers_newsletter'])?$items['v_customers_newsletter']:'');
				$Customers->customers_fax = (isset($items['v_customers_fax'])?$items['v_customers_fax']:'');
				$Customers->save();
				$customers_id = $Customers->customers_id;
				$isNew = true;
			}
				$i = 1;
				while(true){

					if(!isset($items['v_customers_addressbook_firstname_'.$i]) && !isset($items['v_customers_addressbook_lastname_'.$i]) && !isset($items['v_customers_addressbook_postcode_'.$i]) && !isset($items['v_customers_addressbook_state_'.$i])){
						break;
					}

					$isNewAddress = true;
					if(!$isNew){
						$QAddressBook = Doctrine_Query::create()
						->from('AddressBook')
						->where('customers_id = ?', $customers_id)
						->andWhere('entry_firstname = ?', (isset($items['v_customers_addressbook_firstname_'.$i])?$items['v_customers_addressbook_firstname_'.$i]:''))
						->andWhere('entry_lastname = ?', (isset($items['v_customers_addressbook_lastname_'.$i])?$items['v_customers_addressbook_lastname_'.$i]:''))
						->andWhere('entry_gender = ?', (isset($items['v_customers_addressbook_gender_'.$i])?$items['v_customers_addressbook_gender_'.$i]:''))
						->andWhere('entry_street_address = ?', (isset($items['v_customers_addressbook_address_'.$i])?$items['v_customers_addressbook_address_'.$i]:''))
						->andWhere('entry_city = ?', (isset($items['v_customers_addressbook_city_'.$i])?$items['v_customers_addressbook_city_'.$i]:''))
						->andWhere('entry_state = ?', (isset($items['v_customers_addressbook_state_'.$i])?$items['v_customers_addressbook_state_'.$i]:''))
						->andWhere('entry_postcode = ?', (isset($items['v_customers_addressbook_postcode_'.$i])?$items['v_customers_addressbook_postcode_'.$i]:''))
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						if(count($QAddressBook) > 0){
							$isNewAddress = false;
						}
					}
					if($isNewAddress){
						$AddressBook = new AddressBook();
						$AddressBook->customers_id = $customers_id;
						$AddressBook->entry_firstname = (isset($items['v_customers_addressbook_firstname_'.$i])?$items['v_customers_addressbook_firstname_'.$i]:'');
						$AddressBook->entry_lastname = (isset($items['v_customers_addressbook_lastname_'.$i])?$items['v_customers_addressbook_lastname_'.$i]:'');
						$AddressBook->entry_company = (isset($items['v_customers_addressbook_company_'.$i])?$items['v_customers_addressbook_company_'.$i]:'');
						$AddressBook->entry_gender = (isset($items['v_customers_addressbook_gender_'.$i])?$items['v_customers_addressbook_gender_'.$i]:'');
						$AddressBook->entry_street_address = (isset($items['v_customers_addressbook_address_'.$i])?$items['v_customers_addressbook_address_'.$i]:'');
						$AddressBook->entry_city = (isset($items['v_customers_addressbook_city_'.$i])?$items['v_customers_addressbook_city_'.$i]:'');
						$AddressBook->entry_state = (isset($items['v_customers_addressbook_state_'.$i])?$items['v_customers_addressbook_state_'.$i]:'');
						$AddressBook->entry_postcode = (isset($items['v_customers_addressbook_postcode_'.$i])?$items['v_customers_addressbook_postcode_'.$i]:'');

						$Qcountry = Doctrine_Query::create()
						->from('Countries')
						->where('countries_name = ?', (isset($items['v_customers_addressbook_country_'.$i])?$items['v_customers_addressbook_country_'.$i]:''))
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						if(count($Qcountry) > 0){
							$AddressBook->entry_country_id = $Qcountry[0]['countries_id'];
						}else{
							$AddressBook->entry_country_id = '0';
						}

						$QZones = Doctrine_Query::create()
						->from('Zones')
						->where('zone_country_id = ?', (isset($Qcountry[0]['countries_id']) ? $Qcountry[0]['countries_id']:'0'))
						->andWhere('zone_name = ?', (isset($items['v_customers_addressbook_state_'.$i])?$items['v_customers_addressbook_state_'.$i]:'0'))
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						if(count($QZones) > 0){
							$AddressBook->entry_zone_id = $QZones[0]['zone_id'];
						}else{
							$AddressBook->entry_zone_id = '0';
						}
						$AddressBook->save();
						if($i == 1){
							$Customers->customers_default_address_id = $AddressBook->address_book_id;
						}
						if($i == 2){
							$Customers->customers_delivery_address_id = $AddressBook->address_book_id;
						}
					}
					$i++;
				}
				$Customers->save();
				$customersInfo = new CustomersInfo();
				$customersInfo->customers_info_id = $customers_id;
				$customersInfo->customers_info_number_of_logons = 0;
				$customersInfo->customers_info_date_account_created = date('Y-m-d H:i:s');
				$customersInfo->global_product_notifications = 0;
				$customersInfo->save();


			$ordersLogArr = array(
				'ID:'              => $customers_id
			);

			logNew('customer', $ordersLogArr);

			$Customers->free();

			$lineIterator->next();
		}
	}
?>