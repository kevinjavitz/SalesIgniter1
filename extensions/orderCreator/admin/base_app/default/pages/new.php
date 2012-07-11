<style>
.ui-datepicker-group { margin:.3em; }
.ui-datepicker-header { padding:0;text-align:center; }
.ui-datepicker-header span { margin: .5em; }
.ui-datepicker .ui-datepicker-prev, .ui-datepicker .ui-datepicker-next { top: 0px; }
.ui-datepicker-status { margin:.5em;text-align:center;font-weight:bold; }
/*#datePicker { font-size: 1.25em; }
#datePicker .ui-datepicker-calendar td { font-size: 1.25em; }
#datePicker .ui-datepicker-start_date { background: #00FF00; }*/
.ui-datepicker-shipping-day-hover, .ui-datepicker-shipping-day-hover-info { background: #F7C8D3; }
#datePicker .ui-state-active { background:#CACEE6; }
</style>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<form name="new_order" action="<?php echo itw_app_link(tep_get_all_get_params(array('action')) . 'action=saveOrder');?>" method="post">
<div style="text-align:right"><?php
	$saveButton = htmlBase::newElement('button')->usePreset('save')
	->setType('submit')->addClass('saveOrder')->setName('saveOrder');
	$estimateButton = htmlBase::newElement('button')->usePreset('save')
	->setType('submit')->addClass('saveOrder')->setName('estimateOrder')
	->attr('toolTip', 'This saves the enquiry details <br>but does NOT reserve any bikes.<br>You can change this later');

	$resendButton = htmlBase::newElement('button')->usePreset('save')
	->setName('resendEmail')->setId('resendEmail')->setText('Resend Confirmation');

	$emailButton = htmlBase::newElement('button')->usePreset('save')
	->setType('submit')->setName('emailEstimate')->setId('emailEstimate');
	$EmailInput = htmlBase::newElement('input')
	->setName('emailInput')
	->setId('emailInput')
	->setLabel('Email:')
	->setLabelPosition('before');
	
	if (isset($_GET['oID'])){
		if(!isset($_GET['isEstimate'])){
			$saveButton->setText(sysLanguage::get('TEXT_BUTTON_UPDATE_ORDER'));
			$estimateButton->setText(sysLanguage::get('TEXT_BUTTON_SAVE_AS_ESTIMATE'));
			$estimateButton->attr('disabled','disabled');
		}else{
			$saveButton->setText(sysLanguage::get('TEXT_BUTTON_SAVE_AS_ORDER'));
			$estimateButton->setText(sysLanguage::get('TEXT_BUTTON_UPDATE_ESTIMATE'));
			$emailButton->setText(sysLanguage::get('TEXT_BUTTON_SEND_ESTIMATE'));
		}

	}else{
		$saveButton->setText(sysLanguage::get('TEXT_BUTTON_SAVE_AS_ORDER'));
		$estimateButton->setText(sysLanguage::get('TEXT_BUTTON_SAVE_AS_ESTIMATE'));
	}


	$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
	->setHref(itw_app_link(null, 'orders', 'default'));

	$ResReportButton = htmlBase::newElement('button')
	->addClass('resReports')
	->setText('Reservation Reports')
	->setHref(itw_app_link(null, 'orders', 'default'));

	$infobox = htmlBase::newElement('div');
	$infobox/*->append($saveButton)->append($estimateButton)*/->append($ResReportButton)->append($cancelButton);

	EventManager::notify('AdminOrderCreatorAddButton', &$infobox);

	if (isset($_GET['oID'])){
		if(isset($_GET['isEstimate'])){
			$br = htmlBase::newElement('br');
			$infobox->append($br)->append($EmailInput)->append($emailButton);
		}
	}

	echo $infobox->draw();

?></div>
<br />
<span style="font-size:2em;color:red;line-height:1em;">To add products to order, first enter customer details and click update customer</span>
<div class="ui-widget">
<?php
	echo '<h2><u>' . sysLanguage::get('HEADING_CUSTOMER_INFORMATION') . '</u></h2>';
	if (!isset($_GET['oID'])){

		if(sysConfig::get('EXTENSION_ORDER_CREATOR_CHOOSE_CUSTOMER_TYPE') == 'True'){
			$hotelGuest = htmlBase::newElement('button')
			->addClass('hotelGuest')
			->setText('Hotel Guest');
			$walkin = htmlBase::newElement('button')
			->addClass('walkin')
			->setText('Walk In');

			echo '<div class="chooseType">'.$hotelGuest->draw().' '.$walkin->draw().'</div>';

		}

		echo '<div class="customerSection"><b>' . sysLanguage::get('ENTRY_SEARCH_CUSTOMER') . '</b><br><input type="text" name="customer_search" style="width:70%;">' . htmlBase::newElement('button')->addClass('customerSearchReset')->setText(sysLanguage::get('TEXT_BUTTON_RESET'))->draw() . '<br>';
	}
	$beforeInfoTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
	EventManager::notify('OrderCreatorAddToInfoTable', &$beforeInfoTable, $Editor);
	/* customer_info --BEGIN-- */
	$addressesTable = $Editor->editAddresses();

	$infoTable = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->addClass('addressTable');
	$inputType = htmlBase::newElement('input')
	->setName('isType')
	->addClass('isType')
	->setType('hidden')
	->val((isset($_GET['isType'])?$_GET['isType']:'walkin'));

	$infoTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => '<b>Room Number:</b>'),
					array('addCls' => 'main', 'text' => $Editor->editRoomNumber())
				)
	));

	$infoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => ''),
			array('addCls' => 'main', 'text' => $inputType->draw())
		)
	));

	$infoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_TELEPHONE_NUMBER') . '</b>'),
			array('addCls' => 'main', 'text' => $Editor->editTelephone())
		)
	));


	if(sysConfig::get('EXTENSION_ORDER_CREATOR_HAS_MEMBER_NUMBER') == 'True'){
		$infoTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_MEMBER_NUMBER') . '</b>'),
				array('addCls' => 'main', 'text' => $Editor->editMemberNumber())
			)
		));
	}

	if(sysConfig::get('EXTENSION_ORDER_CREATOR_NEEDS_LICENSE_PASSPORT') == 'True'){
		$infoTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => '<b>Drivers License:</b>'),
					array('addCls' => 'main', 'text' => $Editor->editDriversLicense())
				)
			));
		$infoTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => '<b>Passport: </b>'),
					array('addCls' => 'main', 'text' => $Editor->editPassPort())
				)
			));
	}
	$infoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_EMAIL_ADDRESS') . '</b>'),
			array('addCls' => 'main', 'text' => $Editor->editEmailAddress())
		)
	));

		if (!isset($_GET['oID'])){
			$infoTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main','css' => array(
						'display' =>(sysConfig::get('EXTENSION_ORDER_CREATOR_HIDE_PASSWORD') == 'True')?'none':'block'
					), 'text' => '<b>' . sysLanguage::get('ENTRY_PASSWORD') . '</b>'),
					array('addCls' => 'main','css' => array(
						'display' =>(sysConfig::get('EXTENSION_ORDER_CREATOR_HIDE_PASSWORD') == 'True')?'none':'block'
					), 'text' => '<input type="password" name="account_password"' . ($Editor->getCustomerId() > 0 ? ' disabled="disabled"' : '') . '>')
				)

			));
		}


	EventManager::notify('OrderCreatorAddToInfoTableAfter', &$infoTable, $Editor);

	$contents = EventManager::notifyWithReturn('OrderInfoAddBlockEdit', (isset($oID) ? $oID : null));
	if (!empty($contents)){
		foreach($contents as $content){
			$infoTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'colspan' => '2', 'text' => $content),
				)
			));
		}
   }
    echo $beforeInfoTable->draw();
	echo $addressesTable;
	if(sysConfig::get('EXTENSION_ORDER_CREATOR_SHOW_COPY_ADDRESS_BUTTON') == 'True'){
		echo '<div style="text-align:left">' .
			htmlBase::newElement('div')->css(array('float' => 'left'))->html($Editor->AddressManager->getCopyToButtons())->draw();
		'</div><br>';
	}
	echo '<div style="clear:both;"></div>';
	echo $infoTable->draw() .
	htmlBase::newElement('button')->addClass('saveAddressButton')->setText(sysLanguage::get('TEXT_BUTTON_UPDATE_CUSTOMER'))->draw();
	/* customer_info --END-- */

	echo '</div><br /><div class="productSection"><h2><u>' . sysLanguage::get('HEADING_PRODUCTS') . '</u></h2>';

	$contents = EventManager::notifyWithReturn('OrderInfoBeforeProductListingEdit', (isset($oID) ? $oID : null));
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
	/* products --BEGIN-- */
	$productsTable = $Editor->editProducts();

	echo $productsTable->draw();
	/* products --END-- */

	echo '</div><br /><div class="totalSection"><h2><u>' . sysLanguage::get('HEADING_ORDER_TOTALS') . '</u></h2>';

	/* products --BEGIN-- */
	$orderTotalTable = $Editor->editTotals();

	echo $orderTotalTable->draw();

	if ($Editor->hasDebt() === true){
		echo '<div style="text-align:right"><b>' . sysLanguage::get('TEXT_UNPAID_BALANCE') . '</b> <span style="font-weight:bold;color:red;">' . $Editor->getBalance('debt') . '</span></div>';
	}

	if ($Editor->hasPendingPayments() === true){
		echo '<div style="text-align:right"><b>' . sysLanguage::get('TEXT_PENDING_PAYMENTS') . '</b> <span style="font-weight:bold;color:blue;">' . $Editor->getBalance('pending') . '</span></div>';
	}

	if ($Editor->hasCredit() === true){
		echo '<div style="text-align:right"><b>' . sysLanguage::get('TEXT_OVERPAID_BALANCE') . '</b> <span style="font-weight:bold;color:green;">' . $Editor->getBalance('credit') . '</span></div>';
	}
	/* products --END-- */

	echo '</div><br /><div class="paymentSection"><h2><u>' . sysLanguage::get('HEADING_PAYMENT_HISTORY') . '</u></h2>';

	/* payment_info --BEGIN-- */
	$paymentHistoryTable = $Editor->editPaymentHistory();
	
	echo $paymentHistoryTable->draw() . '</div>';

	/* payment_info --END-- */

	if(sysConfig::get('EXTENSION_ORDER_CREATOR_HIDE_ORDER_STATUS') == 'False'){
		/* history --BEGIN-- */
		if (isset($_GET['oID'])){
			echo '<br /><h2><u>' . sysLanguage::get('HEADING_STATUS_HISTORY') . '</u></h2>';

		$historyTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0)->css('width', '100%');
	
		$historyTable->addHeaderRow(array(
			'columns' => array(
				array('addCls' => 'main ui-widget-header', 'align' => 'center', 'text' => sysLanguage::get('TABLE_HEADING_DATE_ADDED')),
				array('addCls' => 'main ui-widget-header', 'css' => array('border-left' => 'none'), 'align' => 'center', 'text' => sysLanguage::get('TABLE_HEADING_CUSTOMER_NOTIFIED')),
				array('addCls' => 'main ui-widget-header', 'css' => array('border-left' => 'none'), 'align' => 'center', 'text' => sysLanguage::get('TABLE_HEADING_STATUS')),
				array('addCls' => 'main ui-widget-header', 'css' => array('border-left' => 'none'), 'align' => 'center', 'text' => sysLanguage::get('TABLE_HEADING_COMMENTS'))
			)
		));

			if ($Editor->hasStatusHistory()){
				foreach($Editor->getStatusHistory() as $history){
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
			echo $historyTable->draw();
		}
		/* history --END-- */
	}

	echo '<br /><br /><div class="commentSection"><h2><u>' . sysLanguage::get('HEADING_COMMENTS_AND_TRACKING') . '</u></h2>';

	if(sysConfig::get('EXTENSION_ORDER_CREATOR_SHOW_TRACKING_DATA') == 'True'){
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
				'heading' => sysLanguage::get('TABLE_HEADING_FEDEX_TRACKING').':<\br><a href="" id="popShipRush" title="ShipRush">'.sysLanguage::get('TABLE_HEADING_FEDEX_SHIPRUSH').'</a>',
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
		$orderInfo = $Editor->getOrderInfo();

		foreach($tracking as $tracker){
			$bodyCols = array(
				array('text' => '<b>' . $tracker['heading'] . ':</b> ')
			);
			foreach($tracker['data'] as $fieldName){
				$inputField = htmlBase::newElement('input')
				->setName($fieldName)
                ->addClass('trackingInput')
				->attr(array(
					'size' => 40,
					'maxlength' => 40
				));

				$trackButton = htmlBase::newElement('button')->addClass('trackingButton')->setHref($tracker['link'], false, '_blank')->setText('Track');
				if (array_key_exists($fieldName, $orderInfo)){
					$inputField->setValue($orderInfo[$fieldName]);
					$trackButton->attr('data-track_number', $orderInfo[$fieldName]);
				}else{
					$trackButton->disable();
				}

				$bodyCols[] = array(
					'text' => $inputField->draw()
				);
				$bodyCols[] = array(
					'text' => $trackButton->draw()
				);
			}
			$trackingTable->addBodyRow(array(
				'columns' => $bodyCols
			));
		}
	}

		$statusDrop = htmlBase::newElement('selectbox')
		->setName('status')
		->selectOptionByValue($Editor->getCurrentStatus(true));
		foreach($orders_statuses as $sInfo){
			$statusDrop->addOption($sInfo['id'], $sInfo['text']);
		}

	
	echo '<div class="main"><b>' . sysLanguage::get('TABLE_HEADING_COMMENTS') . '</b></div>' .
	tep_draw_textarea_field('comments', 'soft', '60', '5') . 
	'<br />' ;
	if(sysConfig::get('EXTENSION_ORDER_CREATOR_SHOW_TRACKING_DATA') == 'True'){
		echo $trackingTable->draw();
	}

	echo '<br />' .
	'<table border="0" cellspacing="0" cellpadding="2">' . 
	'<tr>' . 
	'<td><table border="0" cellspacing="0" cellpadding="2">' .
	'<tr'.((sysConfig::get('EXTENSION_ORDER_CREATOR_HIDE_ORDER_STATUS') == 'False')?'':' style="display:none"').'>' .
	'<td class="main"><b>' . sysLanguage::get('ENTRY_STATUS') . '</b> ' . $statusDrop->draw() . '</td>' .
	'</tr>' . 
	'<tr>' . 
	'<td class="main"><b>' . sysLanguage::get('ENTRY_NOTIFY_CUSTOMER') . '</b> ' . tep_draw_checkbox_field('notify', '', (sysConfig::get('EXTENSION_ORDER_CREATOR_NOTIFY_CUSTOMER_DEFAULT') == 'True'?true:false)) . '</td>' .
	'<td class="main"><b>' . sysLanguage::get('ENTRY_NOTIFY_COMMENTS') . '</b> ' . tep_draw_checkbox_field('notify_comments', '', (sysConfig::get('EXTENSION_ORDER_CREATOR_NOTIFY_CUSTOMER_DEFAULT') == 'True'?true:false)) . '</td>' .
	'</tr>' . 
	'</table></td>' . 
	'</tr>' . 
	'</table></div>';
	/* comments --END-- */
?>
</div>
<br />
<div style="text-align:right"><?php
	echo $saveButton->draw() . $estimateButton->draw() . $resendButton->draw() . $cancelButton->draw() . '<br>';
?></div>
</form>

<script type="text/javascript">
	$(document).ready(function (){
	<?php
		if(sysConfig::get('EXTENSION_ORDER_CREATOR_CHOOSE_CUSTOMER_TYPE') == 'True' && !isset($_GET['oID']) && (!isset($_GET['isType']))){
	?>
		$('.customerSection').hide();
		$('.hotelGuest').click(function(){
			$('.customerSection').show();
			$('input[name$="[entry_street_address]"]').parent().parent().hide();
			$('input[name$="[entry_suburb]"]').parent().parent().hide();
			$('input[name$="[entry_city]"]').parent().parent().hide();
			$('input[name$="[entry_postcode]"]').parent().parent().hide();
			$('input[name$="[entry_state]"]').parent().parent().hide();
			$('select[name$="[entry_state]"]').parent().parent().hide();
			$('select[name$="[entry_country]"]').parent().parent().hide();
			$('input[name$="room_number"]').parent().parent().show();
			//$('input[name$="telephone"]').parent().parent().hide();
			$('input[name$="drivers_license"]').parent().parent().hide();
			$('input[name$="passport"]').parent().parent().hide();
			//$('input[name$="email"]').parent().parent().hide();
			$('input[name$="password"]').parent().parent().hide();
			$('.isType').val('hotelGuest');
		});
		$('.walkin').click(function(){
			$('.customerSection').show();
			$('input[name$="[entry_street_address]"]').parent().parent().show();
			$('input[name$="[entry_suburb]"]').parent().parent().show();
			$('input[name$="[entry_city]"]').parent().parent().show();
			$('input[name$="[entry_postcode]"]').parent().parent().show();
			$('input[name$="[entry_state]"]').parent().parent().show();
			$('select[name$="[entry_state]"]').parent().parent().show();
			$('select[name$="[entry_country]"]').parent().parent().show();
			$('input[name$="room_number"]').parent().parent().hide();
			//$('input[name$="telephone"]').parent().parent().show();
			$('input[name$="drivers_license"]').parent().parent().show();
			$('input[name$="passport"]').parent().parent().show();
			//$('input[name$="email"]').parent().parent().show();
			$('input[name$="password"]').parent().parent().show();
			$('.isType').val('walkin');
		});
	<?php
		}else{
		?>
		$('.chooseType').hide();
	<?php
		if($Editor->getRoomNumber() != '' || (isset($_GET['isType']) && $_GET['isType'] == 'hotelGuest')){
			?>
			$('.customerSection').show();
			$('input[name$="[entry_street_address]"]').parent().parent().hide();
			$('input[name$="[entry_suburb]"]').parent().parent().hide();
			$('input[name$="[entry_city]"]').parent().parent().hide();
			$('input[name$="[entry_postcode]"]').parent().parent().hide();
			$('input[name$="[entry_state]"]').parent().parent().hide();
			$('select[name$="[entry_state]"]').parent().parent().hide();
			$('select[name$="[entry_country]"]').parent().parent().hide();
			$('input[name$="room_number"]').parent().parent().show();
			//$('input[name$="telephone"]').parent().parent().hide();
			$('input[name$="drivers_license"]').parent().parent().hide();
			$('input[name$="passport"]').parent().parent().hide();
			//$('input[name$="email"]').parent().parent().hide();
			$('input[name$="password"]').parent().parent().hide();
		<?php
		}else{
			?>
			$('.customerSection').show();
			$('input[name$="[entry_street_address]"]').parent().parent().show();
			$('input[name$="[entry_suburb]"]').parent().parent().show();
			$('input[name$="[entry_city]"]').parent().parent().show();
			$('input[name$="[entry_postcode]"]').parent().parent().show();
			$('input[name$="[entry_state]"]').parent().parent().show();
			$('select[name$="[entry_state]"]').parent().parent().show();
			$('select[name$="[entry_country]"]').parent().parent().show();
			$('input[name$="room_number"]').parent().parent().hide();
			//$('input[name$="telephone"]').parent().parent().show();
			$('input[name$="drivers_license"]').parent().parent().show();
			$('input[name$="passport"]').parent().parent().show();
			//$('input[name$="email"]').parent().parent().show();
			$('input[name$="password"]').parent().parent().show();
		<?php
		}
	}
	?>
	});
</script>