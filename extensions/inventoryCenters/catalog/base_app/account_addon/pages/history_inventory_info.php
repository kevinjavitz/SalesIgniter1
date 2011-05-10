<?php
ob_start();
?>
<div class="ui-widget ui-widget-content ui-corner-all" style="padding:.5em;">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main" colspan="2"><b><?php echo sprintf(sysLanguage::get('HEADING_ORDER_NUMBER'), $_GET['order_id']) . ' <small>(' . $Order->getCurrentStatus() . ')</small>'; ?></b></td>
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
$contents = EventManager::notifyWithReturn('OrderInfoAddBlock', $_GET['order_id']);
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
    $contents = EventManager::notifyWithReturn('AccountHistoryBeforeShowOrderHistory', $_GET['order_id']);
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
</div>
<div class="ui-widget ui-widget-content ui-corner-all" style="margin-top:1em;padding:.5em;">
 <table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
   <td><?php echo htmlBase::newElement('button')->usePreset('back')->setHref(itw_app_link(tep_get_all_get_params(array('order_id')), 'account', 'history', 'SSL'))->draw(); ?></td>
  </tr>
 </table>
</div>
	<?php

	$pageContents = ob_get_contents();
	ob_end_clean();
	$contentHeading = sysLanguage::get('HEADING_TITLE_HISTORY_INFO');
	$pageTitle = stripslashes($contentHeading);

	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
?>