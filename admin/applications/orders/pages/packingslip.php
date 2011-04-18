<?php

?>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td class="pageHeading"><?php echo nl2br(sysConfig::get('STORE_NAME_ADDRESS')); ?></td>
        <td class="pageHeading" align="right"><?php echo '<img src="'.sysConfig::getDirWsCatalog(). 'images/'. sysConfig::get('STORE_LOGO').'"/>'; ?></td>
      </tr>
    </table></td>
  </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
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

            </table><br></td>
          </tr>
        </table></td>
      </tr>

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
                </table><br/><div style="float:right;"><?php
            	echo $Order->listTotals()->draw();
            ?></div></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
		 <tr>
                <td class="main"><b><?php echo sysLanguage::get('HEADING_PAYMENT_METHOD'); ?></b></td>
              </tr>
              <tr>
                <td class="main"><?php echo $Order->listPaymentHistory()->draw(); ?></td>
              </tr>
		</table>

<?php
	$contents = EventManager::notifyWithReturn('OrderShowExtraPackingData', &$order);
		foreach($contents as $content){
			echo $content;
		}
?>