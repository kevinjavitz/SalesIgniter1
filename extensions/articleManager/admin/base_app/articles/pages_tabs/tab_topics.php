<style>
 .topicListingUL {
     list-style:none;
     margin:0;
     padding:0;
 }
 
 .subTopicContainer {
     margin-left: 10px;
 }
 
 .subTopicContainer ul {
     margin: 0;
     padding: 0;
     margin-left: 10px;
 }
</style>
<?php
	$checkedTopics = array();
	if ($Article->articles_id > 0){
		$QcurTopics = Doctrine_Query::create()
		->select('topics_id')
		->from('ArticlesToTopics')
		->where('articles_id = ?', $Article->articles_id)
		->execute();
		if ($QcurTopics->count() > 0){
			foreach($QcurTopics->toArray() as $topic){
				$checkedTopics[] = $topic['topics_id'];
			}
			unset($topic);
		}
		$QcurTopics->free();
		unset($QcurTopics);
	}
	echo tep_get_topic_tree_list('0', $checkedTopics);
	unset($checkedTopics);
?>