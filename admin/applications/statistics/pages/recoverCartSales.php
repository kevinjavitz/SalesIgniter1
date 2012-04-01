<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_RECOVERED_CARTS');
?></div>
<div style="text-align:right;"><form name="filter" action="<?php echo itw_app_link();?>" method="POST"><?php
	if (isset($_POST['tdate'])){
		$tdate = $_POST['tdate'];
	}else{
		$tdate = RCS_REPORT_DAYS;
	}
	$rawtime = strtotime('-' . $tdate . ' days', time());
	$ndate = date('Ymd', $rawtime);
	
	echo sysLanguage::get('TEXT_DAYS_FILTER_PREFIX');
	echo htmlBase::newElement('input')->setName('tdate')->setValue($tdate)->attr('size', 4)->draw();
	echo sysLanguage::get('TEXT_DAYS_FILTER_POSTFIX');
	echo htmlBase::newElement('button')->usePreset('continue')->setType('submit')->setText(sysLanguage::get('TEXT_BUTTON_GO'))->draw();
?></form></div>
<br />
<?php
	// Init vars
	$custknt = 0;
	$rc_cnt = 0;
	$total_recovered = 0;
	$custlist = '';
	
	$QshoppingCarts = Doctrine_Query::create()
	->from('Scart s')
	->leftJoin('s.Customers c')
	->where('s.dateadded >= CAST(? as DATE)', $ndate)
	->orderBy('s.dateadded DESC');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(false)

	->setQuery($QshoppingCarts)
	->stripeRows('tableRowOdd', 'tableRowEven');
	
	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_SCART_ID')),
			array('text' => '&nbsp;'),
			array('text' => sysLanguage::get('TABLE_HEADING_SCART_DATE')),
			array('text' => '&nbsp;'),
			array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMER')),
			array('text' => sysLanguage::get('TABLE_HEADING_ORDER_DATE')),
			array('text' => sysLanguage::get('TABLE_HEADING_ORDER_STATUS')),
			array('text' => sysLanguage::get('TABLE_HEADING_ORDER_AMOUNT')),
			array('text' => '&nbsp;')
		)
	));
	
	$ShoppingCarts = &$tableGrid->getResults();
	if ($ShoppingCarts){
		$rc_cnt = sizeof($ShoppingCarts);
		foreach($ShoppingCarts as $cInfo){
			$Qorder = Doctrine_query::create()
			->select('o.orders_id, o.customers_id, o.date_purchased, s.orders_status_name, ot.text as order_total, ot.value')
			->from('Orders o')
			->leftJoin('o.OrdersTotal ot')
			->leftJoin('o.OrdersStatus os')
			->where('(o.customers_id = "' . $cInfo['customers_id'] . '" OR o.customers_email_address LIKE "' . $cInfo['Customers']['customers_email_address'] . '")')
			->andWhere('o.orders_status > ?', RCS_PENDING_SALE_STATUS)
			->andWhere('o.date_purchased >= CAST(? as DATE)', $cInfo['dateadded'])
			->andWhereIn('ot.module_type', array('total', 'ot_total'));

			EventManager::notify('OrdersListingBeforeExecute', &$Qorder);

			$Qorder = $Qorder->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qorder){
				$custknt++;
				$total_recovered += $Qorder[0]['OrdersTotal']['value'];
				
				$tableGrid->addBodyRow(array(
					'columns' => array(
						array('align' => 'right', 'text' => $cInfo['scartid']),
						array('text' => '&nbsp;'),
						array('align' => 'center', 'text' => tep_date_short($cInfo['dateadded'])),
						array('text' => '&nbsp;'),
						array('text' => '<a href="' . tep_href_link(FILENAME_CUSTOMERS, 'search=' . $cInfo['Customers']['customers_lastname'], 'NONSSL') . '">' . $cInfo['Customers']['customers_firstname'] . ' ' . $cInfo['Customers']['customers_lastname'] . '</a>'),
						array('text' => tep_date_short($Qorder[0]['date_purchased'])),
						array('align' => 'center', 'text' => $Qorder[0]['OrdersStatus'][0]['orders_status_name']),
						array('align' => 'right', 'text' => strip_tags($Qorder[0]['OrdersTotal'][0]['order_total'])),
						array('text' => '&nbsp;')
					)
				));
			}
		}
	}
?>
 <div style="width:100%;float:left;">
  <div><?php
  	$overViewTable = htmlBase::newElement('table')
  	->setCellPadding(3)
  	->setCellSpacing(0);
  	
  	$overViewTable->addBodyRow(array(
  		'columns' => array(
  			array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('TOTAL_RECORDS') . '</b>'),
  			array('addCls' => 'main', 'text' => $rc_cnt),
  			array('addCls' => 'main', 'text' => '&nbsp;')
  		)
  	));
  	
  	$overViewTable->addBodyRow(array(
  		'columns' => array(
  			array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('TOTAL_SALES') . '</b>'),
  			array('addCls' => 'main', 'text' => $custknt),
  			array('addCls' => 'main', 'text' => sysLanguage::get('TOTAL_SALES_EXPLANATION'))
  		)
  	));
  	
  	$overViewTable->addBodyRow(array(
  		'columns' => array(
  			array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('TOTAL_RECOVERED') . '</b>'),
  			array('addCls' => 'main', 'text' => '<b>' . ($rc_cnt ? tep_round(($custknt / $rc_cnt) * 100, 2) : 0) . '%</b>'),
  			array('addCls' => 'main', 'text' => '<b>( ' . $currencies->format(tep_round($total_recovered, 2)) . ')</b>')
  		)
  	));
  	
  	echo $overViewTable->draw();
  ?></div>
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   </div>
  </div>
 </div>