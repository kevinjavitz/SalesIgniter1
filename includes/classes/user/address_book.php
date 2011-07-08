<?php
class rentalStoreUser_addressBook {
	public $customerId, $addresses;

	public function __construct($customerId){
		$this->customerId = $customerId;
		$this->addresses = array();
		if ($customerId > 0){
			$this->loadCustomersAddresses();
		}
	}
	
	public function &getUserAccount(){
		global $userAccount;
		if (Session::exists('userAccount') === true){
			$userAccountCheck = &Session::getReference('userAccount');
			if (is_object($userAccountCheck)){
				$userAccount =& $userAccountCheck;
			}
		}
		return $userAccount;
	}

	public function loadCustomersAddresses(){

		$addresses = Doctrine::getTable('AddressBook')->findByCustomersId($this->customerId);
		foreach($addresses as $aInfo){
			$this->addAddressEntry($aInfo['address_book_id'], $aInfo->toArray());
		}
	}

	public function reset(){
		$this->customerId = 0;
		$this->addresses = array();
		unset($this->defaultAddress);
		unset($this->deliveryDefaultAddress);
	}

	public function setCustomerId($cId){
		$this->customerId = $cId;
	}

	public function getDefaultAddressId(){
		if (!isset($this->defaultAddress)){
			$userAccount = &$this->getUserAccount();
			$this->defaultAddress = $userAccount->getDefaultAddressId();
		}
		return $this->defaultAddress;
	}

	public function getDeliveryDefaultAddressId(){
		if (!isset($this->deliveryDefaultAddress)){
			$userAccount = &$this->getUserAccount();
			$this->deliveryDefaultAddress = $userAccount->getDeliveryDefaultAddressId();
		}
		return $this->deliveryDefaultAddress;
	}

	public function getRentalAddressId(){
		return $this->rentalAddress;
	}

	public function getAddress($aId){
		if ($this->entryExists($aId) === true){

			return $this->addresses[$aId];
		}else{
			$Qaddress = Doctrine::getTable('AddressBook')->find($aId);
			if ($Qaddress){
				return $Qaddress->toArray(true);
			}
		}
		return false;
	}

	public function entryExists($aID){
		return (isset($this->addresses[$aID]) === true);
	}

	public function setDefaultAddress($aID, $updateDB = false){
		global $userAccount;
		$this->defaultAddress = $aID;

		if ($updateDB === true){
			$Customers = Doctrine::getTable('Customers')->find($this->customerId);
			$Customers->customers_default_address_id = $this->defaultAddress;
			$Customers->customers_gender = $this->addresses[$this->defaultAddress]['entry_gender'];
			$Customers->customers_firstname = $this->addresses[$this->defaultAddress]['entry_firstname'];
			$Customers->customers_lastname = $this->addresses[$this->defaultAddress]['entry_lastname'];
			$Customers->save();

			$userAccount->setFirstName($this->addresses[$this->defaultAddress]['entry_firstname']);
			$userAccount->setLastName($this->addresses[$this->defaultAddress]['entry_lastname']);
			$userAccount->setGender($this->addresses[$this->defaultAddress]['entry_gender']);
		}
	}

	public function setDeliveryDefaultAddress($aID, $updateDB = false){
		global $userAccount;
		$this->deliveryDefaultAddress = $aID;

		if ($updateDB === true){
			$Customers = Doctrine::getTable('Customers')->find($this->customerId);
			$Customers->customers_delivery_address_id = $this->deliveryDefaultAddress;
			$Customers->save();
		}
	}

