<?php
/*
	Royalties Reports Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Qroyal = Doctrine_Query::create()
	->select('r.streaming_id, r.download_id, r.content_provider_id, SUM(royalty) as total, c.customers_firstname, c.customers_lastname')
	->from('RoyaltiesSystemRoyaltiesEarned r')
	->leftJoin('r.Customers c')
	->addGroupBy('r.content_provider_id');
/*
	if (isset($_GET['start_date']) && tep_not_null($_GET['start_date'])){
		$Qroyal->andWhere('r.date_added >= ?', $_GET['start_date'] . ' 00:00:00');
	}
*/

	if (isset($_GET['end_date']) && tep_not_null($_GET['end_date'])){
		$Qroyal->andWhere('r.date_added <= ?', $_GET['end_date'] . ' 23:59:59');
	}

	if (isset($_GET['cnt_provider']) && tep_not_null($_GET['cnt_provider']) ){
		$Qroyal->andWhere('(c.customers_firstname LIKE "%' . $_GET['cnt_provider'] . '%" OR c.customers_lastname LIKE "%' . $_GET['cnt_provider'] . '%" OR c.customers_id = "' . $_GET['cnt_provider'] . '") AND TRUE');
	}
	$saveForm = htmlBase::newElement('form')
		->attr('name', 'royaltiesSaveStatus')
		->attr('action', itw_app_link('appExt=royaltiesSystem&action=savePaid', 'show_reports', 'totals'))
		->attr('method', 'post');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 0))
	->setQuery($Qroyal);

	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('Back To Report')->setHref(itw_app_link('appExt=royaltiesSystem', 'show_reports', 'default')),
		htmlBase::newElement('button')->setText('Pay Providers')->addClass('newButton')->setType('submit'),
		htmlBase::newElement('button')->setText('Payment Report')->setHref(itw_app_link('appExt=royaltiesSystem', 'show_reports', 'paymentReport'))
	));
	
	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => "Owed"),
			array('text' => "Content Provider"),
			array('text' => 'Pay' . '<input type="hidden" name="paid_end_date[]" value="' . (isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d') ) . '" />')
		)
	));

	$Result = $tableGrid->getResults();
	if ($Result){

		foreach($Result as $rInfo){
			if(!is_null($rInfo['royalties_earned_id'])){
				$RoyaltiesSystemRoyaltiesPaidRecords = Doctrine_Query::create()
						->select('SUM(royalty_amount_paid) as total_paid')
						->from('RoyaltiesSystemRoyaltiesPaid')
						->addWhere('content_provider_id = ?', $rInfo['content_provider_id'])
						->fetchOne(array(),Doctrine_Core::HYDRATE_ARRAY);
				$totalOwed = $rInfo['total'] - $RoyaltiesSystemRoyaltiesPaidRecords['total_paid'];
				$tableGrid->addBodyRow(array(
				                            'rowAttr' => array(),
				                            'columns' => array(
					                            array('text' => $currencies->format($totalOwed), 'align' => 'center'),
					                            array('text' => $rInfo['Customers']['customers_firstname'] . ' ' . $rInfo['Customers']['customers_lastname'], 'align' => 'center'),
					                            array('text' => htmlBase::newElement('checkbox')
							                            ->setName('paid_content_provider_id[]')
							                            ->setValue($rInfo['content_provider_id'])
							                            ->draw() .
					                                    '<input type="hidden" name="owed[' . $rInfo['content_provider_id'] . ']" value="' . $totalOwed . '" />', 'align' => 'center')
				                            )
				                       ));
			} else {
				$tableGrid->addBodyRow(array(
				                            'rowAttr' => array(),
				                            'columns' => array(
					                            array('text' => 'No records Found', 'align' => 'center', 'colspan' => 3),
				                            )
				                       ));
			}
		}

		$saveForm->append($tableGrid);
	}
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_TOTALS');?></div>
<br />
<div>
<?php
	$searchForm = htmlBase::newElement('form')
	->attr('name', 'search')
	->attr('action', itw_app_link(tep_get_all_get_params()))
	->attr('method', 'get');
	/*
	$startdateField = htmlBase::newElement('input')
	->setName('start_date')
	->setLabel(sysLanguage::get('HEADING_TITLE_START_DATE'))
	->setLabelPosition('before')
	->setId('start_date')
	->css(array(
		'margin-right' => '20px'
	));
	 */

	$enddateField = htmlBase::newElement('input')
	->setName('end_date')
	->setLabel(sysLanguage::get('HEADING_TITLE_START_END'))
	->setLabelPosition('before')
	->setId('end_date')
	->css(array(
		'margin-right' => '20px'
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
/*
	if (isset($_GET['start_date'])){
		$startdateField->setValue($_GET['start_date']);
	}
*/
	if (isset($_GET['end_date'])){
		$enddateField->setValue($_GET['end_date']);
	}

	if (isset($_GET['cnt_provider'])){
	 	$cntProviderField->selectOptionByValue($_GET['cnt_provider']);
	}

	//$searchForm->append($startdateField);
	$searchForm->append($enddateField);
	$searchForm->append($cntProviderField);
	$searchForm->append($gobut);

	echo $searchForm->draw();
?>
</div>
<div style="width:100%;float:left;">
	<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
		<div style="width:99%;margin:5px;"><?php echo $saveForm->draw();?></div>
	</div>
<?php
	if (isset($_GET['search']) && tep_not_null($_GET['search'])) {
?>
	<div style="text-align:right;"><?php
		$resetButton = htmlBase::newElement('button')
		->setText(sysLanguage::get('TEXT_BUTTON_RESET'))
		->setHref(itw_app_link(null, null, 'totals'));
		echo $resetButton->draw();
	?></div>
<?php
	}
?>
</div>