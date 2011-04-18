<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Index
 *
 * @author Stephen
 */

	namespace Applications\OrdersManager\Pages;

	class Create {
		
		public function __construct(){
			
		}
		
		private function buildInfoTable(){
			$infoTable = htmlBase::newElement('table')
				->setCellPadding(3)
				->setCellSpacing(0);
			
			$infoTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_TELEPHONE_NUMBER') . '</b>'),
					array('addCls' => 'main', 'text' => $Editor->editTelephone())
				)
			));
			
			$infoTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ENTRY_EMAIL_ADDRESS') . '</b>'),
					array('addCls' => 'main', 'text' => $Editor->editEmailAddress())
				)
			));
			
			$contents = EventManager::notifyWithReturn('OrderInfoAddBlockEdit', (isset($oID) ? $oID : null));
			if (!empty($contents)){
				foreach($contents as $content){
					$infoTable->addBodyRow(array(
						'columns' => array(
							array('addCls' => 'main', 'colspan' => '2', 'text' => $content)
						)
					));
				}
			}
			return $infoTable;
		}

		private function buildStatusHistoryTable(){
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
						$icon = tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK);
					}else{
						$icon = tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS);
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
			return $historyTable;
		}

		private function buildTrackingAndCommentsTable(){
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
			$orderInfo = $Editor->getOrderInfo();

			foreach($tracking as $tracker){
				$bodyCols = array(
					array('text' => '<b>' . $tracker['heading'] . ':</b> ')
				);
				foreach($tracker['data'] as $fieldName){
					$inputField = htmlBase::newElement('input')
					->setName($fieldName)
					->attr(array(
						'size' => 40,
						'maxlength' => 40
					));

					$trackButton = htmlBase::newElement('button')->setHref($tracker['link'], false, '_blank')->setText('Track');
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
	
			$statusDrop = htmlBase::newElement('selectbox')
			->setName('status')
			->selectOptionByValue($Editor->getCurrentStatus());
			foreach($orders_statuses as $sInfo){
				$statusDrop->addOption($sInfo['id'], $sInfo['text']);
			}

			return htmlBase::newElement('div')
				->html('<div class="main"><b>' . sysLanguage::get('TABLE_HEADING_COMMENTS') . '</b></div>' .
					tep_draw_textarea_field('comments', 'soft', '60', '5') .
					'<br />' .
					$trackingTable->draw() .
					'<br />' .
					'<table border="0" cellspacing="0" cellpadding="2">' .
						'<tr>' .
							'<td class="main"><b>' . sysLanguage::get('ENTRY_STATUS') . '</b> ' . $statusDrop->draw() . '</td>' .
						'</tr>' .
						'<tr>' .
							'<td class="main"><b>' . sysLanguage::get('ENTRY_NOTIFY_CUSTOMER') . '</b> ' . tep_draw_checkbox_field('notify', '', true) . '</td>' .
							'<td class="main"><b>' . sysLanguage::get('ENTRY_NOTIFY_COMMENTS') . '</b> ' . tep_draw_checkbox_field('notify_comments', '', true) . '</td>' .
						'</tr>' .
					'</table>');
		}
		
		public function output(){
			$brEl = htmlBase::newElement('br');
			
			$PageForm = htmlBase::newElement('form')
				->setName('new_order')
				->setAction(itw_app_link(tep_get_all_get_params(array('action')) . 'action=saveOrder'))
				->setMethod('POST');
			
			$saveButton = htmlBase::newElement('button')
				->usePreset('save')
				->setText('Save As Order')
				->setType('submit')
				->setName('saveOrder');

			$saveProfileButton = htmlBase::newElement('button')
				->usePreset('save')
				->setText('Save To Profile')
				->setName('saveProfileButton');
	
			$saveQuoteButton = htmlBase::newElement('button')
				->usePreset('save')
				->setText('Save As Quote')
				->setName('saveQuoteButton');

			$cancelButton = htmlBase::newElement('button')
				->usePreset('cancel')
				->setHref(itw_app_link(null, 'orders', 'default'));
			
			$TopButtonBar = htmlBase::newElement('div')
				->css(array('text-align' => 'right'))
				->append($saveButton)
				->append($saveProfileButton)
				->append($saveQuoteButton)
				->append($cancelButton);

			$BottomButtonBar = htmlBase::newElement('div')
				->css(array('text-align' => 'right'))
				->append($saveButton)
				->append($saveProfileButton)
				->append($saveQuoteButton)
				->append($cancelButton);

			$customerInfoHeading = htmlBase::newElement('h2')
				->html('<u>Customer Information</u>');
			
			$customerSearchBox = htmlBase::newElement('input')
				->setName('customer_search')
				->css(array('width' => '90%'))
				->setLabel('Search For Customer')
				->setLabelPosition('before');
			
			$addressesTable = $Editor->editAddresses();
			$infoTable = $this->buildInfoTable();

			$ProductsHeading = htmlBase::newElement('h2')
				->html('<u>Products</u>');
		
			$productsTable = $Editor->editProducts();

			$OrderTotalsHeading = htmlBase::newElement('h2')
				->html('<u>Order Totals</u>');

			$orderTotalTable = $Editor->editTotals();
			
			$mainContainer = htmlBase::newElement('div')
				->addClass('ui-widget')
				->append($customerInfoHeading)
				->append($customerSearchBox)
				->append($brEl)
				->append($addressesTable)
				->append($brEl)
				->append($infoTable)
				->append($brEl)
				->append($ProductsHeading)
				->append($productsTable)
				->append($brEl)
				->append($OrderTotalsHeading)
				->append($orderTotalTable);

			if ($Editor->hasDebt() === true){
				$Balance = htmlBase::newElement('span')
					->css(array(
						'font-weight' => 'bold',
						'color' => 'red'
					))
					->html($Editor->getBalance());

				$DebtLine = htmlBase::newElement('div')
					->css(array('text-align' => 'right'))
					->append(htmlBase::newElement('b')->html('Unpaid Balance:'))
					->append($Balance);

				$mainContainer->append($DebtLine);
			}

			if ($Editor->hasCredit() === true){
				$Balance = htmlBase::newElement('span')
					->css(array(
						'font-weight' => 'bold',
						'color' => 'green'
					))
					->html($Editor->getBalance());

				$CreditLine = htmlBase::newElement('div')
					->css(array('text-align' => 'right'))
					->append(htmlBase::newElement('b')->html('Overpaid Balance:'))
					->append($Balance);

				$mainContainer->append($CreditLine);
			}

			$PaymentHistoryHeading = htmlBase::newElement('h2')
				->html('<u>Payment History</u>');

			$paymentHistoryTable = $Editor->editPaymentHistory();

			$mainContainer->append($brEl)
				->append($PaymentHistoryHeading)
				->append($paymentHistoryTable);

			if (isset($_GET['oID'])){
				$StatusHistoryHeading = htmlBase::newElement('h2')
					->html('<u>Status History</u>');

				$statusHistoryTable = $this->buildStatusHistoryTable();

				$mainContainer->append($brEl)
					->append($StatusHistoryHeading)
					->append($statusHistoryTable);
			}

			$TrackingAndCommentsHeading = htmlBase::newElement('h2')
				->html('<u>Comments And Tracking</u>');

			$TrackingAndCommentsTable = $this->buildTrackingAndCommentsTable();

			$mainContainer->append($brEl)
				->append($TrackingAndCommentsHeading)
				->append($TrackingAndCommentsTable);

			$PageForm->append($TopButtonBar)
				->append($mainContainer)
				->append($BottomButtonBar);

			return $PageForm->draw();
		}
	}
?>