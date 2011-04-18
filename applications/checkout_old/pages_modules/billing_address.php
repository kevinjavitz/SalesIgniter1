<div id="billingAddress"><?php
 $addressBook = $userAccount->plugins['addressBook'];
 if ($userAccount->isLoggedIn() === true){
     echo $addressBook->formatAddress('billing', true);
 }else{
     $billingAddress = $addressBook->getAddress('billing');
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (ACCOUNT_GENDER == 'true') {
      $gender = $billingAddress['entry_gender'];
    if (isset($gender)) {
      $male = ($gender == 'm') ? true : false;
      $female = ($gender == 'f') ? true : false;
    } else {
      $male = false;
      $female = false;
    }
?>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_GENDER'); ?><br><?php echo tep_draw_radio_field('billing_gender', 'm', $male) . '&nbsp;&nbsp;' . MALE . '&nbsp;&nbsp;' . tep_draw_radio_field('billing_gender', 'f', $female) . '&nbsp;&nbsp;' . FEMALE; ?></td>
 </tr>
<?php
  }
?>
 <tr>
  <td><table cellpadding="0" cellspacing="0" border="0" width="100%">
   <tr>
    <td class="main" width="50%"><?php echo sysLanguage::get('ENTRY_FIRST_NAME'); ?></td>
    <td class="main" width="50%"><?php echo sysLanguage::get('ENTRY_LAST_NAME'); ?></td>
   </tr>
   <tr>
    <td class="main" width="50%"><?php echo tep_draw_input_field('billing_firstname', $billingAddress['entry_firstname'], 'class="required" style="width:75%;float:left;"'); ?></td>
    <td class="main" width="50%"><?php echo tep_draw_input_field('billing_lastname', $billingAddress['entry_lastname'], 'class="required" style="width:75%;float:left;"'); ?></td>
   </tr>
  </table></td>
 </tr>
<?php
  if (ACCOUNT_DOB == 'true') {
?>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_DATE_OF_BIRTH'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_input_field('billing_dob', $onePageCheckout->onePage['info']['dob'], 'style="width:80%;float:left;"'); ?></td>
 </tr>
<?php
  }

  if ($_SESSION['userAccount']->isLoggedIn() === false){
?>
 <tr id="newAccountEmail">
  <td class="main"><?php echo sysLanguage::get('ENTRY_EMAIL_ADDRESS'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_input_field('billing_email_address', $onePageCheckout->onePage['info']['email_address'], 'class="required" style="width:80%;float:left;"'); ?></td>
 </tr>
<?php      
  }
  if (ACCOUNT_COMPANY == 'true') {
?>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_COMPANY'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_input_field('billing_company', $billingAddress['entry_company'], 'style="width:80%;float:left;"'); ?></td>
 </tr>
<?php
  }
?>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_COUNTRY'); ?></td>
 </tr>
 <tr>
  <td class="main">
  <?php
  if($onePageCheckout->isMembershipCheckout() === true){
	  if(RENTAL_DEFAULT_COUNTRY_ENABLED == 'false'){
        echo tep_get_country_list('billing_country', (isset($billingAddress['entry_country_id']) ? $billingAddress['entry_country_id'] : ONEPAGE_DEFAULT_COUNTRY), 'class="required" style="width:80%;float:left;"');
      }else{
		$val=Array();
		$val[] =  array('id' => RENTAL_DEFAULT_COUNTRY, 'text' => tep_get_country_name(RENTAL_DEFAULT_COUNTRY));
		echo tep_draw_pull_down_menu('billing_country',$val,RENTAL_DEFAULT_COUNTRY,'class="required" style="width:80%;float:left;"');

      }
  }else{
        echo tep_get_country_list('billing_country', (isset($billingAddress['entry_country_id']) ? $billingAddress['entry_country_id'] : ONEPAGE_DEFAULT_COUNTRY), 'class="required" style="width:80%;float:left;"');
  }

  ?>
  </td>
 </tr>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_STREET_ADDRESS'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_input_field('billing_street_address', $billingAddress['entry_street_address'], 'class="required" style="width:80%;float:left;"'); ?></td>
 </tr>
<?php
  if (ACCOUNT_SUBURB == 'true') {
?>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_SUBURB'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_input_field('billing_suburb', $billingAddress['entry_suburb'], 'style="width:80%;float:left"'); ?></td>
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
    <td class="main" width="50%"><?php echo tep_draw_input_field('billing_city', $billingAddress['entry_city'], 'class="required" style="width:80%;float:left;"'); ?></td>
    <td class="main" width="50%"><?php echo tep_draw_input_field('billing_postcode', $billingAddress['entry_postcode'], 'class="required" style="width:80%;float:left;"'); ?></td>
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
    <td class="main" width="100%" id="stateCol_billing"><?php echo tep_draw_input_field('billing_state', $billingAddress['entry_state'], 'class="required" style="width:80%;float:left"');?></td>
   </tr>
  </table></td>
 </tr>
<?php
  }
?>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_TELEPHONE'); ?><br><?php echo tep_draw_input_field('billing_telephone', (isset($onePageCheckout->onePage['info']) ? $onePageCheckout->onePage['info']['telephone'] : ''), ((ACCOUNT_TELEPHONE_REQUIRED == 'true')?'class="required"':''). 'style="width:80%;float:left;"'); ?></td>
 </tr>
 <tr>
  <td><table cellpadding="0" cellspacing="0" border="0" width="100%">
<?php if ($onePageCheckout->isNormalCheckout() === true || ONEPAGE_ACCOUNT_CREATE != 'required'){ ?>  
   <tr>
    <td colspan="2" class="main"><br>If you would like to create an account please enter a password below</td>
   </tr>
<?php } ?>   
   <tr>
    <td class="main"><?php echo sysLanguage::get('ENTRY_PASSWORD'); ?></td>
    <td class="main"><?php echo sysLanguage::get('ENTRY_PASSWORD_CONFIRMATION'); ?></td>
   </tr>
   <tr>
    <td class="main"><?php echo tep_draw_password_field('password', '', ($onePageCheckout->isMembershipCheckout() === true || ONEPAGE_ACCOUNT_CREATE == 'required' ? 'class="required" maxlength="40" ' : 'maxlength="40" ') . 'style="float:left;"'); ?></td>
    <td class="main"><?php echo tep_draw_password_field('confirmation', '', ($onePageCheckout->isMembershipCheckout() === true || ONEPAGE_ACCOUNT_CREATE == 'required' ? 'class="required" ' : '') . 'maxlength="40" style="float:left;"'); ?></td>
   </tr>
   <tr>
    <td class="main" colspan="2"><div id="pstrength_password"></div></td>
   </tr>
  </table></td>
 </tr>
 <tr>
  <td class="main"><?php echo sysLanguage::get('ENTRY_NEWSLETTER'); ?><br><?php echo tep_draw_checkbox_field('billing_newsletter', '1', (isset($onePageCheckout->onePage['info']['newsletter']) && $onePageCheckout->onePage['info']['newsletter'] == '1')); ?></td>
 </tr>
</table>
<?php
 }
?></div>