<noscript><?php
echo tep_get_pages_content(13);
?></noscript>
<div id="pageContentContainer" style="display:none;">
<style>
.pstrength-minchar {
    font-size : 10px;
}
</style>
<?php require(sysConfig::getDirFsCatalog() . 'applications/checkout/javascript/checkout.js.php');?>
<div class="pageHeading"><?php
echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<form name="checkout" id="checkoutForm" action="<?php echo itw_app_link(($onePageCheckout->isMembershipCheckout() === true ? 'checkoutType=rental' : null), 'checkout', 'default', $request_type);?>" method="post">
<?php
if ($onePageCheckout->isMembershipCheckout() === true){
	echo tep_draw_hidden_field('action', 'processRentAccount');
}else{
	echo tep_draw_hidden_field('action', 'process');
}
?><table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
     <td class="main"><?php
     echo '<div class="main"><b>' .
     ($onePageCheckout->isMembershipCheckout() === true ? sysLanguage::get('MEMBERSHIP_OPTIONS') : sysLanguage::get('TABLE_HEADING_PRODUCTS')) .
     '</b></div>';

     echo '<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">';

     if ($onePageCheckout->isMembershipCheckout() === true){
     	include(sysConfig::getDirFsCatalog() . 'applications/checkout/pages_modules/rental_membership.php');
     }else{
     	include(sysConfig::getDirFsCatalog() . 'applications/checkout/pages_modules/cart.php');
     }

     if ($orderTotalModules->modulesAreInstalled()){
     	echo '<div class="orderTotals" style="position:relative;margin-top:1em;text-align:right;">' .
     	'<table cellpadding="2" cellspacing="0" border="0" style="margin-left:auto;">' .
     	$orderTotalModules->output() .
     	'</table>' .
     	'</div>';
     }
     echo '</div>';
     ?></td>
    </tr>
    <tr>
     <td class="main" style="padding-top:5px;"><table cellpadding="0" cellspacing="0" border="0" width="100%">
      <tr>
       <td class="main" width="50%" align="left"><?php
       if (sysConfig::get('MODULE_ORDER_TOTAL_COUPON_STATUS') == 'True'){
       	echo '<table cellpadding="2" cellspacing="0" border="0">
             <tr>
              <td class="main"><b>Have A Coupon?</b></td>
             </tr>
             <tr>
              <td class="main">' . tep_draw_input_field('gv_redeem_code', 'redeem code') . '</td>
              <td class="main">' . itw_template_submit_button('Redeem', 'id="voucherRedeem" name="voucherRedeem"') . '</td>
             </tr>
            </table>';
       }
       ?></td>
       <td class="main" width="50%" align="right"><table cellpadding="2" cellspacing="0" border="0">
        <tr>
         <td class="main"><b>Make Changes?</b></td>
        </tr>
        <tr>
         <td><?php echo itw_template_submit_button('Update Cart', 'name="updateCartButton" id="updateCartButton"');?></td>
        </tr>
       </table></td>
      </tr>
     </table></td>
    </tr>
    <tr>
     <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
    <tr>
     <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
       <td class="main" width="50%" valign="top"><?php
       ob_start();
       include(sysConfig::getDirFsCatalog() . 'applications/checkout/pages_modules/billing_address.php');
       $billingAddress = ob_get_contents();
       ob_end_clean();

       $billingAddress = '<table border="0" width="100%" cellspacing="0" cellpadding="2">
         <tr id="logInRow"' . ($userAccount->isLoggedIn() === true ? ' style="display:none"' : '') . '>
          <td class="main">Already have an account? ' . htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_LOGIN'))->setHref(fixSeoLink(itw_app_link(null, 'account', 'login', 'SSL')))->setId('loginButton')->draw() . '</td>
         </tr>
        </table>' . $billingAddress;

       echo '<div class="main"><b>' . sysLanguage::get('TABLE_HEADING_BILLING_ADDRESS') . '</b></div>';
       echo '<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' . $billingAddress . '</div>';

       ?><table id="changeBillingAddressTable" border="0" width="100%" cellspacing="0" cellpadding="2"<?php echo ($userAccount->isLoggedIn() === true ? '' : ' style="display:none"');?>>
        <tr>
         <td class="main" align="right"><?php echo htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_CHANGE_ADDRESS'))->setHref(itw_app_link(null, 'checkout', 'payment_address', $request_type))->setId('changeBillingAddress')->draw();?></td>
        </tr>
       </table></td>
      <?php
	      if(sysConfig::get('HIDE_SHIP_ADDRESS') == 'false'){
      ?>
       <td class="main" width="50%" valign="top"><?php
       ob_start();
       include(sysConfig::getDirFsCatalog() . 'applications/checkout/pages_modules/shipping_address.php');
       $shippingAddress = ob_get_contents();
       ob_end_clean();

       if ($userAccount->isLoggedIn() === false){
       	$shippingAddress = '<table border="0" width="100%" cellspacing="0" cellpadding="2">
             <tr>
              <td class="main">Different from billing address? <input type="checkbox" name="diffShipping" id="diffShipping" value="1"></td>
             </tr>
            </table>' . $shippingAddress;
       }

       echo '<div class="main"><b>' . sysLanguage::get('TABLE_HEADING_SHIPPING_ADDRESS') . '</b></div>';
       echo '<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' . $shippingAddress . '</div>';
       ?><table id="changeShippingAddressTable" border="0" width="100%" cellspacing="0" cellpadding="2" <?php echo ($userAccount->isLoggedIn() === true ? '' : ' style="display:none"');?>>
        <tr>
         <td class="main" align="right"><?php echo htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_CHANGE_ADDRESS'))->setHref(itw_app_link(null, 'checkout', 'shipping_address', $request_type))->setId('changeShippingAddress')->draw();?></td>
        </tr>
       </table></td>
	 <?php
      }
     ?>
      </tr>
     </table></td>
    </tr>
