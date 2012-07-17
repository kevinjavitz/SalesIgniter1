<form name="new_order" action="<?php echo itw_app_link(tep_get_all_get_params(array('action')) . 'action=saveOrder');?>" method="post">

<div class="fixedElement" style="position:relative;height:40px;background:#ffffff;border:1px solid #000000;top:0;width:100%;z-index:2;"><?php
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
	$infobox->append($saveButton)->append($estimateButton)->append($ResReportButton)->append($cancelButton);

	EventManager::notify('AdminOrderCreatorAddButton', &$infobox);

	if (isset($_GET['oID'])){
		if(isset($_GET['isEstimate'])){
			$br = htmlBase::newElement('br');
			$infobox->append($br)->append($EmailInput)->append($emailButton);
		}
		$infobox->append($resendButton);
	}

	echo $infobox->draw();

?></div>

<div class="" style="margin:0;padding:0;margin-top:20px;display:block;">
<div id="ui-widget-content-left" style="display:inline-block;margin-right:30px;vertical-align: top;width:370px;">
<div id="accordion">
		<?php
		$beforeInfoTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);

		EventManager::notify('OrderCreatorAddToInfoTable', &$beforeInfoTable, $Editor);
		$beforeInfoHtml = $beforeInfoTable->draw();
		if($beforeInfoHtml != '<table cellpadding="3" cellspacing="0"><thead></thead><tbody></tbody></table>'){
		?>
		<div id="panelLeft_1" class="navPanel">
			<h3>Store/Inventory Center Data</h3>
		<?php
			echo $beforeInfoHtml;
			?>
		</div>
			<?php
		}
	        ?>
<!--<div id="panelLeft_2" class="navPanel">-->
<h3>Search/Update Customer</h3>
	<div>
	<?php
	//echo '<h2><u>' . sysLanguage::get('HEADING_CUSTOMER_INFORMATION') . '</u></h2>';

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
	?>

		<div class="customerSection"><b> <?php echo sysLanguage::get('ENTRY_SEARCH_CUSTOMER');?></b><br><input type="text" name="customer_search" style="width:70%;display:inline-block;"><?php echo htmlBase::newElement('button')->addClass('customerSearchReset')->setText(sysLanguage::get('TEXT_BUTTON_RESET'))->draw();?></div>
	<?php
	}
	?>

<div id="tabsCustomer">
	<ul>
		<li><a href="#tabs-1">Customer Data</a></li>
		<li><a href="#tabs-2">Billing Information</a></li>
		<li><a href="#tabs-3">Shipping Information</a></li>
		<?php
		if($Editor->hasPickup()){
		?>
		<li><a href="#tabs-4">Pickup Information</a></li>
			<?php
		}
	?>
		<li><a href="#tabs-5">Extra Information</a></li>
	</ul>
	<div id="tabs-1">
		<?php
		$addressesCustomerTable = $Editor->editCustomer();
		echo $addressesCustomerTable;
		$customerTable = htmlBase::newElement('table')
				->setCellPadding(3)
				->setCellSpacing(0)
				->addClass('customerTable');

		$inputType = htmlBase::newElement('input')
				->setName('isType')
				->addClass('isType')
				->setType('hidden')
				->val((isset($_GET['isType'])?$_GET['isType']:'walkin'));

		$customerTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Room Number:'),
				array('addCls' => 'main', 'text' => $Editor->editRoomNumber())
			)
		));

		$customerTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => ''),
				array('addCls' => 'main', 'text' => $inputType->draw())
			)
		));

		$customerTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' =>sysLanguage::get('ENTRY_TELEPHONE_NUMBER')),
				array('addCls' => 'main', 'text' => $Editor->editTelephone())
			)
		));


		if(sysConfig::get('EXTENSION_ORDER_CREATOR_HAS_MEMBER_NUMBER') == 'True'){
			$customerTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_MEMBER_NUMBER')),
					array('addCls' => 'main', 'text' => $Editor->editMemberNumber())
				)
			));
		}

		if(sysConfig::get('EXTENSION_ORDER_CREATOR_NEEDS_LICENSE_PASSPORT') == 'True'){
			$customerTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => 'Drivers License:'),
					array('addCls' => 'main', 'text' => $Editor->editDriversLicense())
				)
			));
			$customerTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => 'Passport:'),
					array('addCls' => 'main', 'text' => $Editor->editPassPort())
				)
			));
		}
		$customerTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_EMAIL_ADDRESS')),
				array('addCls' => 'main', 'text' => $Editor->editEmailAddress())
			)
		));

		if (!isset($_GET['oID'])){
			$customerTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main','css' => array(
						'display' =>(sysConfig::get('EXTENSION_ORDER_CREATOR_HIDE_PASSWORD') == 'True')?'none':'block'
					), 'text' => sysLanguage::get('ENTRY_PASSWORD')),
					array('addCls' => 'main','css' => array(
						'display' =>(sysConfig::get('EXTENSION_ORDER_CREATOR_HIDE_PASSWORD') == 'True')?'none':'block'
					), 'text' => '<input type="password" name="account_password"' . ($Editor->getCustomerId() > 0 ? ' disabled="disabled"' : '') . '>')
				)

			));
		}
		echo $customerTable->draw();
		?>
	</div>
	<div id="tabs-2">
		<?php
		$addressesBillingTable = $Editor->editBilling();
		echo $addressesBillingTable;
		?>

	</div>
	<div id="tabs-3">
		<?php
		$addressesShippingTable = $Editor->editShipping();
		echo $addressesShippingTable;
		?>
	</div>
	<?php
	if($Editor->hasPickup()){
?>
	<div id="tabs-4">
		<?php
		$addressesPickupTable = $Editor->editPickup();
		echo $addressesPickupTable;
		?>

	</div>
		<?php
	}
