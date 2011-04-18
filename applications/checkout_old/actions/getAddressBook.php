<?php
	$addresses_count = tep_count_customer_address_book_entries();
	
	ob_start();
	$addressType = $_POST['addressType'];
	?>
<table cellpadding="0" cellspacing="0" border="0" width="93%">
<?php
    //if ($addresses_count > 1) {
?>
 <tr>
  <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td class="main"><b><?php echo sysLanguage::get('TABLE_HEADING_ADDRESS_BOOK_ENTRIES') . tep_draw_hidden_field('action', 'selectAddress') . tep_draw_hidden_field('address_type', $addressType); ?></b></td>
   </tr>
  </table></td>
 </tr>
 <tr>
  <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
   <tr class="infoBoxContents">
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
      $radio_buttons = 0;

      $checked = ($addressType == 'shipping' ? $onePageCheckout->onePage['deliveryAddressId'] : $onePageCheckout->onePage['billingAddressId']);
      $Qaddress = dataAccess::setQuery('select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from {address_book} where customers_id = {customer_id}');
      $Qaddress->setTable('{address_book}', TABLE_ADDRESS_BOOK);
      $Qaddress->setValue('{customer_id}', (int)$userAccount->getCustomerId());
      while ($Qaddress->next() !== false) {
        $format_id = tep_get_address_format_id($Qaddress->getVal('country_id'));
?>
     <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
      <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
       <tr class="moduleRow">
        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
        <td class="main" colspan="2"><b><?php echo tep_output_string_protected($Qaddress->getVal('firstname') . ' ' . $Qaddress->getVal('lastname')); ?></b></td>
        <td class="main" align="right"><?php echo tep_draw_radio_field('address', $Qaddress->getVal('address_book_id'), ($Qaddress->getVal('address_book_id') == $checked)); ?></td>
        <td width="10"><small><span style="color:#FF0000;">Select</span></small><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
       </tr>
       <tr>
        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
        <td colspan="3"><table border="0" cellspacing="0" cellpadding="2">
         <tr>
          <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
          <td class="main"><?php echo tep_address_format($format_id, $Qaddress->toArray(), true, ' ', '<br>'); ?></td>
          <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
         </tr>
        </table></td>
        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
       </tr>
      </table></td>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
     </tr>
<?php
        $radio_buttons++;
      }
?>
    </table></td>
   </tr>
  </table></td>
 </tr>
<?php
  //  }
?>
</table>
	<?php
	$html = ob_get_contents();
	ob_end_clean();
	
	EventManager::attachActionResponse($html, 'html');
?>