	public function insertAddress($addressArray, $setAsDefault = false, $setAsShipping = false){
		$newAddress = new AddressBook();
		$newAddress->customers_id = (int)$this->customerId;
		$newAddress->entry_firstname = $addressArray['entry_firstname'];
		$newAddress->entry_lastname = $addressArray['entry_lastname'];
		$newAddress->entry_street_address = $addressArray['entry_street_address'];
		$newAddress->entry_postcode = $addressArray['entry_postcode'];
		$newAddress->entry_city = $addressArray['entry_city'];
		$newAddress->entry_country_id = (int)$addressArray['entry_country_id'];
		$newAddress->entry_gender = $addressArray['entry_gender'];
		$newAddress->entry_company = $addressArray['entry_company'];
		$newAddress->entry_cif = $addressArray['entry_cif'];
		$newAddress->entry_vat = $addressArray['entry_vat'];
		$newAddress->entry_suburb = $addressArray['entry_suburb'];
		$newAddress->entry_state = $addressArray['entry_state'];
		if (!is_numeric($addressArray['entry_state'])){
			$Qcheck = Doctrine_Query::create()
			->select('zone_id')
			->from('Zones')
			->where('zone_name = ?', $addressArray['entry_state'])
			->orWhere('zone_code = ?', $addressArray['entry_state'])
			->execute(array(), Doctrine::HYDRATE_ARRAY);
			if ($Qcheck){
				$newAddress->entry_zone_id = (int)$Qcheck[0]['zone_id'];
			}
		}
		
		/* @TODO: getinto extension */
		if (defined('EXTENSION_INVENTORY_CENTERS_ENABLED')){
			$centerId = $this->getAddressInventoryCenter($addressArray);
			if ($centerId > 0){
				$newAddress->inventory_center_id = $centerId;
			}
		}
		
		$newAddress->save();
		$aID = $newAddress->address_book_id;

		$this->addAddressEntry($aID, $addressArray);
		
		if ($setAsDefault === true){
			$this->setDefaultAddress($aID, true);
		}

		if ($setAsShipping === true){
			$this->setDeliveryDefaultAddress($aID, true);
		}
		return $aID;
	}

	public function updateAddress($aID, $addressArray){
		$Address = Doctrine::getTable('AddressBook')->find($aID);
		
		if (isset($addressArray['entry_firstname'])) $Address->entry_firstname = $addressArray['entry_firstname'];
		if (isset($addressArray['entry_lastname'])) $Address->entry_lastname = $addressArray['entry_lastname'];
		if (isset($addressArray['entry_street_address'])) $Address->entry_street_address = $addressArray['entry_street_address'];
		if (isset($addressArray['entry_postcode'])) $Address->entry_postcode = $addressArray['entry_postcode'];
		if (isset($addressArray['entry_city'])) $Address->entry_city = $addressArray['entry_city'];
		if (isset($addressArray['entry_country_id'])) $Address->entry_country_id = (int)$addressArray['entry_country_id'];
		if (isset($addressArray['entry_gender'])) $Address->entry_gender = $addressArray['entry_gender'];
		if (isset($addressArray['entry_company'])) $Address->entry_company = $addressArray['entry_company'];
		if (isset($addressArray['entry_cif'])) $Address->entry_cif = $addressArray['entry_cif'];
		if (isset($addressArray['entry_vat'])) $Address->entry_vat = $addressArray['entry_vat'];
		if (isset($addressArray['entry_city_birth'])) $Address->entry_city_birth = $addressArray['entry_city_birth'];
		if (isset($addressArray['entry_entry_suburb'])) $Address->entry_suburb = $addressArray['entry_suburb'];
		if (isset($addressArray['entry_zone_id'])) $Address->entry_zone_id = (int)$addressArray['entry_zone_id'];
		if (isset($addressArray['entry_state'])) $Address->entry_state = $addressArray['entry_state'];
		
		if (!isset($addressArray['entry_zone_id']) && isset($addressArray['entry_state'])){
			$addressArray['entry_zone_id'] = $this->getStateZoneId($addressArray['entry_country_id'], $addressArray['entry_state']);
			$Address->entry_zone_id = (int)$addressArray['entry_zone_id'];
		}

		/* @TODO: getinto extension */
		if (defined('EXTENSION_INVENTORY_CENTERS_ENABLED')){
			$centerId = $this->getAddressInventoryCenter($aID);
			if ($centerId > 0){
				$Address->inventory_center_id = $centerId;
			}
		}
		
		$Address->save();

		$this->updateAddressEntry($aID, $addressArray);

		if ($aID == $this->getDefaultAddressId()){
			$this->setDefaultAddress($aID, true);
		}
	}

