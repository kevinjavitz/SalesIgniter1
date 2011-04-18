<?php
	$currentPage = (isset($_GET['page']) ? (int)$_GET['page'] : 1);
	$limitResults = (isset($_GET['limit']) ? (int)$_GET['limit'] : 25);
		
	if (isset($useLimit) && is_null($useLimit) === false){
		$limitResults = $useLimit;
	}
		
	$listingPager = new Doctrine_Pager($Qlisting, $currentPage, $limitResults);
	$pagerLink = itw_app_link(tep_get_all_get_params(array('page', 'action')) . 'page={%page_number}');
	$pagerRange = new Doctrine_Pager_Range_Sliding(array(
		'chunk' => 5
	));
		
	$pagerLayout = new PagerLayoutWithArrows($listingPager, $pagerRange, $pagerLink);
	$pagerLayout->setTemplate('<a href="{%url}" class="ui-widget ui-corner-all ui-state-default productListingRowPagerLink">{%page}</a>');
	$pagerLayout->setSelectedTemplate('<span class="ui-widget ui-corner-all productListingRowPagerLinkActive">{%page}</span>');

	$Pager = $pagerLayout->getPager();
	$Result = $Pager->execute();
		
	$pagerLinks = ($Pager->haveToPaginate() ? $pagerLayout->display(array(), true) : '1 of 1');

	if ($Result->count() > 0){
		if (sysConfig::get('EXTENSION_ARTICLE_MANAGER_ARTICLE_PREV_NEXT_BAR_LOCATION') == 'top' || sysConfig::get('EXTENSION_ARTICLE_MANAGER_ARTICLE_PREV_NEXT_BAR_LOCATION') == 'both'){
?>
<br />
 <div class="productListingRowPager ui-corner-all"><?php
 	echo '<div style="margin:.5em;line-height:2em;text-align:right;"><b>Page:</b> ' . $pagerLinks . '</div>';
 ?></div>
<br />
<?php
		}
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
		$date_start = date('Y-m-d');
		foreach($Result as $aInfo){
			if (tep_date_short($date_start) != tep_date_short($aInfo['articles_date_added'])){
		    ?>
	<tr>
		<td valign="top" class="main"><?php
			if ((date('m') == date('m', strtotime($aInfo['articles_date_added']))) &&(date('Y') == date('Y', strtotime($aInfo['articles_date_added'])))){
				echo '<h2>This months anouncements ' . date('F') . ' ' . date('Y') . '</h2>';
			}else{
				echo '<h2>' . date('F',strtotime($aInfo['articles_date_added'])) . ' - ' . date('Y', strtotime($aInfo['articles_date_added'])) . '</h2>';
			}
		?></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
	</tr>
<?php
		$date_start = $articles_listing['articles_date_added'];
			}
?>
	<tr>
		<td valign="top" class="main" style="border-bottom: 1px solid #e1e1e1;" width="75%"><?php
			echo '<a class="main" href="' . itw_app_link('appExt=articleManager&articles_id=' . $aInfo['articles_id'], 'show', 'info') . '"><b>' . $aInfo['ArticlesDescription'][(int)Session::get('languages_id')]['articles_name'] . '</b></a> ';
					
	 		if (sysConfig::get('DISPLAY_DATE_ADDED_ARTICLE_LISTING') == 'True') {
				echo ' - ' . tep_date_long($aInfo['articles_date_added']);
			}
		?></td>
	</tr>
<?php
			if (sysConfig::get('EXTENSION_ARTICLES_DISPLAY_ABSTRACT_ARTICLE_LISTING') == 'True' || sysConfig::get('EXTENSION_ARTICLES_DISPLAY_DATE_ADDED_ARTICLE_LISTING') == 'True') {
?>
	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
	</tr>
<?php
			}
		} // End of listing loop
?>
</table>
<?php
		if (sysConfig::get('EXTENSION_ARTICLES_ARTICLE_PREV_NEXT_BAR_LOCATION') == 'bottom' || sysConfig::get('EXTENSION_ARTICLES_ARTICLE_PREV_NEXT_BAR_LOCATION') == 'both'){
?>
<br />
 <div class="productListingRowPager ui-corner-all"><?php
 	echo '<div style="margin:.5em;line-height:2em;text-align:right;"><b>Page:</b> ' . $pagerLinks . '</div>';
 ?></div>
<?php
		}
?>
<?php
	} else {
?>
<div class="main"><?php
	if (!isset($topic_depth) || $topic_depth == 'articles'){
		echo sysLanguage::get('TEXT_NO_ARTICLES');
	}
?></div>
<?php
	}
?>