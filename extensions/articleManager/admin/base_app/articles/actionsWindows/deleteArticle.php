<?php
$articles_id = $_GET['aID'];
$article_topics_string = '';
$article_topics = tep_generate_topic_path($articles_id, 'article');

	for ($i = 0, $n = sizeof($article_topics); $i < $n; $i++) {
		$topic_path = '';
		for ($j = 0, $k = sizeof($article_topics[$i]); $j < $k; $j++) {
			$topic_path .= $article_topics[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
		}
		$topic_path = substr($topic_path, 0, -16);
		$article_topics_string .= tep_draw_checkbox_field('article_topics[]', $article_topics[$i][sizeof($article_topics[$i])-1]['id'], true) . '&nbsp;' . $topic_path . '<br>';
	}
?>
<table cellpadding="3" cellspacing="0" border="0">
	<tr>
		<td><b><?php echo sysLanguage::get('TEXT_DELETE_ARTICLE_INTRO');?></b></td>
	</tr>
	<tr>
		<td><?php echo $article_topics_string;?></td>
	</tr>
	<tr>
		<td><?php //echo tep_draw_pull_down_menu('move_to_topic_id', tep_get_topic_tree(), $current_topic_id);?></td>
	</tr>
</table>

