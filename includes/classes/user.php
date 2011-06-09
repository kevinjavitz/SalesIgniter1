<?php
require(sysConfig::getDirFsCatalog() . 'includes/classes/formValidation.php');

class RentalStoreUser implements Serializable {
	protected $customerInfo;

	public function __construct($cID = false){
		/*
		* @todo: Make it possible for these to be disabled and not cause errors.
		*******************************************************************************
		if (RENTALS_ALLOWED == 'True'){
		$this->plugins['membership'] = new rentalStoreUser_membership($cID);
		}

		if (USER_ADDRESS_BOOK_ENABLED == 'True'){
		$this->plugins['addressBook'] = new rentalStoreUser_addressBook($cID);
		}
		*******************************************************************************
		*
		*/

		if ($cID !== false){
			$this->loadCustomersInfo($cID);
		}else{
			/*
			* @todo: Anonymous User - Some possibilities here
			*/
			$this->customerInfo['id'] = 0;
			$this->customerInfo['default_address_id'] = 0;
			$this->customerInfo['delivery_address_id'] = 0;
		}
	}
	
	public function serialize(){
		$serialize = array();
		foreach(get_object_vars($this) as $varName => $varVal){
			if ($varVal instanceof Closure){
				unset($this->$varName);
			}else{
				$serialize[$varName] = $varVal;
			}
		}
		return serialize($serialize);
	}
	
	public function unserialize($data){
		$data = unserialize($data);
		foreach($data as $varName => $varVal){
			$this->$varName = $varVal;
		}
	}

	public function loadPlugins(){
		$this->plugins['membership'] = new rentalStoreUser_membership($this->customerInfo['id']);
		$this->plugins['addressBook'] = new rentalStoreUser_addressBook($this->customerInfo['id']);
	}

	public function processLogOut(){
		global $ShoppingCart;
		$this->customerInfo = array(
			'id' => 0
		);

		/*
		* @todo: Only here until the cart totally supports this class - BEGIN
		*/
		Session::remove('customer_id');
		Session::remove('customer_default_address_id');
		Session::remove('rental_address_id');
		Session::remove('customer_first_name');
		Session::remove('customer_country_id');
		Session::remove('customer_zone_id');
		Session::remove('comments');

		// ###### Added CCGV Contribution #########
		Session::remove('gv_id');
		Session::remove('cc_id');
		// ###### End Added CCGV Contribution #########

		/* Session Cleanup */
		Session::remove('payment');
		Session::remove('agreed_terms');
		Session::remove('shipping');
		Session::remove('onlyReservations');
		Session::remove('onepage');
		Session::remove('billto');
		Session::remove('sendto');
		Session::remove('addressCheck');

		/*
		* @todo: Only here until the cart totally supports this class - END
		*/

		$ShoppingCart->emptyCart();
		Session::remove('rentalQueueBase');
		//$rentalQueueBase = &Session::getReference('rentalQueueBase');
		//$rentalQueueBase->emptyQueue();

		if (USER_ADDRESS_BOOK_ENABLED == 'True'){
			$this->plugins['addressBook']->reset();
		}
	}

	public function setCustomerInfo($dataArray){
		if (!isset($this->customerInfo) || empty($this->customerInfo)){
			$this->customerInfo = $dataArray;
		}else{
			foreach($dataArray as $key => $val){
				$this->customerInfo[$key] = $val;
			}
		}
	}

