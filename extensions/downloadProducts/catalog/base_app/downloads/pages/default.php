<?php
	$ListingTable = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0);
	
	$Qhistory = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.OrdersProductsDownload opd')
	->leftJoin('opd.ProductsDownloads pd')
	->leftJoin('o.OrdersStatus os')
	->leftJoin('os.OrdersStatusDescription osd')
	->where('o.customers_id = ?', $userAccount->getCustomerId())
	->andWhere('osd.language_id = ?', Session::get('languages_id'))
	->andWhere('op.purchase_type = ?', 'download')
	->andWhere('opd.orders_products_id = op.orders_products_id')
	->andWhere('pd.download_id = opd.download_id')
	->andWhere('opd.download_count < opd.download_maxcount')
	->andWhere('(opd.download_maxdays = 0 OR DATE_ADD(o.date_purchased, INTERVAL opd.download_maxdays DAY) > now()) AND TRUE')
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
				array('css' => array('color' => '#FFFFFF'), 'text' => sysLanguage::get('TABLE_HEADING_ACTION'))
			)
		));
		//echo '<pre>';print_r($Qhistory);echo '</pre>';

		$sArr = sysConfig::explode('EXTENSION_DOWNLOADPRODUCTS_ORDERS_STATUS', ',');
		foreach($Qhistory as $oInfo){
			foreach($oInfo['OrdersProducts'] as $opInfo){
				//echo '<pre>';print_r($opInfo);echo '</pre>';
				if (!isset($opInfo['OrdersProductsDownload']) || empty($opInfo['OrdersProductsDownload'])) continue;
				
				foreach($opInfo['OrdersProductsDownload'] as $opdInfo){
					$expires = '';
					$addDays = '0';
					$dInfo = $opdInfo['ProductsDownloads'];
					
					if ($opdInfo['download_maxdays'] == '0'){
						if ($opdInfo['download_maxcount'] > 0){
							$expires = ($opdInfo['download_maxcount'] - $opdInfo['download_count']) . ' More Downloads';
						}else{
							$expires = sysLanguage::get('ACCOUNT_DOWNLOADS_NEVER');
						}
					}else{
						$addDays = $opdInfo['download_maxdays'];
					}
					
					if (!empty($addDays)){
						$dateArr = date_parse($oInfo['date_purchased']);
						$time = mktime(0,0,0,$dateArr['month'],$dateArr['day'],$dateArr['year']);
						$expires = date(sysLanguage::getDateFormat(), strtotime('+ ' . $addDays . ' day', $time));
						if ($opdInfo['download_maxcount'] > 0){
							$expires .= '<br>Or<br>' . ($opdInfo['download_maxcount'] - $opdInfo['download_count']) . ' More Downloads';
						}
					}

					$downloadButton = htmlBase::newElement('button')
					->setText(sysLanguage::get('TEXT_BUTTON_DOWNLOAD'))
					->setHref(itw_app_link('appExt=downloadProducts&action=downloadFile&&oID=' . $oInfo['orders_id'] . '&opID=' . $opInfo['orders_products_id'] . '&dID=' . $dInfo['download_id'], 'main', 'default'))
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
					
					$ext = substr($dInfo['file_name'], strpos($dInfo['file_name'], '.'), strlen($dInfo['file_name']));
					$filename = 'download_file_' . $dInfo['download_id'] . $ext;

					if (!in_array($oInfo['orders_status'], $sArr)){
						$downloadButton = sysLanguage::get('TEXT_INFO_PENDING_APPROVAL');
					}
					
					$ListingTable->addBodyRow(array(
						'columns' => array(
							array('text' => $opInfo['products_name']),
							array('text' => (!empty($dInfo['display_name']) ? $dInfo['display_name'] : $filename)),
							array('text' => tep_date_short($oInfo['date_purchased'])),
							array('text' => $expires),
							array('align' => 'center', 'text' => $downloadButton . '<br>' . $orderButton)
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
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_DOWNLOADS');
	$pageContents = $ListingTable->draw();
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null, 'account', 'default', 'SSL'))
	->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
