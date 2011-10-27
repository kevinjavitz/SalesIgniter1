<?php
class formValidation extends ArrayIterator {
	private $formRules;
	private $validationRules;
	
	public function __construct(&$dataArray){
		parent::__construct(&$dataArray);

		$this->_loadValidationRules();
	}
	
	private function _loadValidationRules(){
		$this->_loadValidationRulesRegEx();
		
		$this->validationRules['firstname'] = array(
			'validate'                => 1,
			'validation_type'         => 'min_length|required',
			'length'                  => sysConfig::get('ENTRY_FIRST_NAME_MIN_LENGTH'),
			'errorMessage_min_length' => sysLanguage::get('ENTRY_FIRST_NAME_ERROR_MIN_LENGTH'),
			'errorMessage_alpha'      => sysLanguage::get('ENTRY_FIRST_NAME_ERROR_ALPHA')
		);
		$this->validationRules['entry_firstname'] = $this->validationRules['firstname'];
		
		$this->validationRules['lastname'] = array(
			'validate'                => 1,
			'validation_type'         => 'min_length|required',
			'length'                  => sysConfig::get('ENTRY_LAST_NAME_MIN_LENGTH'),
			'errorMessage_min_length' => sysLanguage::get('ENTRY_LAST_NAME_ERROR_MIN_LENGTH'),
			'errorMessage_alpha'      => sysLanguage::get('ENTRY_LAST_NAME_ERROR_ALPHA')
		);
		$this->validationRules['entry_lastname'] = $this->validationRules['lastname'];

		if(sysConfig::get('ACCOUNT_TELEPHONE') == 'true'){
			$this->validationRules['telephone'] = array(
				'validate'                => 1,
				'validation_type'         => 'min_length'.((sysConfig::get('ACCOUNT_TELEPHONE_REQUIRED') == 'true')?'|required':''),
				'length'                  => sysConfig::get('ENTRY_TELEPHONE_MIN_LENGTH'),
				'errorMessage_min_length' => sysLanguage::get('ENTRY_TELEPHONE_ERROR_MIN_LENGTH'),
				'errorMessage_phone'      => sysLanguage::get('ENTRY_TELEPHONE_ERROR_PHONE')
			);

			$this->validationRules['entry_telephone'] = $this->validationRules['telephone'];
		}
		
		$this->validationRules['fax'] = array(
			'validate'                => 1,
			'validation_type'         => 'min_length',
			'length'                  => sysConfig::get('ENTRY_FAX_MIN_LENGTH'),
			'errorMessage_min_length' => sysLanguage::get('ENTRY_FAX_ERROR_MIN_LENGTH'),
			'errorMessage_phone'      => sysLanguage::get('ENTRY_FAX_ERROR_PHONE')
		);
		$this->validationRules['entry_fax'] = $this->validationRules['fax'];
		
		$this->validationRules['email_address'] = array(
			'validate'                => 1,
			'validation_type'         => 'min_length|email|required',
			'length'                  => sysConfig::get('ENTRY_EMAIL_ADDRESS_MIN_LENGTH'),
			'errorMessage_min_length' => sysLanguage::get('ENTRY_EMAIL_ADDRESS_ERROR_MIN_LENGTH'),
			'errorMessage_email'      => sysLanguage::get('ENTRY_EMAIL_ADDRESS_ERROR_EMAIL')
		);

		if(sysConfig::get('ACCOUNT_FISCAL_CODE') == 'true'){
			$this->validationRules['fiscal_code'] = array(
			'validate'                => 1,
			'validation_type'         => ''.((sysConfig::get('ACCOUNT_FISCAL_CODE_REQUIRED') == 'true')?'min_length|fiscal_code|required':''),
			'length'                  => 16,
			'errorMessage_min_length' => sysLanguage::get('ENTRY_FISCAL_CODE_ERROR'),
			'errorMessage_cif'      => sysLanguage::get('ENTRY_FISCAL_CODE_ERROR')
			);
			$this->validationRules['entry_cif'] = $this->validationRules['fiscal_code'];
		}

		if(sysConfig::get('ACCOUNT_VAT_NUMBER') == 'true'){
			$this->validationRules['vat_number'] = array(
			'validate'                => 1,
			'validation_type'         => ''.((sysConfig::get('ACCOUNT_VAT_NUMBER_REQUIRED') == 'true')?'min_length|vat_number|required':''),
			'length'                  => 11,
			'errorMessage_min_length' => sysLanguage::get('ENTRY_VAT_NUMBER_ERROR'),
			'errorMessage_vat'      => sysLanguage::get('ENTRY_VAT_NUMBER_ERROR')
			);
			$this->validationRules['entry_vat'] = $this->validationRules['vat_number'];
		}
		
		$this->validationRules['newsletter'] = array(
			'validate'        => 0,
			'validation_type' => 'checkbox',
			'values'          => '0|1'
		);

		
			$this->validationRules['terms'] = array(
				'validate'              => 1,
				'validation_type'       => 'checkbox|required',
				'values'                => '1',
				'errorMessage_checkbox' => sysLanguage::get('ENTRY_TERMS_ERROR_CHECKBOX')
			);

		
		$this->validationRules['password'] = array(
			'validate'                => 1,
			'validation_type'         => 'min_length|required',
			'length'                  => sysConfig::get('ENTRY_PASSWORD_MIN_LENGTH'),
			'errorMessage_min_length' => sysLanguage::get('ENTRY_PASSWORD_ERROR_MIN_LENGTH')
		);
		
		$this->validationRules['confirmation'] = array(
			'validate'                => 1,
			'validation_type'         => 'min_length|required',
			'length'                  => sysConfig::get('ENTRY_PASSWORD_MIN_LENGTH'),
			'errorMessage_min_length' => sysLanguage::get('ENTRY_PASSWORD_CONFIRM_ERROR_MIN_LENGTH')
		);
		if(sysConfig::get('ACCOUNT_GENDER') == 'true'){
			$this->validationRules['gender'] = array(
				'validate'           => 1,
				'validation_type'    => 'radio'.((sysConfig::get('ACCOUNT_GENDER_REQUIRED') == 'true')?'|required':''),
				'values'             => 'm|f',
				'errorMessage_radio' => sysLanguage::get('ENTRY_GENDER_ERROR_RADIO'),
				'errorMessage_required'   => sysLanguage::get('ENTRY_GENDER_ERROR_REQUIRED'),
			);
			$this->validationRules['entry_gender'] = $this->validationRules['gender'];
		}
		if(sysConfig::get('ACCOUNT_DOB') == 'true'){
			$this->validationRules['dob'] = array(
				'validate'          => 1,
				'validation_type'   => 'date'.((sysConfig::get('ACCOUNT_DOB_REQUIRED') == 'true')?'|required':''),
				'errorMessage_date' => sprintf(sysLanguage::get('ENTRY_DATE_OF_BIRTH_ERROR_DATE'),str_replace('%Y','yy',str_replace('%m','mm',str_replace('%d','dd',sysLanguage::getDateFormat('short'))))),
				'errorMessage_required'   => sysLanguage::get('ENTRY_DOB_ERROR_REQUIRED'),
			);
			$this->validationRules['entry_dob'] = $this->validationRules['dob'];
		}
		
		$this->validationRules['street_address'] = array(
			'validate'                  => 1,
			'validation_type'           => 'min_length|required',
			'length'                    => sysConfig::get('ENTRY_STREET_ADDRESS_MIN_LENGTH'),
			'errorMessage_min_length'   => sysLanguage::get('ENTRY_STREET_ADDRESS_ERROR_MIN_LENGTH'),
			'errorMessage_alpha' => sysLanguage::get('ENTRY_STREET_ADDRESS_ERROR_ALPHANUMERIC')
		);
		$this->validationRules['entry_street_address'] = $this->validationRules['street_address'];
		
		$this->validationRules['postcode'] = array(
			'validate'                => 1,
			'validation_type'         => 'min_length|required',
			'length'                  => sysConfig::get('ENTRY_POSTCODE_MIN_LENGTH'),
			'errorMessage_min_length' => sysLanguage::get('ENTRY_POST_CODE_ERROR_MIN_LENGTH')
		);
		$this->validationRules['entry_postcode'] = $this->validationRules['postcode'];
		
		$this->validationRules['city'] = array(
			'validate'                => 1,
			'validation_type'         => 'min_length|required',
			'length'                  => sysConfig::get('ENTRY_CITY_MIN_LENGTH'),
			'errorMessage_min_length' => sysLanguage::get('ENTRY_CITY_ERROR_MIN_LENGTH'),
			'errorMessage_alpha'      => sysLanguage::get('ENTRY_CITY_ERROR_ALPHA')
		);
		$this->validationRules['entry_city'] = $this->validationRules['city'];

		if(sysConfig::get('ACCOUNT_SUBURB') == 'true'){
			$this->validationRules['suburb'] = array(
				'validate'                => 1,
				'validation_type'         => 'min_length'.((sysConfig::get('ACCOUNT_SUBURB_REQUIRED') == 'true')?'|required':''),
				'length'                  => sysConfig::get('ENTRY_SUBURB_MIN_LENGTH'),
				'errorMessage_min_length' => sysLanguage::get('ENTRY_SUBURB_ERROR_MIN_LENGTH'),
				'errorMessage_alpha'      => sysLanguage::get('ENTRY_SUBURB_ERROR_ALPHA')
			);
			$this->validationRules['entry_suburb'] = $this->validationRules['suburb'];
		}

		if(sysConfig::get('ACCOUNT_COMPANY') == 'true'){
			$this->validationRules['company'] = array(
				'validate'                => 1,
				'validation_type'         => ((sysConfig::get('ACCOUNT_COMPANY_REQUIRED') == 'true')?'required':''),
				'errorMessage_required'   => sysLanguage::get('ENTRY_COMPANY_ERROR_REQUIRED')
			);
			$this->validationRules['entry_company'] = $this->validationRules['company'];
		}

		if(sysConfig::get('ACCOUNT_CITY_BIRTH') == 'true'){
			$this->validationRules['city_birth'] = array(
				'validate'                => 1,
				'validation_type'         => ((sysConfig::get('ACCOUNT_CITY_BIRTH_REQUIRED') == 'true')?'required':''),
				'errorMessage_required'   => sysLanguage::get('ENTRY_CITY_BIRTH_ERROR_REQUIRED')
			);
			$this->validationRules['entry_city_birth'] = $this->validationRules['city_birth'];
		}

		if(sysConfig::get('ACCOUNT_STATE') == 'true'){
			$this->validationRules['state'] = array(
				'validate'                => 1,
				'validation_type'         => 'min_length'.((sysConfig::get('ACCOUNT_STATE_REQUIRED') == 'true')?'|required':''),
				'length'                  => sysConfig::get('ENTRY_STATE_MIN_LENGTH'),
				'errorMessage_min_length' => sysLanguage::get('ENTRY_STATE_ERROR_MIN_LENGTH'),
				'errorMessage_alpha'      => sysLanguage::get('ENTRY_STATE_ERROR_ALPHA')
			);
			$this->validationRules['entry_state'] = $this->validationRules['state'];
		}
		
		$this->validationRules['country'] = array(
			'validate'               => 1,
			'validation_type'        => 'numeric|required',
			'errorMessage_numeric'   => sysLanguage::get('ENTRY_COUNTRY_ERROR_NUMERIC')
		);
		$this->validationRules['entry_country'] = $this->validationRules['country'];
		$this->validationRules['entry_country_id'] = $this->validationRules['country'];
	}
	