	public function deleteAddress($aID){
		Doctrine::getTable('AddressBook')->find($aID)->delete();
		$this->deleteAddressEntry($aID);
	}

	public function addAddressEntry($aID, $address){
		$cInfo = $this->getCountryInfo($address['entry_country_id']);
		$this->addresses[$aID] = $address;
		
		if (!is_numeric($address['entry_zone_id']) || $address['entry_zone_id'] == 0){
			if (isset($address['entry_country_id']) && is_numeric($address['entry_country_id'])){
				$countryId = $address['entry_country_id'];
			}elseif (isset($address['entry_country']) && !is_numeric($address['entry_country'])){
				$countryId = $this->getCountryId($address['entry_country']);
			}else{
				//die(__FILE__ . '::' . __LINE__ . '::Country is not set');
			}
			$this->addresses[$aID]['entry_zone_id'] = $this->getStateZoneId($countryId, $address['entry_zone_id']);
		}

		$this->addresses[$aID]['AddressFormat'] = array(
			'address_format_id' => $cInfo['address_format_id'],
			'address_format'    => $cInfo['AddressFormat']['address_format'],
			'address_summary'   => $cInfo['AddressFormat']['address_summary']
		);

		/* @TODO: getinto extension */
		if (defined('EXTENSION_INVENTORY_CENTERS_ENABLED') && !isset($address['inventory_center_id'])){
			$this->addresses[$aID]['inventory_center_id'] = $this->getAddressInventoryCenter($aID);
		}
	}

	public function updateAddressEntry($aID, $updatedFields){
		if (!isset($this->addresses[$aID])) return;

		foreach($updatedFields as $fieldName => $fieldVal){
			$this->addresses[$aID][$fieldName] = $fieldVal;
			
			if ($fieldName == 'entry_zone_id' && (!is_numeric($fieldVal) || $fieldVal == 0)){
				if (isset($updatedFields['entry_country_id']) && is_numeric($updatedFields['entry_country_id'])){
					$countryId = $updatedFields['entry_country_id'];
				}elseif (isset($updatedFields['entry_country_id']) && !is_numeric($updatedFields['entry_country_id'])){
					$countryId = $this->getCountryId($updatedFields['entry_country_id']);
				}else{
					die(__FILE__ . '::' . __LINE__ . '::Country is not set');
				}
				$this->addresses[$aID][$fieldName] = $this->getStateZoneId($countryId, $fieldVal);
			}
		}
	}

	public function deleteAddressEntry($aID){
		if (!isset($this->addresses[$aID])) return;

		unset($this->addresses[$aID]);
	}

	public function getCountryInfo($cID){
		$Qcountry = Doctrine_Query::create()
		->from('Countries c')
		->leftJoin('c.AddressFormat af')
		->where('countries_id = ?', $cID)
		->execute();
		
		return $Qcountry[0];
	}

	public function getAddressForServiceCheck($addressId){
		$address = $this->getAddress($addressId);
		return array(
			'entry_street_address' => $address['entry_street_address'],
			'entry_postcode'       => $address['entry_postcode'],
			'entry_city'           => $address['entry_city'],
			'entry_country_id'     => $address['entry_country_id'],
			'entry_zone_id'        => $address['entry_zone_id'],
			'entry_state'          => $address['entry_state']
		);
	}

