<?php

$problemText = $_POST['feedback'];

$productID = $_POST['products_id'];

$QData = Doctrine_Query::create()
	->from('RentedProducts')
	->where('products_id = ?', $productID)
	->andWhere('customers_id = ?', $_POST['customers_id'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$QProductName = Doctrine_Query::create()
	->from('ProductsDescription')
	->where('products_id = ?', $productID)
	->andWhere('language_id = ?', Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$customerId = $_POST['customers_id'];
$RentIssue = new RentIssues();
	$RentIssue->parent_id = $_GET['fID'];
	$RentIssue->products_id = $productID;
	$RentIssue->products_name = $QProductName[0]['products_name'];
	$RentIssue->reported_date = date('Y-m-d H:i:s');
	$RentIssue->status = 'O';
	$RentIssue->customers_id = 0;
	$RentIssue->feedback = $problemText;
	$RentIssue->save();

	$Qcustomer = Doctrine_Query::create()
	->select('customers_firstname, customers_lastname, customers_firstname, customers_email_address, language_id')
	->from('Customers')
	->where('customers_id = ?', (int) $_POST['customers_id'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$emailEvent = new emailEvent('rental_issues', $Qcustomer[0]['language_id']);
	$emailEvent->setVars(array(
		'firstname'    => $Qcustomer[0]['customers_firstname'],
		'issueID'      => (int) $_GET['fID'],
		'issueDetails' => $_POST['feedback']
	));

	$emailEvent->sendEmail(array(
		'name' => $Qcustomer[0]['customers_firstname'] . ' ' . $Qcustomer[0]['customers_lastname'],
		'email' => $Qcustomer[0]['customers_email_address']
	));

	EventManager::attachActionResponse(itw_app_link('fID=' . (int)$_GET['fID'], 'rental_queue', 'issues'), 'redirect');
?>