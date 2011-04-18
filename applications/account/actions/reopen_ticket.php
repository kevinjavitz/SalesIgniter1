<?php
$problemText = tep_db_prepare_input($_POST['problem_new']);
$problemReply = tep_db_prepare_input($_POST['problem_reply']);
$problemTextOld = tep_db_prepare_input($_POST['old_problem']);
$issueID = tep_db_prepare_input($_POST['issue_id']);
$iInfo = issues_getIssueInfo($issueID);
$productID = tep_db_prepare_input($_POST['rented_products']);

$customer = issues_getCustomerInfo($userAccount->getCustomerId());
if (tep_not_null($iInfo['rented_id'])){
	$product = issues_getQueueInfo($userAccount->getCustomerId(), $productID);
}else{
	$product = issues_getBookingInfo($userAccount->getCustomerId(), $productID);
}

if (tep_not_null($problemText)){
	$dataArray = array(
	'problem' => $problemTextOld . '<br><br>' . $problemText,
	'status' => 'O'
	);
	tep_db_perform(TABLE_RENTAL_ISSUES, $dataArray, 'update', 'issue_id = "' . $issueID . '"');

	$emailText = issues_getEmailText('newIssue', array(
	STORE_OWNER,
	$customer['customers_firstname'] . ' ' . $customer['customers_lastname'],
	$product['products_name'],
	$product['rented_date'],
	$problemText,
	$productID
	));
	tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, sysLanguage::get('EMAIL_SUBJECT_REPORT'), $emailText, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

	tep_redirect(itw_app_link('action=success', 'contact_us', 'success'));
} elseif (tep_not_null($problemReply)){
	$dataArray = array(
	'parent_id'     => $issueID,
	'rented_id'     => $iInfo['rented_id'],
	'products_id'   => $iInfo['products_id'],
	'products_name' => $iInfo['products_name'],
	'reported_date' => 'now()',
	'problem'       => $problemReply,
	'status'        => 'O',
	'customers_id'  => $userAccount->getCustomerId()
	);
	tep_db_perform(TABLE_RENTAL_ISSUES, $dataArray);

	$emailText = issues_getEmailText('replyIssue', array(
	STORE_OWNER,
	$customer['customers_firstname'] . ' ' . $customer['customers_lastname'],
	$product['products_name'],
	$product['rented_date'],
	$problemReply,
	$iInfo['products_id']
	));
	tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, sysLanguage::get('EMAIL_SUBJECT_REPORT'), $emailText, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
} else {
	$error = true;
	$messageStack->add('pageStack', ENTRY_EMAIL_ADDRESS_CHECK_ERROR, 'error');
}
?>