	/* @TODO: getinto extension */
	public function getAddressInventoryCenter($address = false){
		
		if (!class_exists('Services_JSON')){
			require(sysConfig::getDirFsCatalog() . 'includes/functions/google_maps.php');
			require(sysConfig::getDirFsCatalog() . 'includes/classes/json.php');
		}

		$serviceAreas = getServiceAreas();

		if (is_array($address)){
		}elseif (isset($this->addresses[$address])){
			$address = $this->addresses[$address];
		}elseif (is_numeric($address)){
			$address = $this->getAddressForServiceCheck($address);
		}elseif ($address === false && isset($this->defaultAddress)){
			$address = $this->addresses[$this->defaultAddress];
		}else{
			die('Cannot Find Inventory Center: No Address Provided');
		}

		if (!is_array($address)) return 0;

		$centerID = false;
		$state = $address['entry_state'];
		$zoneID = $address['entry_zone_id'];
		$countryID = $address['entry_country_id'];

		$countryName = tep_get_country_name($countryID);
		if (is_numeric($zoneID) && $zoneID > 0){
			$state = tep_get_zone_name($countryID, $zoneID, $state);
		}

		$point = array(
			'entry_street_address' => $address['entry_street_address'],
			'entry_city'           => $address['entry_city'],
			'entry_postcode'       => $address['entry_postcode'],
			'entry_country_name'   => $countryName,
			'entry_state'          => $state
		);
		$coordinates = getGoogleCoordinates($point);

		for($i=0; $i<sizeof($serviceAreas); $i++){
			if (polygonContains($serviceAreas[$i]['decoded'], $coordinates['lng'], $coordinates['lat']) === true){
				$centerID = $serviceAreas[$i]['id'];
				break;
			}
		}
		return $centerID;
	}

