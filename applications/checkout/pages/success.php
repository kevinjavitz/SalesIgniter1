<?php
$isAjax = (isset($_GET['rType']) && $_GET['rType'] == 'ajax');

	$QlastOrder = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsReservation ops')
			->leftJoin('o.OrdersTotal ot')
			->where('o.customers_id = ?', (int)$userAccount->getCustomerId())
			->andWhereIn('ot.module_type', array('ot_total','total'))
			->orderBy('o.orders_id desc')
			->limit(1)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    require(sysConfig::getDirFsCatalog() . 'includes/classes/Order/Base.php');
	$Order = new Order($QlastOrder[0]['orders_id']);

	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True' && sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_EVENT_SUCCESS_PAGE') == 'True'){
		if (
			isset($QlastOrder[0]['OrdersProducts'][0]['OrdersProductsReservation']) && 
			isset($QlastOrder[0]['OrdersProducts'][0]['OrdersProductsReservation'][0])
		){
			$evInfo = ReservationUtilities::getEvent($QlastOrder[0]['OrdersProducts'][0]['OrdersProductsReservation'][0]['event_name']);
			//$htmlEventLink = '<a href="'.itw_app_link('appExt=payPerRentals&ev_id='.$evInfo['events_id'],'show_event','default').'">View Event Details</a>';
			$htmlEventDetails = '<br/><br/><b>Event Details:</b><br/>' .  trim($evInfo['events_details']);
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
				$htmlEventGates = '<br/><br/><b>Event Gate:</b><br/>' .  trim($QlastOrder[0]['OrdersProducts'][0]['OrdersProductsReservation'][0]['event_gate']);
			}
		}
	}

	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_LOS_SUCCESS_PAGE') == 'True'){
		//$htmlViewTerms = '<a href="' . itw_app_link('action=viewTerms&oID='.$QlastOrder[0]['orders_id'], 'account', 'default') . '" onclick="popupWindowTerms(\'' . itw_app_link('action=viewTerms&oID='.$QlastOrder[0]['orders_id'], 'account', 'default', 'SSL') . '\',400,300);return false;">' . 'View Terms and Conditions You Agreed' . '</a>';
		$htmlTermsDetails = $QlastOrder[0]['terms'];
	}
if ($isAjax === false){
	ob_start();
}
?>
<div class="ui-widget">
	<div class="ui-widget-content ui-corner-all">
		<div class="ui-widget-header ui-corner-all">
			<span class="ui-widget-header-text"><?php
				if (isset($message) && $message != ""){
					echo $message;
				}elseif (Session::exists('payment_rental') === true){
					echo sysLanguage::get('HEADING_TITLE_SUCCESS');
				}else{
					echo sysLanguage::get('HEADING_TITLE_SUCCESS');
				}
			?></span>
		</div>
		<div class="ui-widget-text"><?php
			if (!isset($message) && Session::exists('payment_rental') === false){
				echo sysLanguage::get('TEXT_THANKS_FOR_SHOPPING_SUCCESS') . '<br>';
			}else{
				echo sysLanguage::get('TEXT_THANKS_FOR_SHOPPING_SUCCESS') . '<br>';
			}
		?>
		
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main" colspan="2"><b><?php echo sprintf(sysLanguage::get('HEADING_ORDER_NUMBER'),$QlastOrder[0]['orders_id']) . ' <small>(' . $Order->getCurrentStatus() . ')</small>'; ?></b></td>
          </tr>
          <tr>
            <td class="smallText"><?php echo sysLanguage::get('HEADING_ORDER_DATE') . ' ' . tep_date_long($Order->getDatePurchased()); ?></td>
            <td class="smallText" align="right"><?php echo sysLanguage::get('HEADING_ORDER_TOTAL') . ' ' . $currencies->format($Order->getTotal()); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
