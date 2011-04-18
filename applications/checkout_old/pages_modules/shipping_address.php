<div id="shippingAddress"><?php
 $addressBook = $userAccount->plugins['addressBook'];
 if ($userAccount->isLoggedIn() === true){
     echo $addressBook->formatAddress('delivery', true);
 }else{
     $shippingAddress = $addressBook->getAddress('delivery');
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
 <tr>
  <td><table cellpadding="0" cellspacing="0" border="0" width="100%">
   <tr>
    <td class="main" width="50%"><?php echo sysLanguage::get('ENTRY_FIRST_NAME'); ?></td>
    <td class="main" width="50%"><?php echo sysLanguage::get('ENTRY_LAST_NAME'); ?></td>
   </tr>
   <tr>
    <td class="main" width="50%"><?php echo tep_draw_input_field('shipping_firstname', $shippingAddress['entry_firstname'], 'class="required" style="width:80%;float:left;"'); ?></td>
    <td class="main" width="50%"><?php echo tep_draw_input_field('shipping_lastname', $shippingAddress['entry_lastname'], 'class="required" style="width:80%;float:left;"'); ?></td>
   </tr>
  </table></td>
 </tr>
<?php
  if (ACCOUNT_COMPANY == 'true') {
?>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_COMPANY'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_input_field('shipping_company', '', 'style="width:80%;float:left;"'); ?></td>
 </tr>
<?php
  }
?>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_COUNTRY'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_get_country_list('shipping_country', (isset($shippingAddress['entry_country_id']) ? $shippingAddress['entry_country_id'] : ONEPAGE_DEFAULT_COUNTRY), 'class="required" style="width:80%;float:left;"'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_STREET_ADDRESS'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_input_field('shipping_street_address', $shippingAddress['entry_street_address'], 'class="required" style="width:80%;float:left;"'); ?></td>
 </tr>
<?php
  if (ACCOUNT_SUBURB == 'true') {
?>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_SUBURB'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_input_field('shipping_suburb', $shippingAddress['entry_suburb'], 'style="width:80%;float:left;"'); ?></td>
 </tr>
<?php
  }
?>
 <tr>
  <td><table cellpadding="0" cellspacing="0" border="0" width="100%">
   <tr>
    <td class="main" width="50%"><?php echo sysLanguage::get('ENTRY_CITY'); ?></td>
    <td class="main" width="50%"><?php echo sysLanguage::get('ENTRY_POST_CODE'); ?></td>
   </tr>
   <tr>
    <td class="main" width="50%"><?php echo tep_draw_input_field('shipping_city', $shippingAddress['entry_city'], 'class="required" style="width:80%;float:left;"'); ?></td>
    <td class="main" width="50%"><?php echo tep_draw_input_field('shipping_postcode', $shippingAddress['entry_postcode'], 'class="required" style="width:80%;float:left;"'); ?></td>
   </tr>
  </table></td>
 </tr>
<?php
  if (ACCOUNT_STATE == 'true') {
?>
 <tr>
  <td><table cellpadding="0" cellspacing="0" border="0" width="100%">
   <tr>
    <td class="main" width="100%"><?php echo sysLanguage::get('ENTRY_STATE'); ?></td>
   </tr>
   <tr>
    <td class="main" width="100%" id="stateCol_shipping"><?php echo tep_draw_input_field('shipping_state', $shippingAddress['entry_state'], 'class="required" style="width:80%;float:left;"');?></td>
   </tr>
  </table></td>
 </tr>
<?php
  }
?>
</table><?php
 }
?></div>