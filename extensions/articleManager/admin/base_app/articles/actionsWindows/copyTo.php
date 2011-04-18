<table cellpadding="3" cellspacing="0" border="0">
	<tr>
		<td><?php echo sysLanguage::get('TEXT_INFO_COPY_TO_INTRO');?></td>
	</tr>
	<tr>
		<td><?php echo sysLanguage::get('TEXT_INFO_CURRENT_TOPICS');?></td>
	</tr>
	<tr>
		<td><b><?php echo tep_output_generated_topic_path($articles_id, 'article');?></b></td>
	</tr>
	<tr>
		<td><?php echo sysLanguage::get('TEXT_TOPICS');?></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_pull_down_menu('topics_id', tep_get_topic_tree(), $current_topic_id);?></td>
	</tr>
	<tr>
		<td><?php echo sysLanguage::get('TEXT_HOW_TO_COPY');?></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_radio_field('copy_as', 'link', true) . ' ' . sysLanguage::get('TEXT_COPY_AS_LINK') . '<br />' . tep_draw_radio_field('copy_as', 'duplicate') . ' ' . sysLanguage::get('TEXT_COPY_AS_DUPLICATE');?></td>
	</tr>
</table>