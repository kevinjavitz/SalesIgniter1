<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_CUSTOMERS');
?></div>
<br />
<?php
	$Qcustomers = Doctrine_Query::create()
	->select('c.customers_firstname, c.customers_lastname, sum(op.products_quantity * op.final_price) as ordersum')
	->from('Customers c')
	->leftJoin('c.Orders o')
	->leftJoin('o.OrdersProducts op')
	->groupBy('c.customers_firstname, c.customers_lastname')
	->orderBy('ordersum DESC');

	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
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
				'onclick' => 'js_redirect(\'' . tep_href_link(FILENAME_CUSTOMERS, 'search=' . $cInfo['customers_lastname'], 'NONSSL') . '\')',
				'columns' => array(
					array('text' => $rowNum),
					array('text' => '<a href="' . tep_href_link(FILENAME_CUSTOMERS, 'search=' . $cInfo['customers_lastname'], 'NONSSL') . '">' . $cInfo['customers_firstname'] . ' ' . $cInfo['customers_lastname'] . '</a>'),
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