<?php
	$OverViewTable = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0)
	->addClass('royaltiesTable')
	->css(array(
		'width' => '100%'
	));
				
	$OverViewTableHeader = array(
		array('text' => '&nbsp;'),
		array('text' => sysLanguage::get('TABLE_HEADING_PRODUCTS_NAME')),
		array('text' => sysLanguage::get('TABLE_HEADING_CONTENT')),
		array('css' => array('text-align' => 'center'),'text' => sysLanguage::get('TABLE_HEADING_TOTAL_VIEWS')),
		array('css' => array('text-align' => 'right'), 'text' => sysLanguage::get('TABLE_HEADING_ROYALTY'))
	);
				
	EventManager::notify('RoyaltiesOverviewTableAddHeader', &$OverViewTableHeader);
				
	$OverViewTableHeader[] = array('text' => '&nbsp');
				
	$OverViewTable->addHeaderRow(array(
		'addCls' => 'ui-widget-header ui-state-hover',
		'columns' => $OverViewTableHeader
	));
				
	$QcustomerStreams = Doctrine_Query::create()
	->from('ProductsStreams')
	->where('content_provider_id = ?', (int)$userAccount->getCustomerId())
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($QcustomerStreams){
		foreach($QcustomerStreams as $Stream){
			$Qroyalties = Doctrine_Query::create()
			->select('SUM(royalty) as totalEarned, COUNT(streaming_id) as totalViews')
			->from('RoyaltiesSystemViews')
			->where('streaming_id = ?', (int)$Stream['stream_id'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						
			$QproductName = Doctrine_Query::create()
			->select('products_name')
			->from('ProductsDescription')
			->where('products_id = ?', (int) $Stream['products_id'])
			->andWhere('language_id = ?', Session::get('languages_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						
			if ($Qroyalties[0]['totalEarned'] > 0){
				$icon = htmlBase::newElement('icon')
				->addClass('ui-icon-green')
				->attr('data-stream_id', (int) $Stream['stream_id'])
				->attr('data-customer_id', (int) $userAccount->getCustomerId())
				->setType('plusThick')
				->setTooltip('Click to see all views')
				->draw();
			}else{
				$icon = '&nbsp;';
			}
						
			$OverViewTableBody = array(
				array('addCls' => 'first', 'text' => $icon),
				array('text' => $QproductName[0]['products_name']),
				array('text' => $Stream['display_name']),
				array('css' => array('text-align' => 'center'), 'text' => $Qroyalties[0]['totalViews']),
				array('css' => array('text-align' => 'right'), 'text' => $currencies->format($Qroyalties[0]['totalEarned']))
			);
				
			EventManager::notify('RoyaltiesOverviewTableAddBody', &$OverViewTableBody);
			
			$OverViewTableBody[] = array('addCls' => 'last', 'text' => '&nbsp');
				
			$OverViewTable->addBodyRow(array(
				'columns' => $OverViewTableBody
			));
		}
	}
	
	$pageTitle = sysLanguage::get('HEADING_TITLE');
	$pageContents = $OverViewTable->draw();
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null,'account','default'))
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'create_account',
		'action' => itw_app_link('action=createAccount', 'account', 'create', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
