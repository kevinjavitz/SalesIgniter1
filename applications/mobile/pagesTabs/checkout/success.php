<?php
$QlastOrder = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('o.OrdersTotal ot')
	->where('o.customers_id = ?', (int)$userAccount->getCustomerId())
	->andWhereIn('ot.module_type', array('ot_total', 'total'))
	->orderBy('o.orders_id desc')
	->limit(1)
	->execute();

$LastOrder = $QlastOrder[0];
require(sysConfig::getDirFsCatalog() . 'includes/classes/Order/Base.php');
$Order = new Order($LastOrder->orders_id);

if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True' && sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_EVENT_SUCCESS_PAGE') == 'True'){
	$Reservation = $LastOrder->OrdersProducts[0]->OrdersProductsReservation;
	if (
		$Reservation &&
		$Reservation->count() > 0
	){
		$evInfo = ReservationUtilities::getEvent($Reservation->event_name);
		//$htmlEventLink = '<a href="'.itw_app_link('appExt=payPerRentals&ev_id='.$evInfo['events_id'],'show_event','default').'">View Event Details</a>';
		$htmlEventDetails = '<br/><br/><b>Event Details:</b><br/>' . trim($evInfo->events_details);
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
			$htmlEventGates = '<br/><br/><b>Event Gate:</b><br/>' . trim($Reservation->event_gate);
		}
	}
}

if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_LOS_SUCCESS_PAGE') == 'True'){
	//$htmlViewTerms = '<a href="' . itw_app_link('action=viewTerms&oID='.$Order->getOrderId(), 'account', 'default') . '" onclick="popupWindowTerms(\'' . itw_app_link('action=viewTerms&oID='.$Order->getOrderId(), 'account', 'default', 'SSL') . '\',400,300);return false;">' . 'View Terms and Conditions You Agreed' . '</a>';
	$htmlTermsDetails = $LastOrder->terms;
}
?>
<div class="ui-bar ui-bar-e">
	<h3><?php
		if (isset($message) && $message != ""){
			echo $message;
		}
		elseif (Session::exists('payment_rental') === true) {
			echo sysLanguage::get('HEADING_TITLE_SUCCESS');
		}
		else {
			echo sysLanguage::get('HEADING_TITLE_SUCCESS');
		}
		?></h3>

	<p><?php echo sysLanguage::get('TEXT_THANKS_FOR_SHOPPING_SUCCESS');?></p>
</div>
<p>
	<b><?php echo sprintf(sysLanguage::get('HEADING_ORDER_NUMBER'), $Order->getOrderId()) . ' <small>(' . $Order->getCurrentStatus() . ')</small>'; ?></b>
</p>
<div class="ui-grid-a">
	<div class="ui-block-a"><?php
		echo sysLanguage::get('HEADING_ORDER_DATE') . ' ' .
			$Order->getDatePurchased()->format(sysLanguage::getDateFormat('long'));
		?></div>
	<div class="ui-block-b" style="text-align:right;"><?php
		echo sysLanguage::get('HEADING_ORDER_TOTAL') . ' ' . $currencies->format($Order->getTotal());
		?></div>
</div>
<?php
if ($Order->hasShippingMethod() === true){
	$deliveryAddress = $Order->getFormattedAddress('delivery');
	if (is_null($deliveryAddress) === false){
		?>
	<div>
		<div><b><?php echo sysLanguage::get('HEADING_DELIVERY_ADDRESS'); ?></b></div>
		<div><?php echo $deliveryAddress;?></div>
		<div><b><?php echo sysLanguage::get('HEADING_SHIPPING_METHOD'); ?></b></div>
		<div><?php echo $Order->getShippingMethod(); ?></div>
	</div>
	<?php
	}
}

