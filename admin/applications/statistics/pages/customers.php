<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_CUSTOMERS');
?></div>
<br />
<?php
	$Qcustomers = Doctrine_Query::create()
	->select('c.customers_id, c.customers_firstname, c.customers_lastname, sum(ot.value) as ordersum')
	->from('Customers c')
	->leftJoin('c.Orders o')
	->leftJoin('o.OrdersTotal ot')
	->andWhereIn('ot.module_type', array('total', 'ot_total'))
	->groupBy('c.customers_id')
	->orderBy('ordersum DESC');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)

	->setQuery($Qcustomers);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_NUMBER')),
			array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMERS')),
			array('text' => sysLanguage::get('TABLE_HEADING_TOTAL_PURCHASED'))
		)
	));

	$Customers = &$tableGrid->getResults();
	if ($Customers){
		$rowNum = 0;
		foreach($Customers as $cInfo){
			$rowNum++;

			if (strlen($rowNum) < 2) {
				$rowNum = '0' . $rowNum;
			}

			$tableGrid->addBodyRow(array(
				'onclick' => 'js_redirect(\'' . itw_app_link('search=' . $cInfo['customers_lastname'], 'customers', 'default') . '\')',
				'columns' => array(
					array('text' => $rowNum),
					array('text' => '<a href="' . itw_app_link('search=' . $cInfo['customers_lastname'], 'customers', 'default') . '">' . $cInfo['customers_firstname'] . ' ' . $cInfo['customers_lastname'] . '</a>'),
					array('text' => $currencies->format($cInfo['ordersum']), 'align' => 'right')
				)
			));
		}
	}
?>
 <div style="width:100%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   </div>
  </div>
 </div>