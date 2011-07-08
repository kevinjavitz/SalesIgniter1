<?php
class OrderAddress {
	protected $addressInfo = array();
	protected $Id = null;
	protected $Type = null;
	protected $Zone = array();
	protected $Country = array();
	protected $Format = array();

	public function __construct($aInfo = null){
		if (is_null($aInfo) === false){
			$this->addressInfo = $aInfo;
			$this->Type = $this->addressInfo['address_type'];
			if (isset($this->addressInfo['id'])){
				$this->Id = $this->addressInfo['id'];
				if (isset($this->addressInfo['Zones'])){
					$this->Zone = $this->addressInfo['Zones'];
					$this->addressInfo['entry_zone_id'] = $this->Zone['zone_id'];
				}
				if (isset($this->addressInfo['Countries'])){
					$this->Country = $this->addressInfo['Countries'];
					$this->addressInfo['entry_country_id'] = $this->Country['countries_id'];
				}
				if (isset($this->Country['AddressFormat'])){
					$this->Format = $this->Country['AddressFormat'];
				}
			}
		}
	}
	
	public function toArray(){
		return $this->addressInfo;
	}

	public function getAddressType(){
		return $this->Type;
	}

	public function getId(){
		return $this->Id;
	}

	private function getValue($key, $arrayName = null){
		if (is_null($arrayName) === false){
			$Arr = $this->$arrayName;
		}else{
			$Arr = $this->addressInfo;
		}

		if (array_key_exists($key, $Arr)){
			$returnVal = $Arr[$key];
		}else{
			$returnVal = '';
		}

		return $returnVal;
	}

	public function getFormatId(){
		return $this->getValue('address_format_id', 'Format');
	}

	public function getFormat(){
		return $this->getValue('address_format', 'Format');
	}

	public function getGender(){
		return $this->getValue('entry_gender');
	}

	public function getDateOfBirth(){
		return $this->getValue('entry_dob');
	}

	public function getName(){
		return $this->getValue('entry_name');
	}

	public function getFirstName(){
		return substr($this->getValue('entry_name'), 0, strpos($this->getValue('entry_name'), ' '));
	}

	public function getLastName(){
		return substr($this->getValue('entry_name'), strpos($this->getValue('entry_name'), ' '));
	}

	public function getCompany(){
		return $this->getValue('entry_company');
	}

	public function getStreetAddress(){
		return $this->getValue('entry_street_address');
	}

	public function getSuburb(){
		return $this->getValue('entry_suburb');
	}

	public function getCity(){
		return $this->getValue('entry_city');
	}

	public function getVAT(){
		return $this->getValue('entry_vat');
	}

	public function getCIF(){
		return $this->getValue('entry_cif');
	}

	public function getCityBirth(){
		return $this->getValue('entry_city_birth');
	}

	public function getPostcode(){
		return $this->getValue('entry_postcode');
	}

	public function getState(){
		return $this->getValue('entry_state');
	}

	public function getZone(){
		return $this->getValue('zone_name', 'Zone');
	}

	public function getZoneId(){
		return $this->getValue('zone_id', 'Zone');
	}

	public function getZoneCode(){
		return $this->getValue('zone_code', 'Zone');
	}

	public function getCountry(){
		return $this->getValue('countries_name', 'Country');
	}
	
	public function getCountryId(){
		return $this->getValue('countries_id', 'Country');
	}
}
?>