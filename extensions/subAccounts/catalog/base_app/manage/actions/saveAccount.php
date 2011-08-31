<?php
	$error = false;
	$cID = $_GET['cID'];
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

	if(isset($_POST['email_address']) && !empty($_POST['email_address'])){
		$emailAddress = $_POST['email_address'];
	}else{
		$error = true;
		$messageStack->addSession('pageStack','Email Address cannot be blank or already exists','error');
	}

	if(isset($_POST['password']) && !empty($_POST['password'])){
		$password = $_POST['password'];
	}else{
		$password = '';
	}

	if($error === false){
		updateAccount($cID, $firstName, $lastName, $emailAddress, $gender, $dob, $password);
		$messageStack->addSession('pageStack',sysLanguage::get('TEXT_SUBACCOUNT_UPDATED'),'success');
	}

	function updateAccount($cID, $firstName, $lastName, $emailAddress, $gender, $dob, $password){
		global $currencies, $userAccount;

		$newUser = Doctrine::getTable('Customers')->find($cID);
		$newUser->customers_firstname = $firstName;
		$newUser->customers_lastname = $lastName;
		$newUser->customers_email_address = $emailAddress;
		$newUser->customers_gender = $gender;
		$newUser->customers_dob = $dob;
		if($password != ''){
			$newUser->customers_password = $userAccount->encryptPassword($password);
		}
		$newUser->language_id = $userAccount->getLanguageId();
		$newUser->parent = $userAccount->getCustomerId();
		$newUser->save();
	}
	//move into custom fields
	Doctrine::getTable('ProductCustomFieldsToCustomers')->findbyCustomersId($cID)->delete();
	if (isset($_POST['fields'])){
		foreach($_POST['fields'] as $fID => $val){
			/*$QexistingField = Doctrine_Query::create()
			->from('ProductCustomFieldsToCustomers')
			->where('customers_id=?', $cID)
			->andWhere('product_custom_field_id=?', $fID)
			->fetchOne();
			if($QexistingField){
				$QexistingField->options = implode($val,',');
				$QexistingField->save();
			}else{*/
				$ProductCustomFieldsToCustomers = new ProductCustomFieldsToCustomers();
				$ProductCustomFieldsToCustomers->customers_id = $cID;
				$ProductCustomFieldsToCustomers->product_custom_field_id = $fID;
				$ProductCustomFieldsToCustomers->options = implode($val,';');
				$ProductCustomFieldsToCustomers->save();
			//}
		}
	}
	EventManager::attachActionResponse(itw_app_link(null, 'account', 'default', 'SSL'), 'redirect');

?>