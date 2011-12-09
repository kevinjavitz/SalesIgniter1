<?php
$OverViewTable = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0)
		->addClass('royaltiesTable')
		->css(array(
		           'width' => '100%'
		      ));

$OverViewTableHeader = array(
	array('text' => sysLanguage::get('TABLE_HEADING_DATE_ADDED'), 'css' => array('padding' => '5px')),
	array('text' => sysLanguage::get('TABLE_HEADING_ORDERS_ID'), 'css' => array('padding' => '5px')),
	array('text' => sysLanguage::get('TABLE_HEADING_CONTENT'), 'css' => array('padding' => '5px')),
	array('text' => sysLanguage::get('TABLE_HEADING_CONTENT_TYPE'), 'css' => array('padding' => '5px')),
	array('text' => sysLanguage::get('TABLE_HEADING_PRODUCT_NAME'), 'css' => array('padding' => '5px')),
	array('text' => sysLanguage::get('TABLE_HEADING_ROYALTY_FEE'), 'css' => array('padding' => '5px')),
);

EventManager::notify('RoyaltiesOverviewTableAddHeader', &$OverViewTableHeader);

$OverViewTableHeader[] = array('text' => '&nbsp');

$OverViewTable->addHeaderRow(array(
	'addCls' => 'ui-widget-header pageHeaderContainer',
	'columns' => $OverViewTableHeader
));

$QcustomerProducts = Doctrine_Query::create()
		->from('RoyaltiesSystemRoyaltiesEarned r')
		->leftJoin('r.Customers c')
		->leftJoin('r.Products p')
		->leftJoin('p.ProductsDescription pd')
		->where('pd.language_id = "' . Session::get('languages_id') . '" ')
		->andWhere('content_provider_id = ?', (int)$userAccount->getCustomerId())
		->addOrderBy('r.date_added desc');
if (isset($_GET['start_date']) && tep_not_null($_GET['start_date'])){
	$QcustomerProducts->andWhere('r.date_added >= ?', $_GET['start_date']);
} else {
	$QcustomerProducts->andWhere('r.date_added >= ?', date('Y-m-d',strtotime('-30 DAYS')));
}

if (isset($_GET['end_date']) && tep_not_null($_GET['end_date'])){
	$QcustomerProducts->andWhere('r.date_added <= ?', $_GET['end_date']);
}
$royalties = $QcustomerProducts->execute(array(), Doctrine_Core::HYDRATE_RECORD);
if ($royalties){
	foreach($royalties as $rInfo){
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
		//var_dump($rInfo['Products']['ProductsDescription']);
		//die();
		$OverViewTableBody = array(
			array('addCls' => 'first', 'text' => $rInfo['date_added']),
			array('text' => $ordersLink),
			array('text' => $displayName),
			array('text' => $contentType),
			array('text' => $rInfo['Products']['ProductsDescription'][Session::get('languages_id')]['products_name']),
			array('text' => $currencies->format($rInfo['royalty']), 'align' => 'center')
		);

		$OverViewTableBody[] = array('addCls' => 'last', 'text' => '&nbsp');

		$OverViewTable->addBodyRow(array(
		                                'columns' => $OverViewTableBody
		                           ));
	}
}
$startdateField = htmlBase::newElement('input')
		->setName('start_date')
		->setLabel(sysLanguage::get('HEADING_TITLE_START_DATE'))
		->setLabelPosition('before')
		->setId('start_date')
		->setValue(date('Y-m-d',strtotime('-30 DAYS')))
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
$gobut = htmlBase::newElement('button')
		->setType('submit')
		->setText('Submit');

$mainPageTable = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0)
		->addClass('royaltiesTable')
		->css(array(
		           'width' => '100%'
		      ));
$mainPageTable->addBodyRow(array(
                                'columns' => array(
	                                array('text' => $startdateField->draw()),
	                                array('text' =>$enddateField->draw()),
	                                array('text' =>$gobut->draw())
                                )
                           ));
$mainPageTable->addBodyRow(array(
                                'columns' => array(
	                                array('text' => $OverViewTable->draw(), 'colspan' => '3')
                                )
                           ));
$pageTitle = sysLanguage::get('HEADING_TITLE');
$pageContents = $mainPageTable->draw();

$pageButtons = htmlBase::newElement('button')
		->usePreset('back')
		->setHref(itw_app_link(null,'account','default'))
		->draw();

$pageContent->set('pageForm', array(
                                   'name' => 'search',
                                   'action' => itw_app_link(tep_get_all_get_params()),
                                   'method' => 'get'
                              ));

$pageContent->set('pageTitle', $pageTitle);
$pageContent->set('pageContent', $pageContents);
$pageContent->set('pageButtons', $pageButtons);