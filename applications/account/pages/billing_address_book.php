<?php
	ob_start();
?>
<div class="ui-widget ui-widget-content ui-corner-all" style="padding:.5em;"><?php
		$membership =& $userAccount->plugins['membership'];
	$card_number = '';
	$cardExpDate = '';
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

	$ccTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0);

	$ccTable->addBodyRow(array(
			'columns' => array(
				array('text' => sysLanguage::get('CREDIT_CARD_NUMBER')),
				array('text' => tep_draw_input_field('cc_number') . '&nbsp;' .$card_number. (tep_not_null(sysLanguage::get('CREDIT_CARD_NUMBER_TEXT')) ? '<span class="inputRequirement">' . sysLanguage::get('CREDIT_CARD_NUMBER_TEXT') . '</span>': ''))
			)
		));

	$ccTable->addBodyRow(array(
			'columns' => array(
				array('text' => sysLanguage::get('CREDIT_CARD_EXPIRY')),
				array('text' => tep_draw_pull_down_menu('cc_expires_month', $expires_month,$arr_date[0]) . '&nbsp;' . tep_draw_pull_down_menu('cc_expires_year', $expires_year,$year) . '&nbsp;' . (tep_not_null(sysLanguage::get('CREDIT_CARD_EXPIRY_TEXT')) ? '<span class="inputRequirement">' . sysLanguage::get('CREDIT_CARD_EXPIRY_TEXT') . '</span>': ''))
			)
		));

	$ccTable->addBodyRow(array(
			'columns' => array(
				array('text' => sysLanguage::get('CREDIT_CARD_CVV')),
				array('text' => tep_draw_input_field('cc_cvv', $cardCvvNumber, 'size="5" maxlength="4"'))
			)
		));
	echo $ccTable->draw();
		include('address_book_details.php');
?></div>
<?php
	if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
		$pageButtons = htmlBase::newElement('button')
		->usePreset('back')
		->setHref(itw_app_link(null, 'account', 'address_book', 'SSL'))
		->draw();
	
		$pageButtons .= htmlBase::newElement('button')
		->usePreset('save')
		->setType('submit')
		->draw();
    } else {
		$back_link = itw_app_link(null, 'account', 'address_book', 'SSL');
		
		$pageButtons = htmlBase::newElement('button')
		->usePreset('back')
		->setHref($back_link)
		->draw();
	
		$pageButtons .= tep_draw_hidden_field('action', 'process') . htmlBase::newElement('button')
		->usePreset('continue')
		->setType('submit')
		->draw();
    }
  
  $pageContents = ob_get_contents();
  ob_end_clean();

	if (isset($_GET['edit'])){
		$pageTitle = sysLanguage::get('HEADING_TITLE_ADDRESS_BOOK_PROCESS_MODIFY_ENTRY');
	}elseif (isset($_GET['delete'])){
		$pageTitle = sysLanguage::get('HEADING_TITLE_ADDRESS_BOOK_PROCESS_DELETE_ENTRY');
	}else{
		$pageTitle = sysLanguage::get('HEADING_TITLE_ADDRESS_BOOK_PROCESS_ADD_ENTRY');
	}
	
	if (!isset($_GET['delete'])){
		$pageContent->set('pageForm', array(
			'name' => 'addressbook',
			'action' => itw_app_link('action=saveBilling' . (isset($_GET['edit']) ? '&edit=' . $_GET['edit'] : ''), 'account', 'billing_address_book', 'SSL'),
			'method' => 'post'
		));
	}
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
?>