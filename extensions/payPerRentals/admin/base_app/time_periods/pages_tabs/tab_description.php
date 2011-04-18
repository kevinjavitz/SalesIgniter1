<?php
	if (isset($_GET['pID'])){
		$name = $Period->period_name;
		$start_date = $Period->period_start_date;
		$end_date = $Period->period_end_date;
		$details = $Period->period_details;
	}else{
		$name = "";
		$details = "";
		$start_date = date("Y-m-d");
		$end_date = date("Y-m-d");
	}

?>
 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_PERIOD_NAME'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('period_name', $name); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_PERIOD_START_DATE'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('period_start_date', $start_date,'id="period_start_date"'); ?></td>
 </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 <tr>
	<td class="main"><?php echo sysLanguage::get('TEXT_PERIOD_END_DATE'); ?></td>
	<td class="main"><?php echo tep_draw_input_field('period_end_date', $end_date,'id="period_end_date"'); ?></td>
 </tr>
 <tr>
	<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
 </tr>

  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_PERIOD_DETAILS'); ?></td>
   <td class="main"><?php echo tep_draw_textarea_field('period_details', 'soft', 30, 5, $details, 'class="makeFCK"'); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>

 </table>

