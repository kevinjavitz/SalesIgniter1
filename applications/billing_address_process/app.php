<?php
	$appContent = $App->getAppContentFile();

	$addressBook =& $userAccount->plugins['addressBook'];
	
	if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
		$addressEntry = $addressBook->getAddress($_GET['edit']);
		$membership =& $userAccount->plugins['membership'];
		if (empty($addressEntry)) {
			$messageStack->addSession('pageStack', sysLanguage::get('ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY'), 'error');
			tep_redirect(itw_app_link(null, 'account', 'default', 'SSL'));
		}

		############################### Update Credit Card Info Start ###################################
		$cardInfo = $membership->getCreditCardInfo();
		if (!empty($cardInfo['cardNumEnc'])){
			$cardExpDate = cc_decrypt($cardInfo['expDateEnc']);
			if (!empty($cardInfo['cardCvvEnc'])){
				$cardCvvNumber = cc_decrypt($cardInfo['cardCvvEnc']);
			}

			$card_number = parseCC($cardInfo['cardNumEnc']);
			for ($i=1; $i<13; $i++){
				$expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
			}

			$today = getdate();
			for ($i=$today['year']; $i < $today['year']+10; $i++){
				$expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
			}

			$year = substr($cardExpDate,-2);
			if ($year != null){
				$arr_date = explode($year, $cardExpDate);
				//echo date("F Y", mktime(0, 0, 0, $arr_date[0], 1, "20".$year));
			}
		}
		############################### Update Credit Card Info End ###################################
	}
	$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_1'), itw_app_link(null, 'account', 'default', 'SSL'));
	$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_2'), itw_app_link(null, 'account', 'default', 'SSL'));
	$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_MODIFY_ENTRY'), itw_app_link('edit=' . $_GET['edit'], 'billing_address_process', 'default', 'SSL'));
?>