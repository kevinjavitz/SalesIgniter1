<?php
$Article = Doctrine_Core::getTable('Articles')->getRecordInstance()
->getArticle((int)$_GET['articles_id'], (int)Session::get('languages_id'));
?>
<?php
if (is_null($Article) === true){
	$pageTitle = sysLanguage::get('HEADING_ARTICLE_NOT_FOUND');
	echo sysLanguage::get('TEXT_ARTICLE_NOT_FOUND');
}else{
	$Article->updateViews();

    $articlesName = $Article->getArticleName();
    $articlesText = $Article->getArticleText();
    $articlesUrl = $Article->getArticleUrl();
	$articlesDateAvailable = $Article->getArticleDateAvailable();
	$articlesDateAdded = $Article->getArticleDateAdded();
	
	$pageTitle = $articlesName;
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="main"><p><?php echo stripslashes($articlesText); ?></p></td>
	</tr>
<?php
	if (!empty($articlesUrl)){
?>
	<tr>
		<td class="main"><?php echo sprintf(sysLanguage::get('TEXT_MORE_INFORMATION'), itw_app_link('action=url&goto=' . urlencode($articlesUrl), 'redirect', 'default', 'NONSSL')); ?></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
	</tr>
<?php
	}
	if (sysConfig::get('EXTENSION_ARTICLE_MANAGER_DISPLAY_DATE_ADDED_ARTICLE_LISTING') == 'True'){
		if ($articlesDateAvailable > date('Y-m-d H:i:s')){
?>
	<tr>
		<td align="left" class="smallText"><?php echo sprintf(sysLanguage::get('TEXT_DATE_AVAILABLE'), tep_date_long($articlesDateAvailable)); ?></td>
	</tr>
<?php
		}else{
?>
	<tr>
		<td align="left" class="smallText"><?php echo sprintf(sysLanguage::get('TEXT_DATE_ADDED'), tep_date_long($articlesDateAdded)); ?></td>
	</tr>
<?php
		}
	}
?>
	</tr>
</table>
<?php
}
?>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