	public function current(){
		$this->_currentElement['original_field_value'] = parent::current();
		$this->_currentElement['sanitized_field_value'] = $this->_sanitize();
		
		$error = $this->_validate();
		if (empty($error)){
			$error = false;
		}
		$this->_currentElement['field_error_message'] = $error;
		$this->_currentElement['field_name'] = $this->key();
		return $this->_currentElement;
	}
	
	public function key(){
		return parent::key();
	}
	
	public function next(){
		parent::next();
	}
	
	public function rewind(){
		parent::rewind();
	}
	
	public function valid(){
		return parent::valid();
	}
	
	private function _validate(){
        $fieldValue = $this->_currentElement['sanitized_field_value'];
        $fieldName = $this->key();
        $error = false;
        
		if (array_key_exists($fieldName, $this->validationRules) === false){
			return false;
		}elseif ($this->validationRules[$fieldName]['validate'] == 0){
			return false;
		}elseif ($this->validationRules[$fieldName]['validate'] == 1){
			$validationType = $this->validationRules[$fieldName]['validation_type'];
		}
		
		if (!isset($validationType)){
			return false;
		}
		
		$validations = explode('|', $validationType);
		$errorMessage = '';
		foreach($validations as $validationType){
			switch($validationType){
				case 'reg_exp':
					$this->validationRulesRegEx['reg_exp'] = $this->validationRules[$fieldName]['reg_exp'];
					$pattern = $this->validationRulesRegEx[$validationType];
					if (!preg_match($pattern, $fieldValue)){
						$error = true;
					}
					break;
				case 'min_length':
					if (strlen($fieldValue) < $this->validationRules[$fieldName]['length']){
						$error = true;
					}
					break;
				case 'date':
					$rawDate = date_parse(strftime(sysLanguage::getDateFormat('short'),$fieldValue));
					if (checkdate($rawDate['month'], $rawDate['day'], $rawDate['year']) == false){
						$error = true;
					}
					break;
				case 'email':
					if ($this->_isValidEmailAddress($fieldValue) === false){
						$error = true;
					}
					break;
				case 'fiscal_code':
					if ($this->_isValidFiscalCode($fieldValue) === false){
						$error = true;
					}
					break;
				case 'vat_number':
					if ($this->_isValidVatNumber($fieldValue) === false){
						$error = true;
					}
					break;
				case 'checkbox':
					if ($fieldValue != $this->validationRules[$fieldName]['values']){
						$error = true;
					}
					break;
				case 'radio':
					$valuesArray = explode('|', $this->validationRules[$fieldName]['values']);
					if (!in_array($fieldValue, $valuesArray)){
						$error = true;
					}
					break;
				case 'required':
					if(isset($fieldValue) && empty($fieldValue)){
						$error = true;
					}

					break;
				default:
					if (array_key_exists($validationType, $this->validationRulesRegEx)){
						if (!preg_match($this->validationRulesRegEx[$validationType], $fieldValue)){
							$error = true;
						}
					}
					break;
			}
			
			if ($error === true){
				if (isset($this->validationRules[$fieldName]['errorMessage_' . $validationType])){
					if ($validationType == 'min_length'){
						$errorMessage = sprintf(
							$this->validationRules[$fieldName]['errorMessage_' . $validationType],
							$this->validationRules[$fieldName]['length']
						);
					}else{
						$errorMessage = $this->validationRules[$fieldName]['errorMessage_' . $validationType];
					}
				}
				break;
			}
		}
		
		if ($error === true){
			/*if (!in_array('required', $validations) && empty($fieldValue)){
				return false;
			}else{*/
				return $errorMessage;
			//}
		}
		return false;
	}
	
