<table cellpadding="3" cellspacing="0" border="0">
	<tr>
		<td><?php echo sprintf(sysLanguage::get('TEXT_MOVE_ARTICLES_INTRO'), $articles_name);?></td>
	</tr>
	<tr>
		<td><?php echo sysLanguage::get('TEXT_INFO_CURRENT_TOPICS');?></td>
	</tr>
	<tr>
		<td><b><?php echo tep_output_generated_topic_path($articles_id, 'article');?></b></td>
	</tr>
	<tr>
		<td><?php echo sprintf(sysLanguage::get('TEXT_MOVE'), $articles_name);?></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_pull_down_menu('move_to_topic_id', tep_get_topic_tree(), $current_topic_id);?></td>
	</tr>
</table>