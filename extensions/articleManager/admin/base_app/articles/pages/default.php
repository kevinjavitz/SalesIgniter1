<?php
	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 0))
	->setQuery($Qtopics);

	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('New Article')->addClass('newButton'),
		htmlBase::newElement('button')->setText('Edit')->addClass('editButton')->disable(),
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable()
	));
	
	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_ARTICLES')),
			array('text' => sysLanguage::get('TABLE_HEADING_STATUS')),
			array('text' => 'info')
		)
	));

    $Qarticles = Doctrine_Query::create()
    ->select('a.articles_id, ad.articles_name, a.articles_date_added, a.articles_last_modified, a.articles_date_available, a.articles_status, a2t.topics_id')
    ->from('Articles a')
    ->leftJoin('a.ArticlesDescription ad')
    ->leftJoin('a.ArticlesToTopics a2t')
    ->where('ad.language_id = ?', (int)Session::get('languages_id'))
    ->orderBy('ad.articles_name');
    if (isset($_GET['search'])){
    	$Qarticles->andWhere('ad.articles_name LIKE ?', '%' . $_GET['search'] . '%');
    }
    $Result = $Qarticles->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    foreach($Result as $articles){
		$arrowIcon = htmlBase::newElement('icon')->setType('info');
		
		if ($articles['articles_status'] == '1') {
			$statusIcons = tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . itw_app_link('appExt=articleManager&action=setflag&flag=0&aID=' . $articles['articles_id'] . '&tPath=' . $tPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
		}else{
			$statusIcons = '<a href="' . itw_app_link('appExt=articleManager&action=setflag&flag=1&aID=' . $articles['articles_id'] . '&tPath=' . $tPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
		}

		$tableGrid->addBodyRow(array(
			'rowAttr' => array(
				'data-article_id' => $articles['articles_id']
			),
			'columns' => array(
				array('text' => $articles['ArticlesDescription'][0]['articles_name']),
				array('align' => 'center', 'text' => $statusIcons),
				array('text' => $arrowIcon->draw(), 'align' => 'center')
			)
		));
		
		$tableGrid->addBodyRow(array(
			'addCls' => 'gridInfoRow',
			'columns' => array(
				array('colspan' => 6, 'text' => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' . 
					'<tr>' . 
						'<td><b>' . sysLanguage::get('TEXT_DATE_ADDED') . '</b></td>' . 
						'<td> ' . tep_date_short($articles['articles_date_added']) . '</td>' . 
						'<td><b>' . sysLanguage::get('TEXT_LAST_MODIFIED') . '</b></td>' . 
						'<td>' . tep_date_short($articles['articles_last_modified']) . '</td>' .
					'</tr>' . 
					'<tr>' . 
						'<td><b>' . sysLanguage::get('TEXT_DATE_AVAILABLE') . '</b></td>' . 
						'<td>'  . tep_date_short($articles['articles_date_available']) . '</td>' . 
					'</tr>' . 
				'</table>')
			)
		));
    }
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <form name="search" action="<?php echo itw_app_link();?>" method="get">
 <table cellspacing="0" cellpadding="0" style="width:99%;margin-right:5px;margin-left:5px;">
  <tr>
   <td class="smallText" align="right"><?php echo sysLanguage::get('HEADING_TITLE_SEARCH') . ' ' . tep_draw_input_field('search'); ?></td>
  </tr>
 </table></form>
 <div style="width:100%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
 </div>