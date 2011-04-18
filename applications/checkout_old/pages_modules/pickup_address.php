<div id="pickupAddress"><?php
 $addressBook = $userAccount->plugins['addressBook'];
 if ($userAccount->isLoggedIn() === true){
     echo $addressBook->formatAddress('pickup', true);
 }else{
     $pickupAddress = $addressBook->getAddress('pickup');
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
 <tr>
  <td><table cellpadding="0" cellspacing="0" border="0" width="100%">
   <tr>
    <td class="main" width="50%"><?php echo sysLanguage::get('ENTRY_FIRST_NAME'); ?></td>
    <td class="main" width="50%"><?php echo sysLanguage::get('ENTRY_LAST_NAME'); ?></td>
   </tr>
   <tr>
    <td class="main" width="50%"><?php echo tep_draw_input_field('pickup_firstname', $pickupAddress['entry_firstname'], 'class="required" style="width:80%;float:left;"'); ?></td>
    <td class="main" width="50%"><?php echo tep_draw_input_field('pickup_lastname', $pickupAddress['entry_lastname'], 'class="required" style="width:80%;float:left;"'); ?></td>
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
  <td class="main"><?php echo tep_draw_input_field('pickup_company', '', 'style="width:80%;float:left;"'); ?></td>
 </tr>
<?php
  }
?>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_COUNTRY'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_get_country_list('pickup_country', (isset($pickupAddress['entry_country_id']) ? $pickupAddress['entry_country_id'] : ONEPAGE_DEFAULT_COUNTRY), 'class="required" style="width:80%;float:left;"'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_STREET_ADDRESS'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_input_field('pickup_street_address', $pickupAddress['entry_street_address'], 'class="required" style="width:80%;float:left;"'); ?></td>
 </tr>
<?php
  if (ACCOUNT_SUBURB == 'true') {
?>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_SUBURB'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_input_field('pickup_suburb', $pickupAddress['entry_suburb'], 'style="width:80%;float:left;"'); ?></td>
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
    <td class="main" width="50%"><?php echo tep_draw_input_field('pickup_city', $pickupAddress['entry_city'], 'class="required" style="width:80%;float:left;"'); ?></td>
    <td class="main" width="50%"><?php echo tep_draw_input_field('pickup_postcode', $pickupAddress['entry_postcode'], 'class="required" style="width:80%;float:left;"'); ?></td>
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
    <td class="main" width="100%" id="stateCol_pickup"><?php echo tep_draw_input_field('pickup_state', $pickupAddress['entry_state'], 'class="required" style="width:80%;float:left;"');?></td>
   </tr>
  </table></td>
 </tr>
<?php
  }
?>
</table>
 <?php
 }
?>
</div>