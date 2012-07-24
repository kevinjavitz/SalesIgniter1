<?php
	if (isset($_GET['pID'])){
		$name = $Period->block_name;
		$start_date = date('Y-m-d H:i',strtotime($Period->block_start_date));
		$end_date = date('Y-m-d H:i', strtotime($Period->block_end_date));
		$reccuring_year = (($Period->recurring_year == 1)?true:false);
		$reccuring_month = (($Period->recurring_month == 1)?true:false);
		$reccuring_day = (($Period->recurring_day == 1)?true:false);

	}else{
		$name = '';
		$start_date = date('Y-m-d');
		$end_date = date('Y-m-d');
		$reccuring_year = false;
		$reccuring_month = false;
		$reccuring_day = false;
	}
	$htmlCheckboxYear = htmlBase::newElement('checkbox')
	->setName('reccuring_year')
	->setLabel(sysLanguage::get('TEXT_RECURRING_YEAR'))
	->setLabelPosition('before')
	->setChecked($reccuring_year);

	$htmlCheckboxMonth = htmlBase::newElement('checkbox')
	->setName('reccuring_month')
	->setLabel(sysLanguage::get('TEXT_RECURRING_MONTH'))
	->setLabelPosition('before')
	->setChecked($reccuring_month);

	$htmlCheckboxDay = htmlBase::newElement('checkbox')
	->setName('reccuring_day')
	->setLabel(sysLanguage::get('TEXT_RECURRING_DAY'))
	->setLabelPosition('before')
	->setChecked($reccuring_day);

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
		 <td colspan="2" class="main"><?php echo $htmlCheckboxYear->draw(); ?></td>
	  </tr>
	 <tr>
		 <td colspan="2" class="main"><?php echo $htmlCheckboxMonth->draw(); ?></td>
	 </tr>
	 <tr>
		 <td colspan="2" class="main"><?php echo $htmlCheckboxDay->draw(); ?></td>
	 </tr>
	  <tr>
		 <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	  </tr>


 </table>

