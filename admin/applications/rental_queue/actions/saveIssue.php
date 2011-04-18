<?php
	$Issue = Doctrine_Core::getTable('RentalIssues')->find((int) $_GET['tID']);
	$Issue->feedback = $_POST['feedback'];
	$Issue->status = 'C';
	$Issue->save();

	$Qcustomer = Doctrine_Query::create()
	->select('customers_firstname, customers_lastname, customers_firstname, customers_email_address, language_id')
	->from('Customers')
	->where('customers_id = ?', (int) $_POST['customers_id'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$emailEvent = new emailEvent('rental_issues', $Qcustomer[0]['language_id']);
	$emailEvent->setVars(array(
		'firstname'    => $Qcustomer[0]['customers_firstname'],
		'issueID'      => (int) $_GET['tID'],
		'issueDetails' => $_POST['feedback']
	));

	$emailEvent->sendEmail(array(
		'name' => $Qcustomer[0]['customers_firstname'] . ' ' . $Qcustomer[0]['customers_lastname'],
		'email' => $Qcustomer[0]['customers_email_address']
	));

	EventManager::attachActionResponse(itw_app_link('tID=' . (int)$_GET['tID'], 'rental_queue', 'issues'), 'redirect');
?>