	private function _trim(&$trimData){
		if (is_array($trimData)){
			$trimData = array_map('trim', $trimData);
		}else{
			$trimData = trim($trimData);
		}
	}
	
	private function _sanitize(){
        $fieldName = $this->key();
        $fieldValue = $this->_currentElement['original_field_value'];
        $this->_trim($fieldValue);
        if (is_array($fieldValue)){
        	//array_map('addslashes', $fieldValue);
        }else{
     	   //$fieldValue = addslashes($fieldValue);
        }
        return strip_tags(urldecode($fieldValue));
	}
	
	private function _loadValidationRulesRegEx(){
		$this->validationRulesRegEx = array(
			'numeric'      => '/^[0-9]+$/',
			'alpha'        => '/^[-a-zA-Z ]+$/',
			'alphanumeric' => '/^[a-zA-Z0-9 ]+$/',
			'string'       => '/^[^"\r\n]+$/',
			'empty'        => '/^.+$/',
			//'required'     => '/^[a-zA-Z0-9]+$/',
			//'phone'        => '/^\(?[0-9]{3}\)?[\x20|\-|\.]?[0-9]{3}[\x20|\-|\.]?[0-9]{4}$/',
			'phone'        => '/^[0-9- ]+$/',
			'currency'     => '/^[0-9]*\.?[0-9]+$/',
			'zip'          => '/^[0-9]{5}$/',
			'state_code'   => '/^[A-Za-z]{2}$/'
		);
	}


