<?php
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

ob_start();
if ($topic_depth == 'nested'){
	if (!empty($TopicDescription['topics_heading_title'])){
		$pageTitle = $TopicDescription['topics_heading_title']. '<a href="'.itw_app_link(tep_get_all_get_params().'appExt=articleManager','show','rss').'"><img src="'.sysConfig::getDirWsCatalog().'images/rss.png"/></a>';
	}else{
		$pageTitle = sysLanguage::get('HEADING_TITLE_NESTED');
	}
	
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php if (!empty($TopicDescription['topics_description'])){ ?>
	<tr>
		<td align="left" class="main"><?php	echo stripslashes($TopicDescription['topics_description']);?></td>
	</tr>
<?php } ?>
</table>
<?php
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
	
	if (!empty($TopicDescription['topics_heading_title'])){
		$pageTitle = $TopicDescription['topics_heading_title']. '<a href="'.itw_app_link(tep_get_all_get_params().'appExt=articleManager','show','rss').'"><img src="'.sysConfig::getDirWsCatalog().'images/rss.png"/></a>';
	}else{
		$pageTitle = sysLanguage::get('HEADING_TITLE_ARTICLES');
	}
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td><table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
<?php
// optional Article List Filter
if (sysConfig::get('EXTENSION_ARTICLES_ARTICLE_LIST_FILTER') == 'True'){
	$Qfilter = Doctrine_Query::create()
	->select('DISTINCT t.topics_id AS id, td.topics_name AS name, a2t.articles_id')
	->from('Articles a')
	->leftJoin('a.ArticlesToTopics a2t')
	->leftJoin('a2t.Topics t')
	->leftJoin('t.TopicsDescription td')
	->where('a.articles_status = ?', '1')
	->andWhere('a2t.topics_id = ?', (int)$current_topic_id)
	->orderBy('td.topics_name');

	$Result = $Qfilter->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Result){
		$hiddenFields = htmlBase::newElement('input')
		->setType('hidden')
		->setName('sort')
		->setValue($_GET['sort'])
		->draw();
		
		$dropMenu = htmlBase::newElement('selectbox')
		->setName('filter_id')
		->attr('onchange', 'this.form.submit()');
		if (isset($_GET['filter_id'])){
			$dropMenu->selectOptionByValue($_GET['filter_id']);
		}
		
		$dropMenu->addOption('', sysLanguage::get('TEXT_ALL_TOPICS'));
		$hiddenFields .= htmlBase::newElement('input')
		->setType('hidden')
		->setName('tPath')
		->setValue($tPath)
		->draw();

		foreach($Result as $fInfo){
			$dropMenu->addOption($fInfo['id'], $fInfo['name']);
		}
		
		echo '				<td align="right" class="main">' . 
			'<form name="filter" action="' . itw_app_link('appExt=articleManager', 'show', 'default') . '" method="get">' . 
				sysLanguage::get('TEXT_SHOW') . '&nbsp;' . $dropMenu->draw() . $hiddenFields . 
			'</form>' . 
		'</td>' . "\n";
	}
}
?>
			</tr>
<?php
	if (!empty($TopicDescription['topics_description'])){
?>
			<tr>
				<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
			</tr>
			<tr>
				<td align="left" class="main"><?php echo $TopicDescription['topics_description']; ?></td>
			</tr>
			<tr>
				<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
			</tr>
<?php
	}
?>
		</table></td>
	</tr>
	<tr>
		<td><?php include($thisExt->getModule('listing')); ?></td>
	</tr>
</table>
<?php
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
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_DEFAULT');
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="main"><b><?php echo sysLanguage::get('TEXT_ARTICLES');?></b></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
	</tr>
	<tr>
		<td><?php include($thisExt->getModule('listing')); ?></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
	</tr>
	<tr>
		<td><?php include($thisExt->getModule('upcoming')); ?></td>
	</tr>
</table>
<?php
}

	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
