<?php
	$link = itw_app_link(null, 'index', 'default');
if (isset($_GET['goto']) && tep_not_null($_GET['goto'])) {
	$check_query = tep_db_query("select products_url from " . TABLE_PRODUCTS_DESCRIPTION . " where products_url = '" . tep_db_input($_GET['goto']) . "' limit 1");
	if (tep_db_num_rows($check_query)) {
		$link = 'http://' . $_GET['goto'];
	}
	if(sysConfig::exists('EXTENSION_ARTICLE_MANAGER_ENABLED') && sysConfig::get('EXTENSION_ARTICLE_MANAGER_ENABLED') == 'True'){
		$Qcheck = Doctrine_Query::create()
			->select('articles_url')
			->from('ArticlesDescription')
			->where('articles_url = ?', $_GET['goto'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck){
			$link = 'http://' . $_GET['goto'];
		}
	}
}

EventManager::attachActionResponse($link, 'redirect');
?>