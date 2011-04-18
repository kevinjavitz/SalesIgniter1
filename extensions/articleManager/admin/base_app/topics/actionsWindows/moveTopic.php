<table cellpadding="3" cellspacing="0" border="0">
	<tr>
		<td><?php echo sprintf(sysLanguage::get('TEXT_MOVE_TOPICS_INTRO'), $topicName);?></td>
	</tr>
	<tr>
		<td><?php echo sprintf(sysLanguage::get('TEXT_MOVE'), $topicName);?></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_pull_down_menu('move_to_topic_id', tep_get_topic_tree(), $current_topic_id);?></td>
	</tr>
</table>