<?php
    if ($onePageCheckout->onePage['pickupEnabled'] === true){
?>
    <tr>
     <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
    <tr>
     <td><?php
     ob_start();
     include(sysConfig::getDirFsCatalog() . 'applications/checkout/pages_modules/pickup_address.php');
     $pickupAddress = ob_get_contents();
     ob_end_clean();

	  if ($userAccount->isLoggedIn() === false){
        $pickupAddress = '<table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <tr>
                                <td class="main">Different from shipping address? <input type="checkbox" name="diffPickup" id="diffPickup" value="1"></td>
                            </tr>
						</table>' . $pickupAddress;
       }

     echo '<div class="main"><b>' . 'Pickup Address' . '</b></div>';
     echo '<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' . $pickupAddress . '</div>';
     ?></td>
    </tr><tr><td>
		<table id="changePickupAddressTable" border="0" width="100%" cellspacing="0" cellpadding="2" <?php echo ($userAccount->isLoggedIn() === true ? '' : ' style="display:none"');?>>
        <tr>
         <td class="main" align="right"><?php echo htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_CHANGE_ADDRESS'))->setHref(itw_app_link(null, 'checkout', 'pickup_address', $request_type))->setId('changePickupAddress')->draw();?></td>
        </tr>
       </table></td></tr>
<?php
}
?>
    <tr>
     <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
      <tr>
        <td><?php
        echo '<div class="main"><b>' . sysLanguage::get('TABLE_HEADING_PAYMENT_METHOD') . '</b></div>';
        echo '<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;"><div id="noPaymentAddress" class="main noAddress" align="center" style="font-size:15px;' . ($userAccount->isLoggedIn() === true ? 'display:none;' : '') . '">Please fill in your <b>billing address</b> for payment options</div><div id="paymentMethods"' . ($userAccount->isLoggedIn() === false ? ' style="display:none;"' : '') . '>';

        if ($userAccount->isLoggedIn() === true){
        	include(sysConfig::getDirFsCatalog() . 'applications/checkout/pages_modules/payment_method.php');
        }

        echo '</div></div>';
       ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
if ($onePageCheckout->onePage['shippingEnabled'] === true){
	if (OrderShippingModules::modulesAreInstalled() > 0) {
?>
      <tr>
        <td><?php
        $shippingMethod = '';
        if ($userAccount->isLoggedIn() === true){
        	ob_start();
        	include(sysConfig::getDirFsCatalog() . 'applications/checkout/pages_modules/shipping_method.php');
        	$shippingMethod = ob_get_contents();
        	ob_end_clean();
        }

        $shippingMethod = '<div id="noShippingAddress" class="main noAddress" align="center" style="font-size:15px;' . ($userAccount->isLoggedIn() === true ? 'display:none;' : '') . '">Please fill in <b>at least</b> your billing address to get shipping quotes.</div><div id="shippingMethods"' . ($userAccount->isLoggedIn() === false ? ' style="display:none;"' : '') . '>' . $shippingMethod . '</div>';

        echo '<div class="main"><b>' . sysLanguage::get('TABLE_HEADING_SHIPPING_METHOD') . '</b></div>';
        echo '<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' . $shippingMethod . '</div>';
       ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
	}
}

$contents = EventManager::notifyWithReturn('CheckoutAddBlock');
if (!empty($contents)){
	foreach($contents as $content){
		echo '<tr><td>' .
			$content .
		'</td></tr>';
		echo '<tr><td>' . tep_draw_separator('pixel_trans.gif', '100%', '10') . '</td></tr>';
	}
}
?>
     <tr>
        <td><?php
        ob_start();
        include(sysConfig::getDirFsCatalog() . 'applications/checkout/pages_modules/comments.php');
        $commentBox = ob_get_contents();
        ob_end_clean();

        echo '<div class="main"><b>' . sysLanguage::get('TABLE_HEADING_COMMENTS') . '</b></div>';
        echo '<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' . $commentBox . '</div>';
       ?></td>
      </tr>
