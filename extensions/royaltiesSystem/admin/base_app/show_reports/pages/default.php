<?php
/*
	Royalties Reports Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$Qroyal = Doctrine_Query::create()
	->from('RoyaltiesSystemRoyaltiesEarned r')
	->leftJoin('r.Customers c')
	->leftJoin('r.Products p')
	->leftJoin('p.ProductsDescription pd')
	->where('pd.language_id = "' . Session::get('languages_id') . '" AND TRUE')
	->addOrderBy('r.date_added desc');
	
	if (isset($_GET['start_date']) && tep_not_null($_GET['start_date'])){
		$Qroyal->andWhere('r.date_added >= ?', $_GET['start_date']);
	} else {
		$Qroyal->andWhere('r.date_added >= ?', date('Y-m-d',strtotime('-30 DAYS')));
	}
	
	if (isset($_GET['end_date']) && tep_not_null($_GET['end_date'])){
		$Qroyal->andWhere('r.date_added <= ?', $_GET['end_date']);
	}
	
	if (isset($_GET['cnt_provider']) && tep_not_null($_GET['cnt_provider']) ){
		$Qroyal->andWhere('r.content_provider_id = ?', $_GET['cnt_provider']);
	}
	
	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 0))
	->setQuery($Qroyal);
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('View Totals')->setHref(itw_app_link('appExt=royaltiesSystem', 'show_reports', 'totals')),
		htmlBase::newElement('button')->setText('Payment Report')->setHref(itw_app_link('appExt=royaltiesSystem', 'show_reports', 'paymentReport'))
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_DATE_ADDED')),
			array('text' => sysLanguage::get('TABLE_HEADING_ORDERS_ID')),
			array('text' => sysLanguage::get('TABLE_HEADING_CONTENT')),
			array('text' => sysLanguage::get('TABLE_HEADING_CONTENT_TYPE')),
			array('text' => sysLanguage::get('TABLE_HEADING_PRODUCT_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_ROYALTY_FEE')),
			array('text' => sysLanguage::get('TABLE_HEADING_CONTENT_OWNER'))
		)
	));

	$Result = $tableGrid->getResults();
	if ($Result){
		foreach($Result as $rInfo){
			switch($rInfo['purchase_type']){
				case 'new':
					$contentType = 'New Product Sale';
					$displayName = $rInfo['Products']['ProductsDescription'][Session::get('languages_id')]['products_name'];
					$ordersLink = htmlBase::newElement('a')->html($rInfo['orders_id'])->setHref(itw_app_link('oID='.$rInfo['orders_id'],'orders', 'details', 'SSL'))->draw();
					break;
				case 'used':
					$contentType = 'Used Product Sale';
					$displayName = $rInfo['Products']['ProductsDescription'][Session::get('languages_id')]['products_name'];
					$ordersLink = htmlBase::newElement('a')->html($rInfo['orders_id'])->setHref(itw_app_link('oID='.$rInfo['orders_id'],'orders', 'details', 'SSL'))->draw();
					break;
				case 'stream':
					$contentType = 'Stream';
					$displayName = $rInfo['Products']['ProductsDescription'][Session::get('languages_id')]['products_name'];
					$ordersLink = htmlBase::newElement('a')->html($rInfo['orders_id'])->setHref(itw_app_link('oID='.$rInfo['orders_id'],'orders', 'details', 'SSL'))->draw();
					break;
				case 'download':
					$contentType = 'Download';
					$displayName = $rInfo['Products']['ProductsDescription'][Session::get('languages_id')]['products_name'];
					$ordersLink = htmlBase::newElement('a')->html($rInfo['orders_id'])->setHref(itw_app_link('oID='.$rInfo['orders_id'],'orders', 'details', 'SSL'))->draw();
					break;
				case 'rental':
					$contentType = 'Member Rental';
					$displayName = $rInfo['Products']['ProductsDescription'][Session::get('languages_id')]['products_name'];
					$ordersLink = 'Rental';
					break;
			}
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-stream_id' => $rInfo['streaming_id'],
					'data-download_id' => $rInfo['download_id']
				),
				'columns' => array(
					array('text' => $rInfo['date_added']),
					array('text' => $ordersLink),
					array('text' => $displayName),
					array('text' => $contentType),
					array('text' => $rInfo['Products']['ProductsDescription'][Session::get('languages_id')]['products_name']),
					array('text' => $currencies->format($rInfo['royalty']), 'align' => 'center'),
					array('text' => $rInfo['Customers']['customers_firstname'] . ' ' . $rInfo['Customers']['customers_lastname'], 'align' => 'center')

				)
			));
		}
	}
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />
<div>
<?php
	$searchForm = htmlBase::newElement('form')
	->attr('name', 'search')
	->attr('action', itw_app_link(tep_get_all_get_params()))
	->attr('method', 'get');
	
	$startdateField = htmlBase::newElement('input')
	->setName('start_date')
	->setLabel(sysLanguage::get('HEADING_TITLE_START_DATE'))
	->setLabelPosition('before')
	->setId('start_date')
	->css(array(
		'margin-right'	=> '20px'
	));
	
	$enddateField = htmlBase::newElement('input')
	->setName('end_date')
	->setLabel(sysLanguage::get('HEADING_TITLE_START_END'))
	->setLabelPosition('before')
	->setId('end_date')
	->css(array(
		'margin-right'	=> '20px'
	));
	
	$cntProviderField = htmlBase::newElement('selectbox')
	->setName('cnt_provider')
	->setLabel(sysLanguage::get('HEADING_TITLE_CNT_PROVIDER'))
	->setLabelPosition('before')
	->setId('cnt_provider')
	->css(array(
		'margin-right' => '20px'
	));

	$cntProviderField->addOption('', 'All Providers');

	$Qproviders = Doctrine_Query::create()
	->select('customers_id, CONCAT(customers_firstname, " ", customers_lastname) as customers_name')
	->from('Customers')
	->where('is_content_provider = ?', '1')
	->orderBy('customers_name')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qproviders){
		foreach($Qproviders as $pInfo){
			$cntProviderField->addOption($pInfo['customers_id'], $pInfo['customers_name']);
		}
	}
	
	$gobut = htmlBase::newElement('button')
	->setType('submit')
	->setText('Submit');
	
	if (isset($_GET['start_date'])){
		$startdateField->setValue($_GET['start_date']);
	} else {
		$startdateField->setValue(date('Y-m-d',strtotime('-30 DAYS')));
	}
	
	if (isset($_GET['end_date'])){
		$enddateField->setValue($_GET['end_date']);
	}
	
	if (isset($_GET['cnt_provider'])){
		$cntProviderField->selectOptionByValue($_GET['cnt_provider']);
	}
	
	$searchForm->append($startdateField);
	$searchForm->append($enddateField);
	$searchForm->append($cntProviderField);
	$searchForm->append($gobut);
	
	echo $searchForm->draw();
?>
</div>
<div style="width:100%;float:left;">
	<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
		<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
	</div>
<?php
	if (isset($_GET['search']) && tep_not_null($_GET['search'])) {
?>
	<div style="text-align:right;"><?php
		$resetButton = htmlBase::newElement('button')
		->setText(sysLanguage::get('TEXT_BUTTON_RESET'))
		->setHref(itw_app_link(null, null, 'default'));
		echo $resetButton->draw();
	?></div>
<?php
	}
?>
</div>