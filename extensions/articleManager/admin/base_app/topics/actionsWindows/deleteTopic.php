<?php
	$topicId = (int)$_GET['tID'];
	$childs_count = tep_childs_in_topic_count($topicId);
	$articles_count = tep_articles_in_topic_count($topicId);
?>
<table cellpadding="3" cellspacing="0" border="0">
	<tr>
		<td><?php echo sysLanguage::get('TEXT_DELETE_TOPIC_INTRO');?></td>
	</tr>
<?php
	if ($childs_count > 0){
?>
	<tr>
		<td><?php echo sprintf(sysLanguage::get('TEXT_DELETE_WARNING_CHILDS'), $childs_count);?></td>
	</tr>
<?php
	}
	
	if ($articles_count > 0){
?>
	<tr>
		<td><?php echo sprintf(sysLanguage::get('TEXT_DELETE_WARNING_ARTICLES'), $articles_count);?></td>
	</tr>
<?php
	}
?>
</table>