	public function formatAddress($aID, $html = true, $type = 'long'){
		$address = $this->addresses[$aID];

		$QAddressFormat = Doctrine_Query::create()
		->from('AddressFormat')
		->where('address_format_id=?', $address['AddressFormat']['address_format_id'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if($type == 'long'){
			$fmt = $QAddressFormat[0]['address_format'];
		}else{
			$fmt = $QAddressFormat[0]['address_summary'];
		}
		if($html){
			$fmt = nl2br($fmt);
		}
		$company = $address['entry_company'];
		if (isset($address['entry_firstname']) && tep_not_null($address['entry_firstname'])) {
			$firstname = $address['entry_firstname'];
			$lastname = $address['entry_lastname'];
		} elseif (isset($address['entry_name']) && tep_not_null($address['entry_name'])) {
			$firstname = $address['entry_name'];
			$lastname = '';
		} else {
			$firstname = '';
			$lastname = '';
		}

		$street_address = $address['entry_street_address'];
		$suburb = $address['entry_suburb'];
		$city = $address['entry_city'];
		$vat = $address['entry_vat'];
		$cif = $address['entry_cif'];
		$city_birth = $address['entry_city_birth'];
		$state = $address['entry_state'];
		if (isset($address['entry_country_id']) && tep_not_null($address['entry_country_id'])) {
			$country = tep_get_country_name($address['entry_country_id']);

			if (isset($address['entry_zone_id']) && tep_not_null($address['entry_zone_id'])) {
				$abbrstate = tep_get_zone_code($address['entry_country_id'], $address['entry_zone_id'], $state);
			}
		} elseif (isset($address['country']) && tep_not_null($address['country'])) {
			if (is_array($address['country'])){
				$country = $address['country']['title'];
			}
			else{
				$country = tep_output_string_protected($address['country']);
			}
		} else {
			$country = '';
		}
		$postcode = $address['entry_postcode'];

		eval("\$address = \"$fmt\";");
		return $address;
	}

	public function editAddressTable($aID){
		$address = $this->addresses[$aID];
		
		$table = '<table cellpadding="0" cellspacing="0" border="0">' . 
			'<tr>' . 
				'<td>Name:</td>' . 
				'<td><input type="text" name="address[' . $aID . '][entry_name]" value="' . $address['entry_name'] . '"></td>' . 
			'</tr>' . 
			'<tr>' . 
				'<td>Company:</td>' . 
				'<td><input type="text" name="address[' . $aID . '][entry_company]" value="' . $address['entry_company'] . '"></td>' . 
			'</tr>' . 
			'<tr>' . 
				'<td>Address:</td>' . 
				'<td><input type="text" name="address[' . $aID . '][entry_street_address]" value="' . $address['entry_street_address'] . '"></td>' . 
			'</tr>' . 
			'<tr>' . 
				'<td>Suburb:</td>' . 
				'<td><input type="text" name="address[' . $aID . '][entry_suburb]" value="' . $address['entry_suburb'] . '"></td>' . 
			'</tr>' . 
			'<tr>' . 
				'<td>City:</td>' . 
				'<td><input type="text" name="address[' . $aID . '][entry_city]" value="' . $address['entry_city'] . '"></td>' . 
			'</tr>' . 
			'<tr>' . 
				'<td>Postcode:</td>' . 
				'<td><input type="text" name="address[' . $aID . '][entry_postcode]" value="' . $address['entry_postcode'] . '"></td>' . 
			'</tr>' . 
			'<tr>' . 
				'<td>State:</td>' . 
				'<td><input type="text" name="address[' . $aID . '][entry_state]" value="' . $address['entry_state'] . '"></td>' . 
			'</tr>' . 
			'<tr>' . 
				'<td>Country:</td>' . 
				'<td><input type="text" name="address[' . $aID . '][entry_country]" value="' . $address['entry_country'] . '"></td>' . 
			'</tr>' . 
		'</table>';
		return $table;
	}
	
	function getCountryId($countryName){
		$Qcountry = Doctrine_Query::create()
		->select('countries_id')
		->from('Countries')
		->where('countries_name = ?', $countryName)
		->fetchOne();
		if ($Qcountry){
			$country = $Qcountry->toArray();
			return $country['countries_id'];
		}
		return false;
	}
	
	function getCountryZones($countryId){
		$Qzones = Doctrine_Query::create()
		->from('Zones')
		->where('zone_country_id = ?', $countryId)
		->execute(array(), Doctrine::HYDRATE_ARRAY);
		if ($Qzones){
			$zonesArray = array(
				array(
					'id' => '',
					'text' => sysLanguage::get('TEXT_PLEASE_SELECT')
				)
			);
			foreach ($Qzones as $zInfo) {
				$zonesArray[] = array(
					'id'   => $zInfo['zone_name'],
					'text' => $zInfo['zone_name']
				);
			}
			return $zonesArray;
		}
		return false;
	}

	public function getStateZoneId($countryId, $state){
		$zone_id = -1;
		$Qcheck = dataAccess::setQuery('select count(*) as total from {zones} where zone_country_id = {country}')
		->setTable('{zones}', TABLE_ZONES)
		->setValue('{country}', $countryId)
		->runQuery();
		if ($Qcheck->getVal('total') > 0) {
			$zone_id = 0;
			$Qzone = dataAccess::setQuery('select distinct zone_id from {zones} where zone_country_id = {country} and (zone_name = {state} or zone_code = {state})')
			->setTable('{zones}', TABLE_ZONES)
			->setValue('{country}', $countryId)
			->setValue('{state}', $state)
			->runQuery();
			if ($Qzone->numberOfRows() == 1){
				$zone_id = $Qzone->getVal('zone_id');
			}
		}
		return $zone_id;
	}
	
	public function createAddressFields($settings){
		$namePrefix = (isset($settings['name_prefix']) ? $settings['name_prefix'] . '_' : '');
		$fields = $settings['fields'];
		$returnArray = array();
		
		if (isset($fields['gender'])){
			$returnArray['gender'] = htmlBase::newElement('radio')
			->addGroup(array(
				'name' => $namePrefix . 'gender',
				'labelSeparator' => '&nbsp;',
				'checked' => ($fields['gender']['value'] ? $fields['gender']['value'] : 'm'),
				'data' => array(
					array(
						'label' => sysLanguage::get('MALE'),
						'value' => 'm'
					),
					array(
						'label' => sysLanguage::get('FEMALE'),
						'value' => 'f'
					)
				)
			))
			->setRequired((isset($fields['gender']['required']) ? $fields['gender']['required'] : false));
		}

		if (isset($fields['first_name'])){
			$returnArray['first_name'] = htmlBase::newElement('input')
			->setValue((isset($fields['first_name']['value']) ? $fields['first_name']['value'] : ''))
			->setRequired((isset($fields['first_name']['required']) ? $fields['first_name']['required'] : false))
			->setName($namePrefix . 'firstname');
		}

		if (isset($fields['last_name'])){
			$returnArray['last_name'] = htmlBase::newElement('input')
			->setValue((isset($fields['last_name']['value']) ? $fields['last_name']['value'] : ''))
			->setRequired((isset($fields['last_name']['required']) ? $fields['last_name']['required'] : false))
			->setName($namePrefix . 'lastname');
		}

		if (isset($fields['dob'])){
			$returnArray['dob'] = htmlBase::newElement('input')
			->setValue((isset($fields['dob']['value']) ? $fields['dob']['value'] : ''))
			->setRequired((isset($fields['dob']['required']) ? $fields['dob']['required'] : false))
			->setName($namePrefix . 'dob');
		}

		if (isset($fields['email_address'])){
			$returnArray['email_address'] = htmlBase::newElement('input')
			->setValue((isset($fields['email_address']['value']) ? $fields['email_address']['value'] : ''))
			->setRequired((isset($fields['email_address']['required']) ? $fields['email_address']['required'] : false))
			->setName($namePrefix . 'email_address');
			if (isset($fields['email_address']['hidden'])){
				$returnArray['email_address']->attr('style','display:none');
			}
		}

		if (isset($fields['company'])){
			$returnArray['company'] = htmlBase::newElement('input')
			->setValue((isset($fields['company']['value']) ? $fields['company']['value'] : ''))
			->setRequired((isset($fields['company']['required']) ? $fields['company']['required'] : false))
			->setName($namePrefix . 'company');
		}

		if (isset($fields['fiscal_code'])){
			$returnArray['fiscal_code'] = htmlBase::newElement('input')
			->setValue((isset($fields['fiscal_code']['value']) ? $fields['fiscal_code']['value'] : ''))
			->setRequired((isset($fields['fiscal_code']['required']) ? $fields['fiscal_code']['required'] : false))
			->setName($namePrefix . 'fiscal_code');
		}

		if (isset($fields['vat_number'])){
			$returnArray['vat_number'] = htmlBase::newElement('input')
			->setValue((isset($fields['vat_number']['value']) ? $fields['vat_number']['value'] : ''))
			->setRequired((isset($fields['vat_number']['required']) ? $fields['vat_number']['required'] : false))
			->setName($namePrefix . 'vat_number');
		}

		if (isset($fields['city_birth'])){
			$returnArray['city_birth'] = htmlBase::newElement('input')
				->setValue((isset($fields['city_birth']['value']) ? $fields['city_birth']['value'] : ''))
				->setRequired((isset($fields['city_birth']['required']) ? $fields['city_birth']['required'] : false))
				->setName($namePrefix . 'city_birth');
		}

		if (isset($fields['country'])){
			$returnArray['country'] = htmlBase::newElement('selectbox')
			->setName($namePrefix . 'country')
			->attr('id',$namePrefix . 'country')
			->setRequired((isset($fields['country']['required']) ? $fields['country']['required'] : false));

			if (isset($fields['country']['value'])){
				$returnArray['country']->selectOptionByValue($fields['country']['value']);
			}

			if (isset($fields['country']['options'])){
				foreach($fields['country']['options'] as $oInfo){
					$returnArray['country']->addOption(
						$oInfo['id'],
						$oInfo['text']
					);
				}
			}else{
				$Qcountries = Doctrine_Query::create()
				->select('countries_id, countries_name')
				->from('Countries')
				->orderBy('countries_name')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				foreach($Qcountries as $cInfo){
					$returnArray['country']->addOption(
						$cInfo['countries_id'],
						$cInfo['countries_name']
					);
				}
			}
		}

		if (isset($fields['street_address'])){
			$returnArray['street_address'] = htmlBase::newElement('input')
			->setValue((isset($fields['street_address']['value']) ? $fields['street_address']['value'] : ''))
			->setRequired((isset($fields['street_address']['required']) ? $fields['street_address']['required'] : false))
			->setName($namePrefix . 'street_address');
		}

		if (isset($fields['suburb'])){
			$returnArray['suburb'] = htmlBase::newElement('input')
			->setValue((isset($fields['suburb']['value']) ? $fields['suburb']['value'] : ''))
			->setRequired((isset($fields['suburb']['required']) ? $fields['suburb']['required'] : false))
			->setName($namePrefix . 'suburb');
		}

		if (isset($fields['city'])){
			$returnArray['city'] = htmlBase::newElement('input')
			->setValue((isset($fields['city']['value']) ? $fields['city']['value'] : ''))
			->setRequired((isset($fields['city']['required']) ? $fields['city']['required'] : false))
			->setName($namePrefix . 'city');
		}

		if (isset($fields['postcode'])){
			$returnArray['postcode'] = htmlBase::newElement('input')
			->setValue((isset($fields['postcode']['value']) ? $fields['postcode']['value'] : ''))
			->setRequired((isset($fields['postcode']['required']) ? $fields['postcode']['required'] : false))
			->setName($namePrefix . 'postcode');
		}

		if (isset($fields['state'])){
			if (isset($fields['country'])){
				$Qzones = Doctrine_Query::create()
				->select('zone_id, zone_name')
				->from('Zones')
				->where('zone_country_id = ?', $fields['country']['value'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qzones){
					$returnArray['state'] = htmlBase::newElement('selectbox')
					->setRequired((isset($fields['state']['required']) ? $fields['state']['required'] : false))
					->setName($namePrefix . 'state')
					->attr('id',$namePrefix . 'state');
					foreach($Qzones as $zInfo){
						$returnArray['state']->addOption($zInfo['zone_name'], $zInfo['zone_name']);
					}
					$returnArray['state']->selectOptionByValue((isset($fields['state']['value']) ? $fields['state']['value'] : ''));
				}else{
					$returnArray['state'] = htmlBase::newElement('input')
					->setValue((isset($fields['state']['value']) ? $fields['state']['value'] : ''))
					->setRequired((isset($fields['state']['required']) ? $fields['state']['required'] : false))
					->setName($namePrefix . 'state')
					->attr('id',$namePrefix . 'state');
				}
			}else{
				$returnArray['state'] = htmlBase::newElement('input')
				->setValue((isset($fields['state']['value']) ? $fields['state']['value'] : ''))
				->setRequired((isset($fields['state']['required']) ? $fields['state']['required'] : false))
				->setName($namePrefix . 'state')
				->attr('id',$namePrefix . 'state');
			}
		}

		if (isset($fields['telephone'])){
			$returnArray['telephone'] = htmlBase::newElement('input')
			->setValue((isset($fields['telephone']['value']) ? $fields['telephone']['value'] : ''))
			->setRequired((isset($fields['telephone']['required']) ? $fields['telephone']['required'] : false))
			->setName($namePrefix . 'telephone');
		}

		if (isset($fields['fax'])){
			$returnArray['fax'] = htmlBase::newElement('input')
			->setValue((isset($fields['fax']['value']) ? $fields['fax']['value'] : ''))
			->setRequired((isset($fields['fax']['required']) ? $fields['fax']['required'] : false))
			->setName($namePrefix . 'fax');
		}

		return $returnArray;
	}
}
?>