<?php
	$Qproducts = Doctrine_Query::create()
	->select('p.products_id, pd.products_name, p.products_date_available')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->where('p.products_date_available != ?', '')
	->andWhere('pd.language_id = ?', (int) Session::get('languages_id'))
	->orderBy('p.products_date_available DESC');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)

	->setQuery($Qproducts);
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('Edit')->addClass('editButton')->disable()
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_PRODUCTS')),
			array('text' => sysLanguage::get('TABLE_HEADING_DATE_EXPECTED')),
			array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
		)
	));
	
	$Products = &$tableGrid->getResults();
	if ($Products){
		foreach($Products as $pInfo){
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-product_id' => $pInfo['products_id']
				),
				'columns' => array(
					array('text' => $pInfo['ProductsDescription'][Session::get('languages_id')]['products_name']),
					array('text' => tep_date_short($pInfo['products_date_available'])),
					array('align' => 'center', 'text' => '&nbsp;')
				)
			));
		}
	}
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<div class="gridContainer">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		</div>
	</div>
</div>