$pickupAddress = $Order->getFormattedAddress('pickup');
if (is_null($pickupAddress) === false && sysConfig::get('ONEPAGE_CHECKOUT_PICKUP_ADDRESS') == 'true'){
	?>
<div>
	<div><b><?php echo sysLanguage::get('HEADING_PICKUP_ADDRESS'); ?></b></div>
	<div><?php echo $pickupAddress;?></div>
</div>
<?php
}
?>
<br>
<?php
$ProductsTable = htmlBase::newElement('table')
	->setCellSpacing(0)
	->setCellPadding(2)
	->attr('width', '100%')
	->stripeRows('ui-bar-c', 'ui-bar-d');

if ($Order->hasTaxes() === true){
	$ProductsTable->addHeaderRow(array(
		'addCls'  => 'ui-bar-b',
		'columns' => array(
			array(
				'colspan' => 2,
				'text'    => sysLanguage::get('HEADING_PRODUCTS')
			),
			array(
				'align' => 'right',
				'text'  => sysLanguage::get('HEADING_TAX')
			),
			array(
				'align' => 'right',
				'text'  => sysLanguage::get('HEADING_TOTAL')
			)
		)
	));
}
else {
	$ProductsTable->addHeaderRow(array(
		'addCls'  => 'ui-bar-b',
		'columns' => array(
			array(
				'colspan' => 3,
				'text'    => sysLanguage::get('HEADING_PRODUCTS')
			)
		)
	));
}
foreach($Order->getProducts() as $OrderProduct){
	$Columns = array();
	$Columns[] = array(
		'css'  => array('width' => 30),
		'text' => $OrderProduct->getQuantity() . '&nbsp;x'
	);
	$Columns[] = array('text' => $OrderProduct->getNameHtml());
	if ($Order->hasTaxes() === true){
		$Columns[] = array(
			'align' => 'right',
			'text'  => tep_display_tax_value($OrderProduct->getTaxRate()) . '%'
		);
	}
	$Columns[] = array(
		'align' => 'right',
		'text'  => $currencies->format($OrderProduct->getFinalPrice(true, true))
	);

	$ProductsTable->addBodyRow(array(
		'columns' => $Columns
	));
}
echo $ProductsTable->draw();
?>
<br>
<div><?php
	$contents = EventManager::notifyWithReturn('OrderInfoAddBlock', $Order->getOrderId());
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
	?></div>
<br>
<div class="ui-bar ui-bar-b">
	<?php echo sysLanguage::get('HEADING_BILLING_INFORMATION'); ?>
</div>
<div class="ui-body-c">
	<div><b><?php echo sysLanguage::get('HEADING_BILLING_ADDRESS'); ?></b></div>
	<div><?php echo $Order->getFormattedAddress('billing'); ?></div>
</div>
<br>
<div class="ui-bar ui-bar-b">
	<?php echo sysLanguage::get('HEADING_PAYMENT_METHOD'); ?>
</div>
<div class="ui-body-c">
	<?php echo $Order->listPaymentHistory()->draw();?>
</div>
<br>
<div style="float:right;">
	<?php echo $Order->listTotals()->draw();?>
</div>
<br>
<div><?php
	$contents = EventManager::notifyWithReturn('AccountHistoryBeforeShowOrderHistory', $Order->getOrderId());
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
	?></div>
<br>
<div class="ui-bar ui-bar-b">
	<?php echo sysLanguage::get('HEADING_ORDER_HISTORY'); ?>
</div>
<div class="ui-body-c">
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
		<?php
		foreach($Order->getStatusHistory() as $statusEntry){
			echo '              <tr>' . "\n" .
				'                <td class="main" valign="top" width="70">' . $statusEntry['date_added']->format(sysLanguage::getDateFormat('long')) . '</td>' . "\n" .
				'                <td class="main" valign="top" width="70">' . $statusEntry['OrdersStatus']['OrdersStatusDescription'][Session::get('languages_id')]['orders_status_name'] . '</td>' . "\n" .
				'                <td class="main" valign="top">' . (empty($statusEntry['comments']) ? '&nbsp;' : nl2br($statusEntry['comments'])) . '</td>' . "\n" .
				'              </tr>' . "\n";
		}
		?>
	</table>