	public function loadCustomersInfo($cID){
		$Qcustomer = Doctrine_Query::create()
		->from('Customers')
		->where('customers_id = ?', $cID)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcustomer){
			$customer = $Qcustomer[0];

			$Qcountry = Doctrine_Query::create()
			->select('entry_country_id, entry_zone_id, entry_state')
			->from('AddressBook')
			->where('customers_id = ?', $customer['customers_id'])
			->andWhere('address_book_id = ?', $customer['customers_default_address_id'])
			->execute();
			$country = $Qcountry->toArray(true);
		
			$customerInfo = array(
				'id'                 => $customer['customers_id'],
				'default_address_id' => $customer['customers_default_address_id'],
				'delivery_address_id' => $customer['customers_delivery_address_id'],
				'firstName'          => $customer['customers_firstname'],
				'lastName'           => $customer['customers_lastname'],
				'dob'                => $customer['customers_dob'],
				'countryId'          => $country[0]['entry_country_id'],
				'zoneId'             => $country[0]['entry_zone_id'],
				'fullName'           => $customer['customers_firstname'] . ' ' . $customer['customers_lastname'],
				'emailAddress'       => $customer['customers_email_address'],
				'telephone'          => $customer['customers_telephone'],
				'fax'                => $customer['customers_fax'],
				'languageId'         => $customer['language_id']
			);
			
			if (isset($customer['is_provider']) && $customer['is_provider'] == '1'){
				$customerInfo['is_provider'] = true;
			}
			
			$this->setCustomerInfo($customerInfo);
		}
	}
	
	public function isProvider(){
		return ((isset($this->customerInfo['is_provider']))?($this->customerInfo['is_provider'] === true) : false);
	}

	public function processLogIn($username, $password){
		global $messageStack, $ShoppingCart;
		$error = false;
		if (isset($_GET['adminCustomerId']) && empty($username) && empty($password)){
			$Qcheck = Doctrine_Query::create()
			->select('sesskey')
			->from('Sessions')
			->where('sesskey = ?', $_GET['osCAdminID'])
			->andWhere('expiry > ?', time())
			->execute();
			if ($Qcheck->count() > 0){
				$Qcustomer = Doctrine_Query::create()
				->select('customers_id, customers_password, customers_default_address_id')
				->from('Customers')
				->where('customers_id = ?', (int)$_GET['adminCustomerId'])
				->execute();
				
				$noValidate = true;
			}else{
				$noValidate = false;
				$Qcustomer = false;
			}
		}else{
			$Qcustomer = Doctrine_Query::create()
			->select('customers_id, customers_password, customers_default_address_id')
			->from('Customers')
			->where('customers_email_address = ?', $username)
			->execute();
			$noValidate = false;
		}
		
		if ($Qcustomer){
			if ($noValidate === true || $this->validatePassword($password, $Qcustomer[0]->customers_password) === true){
				if (sysConfig::get('SESSION_RECREATE') == 'True') {
					Session::recreate();
				}

				$this->loadCustomersInfo($Qcustomer[0]->customers_id);

				if ($this->customerInfo['id'] <= 0){
					print_r($_SESSION);
					die('PROBLEM');
				}

				if (isset($this->plugins['addressBook'])){
					$this->plugins['addressBook']->__construct($this->customerInfo['id']);
					$this->plugins['addressBook']->setDefaultAddress($Qcustomer[0]->customers_default_address_id);
				}

				if (isset($this->plugins['membership'])){
					$this->plugins['membership']->__construct($this->customerInfo['id']);
				}

				if (sysConfig::get('EXTENSION_INVENTORY_CENTERS_ENABLED') == 'True'){
					$this->setCustomerInfo(array(
						'serviceCenter' => $this->plugins['addressBook']->getAddressInventoryCenter()
					));
				}

				/*
				* @todo: Remove Session Refrences For The Above vars
				*/
				Session::set('customer_id', $this->customerInfo['id']);
				Session::set('customer_first_name', $this->customerInfo['firstName']);
				Session::set('customer_country_id', $this->customerInfo['countryId']);
				Session::set('customer_zone_id', $this->customerInfo['zoneId']);

				if (USER_ADDRESS_BOOK_ENABLED == 'True'){
					Session::set('customer_default_address_id', $this->plugins['addressBook']->defaultAddress);
				}

				if (ALLOW_RENTALS == 'true'){
					Session::set('rental_address_id', $this->plugins['membership']->membershipInfo['rental_address_id']);
				}

				if (sysConfig::get('EXTENSION_INVENTORY_CENTERS_ENABLED') == 'True'){
					$_SESSION['addressCheck']['systemSelected'] = $this->customerInfo['serviceCenter'];
				}

				$this->updateUserLogins();

				$ShoppingCart->restoreContents();

				if (Session::exists('rentalQueueBase') === true){
					$rentalQueueBase = &Session::getReference('rentalQueueBase');
					$rentalQueueBase->customerID = $this->customerInfo['id'];
					$rentalQueueBase->restore_contents();
				}
			}else{
				$error = true;
			}
		}else{
			$error = true;
		}

		if ($error === true){
			$messageStack->addSession('pageStack', sysLanguage::get('TEXT_EMAIL_DO_NOT_MATCH'), 'error');
			return false;
		}
		return true;
	}

	public function processPasswordForgotten($emailAddress){
		global $messageStack;
		$error = false;
		$Qcustomer = Doctrine_Query::create()
		->select('customers_firstname, customers_lastname, customers_password, customers_id')
		->from('Customers')
		->where('customers_email_address = ?', $emailAddress);
		
		$Result = $Qcustomer->execute(array(), Doctrine::HYDRATE_ARRAY);
		if ($Result){
			$cInfo = $Result[0];
			
			$Qcustomer->free();
			unset($Result);
			unset($Qcustomer);
			
			$new_password = tep_create_random_value(sysConfig::get('ENTRY_PASSWORD_MIN_LENGTH'));
			$crypted_password = $this->encryptPassword($new_password);
			$firstname = $cInfo['customers_firstname'];
			$lastname = $cInfo['customers_lastname'];

			Doctrine_Query::create()
			->update('Customers')
			->set('customers_password', '?', $crypted_password)
			->where('customers_id = ?', $cInfo['customers_id'])
			->execute();

			$emailEvent = new emailEvent('password_forgotten');
			$emailEvent->setVars(array(
				'newPassword' => $new_password,
				'firstname'   => $firstname,
				'lastname'    => $lastname,
				'full_name'   => $firstname . ' ' . $lastname,
				'requestIP'   => $_SERVER['REMOTE_ADDR']
			));
			
			$emailEvent->sendEmail(array(
				'email' => $emailAddress,
				'name'  => $firstname . ' ' . $lastname
			));

			$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_PASSWORD_SENT'), 'success');
		}else{
			$error = true;
			$messageStack->addSession('pageStack', sysLanguage::get('TEXT_NO_EMAIL_ADDRESS_FOUND'), 'error');
		}

		if ($error === true){
			return false;
		}
		return true;
	}

	public function createNewAccount(){
		global $currencies;
		if ($this->customerInfo['id'] > 0) return $this->customerInfo['id'];

		$newUser = new Customers();
		$newUser->customers_firstname = $this->customerInfo['firstName'];
		$newUser->customers_lastname = $this->customerInfo['lastName'];
		$newUser->customers_email_address = $this->customerInfo['emailAddress'];
		$newUser->customers_gender = $this->customerInfo['gender'];
		$newUser->customers_telephone = $this->customerInfo['telephone'];
		$newUser->customers_fax = $this->customerInfo['fax'];
		$newUser->customers_password = $this->encryptPassword($this->customerInfo['password']);
		$newUser->customers_dob = $this->customerInfo['dob'];
		$newUser->customers_newsletter = $this->customerInfo['newsletter'];
		$newUser->language_id = $this->customerInfo['languageId'];
		//$newUser->customers_referral = $this->customerInfo['referral'];

		EventManager::notify('NewCustomerAccountBeforeExecute', &$newUser);
		
		$newUser->save();
		$this->customerInfo['id'] = $newUser->customers_id;
		
		$customersInfo = new CustomersInfo();
		$customersInfo->customers_info_id = $this->customerInfo['id'];
		$customersInfo->customers_info_number_of_logons = 0;
//		$customersInfo->customers_info_source_id = $this->customerInfo['referral'];
		$customersInfo->global_product_notifications = 0;
		$customersInfo->save();
		
		$this->plugins['addressBook']->setCustomerId($this->customerInfo['id']);
		$this->plugins['membership']->setCustomerId($this->customerInfo['id']);
		Session::remove('rentalQueueBase');

		// ###### Added CCGV Contribution #########
		$Module = OrderShippingModules::getModule('coupon');
		if ($Module !== false){
			if ($Module->isEnabled() === true){
				$this->newCustomerEmailVars['signupVoucher'] = false;
				$this->newCustomerEmailVars['signupCoupon'] = false;

				if (sysConfig::get('NEW_SIGNUP_GIFT_VOUCHER_AMOUNT') > 0) {
					$coupon_code = create_coupon_code();
				
					$newCoupon = new Coupons();
					$newCoupon->coupon_code = $coupon_code;
					$newCoupon->coupon_type = 'G';
					$newCoupon->coupon_amount = sysConfig::get('NEW_SIGNUP_GIFT_VOUCHER_AMOUNT');
					$newCoupon->date_created = date('Y-m-d');
					$newCoupon->save();

					$newCouponTrack = new CouponEmailTrack();
					$newCouponTrack->coupon_id = $newCoupon->coupon_id;
					$newCouponTrack->customer_id_sent = 0;
					$newCouponTrack->sent_firstname = 'Admin';
					$newCouponTrack->emailed_to = $this->customerInfo['emailAddress'];
					$newCouponTrack->date_sent = date('Y-m-d');
					$newCouponTrack->save();

					$this->newCustomerEmailVars['signupVoucher'] = true;
					$this->newCustomerEmailVars['signupVoucherAmount'] = $currencies->format(sysConfig::get('NEW_SIGNUP_GIFT_VOUCHER_AMOUNT'));
					$this->newCustomerEmailVars['signupVoucherCode'] = $coupon_code;
					$this->newCustomerEmailVars['signupVoucherLink'] = itw_app_link('gv_no=' . $coupon_code, 'gv_redeem', 'default', 'NONSSL', false);
				}

				if (sysConfig::get('NEW_SIGNUP_DISCOUNT_COUPON') != '') {
					$Qcoupon = Doctrine_Query::create()
					->select('c.coupon_id, c.coupon_code, cd.coupon_description')
					->from('Coupons c')
					->leftJoin('c.CouponsDescription cd')
					->where('c.coupon_code = ?', sysConfig::get('NEW_SIGNUP_DISCOUNT_COUPON'))
					->andWhere('cd.language_id = ?', Session::get('languages_id'));

					$Result = $Qcoupon->execute(array(), Doctrine::HYDRATE_ARRAY);
					if ($Result){
						$newCouponTrack = new CouponEmailTrack();
						$newCouponTrack->coupon_id = $Result[0]['coupon_id'];
						$newCouponTrack->customer_id_sent = 0;
						$newCouponTrack->sent_firstname = 'Admin';
						$newCouponTrack->emailed_to = $this->customerInfo['emailAddress'];
						$newCouponTrack->date_sent = date('Y-m-d');
						$newCouponTrack->save();

						$this->newCustomerEmailVars['signupCoupon'] = true;
						$this->newCustomerEmailVars['signupCouponDescription'] = $Result[0]['CouponsDescription'][0]['coupon_description'];
						$this->newCustomerEmailVars['signupCouponCode'] = $Result[0]['coupon_code'];
					}
				}
			}
		}
		// ###### End Added CCGV Contribution #########

		$this->sendNewCustomerEmail();
		return $this->customerInfo['id'];
	}
	
	public function updateUserLogins(){
		$CustomersInfo = Doctrine::getTable('CustomersInfo')->find($this->customerInfo['id']);
		$CustomersInfo->customers_info_number_of_logons++;
		$CustomersInfo->customers_info_date_of_last_logon = date('Y-m-d H:i:s');
		$CustomersInfo->save();
		
		unset($CustomersInfo);
	}

	public function updateCustomerAccount(){
		$Customer = Doctrine::getTable('Customers')->find($this->getCustomerId());
		$Customer->customers_firstname = $this->customerInfo['firstName'];
		$Customer->customers_lastname = $this->customerInfo['lastName'];
		$Customer->customers_email_address = $this->customerInfo['emailAddress'];
		$Customer->customers_telephone = $this->customerInfo['telephone'];
		$Customer->customers_fax = $this->customerInfo['fax'];
		$Customer->customers_newsletter = (isset($this->customerInfo['newsletter']) ? $this->customerInfo['newsletter'] : 0);
		$Customer->language_id = $this->customerInfo['languageId'];
		
		if (sysConfig::get('ACCOUNT_DOB') == 'true'){
			$Customer->customers_dob = $this->customerInfo['dob'];
		}
		
		if (sysConfig::get('ACCOUNT_GENDER') == 'true'){
			$Customer->customers_gender = $this->customerInfo['gender'];
		}
		
		$Customer->save();

		$AddressBook = Doctrine::getTable('AddressBook')->find($this->plugins['addressBook']->getDefaultAddressId());
		$AddressBook->entry_firstname = $this->customerInfo['firstName'];
		$AddressBook->entry_lastname = $this->customerInfo['lastName'];
		$AddressBook->save();
	}

	public function sendNewCustomerEmail(){
		$firstName = $this->customerInfo['firstName'];
		$lastName = $this->customerInfo['lastName'];
		$emailAddress = $this->customerInfo['emailAddress'];
		$fullName = $this->customerInfo['firstName'] . ' ' . $this->customerInfo['lastName'];

		$emailEvent = new emailEvent('create_account');

		$emailEvent->setVars(array(
			'email_address' => $emailAddress,
			'password'      => (is_array($this->customerInfo['password']) ? $this->customerInfo['password']['password'] : $this->customerInfo['password']),
			'firstname'     => $firstName,
			'lastname'      => $lastName,
			'full_name'     => $fullName
		));

		if (isset($this->newCustomerEmailVars)){
			foreach($this->newCustomerEmailVars as $var => $val){
				$emailEvent->setVar($var, $val);
			}
		}

		$emailEvent->sendEmail(array(
			'email' => $emailAddress,
			'name'  => $fullName
		));
	}

	public function emailExists($emailAddress){
		$Qcustomer = Doctrine_Query::create()
		->select('customers_id')
		->from('Customers')
		->where('customers_email_address = ?', $emailAddress);
		
		$Result = $Qcustomer->execute(array(), Doctrine::HYDRATE_ARRAY);
		if ($Result){
			return true;
		}
		return false;
	}

	public function validatePassword($plain, $encrypted){
		if (!empty($plain) && !empty($encrypted)){
			// split apart the hash / salt
			$stack = explode(':', $encrypted);

			if (sizeof($stack) != 2) return false;

			if (md5($stack[1] . $plain) == $stack[0]){
				return true;
			}
		}
		return false;
	}

	public function encryptPassword($plain){
		$password = '';

		for ($i=0; $i<10; $i++) {
			$password .= tep_rand();
		}

		$salt = substr(md5($password), 0, 2);
		$password = md5($salt . $plain) . ':' . $salt;
		return $password;
	}

	public function requestReactivation(){
		global $messageStack;
		$name = $this->getFullName();
		$email_address = $this->getEmailAddress();
		$request = 'The customer, ' . $name . ' has requested to reactivate his membership account.';
		tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, sysLanguage::get('EMAIL_SUBJECT'), $request, $name, $email_address);

		$messageStack->addSession('pageStack', 'Your reactivation request has been sent', 'success');
	}

	public function setFirstName($val){
		$this->customerInfo['firstName'] = $val;
	}

	public function setLastName($val){
		$this->customerInfo['lastName'] = $val;
	}

	public function setEmailAddress($val){
		$this->customerInfo['emailAddress'] = $val;
	}

	public function setFaxNumber($val){
		$this->customerInfo['fax'] = $val;
	}

	public function setTelephoneNumber($val){
		$this->customerInfo['telephone'] = $val;
	}

	public function setPassword($val){
		$this->customerInfo['password'] = $val;
	}

	public function setGender($val){
		$this->customerInfo['gender'] = $val;
	}

	public function setDateOfBirth($val){
		$date = date_parse($val);
		$this->customerInfo['dob'] = date('m/d/Y', mktime(0,0,0,$date['month'],$date['day'],$date['year'])); 
	}

	public function setNewsletter($val){
		$this->customerInfo['newsletter'] = $val;
	}

	public function setZoneId($val){
		$this->customerInfo['zoneId'] = $val;
	}

	public function setCountryId($val){
		$this->customerInfo['countryId'] = $val;
	}

	public function setLanguageId($val){
		$this->customerInfo['languageId'] = $val;
	}

	public function getLanguageId(){
		return $this->customerInfo['languageId'];
	}
	
	public function getDefaultAddressId(){
		return $this->customerInfo['default_address_id'];
	}

	public function getDeliveryDefaultAddressId(){
		return $this->customerInfo['delivery_address_id'];
	}

	public function getFullName(){
		if (!isset($this->customerInfo['fullName'])){
			$this->customerInfo['fullName'] = $this->getFirstName() . ' ' . $this->getLastName();
		}
		return $this->customerInfo['fullName'];
	}

	public function getEmailAddress(){ return $this->customerInfo['emailAddress']; }
	public function getFirstName(){ return $this->customerInfo['firstName']; }
	public function getLastName(){ return $this->customerInfo['lastName']; }
	public function getTelephoneNumber(){ return $this->customerInfo['telephone']; }
	public function getFaxNumber(){ return $this->customerInfo['fax']; }
	public function getCustomerId(){ return $this->customerInfo['id']; }
	public function getDateOfBirth(){ return $this->customerInfo['dob']; }
	public function isLoggedIn(){ return ($this->customerInfo['id'] > 0 ? true : false); }
	public function getCustomerInfo(){ return $this->customerInfo; }

	public function isRentalMember(){
		return ($this->plugins['membership']->isRentalMember() && $this->plugins['membership']->getPlanId() > 0);
	}

	public function membershipIsActivated(){
		return $this->plugins['membership']->isActivated();
	}

	public function needsRenewal(){
		return $this->plugins['membership']->needsRenewal();
	}

      public function validateEmailAddress($emailAddress){
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
								$tlds = file(DIR_WS_INCLUDES . 'tld.txt');
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

		if ($validAddress === true && ENTRY_EMAIL_ADDRESS_CHECK == 'true') {
			if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
				$validAddress = false;
			}
		}
		return $validAddress;
      }
       
	public function validate(&$accountValidation){
		global $messageStack;
		
		$errRow = array();
		$error = false;
		$userValidater = new formValidation($accountValidation);
		foreach($userValidater as $fieldName => $err){
			if (!empty($err['field_error_message'])){
				$errRow[] = $err['field_error_message'];
				$error = true;
			}
			$accountValidation[$fieldName] = $err['sanitized_field_value'];
		}
		
		/*
		 * Has to be outside because it won't always be validating with a password confirmation field
		 */
		if (isset($accountValidation['password'])){
			if ($accountValidation['password'] != $accountValidation['confirmation']){
				$errRow[] = sysLanguage::get('ENTRY_PASSWORD_ERROR_NOT_MATCHING');
				$error = true;
			}
		}

		/*
		 * Has to be outside because sometimes we need to check email with other conditions
		 */
		if(isset($accountValidation['email_address'])){
			$Qcustomer = Doctrine_Query::create()
			->select('customers_id')
			->from('Customers')
			->where('customers_email_address = ?', $accountValidation['email_address']);
			if ($this->getCustomerId() > 0){
				$Qcustomer->andWhere('customers_id != ?', $this->getCustomerId());
			}

			$Result = $Qcustomer->execute(array(), Doctrine::HYDRATE_ARRAY);
			if (count($Result) > 0){
				$errRow[] = sysLanguage::get('TEXT_EMAIL_ADDRESS_EXISTS');
				$error = true;
			}
		}
		
		if (sizeof($errRow) > 0){
			$messageStack->addSessionMultiple('pageStack', $errRow, 'error');
			$error = true;
		}
		return $error;
	}
}
?>