?>
	<div id="tabs-5">
		<?php
		$infoTable = htmlBase::newElement('table')
				->setCellPadding(3)
				->setCellSpacing(0)
				->addClass('addressTable');

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
		echo $infoTable->draw();
		?>

	</div>
</div>

	<?php
echo htmlBase::newElement('button')->addClass('saveAddressButton')->setText(sysLanguage::get('TEXT_BUTTON_UPDATE_CUSTOMER'))->draw();
	/*if(sysConfig::get('EXTENSION_ORDER_CREATOR_SHOW_COPY_ADDRESS_BUTTON') == 'True'){
		echo '<div style="text-align:left">' .
			htmlBase::newElement('div')->css(array('float' => 'left'))->html($Editor->AddressManager->getCopyToButtons())->draw();
		'</div><br>';
	} */
?>
		</div>
<!-- </div> -->


		<?php
		if(sysConfig::get('EXTENSION_ORDER_CREATOR_HIDE_ORDER_STATUS') == 'False'){
			?>

			<?php
			if (isset($_GET['oID'])){
				?>
		<!-- 	<div id="panelLeft_3" class="navPanel"> -->
				<h3>Status History</h3>
					<div>
				<?php
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
				echo 'Comment:<br/>';
				echo tep_draw_textarea_field('comments', 'soft', '60', '5');
				?>
					</div>
		<!--	</div> -->
				<?php
			}
			?>

			<?php
		}
		?>

		<?php
		if(sysConfig::get('EXTENSION_ORDER_CREATOR_SHOW_TRACKING_DATA') == 'True'){
			?>
	<!--	<div id="panelLeft_4" class="navPanel"> -->
			<h3>Tracking</h3>
			<div class="trackingSection">
				<?php
				$tracking = array(
					array(
						'heading' => sysLanguage::get('TABLE_HEADING_USPS_TRACKING'),
						'link' => 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=',
						'data' => array('usps_track_num')
					),
					array(
						'heading' => sysLanguage::get('TABLE_HEADING_USPS_TRACKING'),
						'link' => 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=',
						'data' => array('usps_track_num2')
					),
					array(
						'heading' => sysLanguage::get('TABLE_HEADING_UPS_TRACKING'),
						'link' => 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package&InquiryNumber1=',
						'data' => array('ups_track_num')
					),
					array(
						'heading' => sysLanguage::get('TABLE_HEADING_UPS_TRACKING'),
						'link' => 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package&InquiryNumber1=',
						'data' => array('ups_track_num2')
					),
					array(
						'heading' => sysLanguage::get('TABLE_HEADING_FEDEX_TRACKING'),
						'link' => 'http://www.fedex.com/Tracking?action=track&language=english&cntry_code=us&tracknumbers=',
						'data' => array('fedex_track_num')
					),
					array(
						'heading' => sysLanguage::get('TABLE_HEADING_FEDEX_TRACKING'),
						'link' => 'http://www.fedex.com/Tracking?action=track&language=english&cntry_code=us&tracknumbers=',
						'data' => array('fedex_track_num2')
					),
					array(
						'heading' => sysLanguage::get('TABLE_HEADING_DHL_TRACKING'),
						'link' => 'http://track.dhl-usa.com/atrknav.asp?action=track&language=english&cntry_code=us&ShipmentNumber=',
						'data' => array('dhl_track_num')
					),
					array(
						'heading' => sysLanguage::get('TABLE_HEADING_DHL_TRACKING'),
						'link' => 'http://track.dhl-usa.com/atrknav.asp?action=track&language=english&cntry_code=us&ShipmentNumber=',
						'data' => array('dhl_track_num2')
					)
				);

				$trackingTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
				$orderInfo = $Editor->getOrderInfo();

				foreach($tracking as $tracker){
					$bodyCols = array();
					foreach($tracker['data'] as $fieldName){
						$inputField = htmlBase::newElement('input')
								->setName($fieldName)
								->addClass('trackingInput')
								->attr(array(
							'size' => 30,
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
							'text' => '<b>' . $tracker['heading'] . ':</b> <br/>'.$inputField->draw()
						);
						$bodyCols[] = array(
							'text' => $trackButton->draw(),
							'valign' => 'bottom'
						);
					}
					$trackingTable->addBodyRow(array(
						'columns' => $bodyCols
					));
				}
				echo $trackingTable->draw();
				?>

			</div>
	<!--	</div> -->
			<?php
		}
		?>
</div>
</div>
<div class="ui-widget-content-middle" style="display:inline-block;vertical-align: top;width:70%;">
	<div id="panelCenter_1" class="centralPanel">
		<h3>Edit Products</h3>
		<div class="productSection">
			<?php
				$contents = EventManager::notifyWithReturn('OrderInfoBeforeProductListingEdit', (isset($oID) ? $oID : null));
				if (!empty($contents)){
					foreach($contents as $content){
						echo $content;
					}
				}
				$productsTable = $Editor->editProducts();

				echo $productsTable->draw();
			?>
		</div>
	</div>

	<div id="panelCenter_2" class="centralPanel" style="margin-top:20px;">
		<h3>Edit Totals</h3>

		<div class="totalSection" style="">
		<?php
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

		?>
		</div>
	</div>

	<div id="panelCenter_5" class="centralPanel" style="margin-top:20px;">
		<h3>Order Updates</h3>
		<div class="orderStatus" style="">

			<?php
			$statusDrop = htmlBase::newElement('selectbox')
					->setName('status')
					->selectOptionByValue($Editor->getCurrentStatus(true));
			foreach($orders_statuses as $sInfo){
				$statusDrop->addOption($sInfo['id'], $sInfo['text']);
			}

			echo '<table border="0" cellspacing="0" cellpadding="2">' .
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
					'</table>';
			?>
		</div>
	</div>

	<div id="panelCenter_3" class="centralPanel" style="margin-top:20px;">
		<h3>Edit Payments</h3>
		<div class="paymentSection" style="">
		<?php
		$paymentHistoryTable = $Editor->editPaymentHistory();

		echo $paymentHistoryTable->draw();
		?>
		</div>
	</div>

	</div>
</div>

</form>
<style type="text/css">
	#tabsCustomer{
		width:330px;
	}
	#tabsCustomer.ui-tabs .ui-tabs-nav li{
		width:77px !important;
		white-space: normal !important;
		font-size:11px !important;
		text-align: center !important;
	}

	#tabsCustomer.ui-tabs .ui-tabs-nav li a{
		padding:5px;
	}

	#tabsCustomer.ui-tabs .ui-tabs-panel{
		padding:0px;
	}
	#ui-widget-content-left .ui-accordion .ui-accordion-header {
		padding:5px;
		padding-left:10px;

	}
	#ui-widget-content-left .ui-accordion .ui-accordion-header.ui-state-active{
		background:#DFEFFC url(images/ui-bg_glass_85_dfeffc_1x400.png) 50% 50% repeat-x;
	}
	#ui-widget-content-left .ui-accordion .ui-accordion-header .ui-icon{
		display: none;
	}

</style>

<script type="text/javascript">
	$(document).ready(function (){
		$(".fixedElement").data("top", $(".fixedElement").offset().top);
		$(window).scroll(function(e){
			var $div = $(".fixedElement");
			if ($(window).scrollTop() > $div.data("top")) {
				$div.css({'position': 'fixed', 'top': '0', 'width': '100%'});
			}
			else {
				$div.css({'position': 'static', 'top': 'auto', 'width': '100%'});
			}
		});
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
	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/redmond/jquery-ui.css" type="text/css">