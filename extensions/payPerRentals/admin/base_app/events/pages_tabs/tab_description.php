<?php
	if (isset($_GET['eID'])){
		$name = $Event->events_name;
		$date = $Event->events_date;
		$details = $Event->events_details;
		$shipping = $Event->shipping;
		$countryId = $Event->events_country_id;
		$zoneId = $Event->events_zone_id;
		$state = $Event->events_state;
	}else{
		$name = "";
		$details = "";
		$date =date("Y-m-d");
		$shipping = '';
		$state = '';
		$countryId = '223';
	}
	$methods = explode(',', $shipping);

	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
		$Module = OrderShippingModules::getModule('zonereservation');
	} else{
		$Module = OrderShippingModules::getModule('upsreservation');
	}
    $shippingInputs = array();
	if(isset($Module) && is_object($Module)){
		$quotes = $Module->quote();
		foreach($quotes['methods'] as $mInfo){
			$shippingInputs[] = array(
				'value' => $mInfo['id'],
				'label' => $mInfo['title'],
				'labelPosition' => 'after'
			);
		}
	}
	$shippingGroup = htmlBase::newElement('checkbox')->addGroup(array(
		'separator' => '<br />',
		'name' => 'ppr_shipping[]',
		'checked' => $methods,
		'data' => $shippingInputs
	));
?>
 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_EVENTS_NAME'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('events_name', $name); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_EVENTS_DATE'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('events_date', $date,'id="events_date"'); ?></td>
 </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 <tr>
  <td class="main"><?php echo sysLanguage::get('TEXT_EVENTS_SHIPPING'); ?></td>
   <td class="main"><?php echo $shippingGroup->draw(); ?></td>
 </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_EVENTS_DETAILS'); ?></td>
   <td class="main"><?php echo tep_draw_textarea_field('events_details', 'soft', 30, 5, $details, 'class="makeFCK"'); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>

  <tr>
   <td class="main" valign="top">&nbsp;</td>
   <td class="main">
   <?php
       $checkAddressBox = htmlBase::newElement('contentbox')
               ->setHeader('Event Address')               
               ->setButtonBarAlign('right');

       $checkAddressBox->addContentBlock('<table border="0" cellspacing="2" cellpadding="2" id="addressEntry">' .

           '<tr>' .
               '<td>' . sysLanguage::get('ENTRY_COUNTRY') . '</td>' .
               '<td>' . tep_get_country_list('events_country', $countryId, 'id="countryDrop"') . '</td>' .
           '</tr>' .
            '<tr>' .
               '<td>' . sysLanguage::get('ENTRY_STATE') . '</td>' .
               '<td id="stateCol">' . tep_draw_input_field('events_state', $state,'id="ezone"') . '</td>' .
           '</tr>' .
       '</table>');
        echo $checkAddressBox->draw();
?>
   </td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 </table>

