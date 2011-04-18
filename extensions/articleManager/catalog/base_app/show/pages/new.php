<?php
	$Qlisting = Doctrine_Query::create()
	//->select('a.articles_id, a.articles_date_added, ad.articles_name, ad.articles_head_desc_tag, a2t.topics_id, t.topics_id, td.topics_name')
	->from('Articles a')
	->leftJoin('a.ArticlesDescription ad')
	->leftJoin('a.ArticlesToTopics a2t')
	->leftJoin('a2t.Topics t')
	->leftJoin('t.TopicsDescription td')
	->where('(a.articles_date_available IS NULL OR TO_DAYS(a.articles_date_available) <= TO_DAYS(NOW()))')
	->andWhere('a.articles_status = ?', '1')
	->andWhere('ad.language_id = ?', (int)Session::get('languages_id'))
	->andWhere('td.language_id = ?', (int)Session::get('languages_id'))
	->andWhere('a.articles_date_added > SUBDATE(NOW(), INTERVAL "' . sysConfig::get('EXTENSION_ARTICLE_MANAGER_NEW_ARTICLES_DAYS_DISPLAY') . '" DAY)')
	->orderBy('a.articles_date_added desc, ad.articles_name');

	ob_start();
	include($thisExt->getModule('listing'));
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_NEW');
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
