<?php
	$Qpages = Doctrine_Query::create()
	->from('Pages p')
	->leftJoin('p.PagesFields pf')
	->where('p.page_type = ?', 'field')
	->orderBy('p.page_key');

	$tableGrid = htmlBase::newElement('grid')
	->usePagination(false)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 0))
	->setQuery($Qpages);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_PAGES_KEY')),
			array('text' => sysLanguage::get('TABLE_HEADING_INFOBOX')),
			array('text' => sysLanguage::get('TABLE_HEADING_STATUS')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$pages = $tableGrid->getResults();
	if ($pages){
		$editButton = htmlBase::newElement('button')
		->setText('Edit')
		->css(array(
			'font-size' => '.8em'
		));

		$deleteButton = htmlBase::newElement('button')
		->addClass('deleteButton')
		->usePreset('delete')
		->css(array(
			'font-size' => '.8em'
		));

		foreach($pages as $page){
			$pageId = $page['pages_id'];

			$statusIcon = htmlBase::newElement('icon');
			if ($page['status'] == '1') {
				$statusIcon->setType('circleCheck')->setTooltip('Click to disable')
				->setHref(itw_app_link('appExt=infoPages&action=setflag&flag=0&pID=' . $pageId));
			} else {
				$statusIcon->setType('circleClose')->setTooltip('Click to enable')
				->setHref(itw_app_link('appExt=infoPages&action=setflag&flag=1&pID=' . $pageId));
			}

			$infoBoxIcon = htmlBase::newElement('icon');
			if ($page['infobox_status'] == '1') {
				$infoBoxIcon->setType('circleCheck')->setTooltip('Click to disable')
				->setHref(itw_app_link('appExt=infoPages&action=setiflag&flag=0&pID=' . $pageId));
			} else {
				$infoBoxIcon->setType('circleClose')->setTooltip('Click to enable')
				->setHref(itw_app_link('appExt=infoPages&action=setiflag&flag=1&pID=' . $pageId));
			}

			$editButton->setHref(itw_app_link('appExt=infoPages&pID=' . $pageId, 'manage', 'newFieldPage'));
			$deleteButton->attr('data-page_id', $pageId);

			$tableGrid->addBodyRow(array(
				'columns' => array(
					array('text' => $page['page_key']),
					array('text' => $infoBoxIcon->draw(), 'align' => 'center'),
					array('text' => $statusIcon->draw(), 'align' => 'center'),
					array('text' => $editButton->draw() . $deleteButton->draw(), 'align' => 'right')
				)
			));
		}
	}
	EventManager::attachActionResponse($tableGrid->draw(), 'html');
?>