<table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
$totalModules = OrderPaymentModules::getTotalEnabled();
if ($totalModules > 1) {
?>
 <tr>
  <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
  <td class="main" width="50%" valign="top"><?php echo sysLanguage::get('TEXT_SELECT_PAYMENT_METHOD'); ?></td>
  <td class="main" width="50%" valign="top" align="right"></td>
  <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
 </tr>
<?php
} else {
?>
 <tr>
  <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
  <td class="main" width="100%" colspan="2">111111111<?php echo sysLanguage::get('TEXT_ENTER_PAYMENT_INFORMATION'); ?></td>
  <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
 </tr>
<?php
}

$radio_buttons = 0;
foreach(OrderPaymentModules::getAllModules() as $Module){
	$mInfo = $Module->onSelect();
	
	$code = $Module->getCode();
	$title = $Module->getTitle();
	$fields = null;
	if (isset($mInfo['fields'])){
		$fields = $mInfo['fields'];
	}
?>
 <tr>
  <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
  <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr class="moduleRow paymentRow<?php echo (isset($onePageCheckout->onePage['info']['payment']['id']) && $code == $onePageCheckout->onePage['info']['payment']['id'] ? ' moduleRowSelected' : '');?>">
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
    <td class="main" width="10"><?php
    if ($totalModules > 1) {
    	echo tep_draw_radio_field('payment', $code, (isset($onePageCheckout->onePage['info']['payment']['id']) && $mInfo['id'] == $onePageCheckout->onePage['info']['payment']['id']));
    } else {
    	echo tep_draw_hidden_field('payment', $code);
    }
    ?></td>
    <td class="main" width="100%"><b><?php echo $title; ?></b></td>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
   </tr>
<?php
if (isset($mInfo['error'])) {
?>
   <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
    <td class="main" colspan="2"><?php echo $mInfo['error']; ?></td>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
   </tr>
<?php
} elseif (is_null($fields) === false && (isset($onePageCheckout->onePage['info']['payment']['id']) && $code == $onePageCheckout->onePage['info']['payment']['id'])){
?>
   <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
    <td><table border="0" cellspacing="0" cellpadding="2" class="paymentFields">
<?php
	foreach($fields as $fInfo){
?>
     <tr>
      <td class="main" width="150px"><?php echo $fInfo['title']; ?></td>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
      <td class="main" width="400px"><?php echo $fInfo['field']; ?></td>
     </tr>
<?php
	}
?>
    </table></td>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
   </tr>
<?php
}
?>
  </table></td>
  <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
 </tr>
<?php
$radio_buttons++;
}
// Start - CREDIT CLASS Gift Voucher Contribution
if ($userAccount->isLoggedIn() && $orderTotalModules->moduleIsEnabled('ot_gv')) {
	$gvModule = $orderTotalModules->getModule('ot_gv');
	if ($gvModule->user_has_gv_account($userAccount->getCustomerId())){
		echo $orderTotalModules->sub_credit_selection();
	}
}
// End - CREDIT CLASS Gift Voucher Contribution
?>
</table>