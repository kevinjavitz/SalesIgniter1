<?php
$OverViewTable = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0)
		->addClass('royaltiesTable')
		->css(array(
		           'width' => '100%'
		      ));
$OverViewTable1 = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0)
		->addClass('royaltiesTable')
		->css(array(
		           'width' => '100%'
		      ));

$OverViewTableHeader = array(
	array('text' => sysLanguage::get('TABLE_HEADING_DATE_ADDED')),
	array('text' => sysLanguage::get('TABLE_HEADING_ORDERS_ID')),
	//array('text' => sysLanguage::get('TABLE_HEADING_CONTENT')),
	array('text' => sysLanguage::get('TABLE_HEADING_CONTENT_TYPE')),
	array('text' => sysLanguage::get('TABLE_HEADING_PRODUCT_NAME')),
	array('text' => sysLanguage::get('TABLE_HEADING_POINTS_EARNED')),
);
$OverViewTableHeader1 = array(
	array('text' => sysLanguage::get('TABLE_HEADING_DATE_ADDED')),
	array('text' => sysLanguage::get('TABLE_HEADING_ORDERS_ID')),
	//array('text' => sysLanguage::get('TABLE_HEADING_CONTENT')),
	array('text' => sysLanguage::get('TABLE_HEADING_CONTENT_TYPE')),
	array('text' => sysLanguage::get('TABLE_HEADING_PRODUCT_NAME')),
	array('text' => sysLanguage::get('TABLE_HEADING_POINTS_DEDUCTED')),
);

//EventManager::notify('RoyaltiesOverviewTableAddHeader', &$OverViewTableHeader);

//$OverViewTableHeader[] = array('text' => '&nbsp');




$QpointsEarned = Doctrine_Query::create()
		->from('pointsRewardsPointsEarned r')
		->leftJoin('r.Customers c')
		->where('customers_id = ?', (int)$userAccount->getCustomerId())
		->addOrderBy('r.date desc');
$QpointsDeducted = Doctrine_Query::create()
		->from('pointsRewardsPointsDeducted r')
		->leftJoin('r.Customers c')
		->where('customers_id = ?', (int)$userAccount->getCustomerId())
		->addOrderBy('r.date desc');

if (isset($_GET['start_date']) && tep_not_null($_GET['start_date'])){
	$QpointsEarned->andWhere('r.date >= ?', $_GET['start_date']);
	$QpointsDeducted->andWhere('r.date >= ?', $_GET['start_date']);
} else {
	$QpointsEarned->andWhere('r.date >= ?', date('Y-m-d',strtotime('-30 DAYS')));
	$QpointsDeducted->andWhere('r.date >= ?', date('Y-m-d',strtotime('-30 DAYS')));
}