<?php
    if ($onePageCheckout->isMembershipCheckout() === true){
}else{
?>
      <tr>
       <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
       <td><div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;"><table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
         <td><div class="finalProducts"><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <thead>
           <tr>
            <td class="smallText"><b><?php echo sysLanguage::get('TABLE_HEADING_PRODUCTS_NAME');?></b></td>
            <td class="smallText"><b><?php echo 'Purchase Type';?></b></td>
            <td class="smallText" align="right"><b><?php echo sysLanguage::get('TABLE_HEADING_PRODUCTS_FINAL_PRICE');?></b></td>
           </tr>
          </thead>
          <tbody></tbody>
         </table></div><br />
         <div style="float:right;position: relative; margin-top: 1em; text-align: right;" class="orderTotals"><?php
         if ($orderTotalModules->modulesAreInstalled()){
         	echo '<table cellpadding="2" cellspacing="0" border="0">' .
         	$orderTotalModules->output() .
         	'</table>';
         }
         ?></div></td>
        </tr>
       </table></div></td>
      </tr>
<?php
}
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><div class="ui-widget ui-widget-content ui-corner-all" style="padding:.5em;"><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td class="main" id="checkoutMessage"><?php echo '<b>' . sysLanguage::get('TITLE_CONTINUE_CHECKOUT_PROCEDURE') . '</b><br>' . sysLanguage::get('TEXT_CONTINUE_CHECKOUT_PROCEDURE'); ?></td>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
	        <?php if (sysConfig::get('TERMS_CONDITIONS_SHOPPING_CART') == 'false'){?>
                <td class="main"><?php echo tep_draw_checkbox_field('terms', '1', false) . '&nbsp;<a href="' . itw_app_link('appExt=infoPages', 'show_page', 'conditions') . '" onclick="popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'conditions', 'SSL') . '\',\'800\',\'600\');return false;">' . sysLanguage::get('TEXT_AGREE_TO_TERMS') . '</a>';?></td>
            <?php }else{
	            ?><td class="main" style="display:none;"> <?php echo tep_draw_checkbox_field('terms', '1', true);?></td>
			<?php
              }
            ?>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td class="main" align="right"><?php
            if (sysConfig::get('ONEPAGE_CHECKOUT_LOADING_MESSAGE_METHOD') == 'Default'){
            	echo '<div id="ajaxMessages" style="display:none;"></div>';
            }
            ?><div id="checkoutButtonContainer"><?php
            echo htmlBase::newElement('button')
            ->setType('submit')
            ->setText('Checkout')
            ->setId('checkoutButton')
            ->setName('checkoutButton')
            ->draw();
            ?></div><div id="paymentHiddenFields" style="display:none;"></div></td>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
          </tr>
        </table></div></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%" align="right"><?php echo tep_image(sysConfig::getDirWsCatalog(). sysConfig::get('DIR_WS_IMAGES'). 'checkout_bullet.gif'); ?></td>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
              </tr>
            </table></td>
            <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td align="center" width="25%" class="checkoutBarTo"><?php echo sysLanguage::get('CHECKOUT_BAR_CONFIRMATION'); ?></td>
            <td align="center" width="25%" class="checkoutBarTo"><?php echo sysLanguage::get('CHECKOUT_BAR_FINISHED'); ?></td>
          </tr>
        </table></td>
      </tr>
    </table></form>

<!-- dialogs_bof //-->
<div id="loginBox" title="Log Into My Account" style="display:none;"><table cellpadding="2" cellspacing="0" border="0">
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_EMAIL_ADDRESS');?></td>
  <td><?php echo tep_draw_input_field('email_address');?></td>
 </tr>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_PASSWORD');?></td>
  <td><?php echo tep_draw_password_field('password');?></td>
 </tr>
 <tr>
  <td colspan="2" align="right" class="main"><a href="<?php echo itw_app_link(null, 'account', 'password_forgotten', 'SSL');?>"><?php echo sysLanguage::get('TEXT_PASSWORD_FORGOTTEN');?></a></td>
 </tr>
 <tr>
  <td colspan="2" align="right"><?php echo htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_LOGIN'))->setId('loginWindowSubmit')->draw();?></td>
 </tr>
</table></div>
<div id="addressBook" title="Address Book" style="display:none"></div>
<div id="newAddress" title="New Address" style="display:none"></div>
<!-- dialogs_eof//-->
</div>
<?php if (sysConfig::get('ONEPAGE_CHECKOUT_LOADING_MESSAGE_METHOD') == 'Dialog'){ ?>
<div id="ajaxMessages" style="display:none;text-align:center;" title="Ajax Processing">Loading Please Wait....<br /><br /><span class="ui-ajax-loader ui-ajax-loader-large" style="margin-left:auto;margin-right:auto;"></span><br /><span class="message"></span></div>
<?php } ?>