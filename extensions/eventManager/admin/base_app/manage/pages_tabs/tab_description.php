<?php
echo '<ul>';
foreach(sysLanguage::getLanguages() as $lInfo){
	echo '<li class="ui-tabs-nav-item"><a href="#langTab_' . $lInfo['id'] . '"><span>' . '&nbsp;' . $lInfo['showName']() . '</span></a></li>';
}
echo '</ul>';
if (isset($_GET['eID'])){

		$start_date = $Event->events_start_date;
		$end_date = $Event->events_end_date;

	}else{
		$start_date =date("Y-m-d");
		$end_date =date("Y-m-d");
	}

	foreach(sysLanguage::getLanguages() as $lInfo){
		$lID = $lInfo['id'];
		$name = ''; $description = '';$seo_url = ''; $htc_title = ''; $htc_desc = ''; $htc_keywords = ''; $htc_descrip = '';
		if (isset($_GET['eID'])){
			$name = $Event->EventManagerEventsDescription[$lID]->events_title;
			$description = $Event->EventManagerEventsDescription[$lID]->events_description_text;
			//$seo_url = $Category->PhotoGalleryCategoriesDescription[$lID]->categories_seo_url;
		}
		?>
	<div id="langTab_<?php echo $lID;?>">
		<table cellpadding="3" cellspacing="0" border="0">
			<tr>
				<td class="main"><?php echo sysLanguage::get('TEXT_EVENTS_NAME'); ?></td>
				<td class="main"><?php echo tep_draw_input_field('events_title[' . $lID . ']', $name); ?></td>
			</tr>
			<tr>
				<td class="main" valign="top"><?php echo sysLanguage::get('TEXT_EVENTS_DETAILS'); ?></td>
				<td class="main"><?php echo tep_draw_textarea_field('events_description[' . $lID . ']', 'hard', 30, 5, $description, 'class="makeFCK"'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
			</tr>
		</table>

	</div>
	<?php
}
		?>
 <table cellpadding="3" cellspacing="0" border="0">

 <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_EVENTS_START_DATE'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('events_start_date', $start_date,'id="events_start_date"'); ?></td>
 </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
	 <tr>
		 <td class="main"><?php echo sysLanguage::get('TEXT_EVENTS_END_DATE'); ?></td>
		 <td class="main"><?php echo tep_draw_input_field('events_end_date', $start_date,'id="events_end_date"'); ?></td>
	 </tr>
	 <tr>
		 <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	 </tr>


 </table>