if (isset($_GET['end_date']) && tep_not_null($_GET['end_date'])){
	$QpointsEarned->andWhere('r.date <= ?', $_GET['end_date']);
	$QpointsDeducted->andWhere('r.date <= ?', $_GET['end_date']);
}
$pointsEarned = $QpointsEarned->execute(array(), Doctrine_Core::HYDRATE_RECORD);
$pointsDeducted = $QpointsDeducted->execute(array(), Doctrine_Core::HYDRATE_RECORD);
if ($pointsEarned){
    $OverViewTable->addHeaderRow(array(
        'addCls' => 'ui-widget-header ui-state-hover',
        'columns' => $OverViewTableHeader
    ));
	foreach($pointsEarned as $rInfo){
        if($rInfo['products_id'] > 0){
            $productInfo = Doctrine_Query::create()
                    ->from('Products p')
                    ->leftJoin('p.ProductsDescription pd')
                    ->where('pd.language_id = "' . Session::get('languages_id') . '" ');
            $ordersLink = $rInfo['orders_id'];
            switch($rInfo['purchase_type']){
                case 'new':
                    $contentType = 'New Product Sale';
                    $displayName = $productInfo['ProductsDescription'][Session::get('languages_id')]['products_name'];
                    break;
                case 'used':
                    $contentType = 'Used Product Sale';
                    $displayName = $productInfo['ProductsDescription'][Session::get('languages_id')]['products_name'];
                    break;
                case 'stream':
                    $contentType = 'Stream';
                    $displayName = $productInfo['ProductsDescription'][Session::get('languages_id')]['products_name'];
                    break;
                case 'download':
                    $contentType = 'Download';
                    $displayName = $productInfo['ProductsDescription'][Session::get('languages_id')]['products_name'];
                    break;
                case 'rental':
                    $contentType = 'Member Rental';
                    $displayName = $productInfo['ProductsDescription'][Session::get('languages_id')]['products_name'];
                    $ordersLink = 'Rental';
                    break;
                case 'reservation':
                    $contentType = 'Pay per Rental';
                    $displayName = $productInfo['ProductsDescription'][Session::get('languages_id')]['products_name'];
                    //$ordersLink = 'Rental';
                    break;
            }
        } else {
            $contentType = 'Credited By Admin';
            $displayName = '&nbsp;';
        }
        
		$OverViewTableBody = array(
			array('text' => $rInfo['date'], 'align' => 'center'),
			array('text' => $ordersLink, 'align' => 'center'),
			array('text' => $contentType, 'align' => 'center'),
			array('text' => $displayName, 'align' => 'center'),
			array('text' => $rInfo['points'], 'align' => 'center')
		);

		$OverViewTableBody[] = array('addCls' => 'last', 'text' => '&nbsp');

		$OverViewTable->addBodyRow(array(
		                                'columns' => $OverViewTableBody
		                           ));
	}
}
//for deducted points
if ($pointsDeducted){
    $OverViewTable1->addHeaderRow(array(
        'addCls' => 'ui-widget-header ui-state-hover',
        'columns' => $OverViewTableHeader1
    ));
	foreach($pointsDeducted as $rInfo){
        if($rInfo['products_id'] > 0){
            $productInfo = Doctrine_Query::create()
                    ->from('Products p')
                    ->leftJoin('p.ProductsDescription pd')
                    ->where('pd.language_id = "' . Session::get('languages_id') . '" ');
            $ordersLink = $rInfo['orders_id'];
            switch($rInfo['purchase_type']){
                case 'new':
                    $contentType = 'New Product Sale';
                    $displayName = $productInfo['ProductsDescription'][Session::get('languages_id')]['products_name'];
                    break;
                case 'used':
                    $contentType = 'Used Product Sale';
                    $displayName = $productInfo['ProductsDescription'][Session::get('languages_id')]['products_name'];
                    break;
                case 'stream':
                    $contentType = 'Stream';
                    $displayName = $productInfo['ProductsDescription'][Session::get('languages_id')]['products_name'];
                    break;
                case 'download':
                    $contentType = 'Download';
                    $displayName = $productInfo['ProductsDescription'][Session::get('languages_id')]['products_name'];
                    break;
                case 'rental':
                    $contentType = 'Member Rental';
                    $displayName = $productInfo['ProductsDescription'][Session::get('languages_id')]['products_name'];
                    $ordersLink = 'Rental';
                    break;
                case 'reservation':
                    $contentType = 'Pay per Rental';
                    $displayName = $productInfo['ProductsDescription'][Session::get('languages_id')]['products_name'];
                    //$ordersLink = 'Rental';
                    break;
            }
        } else {
            $contentType = 'Deducted By Admin';
            $displayName = '&nbsp;';
        }
		$OverViewTableBody1 = array(
			array('text' => $rInfo['date'], 'align' => 'center'),
			array('text' => $ordersLink, 'align' => 'center'),
			array('text' => $contentType, 'align' => 'center'),
			array('text' => $displayName, 'align' => 'center'),
			array('text' => $rInfo['points'], 'align' => 'center')
		);

		$OverViewTableBody1[] = array('addCls' => 'last', 'text' => '&nbsp');

		$OverViewTable1->addBodyRow(array(
		                                'columns' => $OverViewTableBody1
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
if (isset($_GET['start_date']) && tep_not_null($_GET['start_date'])){
	$startdateField->setValue($_GET['start_date']);
}
$enddateField = htmlBase::newElement('input')
		->setName('end_date')
		->setLabel(sysLanguage::get('HEADING_TITLE_START_END'))
		->setLabelPosition('before')
		->setId('end_date')

		->css(array(
		           'margin-right'	=> '20px'
		      ));
if (isset($_GET['end_date']) && tep_not_null($_GET['end_date'])){
	$enddateField->setValue($_GET['end_date']);
}
$mainPageTable = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0)
		->addClass('royaltiesTable')
		->css(array(
		           'width' => '100%'
		      ));
$gobut = htmlBase::newElement('button')
		->setType('submit')
		->setText('Submit');

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
$mainPageTable->addBodyRow(array(
                                'columns' => array(
									array('addCls' => 'ui-widget-header ui-widget-header-text','text'=>sysLanguage::get('HEADING_POINTS_DEDUCTED'), 'colspan' => '3')
									)));
$mainPageTable->addBodyRow(array(
                                'columns' => array(
	                                array('text' => $OverViewTable1->draw(), 'colspan' => '3')
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