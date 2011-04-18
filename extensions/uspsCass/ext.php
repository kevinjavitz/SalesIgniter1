<?php
class Extension_uspsCass extends ExtensionBase {
			  
	public function __construct(){
		parent::__construct('uspsCass');
	}
	
	public function init(){
		global $appExtension;
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'CheckoutValidateAddress'
		), null, $this);
	}
	
	public function getCassAddresses($Address){
		$return = array();
		if (sysConfig::get('EXTENSION_USPSCASS_API_TEST_MODE') == 'False'){
			if (!class_exists('CurlRequest')){
				require(sysConfig::getDirFsCatalog() . '/includes/classes/curl/Request.php');
			}
			if (!class_exists('CurlResponse')){
				require(sysConfig::getDirFsCatalog() . '/includes/classes/curl/Response.php');
			}
			
			$subDomain = 'production';
			$dll = 'ShippingAPI';

			$xmlToSend = '<AddressValidateRequest USERID="' . sysConfig::get('EXTENSION_USPSCASS_API_USER_ID') . '">' . 
				'<Address ID="0">' . 
					'<FirmName>' . $Address['entry_company'] . '</FirmName>' . 
					'<Address1></Address1>' . 
					'<Address2>' . $Address['entry_street_address'] . '</Address2>' . 
					'<City>' . $Address['entry_city'] . '</City>' . 
					'<State>' . $Address['entry_state'] . '</State>' . 
					'<Zip5>' . $Address['entry_postcode'] . '</Zip5>' . 
					'<Zip4></Zip4>' . 
				'</Address>' . 
			'</AddressValidateRequest>';

			$url = 'http://' . $subDomain . '.shippingapis.com/' . $dll . '.dll?API=Verify&XML=' . urlencode($xmlToSend);
			
			$Request = new CurlRequest($url);
			$Request->setSendMethod('get');
			$Response = $Request->execute();
			if (!$Response->hasError()){
				$XmlInfo = simplexml_load_string($Response->getResponse());
				if (!isset($XmlInfo->Address->Error)){
					$return = array();
					foreach($XmlInfo->Address as $aInfo){
						$Qstate = Doctrine_Query::create()
						->select('zone_name')
						->from('Zones')
						->where('zone_code = ?', (string) $aInfo->State)
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						
						$return[] = array(
							'entry_company' => (string) $aInfo->FirmName,
							'entry_street_address' => (string) $aInfo->Address2,
							'entry_postcode' => (string) $aInfo->Zip5 . '-' . (string) $aInfo->Zip4,
							'entry_city' => (string) $aInfo->City,
							'entry_state' => $Qstate[0]['zone_name'],
							'entry_country_id' => (string) $Address['entry_country_id'],
						);
					}
				}else{
					$return['errorMsg'] = (string) $XmlInfo->Address->Error->Description;
				}
			}else{
				$return['errorMsg'] = $Response->getError();
			}
		}
		return $return;
	}
	
	public function validateAddressBookAddress($id){
		global $userAccount;
		
		$Address = $userAccount->plugins['addressBook']->getAddress($id);
		if (!isset($Address['cass_validated']) || $Address['cass_validated'] == 0){
			if (!class_exists('CurlRequest')){
				require(sysConfig::getDirFsCatalog() . '/includes/classes/curl/Request.php');
			}
			if (!class_exists('CurlResponse')){
				require(sysConfig::getDirFsCatalog() . '/includes/classes/curl/Response.php');
			}
			
			if (sysConfig::get('EXTENSION_USPSCASS_API_TEST_MODE') == 'True'){
				$subDomain = 'testing';
				$dll = 'ShippingAPITest';
				
				$xmlToSend = '<AddressValidateRequest USERID="' . sysConfig::get('EXTENSION_USPSCASS_API_USER_ID') . '">' . 
					'<Address ID="0">' . 
						'<Address1></Address1>' . 
						'<Address2>8 Wildwood Drive</Address2>' . 
						'<City>Old Lyme</City>' . 
						'<State>CT</State>' . 
						'<Zip5>06371</Zip5>' . 
						'<Zip4></Zip4>' . 
					'</Address>' . 
				'</AddressValidateRequest>';
			}else{
				$subDomain = 'production';
				$dll = 'ShippingAPI';

				$xmlToSend = '<AddressValidateRequest USERID="' . sysConfig::get('EXTENSION_USPSCASS_API_USER_ID') . '">' . 
					'<Address ID="0">' . 
						'<FirmName>' . $Address['entry_company'] . '</FirmName>' . 
						'<Address1></Address1>' . 
						'<Address2>' . $Address['entry_street_address'] . '</Address2>' . 
						'<City>' . $Address['entry_city'] . '</City>' . 
						'<State>' . $Address['entry_state'] . '</State>' . 
						'<Zip5>' . $Address['entry_postcode'] . '</Zip5>' . 
						'<Zip4></Zip4>' . 
					'</Address>' . 
				'</AddressValidateRequest>';
			}
			$url = 'http://' . $subDomain . '.shippingapis.com/' . $dll . '.dll?API=Verify&XML=' . urlencode($xmlToSend);
			
			echo $url;
			return false;
			$Request = new CurlRequest($url);
			$Request->setSendMethod('get');
			$Response = $Request->execute();
			if (!$Response->hasError()){
				$XmlInfo = simplexml_load_string($Response->getResponse());
				if (isset($XmlInfo->Error)){
					$AddressValid = $XmlInfo->Address[0];
					$userAccount->plugins['addressBook']->updateAddressEntry($id, array(
						'entry_street_address' => (string) $AddressValid->Address2,
						'entry_postcode' => (string) $AddressValid->Zip5 . '-' . (string) $AddressValid->Zip4,
						'entry_city' => (string) $AddressValid->City,
						'entry_state' => (string) $AddressValid->State,
						'cass_validated' => 1
					));
				}else{
					$userAccount->plugins['addressBook']->updateAddressEntry($id, array(
						'cass_validated' => 0
					));
					return false;
				}
			}
		}
		return true;
	}
	
	public function CheckoutValidateAddress(&$error){
		$error = $this->validateAddressBookAddress('delivery');
	}
}
?>