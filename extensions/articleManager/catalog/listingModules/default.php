<?php
	$currentPage = (isset($_GET['page']) ? (int)$_GET['page'] : 1);
	$limitResults = (isset($_GET['limit']) ? (int)$_GET['limit'] : 25);
		
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

	if ($Result){
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
	<tr>
		<td><table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="main"><?php echo sysLanguage::get('TEXT_ARTICLES'); ?></td>
			</tr>
			<tr>
				<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
			</tr>
<?php
		foreach($Result as $aInfo){
?>
			<tr>
				<td valign="top" class="main" width="75%"><?php
					echo '<a class="main" href="' . itw_app_link('appExt=articleManager&articles_id=' . $aInfo['articles_id'], 'show', 'info') . '"><b>' . $aInfo['ArticlesDescription'][(int)Session::get('languages_id')]['articles_name'] . '</b></a> ';
				?></td>
<?php
			if (sysConfig::get('EXTENSION_ARTICLE_MANAGER_DISPLAY_TOPIC_ARTICLE_LISTING') == 'True' && !empty($aInfo['topics_name'])){
?>
				<td valign="top" class="main" width="25%" style="white-space:nowrap;"><?php
            		echo sysLanguage::get('TEXT_TOPIC') . '&nbsp;<a href="' . itw_app_link('appExt=articleManager&tPath=' . $aInfo['topics_id'], 'show', 'default') . '">' . $aInfo['Topics'][0]['TopicsDescription'][(int)Session::get('languages_id')]['topics_name'] . '</a>';
            	?></td>
<?php
			}
?>
			</tr>
<?php
			if (sysConfig::get('EXTENSION_ARTICLE_MANAGER_DISPLAY_ABSTRACT_ARTICLE_LISTING') == 'True') {
?>
			<tr>
				<td class="main" style="padding-left:15px"><?php
					echo clean_html_comments(substr($aInfo['ArticlesDescription'][(int)Session::get('languages_id')]['articles_head_desc_tag'],0, sysConfig::get('EXTENSION_ARTICLE_MANAGER_MAX_ARTICLE_ABSTRACT_LENGTH'))) . ((strlen($aInfo['ArticlesDescription'][(int)Session::get('languages_id')]['articles_head_desc_tag']) >= sysConfig::get('EXTENSION_ARTICLE_MANAGER_MAX_ARTICLE_ABSTRACT_LENGTH')) ? '...' : '');
				?></td>
			</tr>
<?php
			}
			if (sysConfig::get('EXTENSION_ARTICLE_MANAGER_DISPLAY_DATE_ADDED_ARTICLE_LISTING') == 'True') {
?>
			<tr>
				<td class="smalltext" style="padding-left:15px"><?php
					echo sysLanguage::get('TEXT_DATE_ADDED') . ' ' . tep_date_long($aInfo['articles_date_added']);
				?></td>
			</tr>
<?php
			}
			if (sysConfig::get('EXTENSION_ARTICLE_MANAGER_DISPLAY_ABSTRACT_ARTICLE_LISTING') == 'True' || sysConfig::get('EXTENSION_ARTICLE_MANAGER_DISPLAY_DATE_ADDED_ARTICLE_LISTING') == 'True') {
?>
			<tr>
				<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
			</tr>
<?php
			}
		} // End of listing loop
?>
		</table></td>
	</tr>
</table>
<?php
		if (sysConfig::get('EXTENSION_ARTICLE_MANAGER_ARTICLE_PREV_NEXT_BAR_LOCATION') == 'bottom' || sysConfig::get('EXTENSION_ARTICLE_MANAGER_ARTICLE_PREV_NEXT_BAR_LOCATION') == 'both'){
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
	if ($topic_depth == 'articles'){
		echo sysLanguage::get('TEXT_NO_ARTICLES');
	}
?></div>
<?php
	}
?>