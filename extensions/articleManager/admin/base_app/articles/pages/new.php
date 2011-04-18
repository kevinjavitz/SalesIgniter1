<?php
	$Articles = Doctrine_Core::getTable('Articles');
	if (isset($_GET['aID'])){
		$Article = $Articles->find((int)$_GET['aID']);
		$Article->refresh(true);

		$sortOrder = $Articles->sort_order;
		$headingTitle = sysLanguage::get('HEADING_TITLE_EDIT');
	}else{
		$Article = $Articles->getRecord();

		$sortOrder = 0;
		$headingTitle = sysLanguage::get('HEADING_TITLE_NEW');
	}

	$languages = tep_get_languages();

	switch ($Article->articles_status){
		case '0': $in_status = false; $out_status = true; break;
		case '1':
		default: $in_status = true; $out_status = false;
	}
?>
<div class="pageHeading"><?php
	echo $headingTitle;
?></div>
<br />
<?php
	$tabDir = sysConfig::getDirFsCatalog() . 'extensions/articleManager/admin/base_app/articles/pages_tabs/';
?>
<form name="new_article" action="<?php echo itw_app_link('appExt=articleManager&action=save' . (isset($_GET['aID']) ? '&aID=' . $_GET['aID'] : ''), 'articles', 'new');?>" method="post">
	<div id="tab_container">
		<ul>
			<li class="ui-tabs-nav-item"><a href="#page-1"><span><?php echo sysLanguage::get('TAB_GENERAL');?></span></a></li>
			<li class="ui-tabs-nav-item"><a href="#page-2"><span><?php echo sysLanguage::get('TAB_DESCRIPTION');?></span></a></li>
			<li class="ui-tabs-nav-item"><a href="#page-3"><span><?php echo sysLanguage::get('TAB_TOPICS');?></span></a></li>
<?php
	$contents = EventManager::notifyWithReturn('NewArticleTabHeader');
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
?>
		</ul>
		
		<div id="page-1"><?php include($tabDir . 'tab_general.php');?></div>
		<div id="page-2"><?php include($tabDir . 'tab_description.php');?></div>
		<div id="page-3"><?php include($tabDir . 'tab_topics.php');?></div>
<?php
	$contents = EventManager::notifyWithReturn('NewArticleTabBody', &$Category);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
?>
	</div>
	<br />
	<div style="text-align:right"><?php
		$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
		$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
		->setHref(itw_app_link('appExt=articleManager' . (isset($_GET['aID']) ? '&aID=' . $_GET['aID'] : ''), 'articles', 'default'));
		
		echo $saveButton->draw() . $cancelButton->draw();
	?></div>
</form>