<?php
	error_reporting(0);
	header("Content-type: text/xml");
	if ($topic_depth == 'nested' || ($topic_depth == 'articles' || isset($_GET['authors_id']))){
		$Qtopic = Doctrine_Query::create()
		->select('t.topics_id, td.topics_name, td.topics_heading_title, td.topics_description')
		->from('Topics t')
		->leftJoin('t.TopicsDescription td')
		->where('t.topics_id = ?', (int)$current_topic_id)
		->andWhere('td.language_id = ?', (int)Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$Topic = $Qtopic[0];
		$TopicDescription = $Topic['TopicsDescription'][0];
	}

	$items = '';
	if ($topic_depth == 'nested'){

	}elseif ($topic_depth == 'articles'){
	// show the articles of a specified author
		$Qlisting = Doctrine_Query::create()
		//->select('a2t.articles_id, a.articles_date_added, ad.articles_name, ad.articles_head_desc_tag, t.topics_id, td.topics_name, a2t.topics_id')
		->from('Articles a')
		->leftJoin('a.ArticlesDescription ad')
		->leftJoin('a.ArticlesToTopics a2t')
		->leftJoin('a2t.Topics t')
		->leftJoin('t.TopicsDescription td')
		->where('(a.articles_date_available IS NULL OR TO_DAYS(a.articles_date_available) <= TO_DAYS(NOW()))')
		->andWhere('a.articles_status = ?', '1')
		->andWhere('ad.language_id = ?', (int)Session::get('languages_id'))
		->andWhere('td.language_id = ?', (int)Session::get('languages_id'))
		->orderBy('a.articles_date_added DESC, ad.articles_name')
		->andWhere('a2t.topics_id = ?', (int)$current_topic_id);

	}else{
		$Qlisting = Doctrine_Query::create()
		//->select('a2t.articles_id, a.articles_date_added, ad.articles_name, ad.articles_head_desc_tag, t.topics_id, td.topics_name')
		->from('Articles a')
		->leftJoin('a.ArticlesDescription ad')
		->leftJoin('a.ArticlesToTopics a2t')
		->leftJoin('a2t.Topics t')
		->leftJoin('t.TopicsDescription td')
		->where('(a.articles_date_available IS NULL OR TO_DAYS(a.articles_date_available) <= TO_DAYS(NOW()))')
		->andWhere('a.articles_status = ?', '1')
		->andWhere('ad.language_id = ?', (int)Session::get('languages_id'))
		->andWhere('td.language_id = ?', (int)Session::get('languages_id'))
		->orderBy('a.articles_date_added desc, ad.articles_name');
	}
    if ($topic_depth != 'nested'){
		$items = '<?xml version="1.0" encoding="ISO-8859-1" ?>
					<rss version="2.0">
						<channel>
							<title>' . $TopicDescription['topics_heading_title'] . '</title>
							<link>' . itw_app_link('appExt=articleManager&tPath=' . $Topic['topics_id'], 'show', 'default') . '</link>
							<description><![CDATA[' . stripslashes($TopicDescription['topics_description']) . ']]></description>
							<language>' . Session::get('languages_code') . '</language>
							';

		$Qlisting = $Qlisting->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		foreach($Qlisting as $lInfo){
			$Article = Doctrine_Core::getTable('Articles')->getRecordInstance()
			->getArticle($lInfo['articles_id'], (int)Session::get('languages_id'));
			$articlesName = $Article->getArticleName();
			$articlesText = $Article->getArticleText();
			$articlesDateAdded = $Article->getArticleDateAdded();
			$items .= '<item>
						<title>' . $articlesName. '</title>
						<link>' . itw_app_link('appExt=articleManager&articles_id=' . $lInfo['articles_id'], 'show', 'info') . '</link>
						<description><![CDATA[' . $articlesText . ']]></description>';

			if (isset($articlesDateAdded)){
				$items .= '<pubDate>'. strftime(sysLanguage::getDateFormat('long'), strtotime($articlesDateAdded)) .'</pubDate>';
			}

			$items .= '</item>';
		}

		$items .= '</channel>
					</rss>';
	}
	die($items);
?>