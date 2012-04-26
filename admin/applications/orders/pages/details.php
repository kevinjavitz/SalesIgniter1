<div class="pageHeading" data-order_id="<?php echo $oID;?>"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<div style="text-align:right"><?php
	$invoiceButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_INVOICE'))
	->setHref(itw_app_link('oID=' . $oID, 'orders', 'invoice'));

	$packingSlipButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_PACKINGSLIP'))
	->setHref(itw_app_link('oID=' . $oID, 'orders', 'packingslip'));

	$resendButton = htmlBase::newElement('button')->usePreset('save')
	->setName('resendEmail')->setId('resendEmail')->setText('Resend Confirmation');


	$backButton = htmlBase::newElement('button')->usePreset('back')
	->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, 'default'));

	$infobox = htmlBase::newElement('div');

	$infobox->append($invoiceButton);
	$infobox->append($resendButton);
	if(sysConfig::get('SHOW_PACKING_SLIP_BUTTONS') == 'true'){
		$infobox->append($packingSlipButton);
	}
	$infobox->append($backButton);

	EventManager::notify('AdminOrderDetailsAddButton', $oID, &$infobox);

	echo $infobox->draw(). '<br>';


?></div>
<br />
<?php
	$tabsObj = htmlBase::newElement('tabs')->setId('tabs')	
	->addTabHeader('tab_customer_info', array('text' => 'Customer Info'))
	->addTabHeader('tab_products',      array('text' => 'Products'))
	->addTabHeader('tab_payment_info',  array('text' => 'Payment Info'))
	->addTabHeader('tab_history',       array('text' => 'History'))
	->addTabHeader('tab_comments',      array('text' => 'Comments/Tracking'));
	
	/* Tab: tab_customer_info --BEGIN-- */
	$addressesTable = $Order->listAddresses();

	$infoTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
	$infoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_TELEPHONE_NUMBER') . '</b>'),
			array('addCls' => 'main', 'text' => $Order->getTelephone())
		)
	));
    if(sysConfig::get('SHOW_IP_ADDRESS_ORDERS_DETAILS') == 'true'){
		$infoTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_IPADDRESS') . '</b>'),
				array('addCls' => 'main', 'text' => $Order->getIPAddress())
			)
		));
    }
    $oEmail = $Order->getEmailAddress();
	if(strpos($oEmail, '@') === false){
		$oEmail = 'N/A';
	}
	$infoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_EMAIL_ADDRESS') . '</b>'),
			array('addCls' => 'main', 'text' => '<a href="mailto:' . $oEmail . '"><u>' . $oEmail . '</u></a>')
		)
	));

	$contents = EventManager::notifyWithReturn('OrderInfoAddBlock', $oID);
	if (!empty($contents)){
		foreach($contents as $content){
			$infoTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'colspan' => '2', 'text' => $content),
				)
			));
		}
	}
	
	$tabsObj->addTabPage('tab_customer_info', array('text' => $addressesTable . '<br />' . $infoTable->draw()));
	
	$contents = EventManager::notifyWithReturn('OrderShowExtraShippingInfo', &$order, &$tabsObj);
	foreach($contents as $content){
		echo $content;
	}
	$contents = EventManager::notifyWithReturn('OrderShowExtraPaymentInfo', &$order, &$tabsObj);
	foreach($contents as $content){
		echo $content;
	}
	/* Tab: tab_customer_info --END-- */
	
	/* Tab: tab_products --BEGIN-- */
	$productsTable = $Order->listProducts();
	$orderTotalTable = $Order->listTotals();

	$productsTable->addBodyRow(array(
		'columns' => array(
			array('colspan' => 9, 'align' => 'right', 'text' => $orderTotalTable)
		)
	));
	
	$tabsObj->addTabPage('tab_products', array('text' => $productsTable));
	/* Tab: tab_products --END-- */
	
	/* Tab: tab_payment_info --BEGIN-- */
	$paymentHistoryTable = $Order->listPaymentHistory();
	
	$tabsObj->addTabPage('tab_payment_info', array('text' => $paymentHistoryTable));
	/* Tab: tab_payment_info --END-- */
	
	/* Tab: tab_history --BEGIN-- */
	$historyTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0)->css('width', '100%');
	
	$historyTable->addHeaderRow(array(
		'columns' => array(
			array('addCls' => 'main ui-widget-header', 'align' => 'center', 'text' => sysLanguage::get('TABLE_HEADING_DATE_ADDED')),
			array('addCls' => 'main ui-widget-header', 'css' => array('border-left' => 'none'), 'align' => 'center', 'text' => sysLanguage::get('TABLE_HEADING_CUSTOMER_NOTIFIED')),
			array('addCls' => 'main ui-widget-header', 'css' => array('border-left' => 'none'), 'align' => 'center', 'text' => sysLanguage::get('TABLE_HEADING_STATUS')),
			array('addCls' => 'main ui-widget-header', 'css' => array('border-left' => 'none'), 'align' => 'center', 'text' => sysLanguage::get('TABLE_HEADING_COMMENTS'))
		)
	));

	if ($Order->hasStatusHistory()){
		foreach($Order->getStatusHistory() as $history){
			if ($history['customer_notified'] == '1'){
				$icon = '<img src="images/icons/tick.gif"/>';
			}else{
				$icon = '<img src="images/icons/cross.gif"/>';
			}
			
			$historyTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 'none'), 'align' => 'center', 'text' => tep_datetime_short($history['date_added'])),
					array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 'none', 'border-left' => 'none'), 'align' => 'center', 'text' => $icon),
					array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 'none', 'border-left' => 'none'), 'text' => $history['OrdersStatus']['OrdersStatusDescription'][Session::get('languages_id')]['orders_status_name']),
					array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 'none', 'border-left' => 'none'), 'text' => nl2br(stripslashes($history['comments']))),
				)
			));
		}
	}else{
		$historyTable->addBodyRow(array(
			'columns' => array(
				array('align' => 'center', 'colspan' => 5, 'text' => sysLanguage::get('TEXT_NO_ORDER_HISTORY'))
			)
		));
	}
	$tabsObj->addTabPage('tab_history', array('text' => $historyTable));
	/* Tab: tab_history --END-- */
	
	/* Tab: tab_comments --BEGIN-- */
	$tracking = array(
		array(
			'heading' => sysLanguage::get('TABLE_HEADING_USPS_TRACKING'),
			'link' => 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=',
			'data' => array('usps_track_num', 'usps_track_num2')
		),
		array(
			'heading' => sysLanguage::get('TABLE_HEADING_UPS_TRACKING'),
			'link' => 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package&InquiryNumber1=',
			'data' => array('ups_track_num', 'ups_track_num2')
		),
		array(
			'heading' => sysLanguage::get('TABLE_HEADING_FEDEX_TRACKING'),
			'link' => 'http://www.fedex.com/Tracking?action=track&language=english&cntry_code=us&tracknumbers=',
			'data' => array('fedex_track_num', 'fedex_track_num2')
		),
		array(
			'heading' => sysLanguage::get('TABLE_HEADING_DHL_TRACKING'),
			'link' => 'http://track.dhl-usa.com/atrknav.asp?action=track&language=english&cntry_code=us&ShipmentNumber=',
			'data' => array('dhl_track_num', 'dhl_track_num2')
		)
	);
	
	$trackingTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
	$orderInfo = $Order->getOrderInfo();

	foreach($tracking as $tracker){
		$bodyCols = array(
			array('text' => '<b>' . $tracker['heading'] . ':</b> ')
		);
		foreach($tracker['data'] as $fieldName){
			$trackNum = $orderInfo[$fieldName];
			$bodyCols[] = array(
				'text' => tep_draw_input_field($fieldName, $trackNum, 'size="40" maxlength="40"')
			);
			$bodyCols[] = array(
				'text' => htmlBase::newElement('button')->setHref($tracker['link'] . $trackNum, false, '_blank')->setText('Track')
			);
		}
		$trackingTable->addBodyRow(array(
			'columns' => $bodyCols
		));
	}
	
	$tabContent = '<div class="main"><b>' . sysLanguage::get('TABLE_HEADING_COMMENTS') . '</b></div>' . 
	'<form name="status" action="' . itw_app_link(tep_get_all_get_params(array('action')) . 'action=updateOrder') . '" method="post">' . 
	tep_draw_textarea_field('comments', 'hard', '60', '5') .
	'<br />';
	EventManager::notify('OrderDetailsTabPaneInsideComments', &$orderInfo, &$tabContent);
	$tabContent .= $trackingTable->draw() .
	'<br />' . 
	'<table border="0" cellspacing="0" cellpadding="2">' . 
	'<tr>' . 
	'<td><table border="0" cellspacing="0" cellpadding="2">' . 
	'<tr>' . 
	'<td class="main"><b>' . sysLanguage::get('ENTRY_STATUS') . '</b> ' . tep_draw_pull_down_menu('status', $orders_statuses, $Order->getCurrentStatus(true)) . '</td>' .
	'</tr>' . 
	'<tr>' . 
	'<td class="main"><b>' . sysLanguage::get('ENTRY_NOTIFY_CUSTOMER') . '</b> ' . tep_draw_checkbox_field('notify', '', (sysConfig::get('CUSTOMER_CHANGE_SEND_NOTIFICATION_EMAIL_DEFAULT') == 'true'?true:false)) . '</td>' .
	'<td class="main"><b>' . sysLanguage::get('ENTRY_NOTIFY_COMMENTS') . '</b> ' . tep_draw_checkbox_field('notify_comments', '', true) . '</td>' . 
	'</tr>' . 
	'</table></td>' . 
	'<td valign="top">' . htmlBase::newElement('button')->usePreset('save')->setText('Update')->setType('submit')->draw() . '</td>' . 
	'</tr>' . 
	'</table>' . 
	'</form>';
	$tabsObj->addTabPage('tab_comments', array('text' => $tabContent));
	/* Tab: tab_comments --END-- */
	
	EventManager::notify('OrderDetailsTabPaneBeforeDraw', &$order, &$tabsObj);
	
	echo $tabsObj->draw();
?>
<br />
<div style="text-align:right"><?php
	echo $invoiceButton->draw();
	echo $resendButton->draw();
	if(sysConfig::get('SHOW_PACKING_SLIP_BUTTONS') == 'true'){
		echo $packingSlipButton->draw();
	}
	echo $backButton->draw();
?></div>