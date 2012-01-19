<?php
$productID = tep_db_prepare_input($_POST['rented_products']);
$problemText = tep_db_prepare_input($_POST['problem_desc']);

if (tep_not_null($productID) && tep_not_null($problemText)){
	$customer = issues_getCustomerInfo($userAccount->getCustomerId());
	if (!isset($_POST['onetime'])){
		$product = issues_getQueueInfo($userAccount->getCustomerId(), $productID);
	}else{
		$product = issues_getBookingInfo($userAccount->getCustomerId(), $productID);
	}

	$dataArray = array(
	'rented_id'     => (isset($product['customers_queue_id']) ? $product['customers_queue_id'] : 'null'),
	'products_id'   => $product['products_id'],
	'products_name' => $product['products_name'],
	'reported_date' => 'now()',
	'problem'       => $problemText,
	'status'        => 'O',
	'customers_id'  => $userAccount->getCustomerId()
	);
	tep_db_perform(TABLE_RENTAL_ISSUES, $dataArray);

	$emailText = issues_getEmailText('newIssue', array(
	STORE_OWNER,
	$customer['customers_firstname'] . ' ' . $customer['customers_lastname'],
	$product['products_name'],
	$product['rented_date'],
	$problemText,
	$product['products_id']
	));
	tep_mail(sysConfig::get('STORE_OWNER'), sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'), sysLanguage::get('EMAIL_SUBJECT_REPORT'), $emailText, sysConfig::get('STORE_OWNER'), sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'));

	tep_redirect(itw_app_link(null, 'contact_us', 'success'));
} else {
	$error = true;
	$messageStack->add('pageStack',sysLanguage::get('ENTRY_EMAIL_ADDRESS_CHECK_ERROR'), 'error');
}
?>