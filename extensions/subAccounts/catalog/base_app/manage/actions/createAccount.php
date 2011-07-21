<?php
    $error = false;
	if(isset($_POST['firstname']) && !empty($_POST['firstname'])){
		$firstName = $_POST['firstname'];
	}else{
		$error = true;
		$messageStack->addSession('pageStack','Firstname cannot be blank','error');
	}

	if(isset($_POST['lastname']) && !empty($_POST['lastname'])){
		$lastName = $_POST['lastname'];
	}else{
		$error = true;
		$messageStack->addSession('pageStack','Lastname cannot be blank','error');
	}

	if(isset($_POST['gender']) && !empty($_POST['gender'])){
		$gender = $_POST['gender'];
	}else{
		$error = true;
		$messageStack->addSession('pageStack','Gender cannot be blank','error');
	}

	if(isset($_POST['dob']) && !empty($_POST['dob'])){
		$dob = $_POST['dob'];
	}else{
		$error = true;
		$messageStack->addSession('pageStack','Date of birth cannot be blank','error');
	}

	if(isset($_POST['email_address']) && !empty($_POST['email_address'])&& emailExists($_POST['email_address']) === false){
		$emailAddress = $_POST['email_address'];
	}else{
		$error = true;
		$messageStack->addSession('pageStack','Email Address cannot be blank or already exists','error');
	}

	if(isset($_POST['password']) && !empty($_POST['password'])){
		$password = $_POST['password'];
	}else{
		$error = true;
		$messageStack->addSession('pageStack','Password cannot be blank','error');
	}

    if($error === false){
	    createNewAccount($firstName, $lastName, $emailAddress, $gender, $dob, $password);
	    $messageStack->addSession('pageStack',sysLanguage::get('TEXT_SUBACCOUNT_CREATED'),'success');
    }
	function createNewAccount($firstName, $lastName, $emailAddress, $gender, $dob, $password){
		global $currencies, $userAccount;

		$newUser = new Customers();
		$newUser->customers_firstname = $firstName;
		$newUser->customers_lastname = $lastName;
		$newUser->customers_email_address = $emailAddress;
		$newUser->customers_gender = $gender;
		$newUser->customers_dob = $dob;
		$newUser->customers_password = $userAccount->encryptPassword($password);
		$newUser->language_id = $userAccount->getLanguageId();
		$newUser->parent = $userAccount->getCustomerId();

		$newUser->save();

		sendNewCustomerEmail($newUser->customers_firstname, $newUser->customers_lastname, $newUser->customers_email_address, $newUser->customers_password);
	}

	function sendNewCustomerEmail($firstName, $lastName, $emailAddress, $password){

		$fullName = $firstName . ' ' . $lastName;

		$emailEvent = new emailEvent('create_account');

		$emailEvent->setVars(array(
			'email_address' => $emailAddress,
			'password'      => $password,
			'firstname'     => $firstName,
			'lastname'      => $lastName,
			'full_name'     => $fullName
		));

		$emailEvent->sendEmail(array(
			'email' => $emailAddress,
			'name'  => $fullName
		));
	}

	function emailExists($emailAddress){
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

	EventManager::attachActionResponse(itw_app_link('appExt=subAccounts', 'manage', 'default'), 'redirect');

?>