    private function _isValidVatNumber($vat){
	    $error = false;
	    if (($vat == "")) {
		    $error = true;

	    } else if ((strlen($vat) != 11) && ($vat != "")) {
		    $error = true;
	    } else if (strlen($vat) == 11) {
		    if (!preg_match("/^[0-9]+$/", $vat)) {
			    $error = true;
		    } else {
			    $s = 0;
			    for ($i = 0; $i <= 9; $i += 2) $s += ord($vat[$i]) - ord('0');
			    for ($i = 1; $i <= 9; $i += 2) {
				    $c = 2 * (ord($vat[$i]) - ord('0'));
				    if ($c > 9) $c = $c - 9;
				    $s += $c;
			    }
			    if ((10 - $s % 10) % 10 != ord($vat[10]) - ord('0')) {
				    $error = true;

			    }
		    }
	    }
	    return $error;
    }

	private function _isValidFiscalCode($cf){
		$error = false;
		if (($cf == "")) {
			$error = true;

		} else if ((strlen($cf) != 16) && ($cf != "")) {
			$error = true;

		} else if (strlen($cf) == 16) {
			$cf = strtoupper($cf);
			if (!preg_match("/^[A-Z0-9]+$/", $cf)) {
				$error = true;

			} else {
				$s = 0;
				for ($i = 1; $i <= 13; $i += 2) {
					$c = $cf[$i];
					if ('0' <= $c && $c <= '9')
						$s += ord($c) - ord('0');
					else
						$s += ord($c) - ord('A');
				}
				for ($i = 0; $i <= 14; $i += 2) {
					$c = $cf[$i];
					switch ($c) {
						case '0':
							$s += 1;
							break;
						case '1':
							$s += 0;
							break;
						case '2':
							$s += 5;
							break;
						case '3':
							$s += 7;
							break;
						case '4':
							$s += 9;
							break;
						case '5':
							$s += 13;
							break;
						case '6':
							$s += 15;
							break;
						case '7':
							$s += 17;
							break;
						case '8':
							$s += 19;
							break;
						case '9':
							$s += 21;
							break;
						case 'A':
							$s += 1;
							break;
						case 'B':
							$s += 0;
							break;
						case 'C':
							$s += 5;
							break;
						case 'D':
							$s += 7;
							break;
						case 'E':
							$s += 9;
							break;
						case 'F':
							$s += 13;
							break;
						case 'G':
							$s += 15;
							break;
						case 'H':
							$s += 17;
							break;
						case 'I':
							$s += 19;
							break;
						case 'J':
							$s += 21;
							break;
						case 'K':
							$s += 2;
							break;
						case 'L':
							$s += 4;
							break;
						case 'M':
							$s += 18;
							break;
						case 'N':
							$s += 20;
							break;
						case 'O':
							$s += 11;
							break;
						case 'P':
							$s += 3;
							break;
						case 'Q':
							$s += 6;
							break;
						case 'R':
							$s += 8;
							break;
						case 'S':
							$s += 12;
							break;
						case 'T':
							$s += 14;
							break;
						case 'U':
							$s += 16;
							break;
						case 'V':
							$s += 10;
							break;
						case 'W':
							$s += 22;
							break;
						case 'X':
							$s += 25;
							break;
						case 'Y':
							$s += 24;
							break;
						case 'Z':
							$s += 23;
							break;
					}
				}
				if (chr($s % 26 + ord('A')) != $cf[15]) {
					$error = true;
				}
			}
		}
		return $error;
    }
	
