<?php
	function addTopicTreeToGrid($parentId, &$tableGrid, $namePrefix = ''){
		global $allGetParams;
		$Qtopics = Doctrine_Query::create()
		->select('t.topics_id, td.topics_name, t.parent_id, t.sort_order, t.date_added, t.last_modified')
		->from('Topics t')
		->leftJoin('t.TopicsDescription td')
		->where('td.language_id = ?', (int)Session::get('languages_id'))
		->andWhere('t.parent_id = ?', $parentId)
		->orderBy('t.sort_order, td.topics_name');

		EventManager::notify('TopicListingQueryBeforeExecute', &$Qtopics);
		
		$Result = $Qtopics->execute();
		if ($Result->count() > 0){
			foreach($Result->toArray(true) as $Topic){
				$topicId = $Topic['topics_id'];
				
				$arrowIcon = htmlBase::newElement('icon')->setType('info');
				$folderIcon = htmlBase::newElement('icon')->setType('folderClosed');
				
				$tableGrid->addBodyRow(array(
					'rowAttr' => array(
						'data-topic_id' => $topicId
					),
					'columns' => array(
						array('text' => $namePrefix . $folderIcon->draw() . ' <span class="topicListing-name">' . $Topic['TopicsDescription'][(int)Session::get('languages_id')]['topics_name'] . '</span>'),
						array('text' => $arrowIcon->draw(), 'align' => 'center')
					)
				));
		
				$tableGrid->addBodyRow(array(
					'addCls' => 'gridInfoRow',
					'columns' => array(
						array(
							'colspan' => 2,
							'text' => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' . 
								'<tr>' . 
									'<td><b>' . sysLanguage::get('TEXT_DATE_ADDED') . '</b></td>' . 
									'<td> ' . tep_date_short($Topic['date_added']) . '</td>' . 
									'<td><b>' . sysLanguage::get('TEXT_LAST_MODIFIED') . '</b></td>' . 
									'<td>' . tep_date_short($Topic['last_modified']) . '</td>' .
									'<td></td>' .
								'</tr>' . 
								'<tr>' . 
									'<td><b>' . sysLanguage::get('TEXT_SUBTOPICS') . '</b></td>' . 
									'<td>'  . tep_childs_in_topic_count($topicId) . '</td>' . 
									'<td><b>' . sysLanguage::get('TEXT_ARTICLES') . '</b></td>' . 
									'<td>' . tep_articles_in_topic_count($topicId) . '</td>' .
									'<td></td>' .
								'</tr>' . 
							'</table>'
						)
					)
				));
				
				addTopicTreeToGrid($Topic['topics_id'], &$tableGrid, '&nbsp;&nbsp;&nbsp;' . $namePrefix);
			}
		}
	}

	$tableGrid = htmlBase::newElement('newGrid');

	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('New Root Topic')->addClass('newButton'),
		htmlBase::newElement('button')->setText('Add Child Topic')->addClass('newChildButton')->disable(),
		htmlBase::newElement('button')->setText('Edit')->addClass('editButton')->disable(),
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable()
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_TOPICS')),
			array('text' => 'info')
		)
	));

	addTopicTreeToGrid(0, $tableGrid, '');
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:100%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
 </div>