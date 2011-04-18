<?php
/*
	Articles Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	
	$appContent = $App->getAppContentFile();
	$thisExt = $appExtension->getExtension('articleManager');
	
	/*
	 * Determine what page to show based on the current topic
	 */
	if ($App->getPageName() == 'default' || $App->getPageName() == 'rss'){
		$topic_depth = 'top';
		
		if (isset($tPath) && tep_not_null($tPath)){
			$Qarticles = Doctrine_Query::create()
			->select('count(*) as total')
			->from('ArticlesToTopics')
			->where('topics_id = ?', (int)$current_topic_id)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qarticles[0]['total'] > 0) {
				$topic_depth = 'articles'; // display articles
			}else{
				$Qtopics = Doctrine_Query::create()
				->select('count(*) as total')
				->from('Topics')
				->where('parent_id = ?', (int)$current_topic_id)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qtopics[0]['total'] > 0) {
					$topic_depth = 'nested'; // navigate through the topics
				}else{
					$topic_depth = 'articles'; // topic has no articles, but display the 'no articles' message
				}
			}
		}
	}
	
	/*
	 * Set up the breadcrumb based on the application page name
	 */
	$breadcrumb->add(
		sysLanguage::get('NAVBAR_TITLE_ARTICLES'),
		itw_app_link('appExt=articleManager', 'show', 'default')
	);

	switch($App->getPageName()){
		case 'default':
			if ($topic_depth == 'top' && !isset($_GET['authors_id'])){
				$breadcrumb->add(
					sysLanguage::get('NAVBAR_TITLE_DEFAULT'),
					itw_app_link('appExt=articleManager', 'show', 'default')
				);
			}elseif ($topic_depth == 'articles'){
				$breadcrumb->add(
					sysLanguage::get('NAVBAR_TITLE_ARTICLES'),
					itw_app_link(tep_get_all_get_params(array('appExt')) . 'appExt=articleManager', 'show', 'default')
				);
			}elseif ($topic_depth == 'nested'){
				$breadcrumb->add(
					sysLanguage::get('NAVBAR_TITLE_NESTED'),
					itw_app_link(tep_get_all_get_params(array('appExt')) . 'appExt=articleManager', 'show', 'default')
				);
			}
			break;
		case 'info':
			$Qarticle = Doctrine_Query::create()
			->select('articles_name')
			->from('ArticlesDescription')
			->where('articles_id = ?', (int)$_GET['articles_id'])
			->andWhere('language_id = ?', (int)Session::get('languages_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qarticle){
				$breadcrumb->add(
					$Qarticle[0]['articles_name'],
					itw_app_link(tep_get_all_get_params(array('appExt')) . 'appExt=articleManager', 'show', 'info')
				);
			}
			break;
		case 'new':
			$breadcrumb->add(
				sysLanguage::get('NAVBAR_TITLE_NEW'),
				itw_app_link(tep_get_all_get_params(array('appExt')) . 'appExt=articleManager', 'show', 'new')
			);
			break;
	}
?>