	private function _isValidEmailAddress($emailAddress){
		$validAddress = true;
		$mail_pat = '^(.+)@(.+)$';
		$valid_chars = "[^] \(\)<>@,;:\.\\\"\[]";
		$atom = "$valid_chars+";
		$quoted_user='(\"[^\"]*\")';
		$word = "($atom|$quoted_user)";
		$user_pat = "^$word(\.$word)*$";
		$ip_domain_pat='^\[([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\]$';
		$domain_pat = "^$atom(\.$atom)*$";

		if (preg_match("/$mail_pat/", $emailAddress, $components)) {
			$user = $components[1];
			$domain = $components[2];
			// validate user
			if (preg_match("/$user_pat/", $user)) {
				// validate domain
				if (preg_match("/$ip_domain_pat/", $domain, $ip_components)) {
					// this is an IP address
					for ($i=1;$i<=4;$i++) {
						if ($ip_components[$i] > 255) {
							$validAddress = false;
							break;
						}
					}
				} else {
					// Domain is a name, not an IP
					if (preg_match("/$domain_pat/", $domain)) {
						/* domain name seems valid, but now make sure that it ends in a valid TLD or ccTLD
						and that there's a hostname preceding the domain or country. */
						$domain_components = explode(".", $domain);
						// Make sure there's a host name preceding the domain.
						if (sizeof($domain_components) < 2) {
							$validAddress = false;
						} else {
							$top_level_domain = strtolower($domain_components[sizeof($domain_components)-1]);
							// Allow all 2-letter TLDs (ccTLDs)
							if (preg_match('/^[a-z][a-z]$/', $top_level_domain) != 1) {
								$tld_pattern = '';
								// Get authorized TLDs from text file
								$tlds = file(sysConfig::getDirFsCatalog() . 'includes/tld.txt');
								while (list(,$line) = each($tlds)) {
									// Get rid of comments
									$words = explode('#', $line);
									$tld = trim($words[0]);
									// TLDs should be 3 letters or more
									if (preg_match('/^[a-z]{3,}$/', $tld) == 1) {
										$tld_pattern .= '^' . $tld . '$|';
									}
								}
								// Remove last '|'
								$tld_pattern = substr($tld_pattern, 0, -1);
								if (preg_match("/$tld_pattern/", $top_level_domain) == 0) {
									$validAddress = false;
								}
							}
						}
					} else {
						$validAddress = false;
					}
				}
			} else {
				$validAddress = false;
			}
		} else {
			$validAddress = false;
		}

		if ($validAddress === true && sysConfig::get('ENTRY_EMAIL_ADDRESS_CHECK') == 'true') {
			if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
				$validAddress = false;
			}
		}
		return $validAddress;
	}
}
?>