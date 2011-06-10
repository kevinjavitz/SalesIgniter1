<?php

	if (isset($_GET['fID'])){
		$address_format = $AddressFormat->address_format;
		$address_summary = $AddressFormat->address_summary;

	}else{
		$address_format = '';
		$address_summary = '';
	}
                               
?>

 <table cellpadding="3" cellspacing="0" border="0">
 <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_ADDRESS_FORMAT_COLUMNS'); ?></td>
   <td class="main"><?php echo $columns;?></td>
  </tr>

  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_ADDRESS_FORMAT_NAME'); ?></td>
   <td class="main"><?php echo tep_draw_textarea_field('address_summary','hard', 20, 5, $address_summary, 'class="makeFCK"'); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_ADDRESS_FORMAT'); ?></td>
   <td class="main"><?php echo tep_draw_textarea_field('address_format','hard',40,3, $address_format, 'class="makeFCK"'); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
	 
 </table>

