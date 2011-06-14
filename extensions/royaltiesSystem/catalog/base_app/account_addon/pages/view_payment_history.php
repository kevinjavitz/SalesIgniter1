<?php
/*
	Royalties Reports Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
$RoyaltiesSystemRoyaltiesPaidQuery = Doctrine_Query::create()
		->select('c.customers_firstname, c.customers_lastname, rp.royalty_amount_paid, rp.royalty_payment_date')
		->from('RoyaltiesSystemRoyaltiesPaid rp')
		->leftJoin('rp.Customers c')
		->where('content_provider_id = ?', (int)$userAccount->getCustomerId());

if (isset($_GET['end_date']) && tep_not_null($_GET['end_date'])){
	$RoyaltiesSystemRoyaltiesPaidQuery->andWhere('rp.royalty_payment_date <= ?', $_GET['end_date']);
}

$OverViewTable = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0)
		->addClass('royaltiesTable')
		->css(array(
		           'width' => '100%'
		      ));

$OverViewTableHeader = array(
	array('text' => sysLanguage::get('TABLE_HEADING_DATE_PAID')),
	array('text' => sysLanguage::get('TABLE_HEADING_AMOUNT_PAID'))
);

EventManager::notify('RoyaltiesPaymentHistoryTableAddHeader', &$OverViewTableHeader);

$OverViewTableHeader[] = array('text' => '&nbsp');

$OverViewTable->addHeaderRow(array(
                                  'addCls' => 'ui-widget-header ui-state-hover',
                                  'columns' => $OverViewTableHeader
                             ));

$Result = $RoyaltiesSystemRoyaltiesPaidQuery->execute(array(), Doctrine_Core::HYDRATE_RECORD);
if ($Result){
	if(!is_null($Result[0]['royalties_royalty_royalties_paid_id'])){
		foreach($Result as $rInfo){
			$OverViewTable->addBodyRow(array(
			                                'rowAttr' => array(),
			                                'columns' => array(
				                                array('text' => $rInfo['royalty_payment_date'], 'align' => 'center'),
				                                array('text' => $currencies->format($rInfo['royalty_amount_paid']), 'align' => 'center')
			                                )
			                           ));
		}
	}
}
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
		           'margin-right' => '20px'
		      ));


$enddateField = htmlBase::newElement('input')
		->setName('end_date')
		->setLabel(sysLanguage::get('HEADING_TITLE_START_END'))
		->setLabelPosition('before')
		->setId('end_date')
		->css(array(
		           'margin-right' => '20px'
		      ));

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


echo $searchForm->draw();
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
	                                array('text' => $OverViewTable->draw(), 'colspan' => '2')
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
?>