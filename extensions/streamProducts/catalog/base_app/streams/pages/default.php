<?php
	$ListingTable = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0);
	
	$Qhistory = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.OrdersProductsStream ops')
	->leftJoin('ops.ProductsStreams ps')
	->leftJoin('o.OrdersStatus os')
	->leftJoin('os.OrdersStatusDescription osd')
	->where('o.customers_id = ?', $userAccount->getCustomerId())
	->andWhere('osd.language_id = ?', Session::get('languages_id'))
	->andWhere('op.purchase_type = ?', 'stream')
	->andWhere('(ops.stream_maxdays = 0 OR DATE_ADD(o.date_purchased, INTERVAL ops.stream_maxdays DAY) > now()) AND TRUE')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qhistory){
		$ListingTable->addClass('ui-widget ui-widget-content')
		->css(array(
			'width' => '98%'
		));

		$ListingTable->addHeaderRow(array(
			'addCls' => 'ui-widget-header',
			'columns' => array(
				array('align' => 'left', 'css' => array('color' => '#FFFFFF'), 'text' => sysLanguage::get('TABLE_HEADING_PRODUCTS_NAME')),
				array('align' => 'left', 'css' => array('color' => '#FFFFFF'), 'text' => sysLanguage::get('TABLE_HEADING_FILENAME')),
				array('align' => 'left', 'css' => array('color' => '#FFFFFF'), 'text' => sysLanguage::get('TABLE_HEADING_DATE_PURCHASED')),
				array('align' => 'left', 'css' => array('color' => '#FFFFFF'), 'text' => sysLanguage::get('TABLE_HEADING_DATE_EXPIRES')),
				array('css' => array('color' => '#FFFFFF'), 'text' => sysLanguage::get('TABLE_HEADING_ACTION')),
				//array('css' => array('color' => '#FFFFFF'), 'text' => sysLanguage::get('TABLE_HEADING_ORDER_DETAILS')),
			)
		));
		//echo '<pre>';print_r($Qhistory);echo '</pre>';

		$sArr = sysConfig::explode('EXTENSION_STREAMPRODUCTS_ORDERS_STATUS', ',');
		foreach($Qhistory as $oInfo){
			foreach($oInfo['OrdersProducts'] as $opInfo){
				//echo '<pre>';print_r($opInfo);echo '</pre>';
				if (!isset($opInfo['OrdersProductsStream']) || empty($opInfo['OrdersProductsStream'])) continue;
				
				foreach($opInfo['OrdersProductsStream'] as $opsInfo){
					$expires = '';
					$sInfo = $opsInfo['ProductsStreams'];

					if ($opsInfo['stream_maxdays'] == '0'){
						$expires = sysLanguage::get('TEXT_INFO_EXPIRES_NEVER');
					}else{
						$addDays = $opsInfo['stream_maxdays'];
						$dateArr = date_parse($oInfo['date_purchased']);
						$time = mktime(0,0,0,$dateArr['month'],$dateArr['day'],$dateArr['year']);
						$expires = date('Y-m-d', strtotime('+ ' . $addDays . ' day', $time));
					}

					$streamButton = htmlBase::newElement('button')
					->setText(sysLanguage::get('TEXT_BUTTON_VIEW_STREAM'))
					->setHref(itw_app_link('appExt=streamProducts&oID=' . $oInfo['orders_id'] . '&opID=' . $opInfo['orders_products_id'] . '&sID=' . $sInfo['stream_id'], 'streams', 'view'))
					->css(array(
						'font-size' => '.8em'
					))
					->draw();
					
					$orderButton = htmlBase::newElement('button')
					->setText(sysLanguage::get('TEXT_BUTTON_VIEW_ORDER'))
					->setHref(itw_app_link('order_id=' . $oInfo['orders_id'], 'account', 'history_info'))
					->css(array(
						'font-size' => '.8em'
					))
					->draw();
					
					$ext = substr($sInfo['file_name'], strpos($sInfo['file_name'], '.'), strlen($sInfo['file_name']));
					$filename = 'stream_file_' . $sInfo['stream_id'] . $ext;

					if (!in_array($oInfo['orders_status'], $sArr)){
						$streamButton = sysLanguage::get('TEXT_INFO_PENDING_APPROVAL');
					}
					
					$ListingTable->addBodyRow(array(
						'columns' => array(
							array('text' => $opInfo['products_name']),
							array('text' => (!empty($sInfo['display_name']) ? $sInfo['display_name'] : $filename)),
							array('text' => tep_date_short($oInfo['date_purchased'])),
							array('text' => $expires),
							//array('align' => 'center', 'text' => $streamButton),
							array('align' => 'center', 'text' => $streamButton . '<br>' . $orderButton)
						)
					));
				}
			}
		}
	}else{
		$ListingTable->addBodyRow(array(
			'columns' => array(
				array('text' => sysLanguage::get('TEXT_NO_PURCHASES')),
			)
		));
	}
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_STREAMS');
	$pageContents = $ListingTable->draw();
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null, 'account', 'default', 'SSL'))
	->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
