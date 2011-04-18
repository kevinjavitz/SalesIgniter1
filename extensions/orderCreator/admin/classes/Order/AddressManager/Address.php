<?php
class OrderCreatorAddress extends OrderAddress implements Serializable {

	public function serialize(){
		$data = array(
			'addressInfo' => $this->addressInfo,
			'Id' => $this->Id,
			'Type' => $this->Type,
			'Zone' => $this->Zone,
			'Country' => $this->Country,
			'Format' => $this->Format
		);
		return serialize($data);
	}

	public function unserialize($data){
		$data = unserialize($data);
		foreach($data as $key => $dInfo){
			$this->$key = $dInfo;
		}
	}

	public function setName($val){
		$this->addressInfo['entry_name'] = $val;
	}

	public function setCompany($val){
		$this->addressInfo['entry_company'] = $val;
	}

	public function setStreetAddress($val){
		$this->addressInfo['entry_street_address'] = $val;
	}

	public function setSuburb($val){
		$this->addressInfo['entry_suburb'] = $val;
	}

	public function setCity($val){
		$this->addressInfo['entry_city'] = $val;
	}

	public function setPostcode($val){
		$this->addressInfo['entry_postcode'] = $val;
	}

	public function setState($val){
		$this->addressInfo['entry_state'] = $val;
		
		$Qcheck = Doctrine_Query::create()
		->from('Zones')
		->where('zone_name = ?', $val)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck){
			$this->Zone = $Qcheck[0];
		}
	}

	public function setCountry($val){
		$this->addressInfo['entry_country'] = $val;
		
		$Qcheck = Doctrine_Query::create()
		->from('Countries c')
		->leftJoin('c.AddressFormat')
		->where('c.countries_name = ?', $val)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck){
			$this->Country = $Qcheck[0];
			$this->addressInfo['entry_country_id'] = $Qcheck[0]['countries_id'];
			$this->Format = $Qcheck[0]['AddressFormat'];
		}
	}
}
?>