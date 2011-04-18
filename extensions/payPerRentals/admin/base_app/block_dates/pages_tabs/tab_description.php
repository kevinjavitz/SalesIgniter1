<?php
	if (isset($_GET['pID'])){
		$name = $Period->block_name;
		$start_date = $Period->block_start_date;
		$end_date = $Period->block_end_date;
		$reccuring = (($Period->recurring == 1)?true:false);

	}else{
		$name = '';
		$start_date = date('Y-m-d');
		$end_date = date('Y-m-d');
		$reccuring = false;
	}
	$htmlCheckbox = htmlBase::newElement('checkbox')
	->setName('reccuring')
	->setLabel(sysLanguage::get('TEXT_RECURRING'))
	->setLabelPosition('before')
	->setChecked($reccuring);

?>
 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_BLOCK_NAME'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('block_name', $name); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_BLOCK_START_DATE'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('block_start_date', $start_date,'id="block_start_date"'); ?></td>
 </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 <tr>
	<td class="main"><?php echo sysLanguage::get('TEXT_BLOCK_END_DATE'); ?></td>
	<td class="main"><?php echo tep_draw_input_field('block_end_date', $end_date,'id="block_end_date"'); ?></td>
 </tr>
 <tr>
	<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
 </tr>
	 <tr>
		 <td colspan="2" class="main"><?php echo $htmlCheckbox->draw(); ?></td>
	  </tr>
	  <tr>
		 <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	  </tr>


 </table>