<?php
if ($Order->hasShippingMethod() === true) {
	$deliveryAddress = $Order->getFormattedAddress('delivery');
	if (is_null($deliveryAddress) === false) {
?>
            <td width="30%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><b><?php echo sysLanguage::get('HEADING_DELIVERY_ADDRESS'); ?></b></td>
              </tr>
              <tr>
                <td class="main"><?php
 	                echo $deliveryAddress;
                ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo sysLanguage::get('HEADING_SHIPPING_METHOD'); ?></b></td>
              </tr>
              <tr>
                <td class="main"><?php echo $Order->getShippingMethod(); ?></td>
              </tr>
            </table></td>
<?php
	}
}
?>
	<?php

	$pickupAddress = $Order->getFormattedAddress('pickup');
	if (is_null($pickupAddress) === false && sysConfig::get('ONEPAGE_CHECKOUT_PICKUP_ADDRESS') == 'true') {
?>          </tr>
			<tr>
				<td width="30%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
				  <tr>
					<td class="main"><b><?php echo sysLanguage::get('HEADING_PICKUP_ADDRESS'); ?></b></td>
				  </tr>
				  <tr>
					<td class="main"><?php
						echo $pickupAddress;
					?></td>
				  </tr>
				</table></td>
			</tr>
			<tr>
<?php
}
?>
            <td width="<?php echo ($Order->hasShippingMethod() === true ? '70%' : '100%'); ?>" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
if ($Order->hasTaxes() === true) {
?>
                  <tr>
                    <td class="main" colspan="2"><b><?php echo sysLanguage::get('HEADING_PRODUCTS'); ?></b></td>
                    <td class="smallText" align="right"><b><?php echo sysLanguage::get('HEADING_TAX'); ?></b></td>
                    <td class="smallText" align="right"><b><?php echo sysLanguage::get('HEADING_TOTAL'); ?></b></td>
                  </tr>
<?php
} else {
?>
                  <tr>
                    <td class="main" colspan="3"><b><?php echo sysLanguage::get('HEADING_PRODUCTS'); ?></b></td>
                  </tr>
<?php
}

foreach($Order->getProducts() as $OrderProduct){
	echo '          <tr>' . "\n" .
	'            <td class="main" align="right" valign="top" width="30">' . $OrderProduct->getQuantity() . '&nbsp;x</td>' . "\n" .
	'            <td class="main" valign="top">' . $OrderProduct->getNameHtml();

	echo '</td>' . "\n";

	if ($Order->hasTaxes() === true) {
		echo '            <td class="main" valign="top" align="right">' . tep_display_tax_value($OrderProduct->getTaxRate()) . '%</td>' . "\n";
	}

	echo '            <td class="main" align="right" valign="top">' . $currencies->format($OrderProduct->getFinalPrice(true, true)) . '</td>' . "\n" .
	'          </tr>' . "\n";
}
?>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td>
      <?php
$contents = EventManager::notifyWithReturn('OrderInfoAddBlock',$QlastOrder[0]['orders_id']);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
      ?>
      </td>
      </tr>
      <tr>
        <td class="main"><b><?php echo sysLanguage::get('HEADING_BILLING_INFORMATION'); ?></b></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><b><?php echo sysLanguage::get('HEADING_BILLING_ADDRESS'); ?></b></td>
              </tr>
              <tr>
                <td class="main"><?php
	                echo $Order->getFormattedAddress('billing');
                ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo sysLanguage::get('HEADING_PAYMENT_METHOD'); ?></b></td>
              </tr>
              <tr>
                <td class="main"><?php echo $Order->listPaymentHistory()->draw(); ?></td>
              </tr>
            </table><br><div style="float:right;"><?php
            	echo $Order->listTotals()->draw();
            ?></div></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td>
 <?php
    $contents = EventManager::notifyWithReturn('AccountHistoryBeforeShowOrderHistory',$QlastOrder[0]['orders_id']);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
 ?>
        </td>
      </tr>
      <tr>
        <td class="main"><b><?php echo sysLanguage::get('HEADING_ORDER_HISTORY'); ?></b></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
foreach($Order->getStatusHistory() as $statusEntry){
	echo '              <tr>' . "\n" .
	'                <td class="main" valign="top" width="70">' . tep_date_short($statusEntry['date_added']) . '</td>' . "\n" .
	'                <td class="main" valign="top" width="70">' . $statusEntry['OrdersStatus']['OrdersStatusDescription'][Session::get('languages_id')]['orders_status_name'] . '</td>' . "\n" .
	'                <td class="main" valign="top">' . (empty($statusEntry['comments']) ? '&nbsp;' : nl2br($statusEntry['comments'])) . '</td>' . "\n" .
	'              </tr>' . "\n";
}
?>
            </table></td>
          </tr>
        </table></td>
      </tr>
