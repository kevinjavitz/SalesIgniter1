<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_KEYWORDS');
?></div>
<br />
<?php
	if (!isset($_GET['wordList'])){
?>
<div style="text-align:right;"><?php
	echo htmlBase::newElement('button')
	->setText(sysLanguage::get('TEXT_BUTTON_UPDATE_WORD_LIST'))
	->setHref(itw_app_link('action=updateSearchWordList'))
	->draw();
	echo htmlBase::newElement('button')
	->setText(sysLanguage::get('TEXT_BUTTON_CLEAR_WORD_LIST'))
	->setHref(itw_app_link('action=clearSearchWords'))
	->draw();
	echo htmlBase::newElement('button')
	->setText(sysLanguage::get('TEXT_BUTTON_VIEW_WORD_LIST'))
	->setHref(itw_app_link('wordList=1'))
	->draw();
?></div>
<br />
<?php
	}
	
	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->useSorting(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1));
	if (isset($_GET['wordList'])){
		$Qwords = Doctrine_Query::create()
		->from('SearchwordSwap');

		$tableGrid->setQuery($Qwords);

		$tableGrid->addHeaderRow(array(
			'columns' => array(
				array('allowSort' => true, 'sortKey' => 'sws_word', 'text' => sysLanguage::get('TABLE_HEADING_ORIGINAL_WORDS')),
				array('allowSort' => true, 'sortKey' => 'sws_replacement', 'text' => sysLanguage::get('TABLE_HEADING_REPLACEMENT_WORDS')),
				array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
			)
		));

		$Result = &$tableGrid->getResults();
		if ($Result){
			foreach($Result as $wInfo){
				$editButton = htmlBase::newElement('button')
				->usePreset('edit')
				->attr('data-word_id', $wInfo['sws_id'])
				->css(array(
					'font-size' => '.8em'
				))
				->addClass('editButton');
				
				$deleteButton = htmlBase::newElement('button')
				->usePreset('delete')
				->attr('data-word_id', $wInfo['sws_id'])
				->css(array(
					'font-size' => '.8em'
				))
				->addClass('deleteButton')
				->setHref(itw_app_link(tep_get_all_get_params(array('action')) . 'action=deleteWord'));
				
				$tableGrid->addBodyRow(array(
					'columns' => array(
						array('text' => stripslashes($wInfo['sws_word'])),
						array('text' => stripslashes($wInfo['sws_replacement'])),
						array('align' => 'right', 'text' => $editButton->draw() . $deleteButton->draw())
					)
				));
			}
		}
	}else{
		$Qwords = Doctrine_Query::create()
		->from('SearchQueriesSorted');
	
		$tableGrid->setQuery($Qwords);

		$tableGrid->addHeaderRow(array(
			'columns' => array(
				array('allowSort' => true, 'sortKey' => 'search_text', 'text' => sysLanguage::get('TABLE_HEADING_SEARCH_WORDS')),
				array('allowSort' => true, 'sortKey' => 'search_count', 'text' => sysLanguage::get('TABLE_HEADING_SEARCH_COUNT'))
			)
		));

		$Result = &$tableGrid->getResults();
		if ($Result){
			foreach($Result as $wInfo){
				$tableGrid->addBodyRow(array(
					'columns' => array(
						array('text' => '<a target="_blank" href="' . tep_catalog_href_link('advanced_search_result.php', 'keywords=' . urlencode($wInfo['search_text']). '&search_in_description=1' ) . '">' . $wInfo['search_text'] . '</a>'),
						array('text' => $wInfo['search_count'])
					)
				));
			}
		}
	}
?>
 <div style="width:100%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   </div>
  </div>
  <?php
  if (isset($_GET['wordList'])){
  ?>
  <div style="text-align:right;"><?php
  echo htmlBase::newElement('button')
  ->css(array(
  	'margin' => '.5em'
  ))
  ->usePreset('new')
  ->setText(sysLanguage::get('TEXT_BUTTON_NEW_ENTRY'))
  ->addClass('insertButton')
  ->draw();
  ?></div>
  <?php
  }
  ?>
 </div>