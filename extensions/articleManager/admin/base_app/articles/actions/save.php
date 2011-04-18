<?php
	$Articles = Doctrine_Core::getTable('Articles');
	if (isset($_GET['aID'])){
		$Article = $Articles->find((int)$_GET['aID']);
	}else{
		$Article = $Articles->create();
	}

	$Article->articles_date_available = (date('Y-m-d') < $_POST['articles_date_available'] ? $_POST['articles_date_available'] : null);
	$Article->articles_status = $_POST['articles_status'];

	if (isset($_POST['articles_date_available']) && tep_not_null($_POST['articles_date_available'])) {
		$Article->articles_date_added = $_POST['articles_date_available'];
	}else{
		$Article->articles_date_added = date('Y-m-d');
	}

	/* Add to topics */
	$ArticlesToTopics =& $Article->ArticlesToTopics;
	$ArticlesToTopics->delete();
	if (isset($_POST['topics']) && !empty($_POST['topics'])){
		foreach($_POST['topics'] as $i => $topicId){
			$ArticlesToTopics[$i]->topics_id = $topicId;
		}
	}

	$languages = tep_get_languages();
	$ArticlesDescription =& $Article->ArticlesDescription;
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		$lID = $languages[$i]['id'];

		$ArticlesDescription[$lID]->language_id = $lID;
		$ArticlesDescription[$lID]->articles_name = $_POST['articles_name'][$lID];
		$ArticlesDescription[$lID]->articles_description = $_POST['articles_description'][$lID];
		$ArticlesDescription[$lID]->articles_url = $_POST['articles_url'][$lID];

	}

	/*
	 * anything additional to handle into $ArticlesDescription ?
	 */
	EventManager::notify('ArticleManagerDescriptionsBeforeSave', &$ArticlesDescription);


	$Article->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'aID')) . 'aID=' . $Article->articles_id, 'articles', 'default'), 'redirect');
?>
