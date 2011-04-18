<?php
if ($session_started == false) {
		tep_redirect(itw_app_link('appExt=infoPages', 'show_page', 'cookie_usage'));
	}
	
	if ($App->getPageName() != 'login' && $App->getPageName() != 'password_forgotten' && $App->getPageName() != 'create' && $userAccount->isLoggedIn() === false){
		$navigation->set_snapshot();
		tep_redirect(itw_app_link(null, 'account', 'login', 'SSL'));
	}
	
	$appContent = $App->getAppContentFile();
	$addressBook = $userAccount->plugins['addressBook'];

	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');

	$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_DEFAULT'), itw_app_link(null, 'account', 'default', 'SSL'));
	if ($App->getPageName() != 'default' && $App->getPageName() != 'history_info' && defined('NAVBAR_TITLE_' . strtoupper($App->getPageName()))){
		$breadcrumb->add(constant('NAVBAR_TITLE_' . strtoupper($App->getPageName())), itw_app_link(null, 'account', $App->getPageName(), 'SSL'));
	}
	
	if ($App->getPageName() == 'address_book_process'){
		if (isset($_GET['edit']) && is_numeric($_GET['edit'])){
			$addressEntry = $addressBook->getAddress($_GET['edit']);
			if (empty($addressEntry)){
				$messageStack->addSession('pageStack', sysLanguage::get('ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY'), 'error');
				tep_redirect(itw_app_link(null, 'account', 'address_book', 'SSL'));
			}
		}elseif (isset($_GET['delete']) && is_numeric($_GET['delete'])){
			if ($_GET['delete'] == $addressBook->getDefaultAddressId()){
				$messageStack->addSession('pageStack', sysLanguage::get('WARNING_PRIMARY_ADDRESS_DELETION'), 'warning');
				tep_redirect(itw_app_link(null, 'account', 'address_book', 'SSL'));
			}else{
				if (sizeof($addressBook->addresses) < 1){
					$messageStack->addSession('pageStack', sysLanguage::get('ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY'), 'error');
					tep_redirect(itw_app_link(null, 'account', 'address_book', 'SSL'));
				}
			}
		}

		if (!isset($_GET['delete']) && !isset($_GET['edit'])){
			if (sizeof($addressBook->addresses) >= MAX_ADDRESS_BOOK_ENTRIES){
				$messageStack->addSession('pageStack', sysLanguage::get('ERROR_ADDRESS_BOOK_FULL'), 'error');
				tep_redirect(itw_app_link(null, 'account', 'address_book', 'SSL'));
			}
		}
		
		if (isset($_GET['edit'])){
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_ADDRESS_BOOK_PROCESS_MODIFY_ENTRY'), itw_app_link(tep_get_all_get_params(array('app', 'appExt')), 'account', 'address_book_process', 'SSL'));
		}elseif (isset($_GET['delete'])){
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_ADDRESS_BOOK_PROCESS_DELETE_ENTRY'), itw_app_link(tep_get_all_get_params(array('app', 'appExt')), 'account', 'address_book_process', 'SSL'));
		}else{
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_ADDRESS_BOOK_PROCESS_ADD_ENTRY'), itw_app_link(tep_get_all_get_params(array('app', 'appExt')), 'account', 'address_book_process', 'SSL'));
		}
	}elseif ($App->getPageName() == 'history_info'){
		if (!isset($_GET['order_id']) || (isset($_GET['order_id']) && !is_numeric($_GET['order_id']))){
			tep_redirect(itw_app_link(null, 'account', 'history', 'SSL'));
		}
		
		$customer_info_query = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '". (int)$_GET['order_id'] . "'");
		$customer_info = tep_db_fetch_array($customer_info_query);
		if ($customer_info['customers_id'] != $userAccount->getCustomerId()){
			tep_redirect(itw_app_link(null, 'account', 'history', 'SSL'));
		}
		
		require(sysConfig::getDirFsCatalog() . 'includes/classes/Order/Base.php');
		$Order = new Order($_GET['order_id']);
		
		$breadcrumb->add(sprintf(sysLanguage::get('NAVBAR_TITLE_HISTORY'), $_GET['order_id']), itw_app_link(null, 'account', 'history_info', 'SSL'));
		$breadcrumb->add(sprintf(sysLanguage::get('NAVBAR_TITLE_HISTORY_INFO'), $_GET['order_id']), itw_app_link('order_id=' . $_GET['order_id'], 'account', 'history_info', 'SSL'));
	}elseif ($App->getPageName() == 'newsletters'){
		$newsletter_query = tep_db_query("select customers_newsletter from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$userAccount->getCustomerId() . "'");
		$newsletter = tep_db_fetch_array($newsletter_query);
	}elseif ($App->getPageName() == 'logoff'){
		$userAccount->processLogOut();
		Session::remove('userAccount');
		$userAccount = new rentalStoreUser();
		$userAccount->loadPlugins();
		Session::set('userAccount', $userAccount);
	}elseif ($App->getPageName() == 'rental_issues'){
		function issues_getCustomerInfo($cID){
			$Qcustomer = tep_db_query('select customers_firstname, customers_lastname, customers_email_address from ' . TABLE_CUSTOMERS . ' where customers_id = "' . $cID . '"');
			return tep_db_fetch_array($Qcustomer);
		}

		function issues_getQueueInfo($cID, $pID){
			$Qproduct = tep_db_query('select r.customers_queue_id, r.customers_queue_id, r.products_id, p.products_name, date_format(r.shipment_date,"%m/%d/%Y") as rented_date from ' . TABLE_RENTED_QUEUE . ' r, ' . TABLE_PRODUCTS_DESCRIPTION . ' p where p.products_id = r.products_id and customers_id = "' . $cID . '" and  r.products_id = "' . $pID . '"');
			return tep_db_fetch_array($Qproduct);
		}

		function issues_getBookingInfo($cID, $pID){
			$Qproduct = tep_db_query('select r.rental_booking_id, r.orders_id, r.products_id, p.products_name, date_format(r.date_shipped,"%m/%d/%Y") as rented_date from rental_bookings r, ' . TABLE_PRODUCTS_DESCRIPTION . ' p where p.products_id = r.products_id and r.customers_id = "' . $cID . '" and  r.products_id = "' . $pID . '"');
			return tep_db_fetch_array($Qproduct);
		}

		function issues_getIssueInfo($iID){
			$Qissue = tep_db_query('select * from ' . TABLE_RENTAL_ISSUES . ' where issue_id = "' . $iID . '"');
			return tep_db_fetch_array($Qissue);
		}

		function issues_getEmailText($type, $eInfo){
			switch($type){
				case 'newIssue':
					$text = '%s, ' . "\n" . '%s has reported the following issue on his order/rental for: %s placed on %s:' . "\n" . '%s' . "\n" . 'You may reply at this url: ' . itw_admin_app_link('action=edit&tID='.$eInfo[5],'rental_queue','issues');
					break;
				case 'replyIssue':
					$text = '%s, ' . "\n" . '%s has responded to the issue on his order/rental for: %s placed on %s:' . "\n" . '%s' . "\n" . 'You may reply at this url: ' . itw_admin_app_link('action=edit&tID='.$eInfo[5],'rental_queue','issues');
					break;
			}
			array_unshift($eInfo, $text);
			return call_user_func_array('sprintf', $eInfo);
		}
	}
?>