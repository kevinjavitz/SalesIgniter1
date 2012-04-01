<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_PURCHASED_PRODUCTS');
?></div>
<br />
<?php
	$Qproducts = Doctrine_Query::create()
	->select('p.products_id, p.products_ordered, pd.products_name')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->where('pd.language_id = ?', Session::get('languages_id'))
	->andWhere('p.products_ordered > ?', 0)
	->groupBy('pd.products_id')
	->orderBy('p.products_ordered DESC, pd.products_name');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)

	->setQuery($Qproducts);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_NUMBER')),
			array('text' => sysLanguage::get('TABLE_HEADING_PRODUCTS')),
			array('text' => sysLanguage::get('TABLE_HEADING_PURCHASED'))
		)
	));

	$Products = &$tableGrid->getResults();
	if ($Products){
		 $rowNum = ((isset($_GET['limit']) ? (int)$_GET['limit']:25)*((isset($_GET['page']) ? (int)$_GET['page'] : 1)-1));
		foreach($Products as $pInfo){
			$rowNum++;

			if (strlen($rowNum) < 2) {
				$rowNum = '0' . $rowNum;
			}

			$tableGrid->addBodyRow(array(
				'columns' => array(
					array('text' => $rowNum),
					array('text' => $pInfo['ProductsDescription'][Session::get('languages_id')]['products_name']),
					array('text' => $pInfo['products_ordered'], 'align' => 'right')
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