<!-- Package Tracking Plus BEGIN -->
<?php
$trackingCompanies = array(
'usps' => array(
'max_numbers' => 2,
'url'         => 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum='
),
'ups' => array(
'max_numbers' => 2,
'url'         => 'http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package&InquiryNumber1='
),
'fedex' => array(
'max_numbers' => 2,
'url'         => 'http://www.fedex.com/Tracking?action=track&language=english&cntry_code=us&tracknumbers='
),
'dhl' => array(
'max_numbers' => 2,
'url'         => 'http://track.dhl-usa.com/atrknav.asp?action=track&language=english&cntry_code=us&ShipmentNumber='
)
);

$trackings = array();
$OrderInfo = $Order->getOrderInfo();
foreach($trackingCompanies as $company => $cInfo){
	for($i=0; $i<$cInfo['max_numbers']; $i++){
		if ($i == 0){
			$column = $company . '_track_num';
		}else{
			$column = $company . '_track_num' . ($i+1);
		}
		if (array_key_exists($column, $OrderInfo) && !empty($OrderInfo[$column])){
			$trackings[] = array(
			'url'    => $cInfo['url'],
			'text'   => sysLanguage::get('TEXT_INFO_TRACKING_' . strtoupper($company) . ($i+1)),
			'number' => $OrderInfo[$column]
			);
		}
	}
}

if (sizeof($trackings) > 0){
?>
    <tr>
     <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
    <tr>
     <td class="main"><b><?php echo sysLanguage::get('HEADING_TRACKING'); ?></b></td>
    </tr>
    <tr>
     <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
    <tr>
     <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
      <tr class="infoBoxContents">
       <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
for($i=0, $n=sizeof($trackings); $i<$n; $i++){
	echo '<tr>
                <td class="main" align="left">' . $trackings[$i]['text'] . '</td>
                <td class="main" align="left"><a target="_blank" href="' . $trackings[$i]['url'] . $trackings[$i]['number'] . '">' . $trackings[$i]['number'] . '</a></td>
                <td class="main" align="left">' . htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_TRACK_PACKAGE'))->setHref($trackings[$i]['url'] . $trackings[$i]['number'])->setHrefTarget('_blank')->draw() . '</td>
               </tr>';
}
?>
       </table></td>
      </tr>
     </table></td>
    </tr>
    <tr>
     <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
<?php
}
?>
<!-- Package Tracking Plus END -->
    </table>
    		<?php
				echo '<input type="hidden" name="currentPage" id="currentPage" value="success">';
				echo htmlBase::newElement('a')->html(sysLanguage::get('TEXT_PRINT_ORDER'))->attr('id','printOrder')->draw();

				if (isset($htmlEventGates) && !empty($htmlEventGates)){
					echo '<br/>' . $htmlEventGates . '<br>';
				}

				if (isset($htmlEventDetails) && !empty($htmlEventDetails)){
					echo '<br/>' . $htmlEventDetails . '<br>';
				}

				if (isset($htmlTermsDetails) && !empty($htmlTermsDetails)){
					echo '<br/>' . $htmlTermsDetails;
				}

			?>
		</div>
	</div>
	<div class="ui-widget-content ui-widget-footer-box ui-corner-all"><?php
	?></div>
</div>
<?php
	$contents = EventManager::notifyWithReturn('CheckoutSuccessFinish',$QlastOrder[0]);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}

if ($isAjax === false){
	$pageContents = ob_get_contents();
	ob_end_clean();

	$pageContent->set('pageContent', $pageContents);
}

$ShoppingCart->emptyCart(true);

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
	if(Session::exists('add_to_queue_product_id')){
		$pID = Session::get('add_to_queue_product_id');
		$attribs = Session::get('add_to_queue_product_attrib');
		Session::remove('add_to_queue_product_id');
		Session::remove('add_to_queue_product_attrib');
		$rentalQueue->addToQueue($pID, $attribs);
		//tep_redirect( itw_app_link(null,'rentals','queue'));
	}
?>
<div class="" style="display:none;">
	<iframe src="<?php echo itw_app_link(null,'account','success_outside');?>"></iframe>
</div>