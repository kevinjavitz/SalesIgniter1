<?php

if (isset($_POST['selectedCustomer']) && is_array($_POST['selectedCustomer'])){
    $dataExport = new dataExport();

	$fields = array();

	if (isset($_POST['v_customers_id'])){
		$fields[] = 'v_customers_id';
	}

	if (isset($_POST['v_customers_lastname'])){
		$fields[] = 'v_customers_lastname';
	}

	if (isset($_POST['v_customers_firstname'])){
		$fields[] = 'v_customers_firstname';
	}

	if (isset($_POST['v_customers_email_address'])){
		$fields[] = 'v_customers_email_address';
	}
	if (isset($_POST['v_customers_dob'])){
		$fields[] = 'v_customers_dob';
	}
	if (isset($_POST['v_customers_gender'])){
		$fields[] = 'v_customers_gender';
	}
	if (isset($_POST['v_customers_telephone'])){
		$fields[] = 'v_customers_telephone';
	}
	if (isset($_POST['v_customers_fax'])){
		$fields[] = 'v_customers_fax';
	}
	if (isset($_POST['v_customers_newsletter'])){
		$fields[] = 'v_customers_newsletter';
	}

	if (sizeof($fields) > 0) {
		$dataExport->setHeaders($fields);
	}
		EventManager::notify('CustomersExportQueryFileLayoutHeader', &$dataExport);

		$QHeaders = Doctrine_Query::create()
					->select('count(*) as vmax')
					->from('AddressBook ab')
					->groupBy('ab.customers_id')
					->whereIn('ab.customers_id', $_POST['selectedCustomer'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$maxVal = -1;
		if (count($QHeaders)){
			foreach($QHeaders as $hInfo){
				if($hInfo['vmax'] > $maxVal){
					$maxVal = $hInfo['vmax'];
				}
			}
		}

		if (count($QHeaders)){
			for($i=1;$i<=$maxVal;$i++){
				$fieldsp = array();
				if (isset($_POST['v_customers_addressbook_firstname'])){
					$fieldsp[] = 'v_customers_addressbook_firstname_'.$i;
				}
				if (isset($_POST['v_customers_addressbook_lastname'])){
					$fieldsp[] = 'v_customers_addressbook_lastname_'.$i;
				}
				if (isset($_POST['v_customers_addressbook_gender'])){
					$fieldsp[] = 'v_customers_addressbook_gender_'.$i;
				}
				if (isset($_POST['v_customers_addressbook_company'])){
					$fieldsp[] = 'v_customers_addressbook_company_'.$i;
				}
				if (isset($_POST['v_customers_addressbook_city'])){
					$fieldsp[] = 'v_customers_addressbook_city_'.$i;
				}
				if (isset($_POST['v_customers_addressbook_state'])){
					$fieldsp[] = 'v_customers_addressbook_state_'.$i;
				}
				if (isset($_POST['v_customers_addressbook_country'])){
					$fieldsp[] = 'v_customers_addressbook_country_'.$i;
				}
				if (isset($_POST['v_customers_addressbook_address'])){
					$fieldsp[] = 'v_customers_addressbook_address_'.$i;
				}
				if (isset($_POST['v_customers_addressbook_postcode'])){
					$fieldsp[] = 'v_customers_addressbook_postcode_'.$i;
				}

				if (sizeof($fieldsp) > 0){
					$dataExport->setHeaders($fieldsp);
					unset($fieldsp);
				}
				EventManager::notify('CustomersAddressBookExportQueryFileLayoutHeader', &$dataExport, $i);
			}
		}

		$QfileLayout = Doctrine_Query::create()
		->from('Customers c')
		->leftJoin('c.AddressBook ab')
		->whereIn('c.customers_id', $_POST['selectedCustomer']);

		EventManager::notify('CustomersExportQueryBeforeExecute', &$QfileLayout);

		$Result = $QfileLayout->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$dataRows = array();
		foreach($Result as $cInfo){
			if (isset($_POST['v_customers_id'])){
				$pInfo['v_customers_id'] = $cInfo['customers_id'];
			}

			if (isset($_POST['v_customers_firstname'])){
				$pInfo['v_customers_firstname'] = $cInfo['customers_firstname'];
			}
			if (isset($_POST['v_customers_lastname'])){
				$pInfo['v_customers_lastname'] = $cInfo['customers_lastname'];
			}
			if (isset($_POST['v_customers_email_address'])){
				$pInfo['v_customers_email_address'] = $cInfo['customers_email_address'];
			}
			if (isset($_POST['v_customers_telephone'])){
				$pInfo['v_customers_telephone'] = $cInfo['customers_telephone'];
			}
			if (isset($_POST['v_customers_fax'])){
				$pInfo['v_customers_fax'] = $cInfo['customers_fax'];
			}
			if (isset($_POST['v_customers_dob'])){
				$pInfo['v_customers_dob'] = $cInfo['customers_dob'];
			}
			if (isset($_POST['v_customers_gender'])){
				$pInfo['v_customers_gender'] = $cInfo['customers_gender'];
			}
			if (isset($_POST['v_customers_newsletter'])){
				$pInfo['v_customers_newsletter'] = $cInfo['customers_newsletter'];
			}
		    $i = 3;
			foreach($cInfo['AddressBook'] as $abInfo){
				if($abInfo['address_book_id'] == $cInfo['customers_default_address_id']){

					if (isset($_POST['v_customers_addressbook_firstname'])){
						$pInfo['v_customers_addressbook_firstname_1'] = $abInfo['entry_firstname'];
					}
					if (isset($_POST['v_customers_addressbook_lastname'])){
						$pInfo['v_customers_addressbook_lastname_1'] = $abInfo['entry_lastname'];
					}
					if (isset($_POST['v_customers_addressbook_company'])){
						$pInfo['v_customers_addressbook_company_1'] = $abInfo['entry_company'];
					}
					if (isset($_POST['v_customers_addressbook_gender'])){
						$pInfo['v_customers_addressbook_gender_1'] = $abInfo['entry_gender'];
					}
					if (isset($_POST['v_customers_addressbook_address'])){
						$pInfo['v_customers_addressbook_address_1'] = $abInfo['entry_street_address'];
					}
					if (isset($_POST['v_customers_addressbook_city'])){
						$pInfo['v_customers_addressbook_city_1'] = $abInfo['entry_city'];
					}
					if (isset($_POST['v_customers_addressbook_state'])){
						$pInfo['v_customers_addressbook_state_1'] = $abInfo['entry_state'];
					}
					if (isset($_POST['v_customers_addressbook_postcode'])){
						$pInfo['v_customers_addressbook_postcode_1'] = $abInfo['entry_postcode'];
					}
					if (isset($_POST['v_customers_addressbook_country'])){
						$Qcountry = Doctrine_Query::create()
						->from('Countries')
						->where('countries_id = ?', $abInfo['entry_country_id'])
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						$pInfo['v_customers_addressbook_country_1'] = $Qcountry[0]['countries_name'];
					}


				}elseif($abInfo['address_book_id'] == $cInfo['customers_delivery_address_id']){
					if (isset($_POST['v_customers_addressbook_firstname'])){
						$pInfo['v_customers_addressbook_firstname_2'] = $abInfo['entry_firstname'];
					}
					if (isset($_POST['v_customers_addressbook_lastname'])){
						$pInfo['v_customers_addressbook_lastname_2'] = $abInfo['entry_lastname'];
					}
					if (isset($_POST['v_customers_addressbook_company'])){
						$pInfo['v_customers_addressbook_company_2'] = $abInfo['entry_company'];
					}
					if (isset($_POST['v_customers_addressbook_gender'])){
						$pInfo['v_customers_addressbook_gender_2'] = $abInfo['entry_gender'];
					}
					if (isset($_POST['v_customers_addressbook_address'])){
						$pInfo['v_customers_addressbook_address_2'] = $abInfo['entry_street_address'];
					}
					if (isset($_POST['v_customers_addressbook_city'])){
						$pInfo['v_customers_addressbook_city_2'] = $abInfo['entry_city'];
					}
					if (isset($_POST['v_customers_addressbook_state'])){
						$pInfo['v_customers_addressbook_state_2'] = $abInfo['entry_state'];
					}
					if (isset($_POST['v_customers_addressbook_postcode'])){
						$pInfo['v_customers_addressbook_postcode_2'] = $abInfo['entry_postcode'];
					}
					if (isset($_POST['v_customers_addressbook_country'])){
						$Qcountry = Doctrine_Query::create()
							->from('Countries')
							->where('countries_id = ?', $abInfo['entry_country_id'])
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						$pInfo['v_customers_addressbook_country_2'] = $Qcountry[0]['countries_name'];
					}
				}else{
					if (isset($_POST['v_customers_addressbook_firstname'])){
						$pInfo['v_customers_addressbook_firstname_'.$i] = $abInfo['entry_firstname'];
					}
					if (isset($_POST['v_customers_addressbook_lastname'])){
						$pInfo['v_customers_addressbook_lastname_'.$i] = $abInfo['entry_lastname'];
					}
					if (isset($_POST['v_customers_addressbook_company'])){
						$pInfo['v_customers_addressbook_company_'.$i] = $abInfo['entry_company'];
					}
					if (isset($_POST['v_customers_addressbook_gender'])){
						$pInfo['v_customers_addressbook_gender_'.$i] = $abInfo['entry_gender'];
					}
					if (isset($_POST['v_customers_addressbook_address'])){
						$pInfo['v_customers_addressbook_address_'.$i] = $abInfo['entry_street_address'];
					}
					if (isset($_POST['v_customers_addressbook_city'])){
						$pInfo['v_customers_addressbook_city_'.$i] = $abInfo['entry_city'];
					}
					if (isset($_POST['v_customers_addressbook_state'])){
						$pInfo['v_customers_addressbook_state_'.$i] = $abInfo['entry_state'];
					}
					if (isset($_POST['v_customers_addressbook_postcode'])){
						$pInfo['v_customers_addressbook_postcode_'.$i] = $abInfo['entry_postcode'];
					}
					if (isset($_POST['v_customers_addressbook_country'])){
						$Qcountry = Doctrine_Query::create()
							->from('Countries')
							->where('countries_id = ?', $abInfo['entry_country_id'])
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						$pInfo['v_customers_addressbook_country_'.$i] = $Qcountry[0]['countries_name'];
					}
					$i++;
				}
			}


			EventManager::notify('CustomersExportBeforeFileLineCommit', &$pInfo, &$cInfo);

			$dataRows[] = $pInfo;
		}

		$dataExport->setExportData($dataRows);
		$dataExport->output(true);

}
?>