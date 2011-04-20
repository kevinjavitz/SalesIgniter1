<?php
/*
$Id: gv_send.php,v 1.1.2.3 2003/05/12 22:57:20 wilt Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2002 - 2003 osCommerce

Gift Voucher System v1.0
Copyright (c) 2001, 2002 Ian C Wilson
http://www.phesis.org

Released under the GNU General Public License
*/

require('includes/application_top.php');

require('includes/classes/http_client.php');

// if the customer is not logged on, redirect them to the login page
if ($userAccount->isLoggedIn() === false) {
	$navigation->set_snapshot();
	tep_redirect(itw_app_link(null, 'account', 'login', 'SSL'));
}

if (isset($_POST['back_x']) || isset($_POST['back_y'])){
	$_GET['action'] = '';
}

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (!empty($action)){
	switch($action){
		case 'send':
			$error = false;
			if (!tep_validate_email(trim($_POST['email']))) {
				$messageStack->addSession('pageStack', ERROR_ENTRY_EMAIL_ADDRESS_CHECK, 'error');
				$error = true;
			}
			
			if ($error === false){
				$QvoucherBalance = Doctrine_Query::create()
				->select('amount')
				->from('CouponGvCustomer')
				->where('customer_id = ?', $userAccount->getCustomerId())
				->fetchOne();

				$customer_amount = $QvoucherBalance['amount'];
				$gv_amount = (float)trim($_POST['amount']);
				if (ereg('[^0-9/.]', $gv_amount)){
					$messageStack->addSession('pageStack', ERROR_ENTRY_AMOUNT_CHECK, 'error');
					$error = true;
				}
				if ($gv_amount>$customer_amount || $gv_amount == 0) {
					$messageStack->addSession('pageStack', ERROR_ENTRY_AMOUNT_CHECK, 'error');
					$error = true;
				}
			}
			break;
		case 'process':
			$id1 = create_coupon_code($userAccount->getEmailAddress());

			$QvoucherBalance = Doctrine_Query::create()
			->select('amount')
			->from('CouponGvCustomer')
			->where('customer_id = ?', $userAccount->getCustomerId())
			->fetchOne();

			$new_amount = $QvoucherBalance['amount'] - (float)$_POST['amount'];
			if ($new_amount < 0) {
				$error = true;
				$messageStack->addSession('pageStack', ERROR_ENTRY_AMOUNT_CHECK, 'error');
				$action = 'send';
			}else{
				Doctrine_Query::create()
				->update('CouponGvCustomer')
				->set('amount', '?', $new_amount)
				->where('customer_id = ?', $userAccount->getCustomerId())
				->execute();

				$newCoupon = new Coupons();
				$newCoupon->coupon_type = 'G';
				$newCoupon->coupon_code = $id1;
				$newCoupon->coupon_amount = (float)$_POST['amount'];
				$newCoupon->save();

				$newEmailTrack = new CouponEmailTrack();
				$newEmailTrack->coupon_id = $newCoupon->coupon_id;
				$newEmailTrack->customer_id_sent = $userAccount->getCustomerId();
				$newEmailTrack->sent_firstname = addslashes($userAccount->getFirstName());
				$newEmailTrack->sent_lastname = addslashes($userAccount->getLastName());
				$newEmailTrack->emailed_to = $_POST['email'];
				$newEmailTrack->save();
				
				$voucherAmount = $currencies->format($_POST['amount']);
				$voucherID = $newCoupon->coupon_code;
				$voucherLink = itw_app_link('gv_no=' . $voucherID, 'gv_redeem', 'default', 'NONSSL', false);
				$sentFrom = stripslashes($_POST['send_name']);
				$sentTo = stripslashes($_POST['to_name']);
				if (isset($_POST['message'])){
					$message = stripslashes($_POST['message']);
				}
				
				require_once(DIR_WS_CLASSES . 'email_events.php');
				$email_event = new email_event(GIFT_VOUCHER_SEND_EMAIL);
				$email_event->sendEmail(array(
					'email' => $_POST['email'],
					'name' => $sentTo
				));
			}
			break;
	}
}

$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE'));

$content = CONTENT_GV_SEND;
if (isset($_GET['dialog']) && $_GET['dialog'] == 'true'){
	require(sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/popup.tpl.php');
}else{
	require(sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/main_page.tpl.php');
}

require(DIR_WS_INCLUDES . 'application_bottom.php');
?>