</div>
<!-- Package Tracking Plus BEGIN -->
<?php
$trackingCompanies = array(
	'usps'  => array(
		'max_numbers' => 2,
		'url'		 => 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum='
	),
	'ups'   => array(
		'max_numbers' => 2,
		'url'		 => 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package&InquiryNumber1='
	),
	'fedex' => array(
		'max_numbers' => 2,
		'url'		 => 'http://www.fedex.com/Tracking?action=track&language=english&cntry_code=us&tracknumbers='
	),
	'dhl'   => array(
		'max_numbers' => 2,
		'url'		 => 'http://track.dhl-usa.com/atrknav.asp?action=track&language=english&cntry_code=us&ShipmentNumber='
	)
);

$trackings = array();
$OrderInfo = $Order->getOrderInfo();
foreach($trackingCompanies as $company => $cInfo){
	for($i = 0; $i < $cInfo['max_numbers']; $i++){
		if ($i == 0){
			$column = $company . '_track_num';
		}
		else {
			$column = $company . '_track_num' . ($i + 1);
		}
		if (array_key_exists($column, $OrderInfo) && !empty($OrderInfo[$column])){
			$trackings[] = array(
				'url'	=> $cInfo['url'],
				'text'   => sysLanguage::get('TEXT_INFO_TRACKING_' . strtoupper($company) . ($i + 1)),
				'number' => $OrderInfo[$column]
			);
		}
	}
}

if (sizeof($trackings) > 0){
	?>
<br>
<div class="ui-bar ui-bar-b">
	<?php echo sysLanguage::get('HEADING_TRACKING'); ?>
</div>
<div class="ui-body-c">
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
		<?php
		for($i = 0, $n = sizeof($trackings); $i < $n; $i++){
			echo '<tr>
                <td class="main" align="left">' . $trackings[$i]['text'] . '</td>
                <td class="main" align="left"><a target="_blank" href="' . $trackings[$i]['url'] . $trackings[$i]['number'] . '">' . $trackings[$i]['number'] . '</a></td>
                <td class="main" align="left">' . htmlBase::newElement('button')
				->setText(sysLanguage::get('TEXT_BUTTON_TRACK_PACKAGE'))
				->setHref($trackings[$i]['url'] . $trackings[$i]['number'])->setHrefTarget('_blank')
				->draw() . '</td>
               </tr>';
		}
		?>
	</table>
</div>
<?php
}
?>
<!-- Package Tracking Plus END -->
<?php
echo '<input type="hidden" name="currentPage" id="currentPage" value="success">';
//echo htmlBase::newElement('a')->html(sysLanguage::get('TEXT_PRINT_ORDER'))->attr('id','printOrder')->draw();

if (isset($htmlEventGates) && !empty($htmlEventGates)){
	echo '<br/>' . $htmlEventGates . '<br>';
}

if (isset($htmlEventDetails) && !empty($htmlEventDetails)){
	echo '<br/>' . $htmlEventDetails . '<br>';
}

if (isset($htmlTermsDetails) && !empty($htmlTermsDetails)){
	echo '<br/>' . $htmlTermsDetails;
}

$contents = EventManager::notifyWithReturn('CheckoutSuccessFinish', $LastOrder->toArray());
if (!empty($contents)){
	foreach($contents as $content){
		echo $content;
	}
}

Session::remove('sendto');
Session::remove('billto');
Session::remove('shipping');
Session::remove('payment');
Session::remove('comments');

if (Session::exists('credit_covers') === true){
	Session::remove('credit_covers');
}

Session::remove('cc_id');
Session::remove('payment_recurring');
Session::remove('cancel_request');
Session::remove('onepage');

$onePageCheckout->setMode('');
if (Session::exists('add_to_queue_product_id')){
	$pID = Session::get('add_to_queue_product_id');
	$attribs = Session::get('add_to_queue_product_attrib');
	Session::remove('add_to_queue_product_id');
	Session::remove('add_to_queue_product_attrib');
	$rentalQueue->addToQueue($pID, $attribs);
	//tep_redirect( itw_app_link(null,'rentals','queue'));
}
?>