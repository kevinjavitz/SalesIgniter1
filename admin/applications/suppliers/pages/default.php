<?php
	require('../includes/classes/supplier.php');

	function addGridRow($supplierClass, &$tableGrid, &$infoBoxes){
		global $allGetParams, $editButton, $copyButton, $deleteButton, $currencies;
		$supplierId = $supplierClass->getID();
		$supplierName = $supplierClass->getName();
		
		

		$nameAlignCenter = false;
		if (empty($supplierName)){
			$nameAlignCenter = true;
			$supplierName = htmlBase::newElement('icon')->setType('alert')->setTooltip('This Supplier needs a name')->draw();
			$nameSpacing = '';
		}
		
		$tableGrid->addBodyRow(array(
			//'rowAttr' => $rowAttr,
			'columns' => array(
				array('text' => $nameSpacing . '<input type="checkbox" class="selectedSuppliers" name="selectedSuppliers[]" value="'.$supplierId.'">', 'align' => ($nameAlignCenter === true ? 'center' : 'left')),
                array('text' => $supplierId, 'format' => 'int'),
                array('text' => $nameSpacing . $supplierName, 'align' => ($nameAlignCenter === true ? 'center' : 'left')),
				array('text' => '<a href="'. itw_app_link($allGetParams . 'sID='.$supplierId, null, 'new_supplier').'">'.'[View/Edit/Add]'.'</a>', 'align' => 'center'),
				
			)
		));


		$editButton->setHref(itw_app_link(tep_get_all_get_params(array('sID')). 'sID=' . $supplierId, null, 'new_supplier'));
		$deleteButton->attr('suppliers_id', $supplierId);
		$copyButton->attr('suppliers_id', $supplierId);

		/*$infoBox->addButton($editButton)->addButton($copyButton)->addButton($deleteButton);

		
		
		$infoBox->addContentRow(sysLanguage::get('TEXT_DATE_ADDED') . ' ' . tep_date_short($supplierClass->getDateAdded()));
		if (tep_not_null($supplierClass->getLastModified())){
			$infoBox->addContentRow(sysLanguage::get('TEXT_LAST_MODIFIED') . ' ' . tep_date_short($supplierClass->getLastModified()));
		}*/

				
	}

	$rows = 0;
	$suppliers_count = 0;
	$lID = (int)Session::get('languages_id');

	$Qsuppliers = Doctrine_Query::create()
	->select('s.suppliers_id, s.suppliers_name')
	->from('Suppliers s')
	->orderBy('s.suppliers_name asc, s.suppliers_id desc');
	if (isset($_GET['search'])) {
		$search = $_GET['search'];
		$Qsuppliers->orWhere('s.suppliers_name LIKE ?', '%'.$search.'%');
	}
	

	//EventManager::notify('AdminSuppliersListingQueryBeforeExecute', &$Qsuppliers);

   	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setQuery($Qsuppliers);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
            array('text' => sysLanguage::get('TABLE_HEADING_SELECT')),
            array('text' => sysLanguage::get('TABLE_HEADING_ID')),
			array('text' => sysLanguage::get('TABLE_HEADING_SUPPLIERS')),
			array('text' => sysLanguage::get('TABLE_HEADING_VIEW_COMMENTS')),

		)
	));

	$suppliers = &$tableGrid->getResults();
	$infoBoxes = array();
	if ($suppliers){
		$editButton = htmlBase::newElement('button')->usePreset('edit');
		$copyButton = htmlBase::newElement('button')->setText('Copy')->addClass('copyButton');
		$deleteButton = htmlBase::newElement('button')->usePreset('delete')->addClass('deleteSupplierButton');

		$allGetParams = tep_get_all_get_params(array('action', 'sID', 'flag', 'fflag'));
		foreach($suppliers as $supplier){
			$supplierId = (int)$supplier['suppliers_id'];
			$supplierClass = new supplier($supplierId);

			addGridRow($supplierClass, $tableGrid, $infoBoxes);
		}
	}
	$array_limit = array(
		array(
			'id'   => '10',
			'text' => '10'
		),
		array(
			'id'   => '25',
			'text' => '25'
		),
		array(
			'id'   => '50',
			'text' => '50'
		),
		array(
			'id'   => '100',
			'text' => '100'
		)
	);
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <table border="0" width="100%" cellspacing="0" cellpadding="3">
  <tr>
   <td class="smallText" align="right" colspan="2"><?php
	   $searchForm = htmlBase::newElement('form')
		   ->attr('name', 'search')
		   ->attr('id', 'search')
		   ->attr('action', itw_app_link(null, null, null, 'SSL'))
		   ->attr('method', 'get');

	   $pageLimit = htmlBase::newElement('selectbox')
			   ->setName('limit')
			   ->setId('limit')
			   ->setLabel(sysLanguage::get('TEXT_SEARCH_RESULTS'))
			   ->setLabelPosition('before');
	   foreach($array_limit as $limitOption){
		   $pageLimit->addOption($limitOption['id'], $limitOption['text']);
	   }
	   $pageLimit->selectOptionByValue(isset($_GET['limit']) ? $_GET['limit'] : '10');

   

   $searchField = htmlBase::newElement('input')
			   ->setName('search')
			   ->setLabel(sysLanguage::get('HEADING_TITLE_SEARCH'))
			   ->setLabelPosition('before');
   if (isset($_GET['search'])){
   	$searchField->setValue($_GET['search']);
   }
   $searchForm->append($pageLimit);

   $searchForm->append($searchField)->append($categorySelect);

   //$contents = EventManager::notify('SuppliersDefaultAddFilterOptions', &$searchForm);

   $deleteMultipleSuppliers = '<div class="deleteForm" style="float:left;display:inline-block;margin-right:30px"><div class="" style="display:inline-block;"><input type="checkbox" class="selectallSuppliers"> <span class="selectAllSuppliersText">Check All Suppliers</span></div>';
   $deleteSuppliersButton = htmlBase::newElement('button')
   ->usePreset('delete')
   ->setText('Delete Selected Suppliers')
   ->addClass('deleteMultipleSuppliers')
   ->css(array(
		   'display' =>'inline-block'
	   ));
   $deleteMultipleSuppliers.= $deleteSuppliersButton->draw().'</div>';

   echo $deleteMultipleSuppliers. $searchForm->draw();
   ?></td>
  </tr>
 </table>

 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>

   </div>
  </div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td align="right" class="smallText"><?php
    if (!isset($_GET['search'])){
    	$newSuppButton = htmlBase::newElement('button')->usePreset('install')->setText(sysLanguage::get('TEXT_BUTTON_NEW_SUPPLIER'))
    	->setHref(itw_app_link(null, null, 'new_supplier'));

    	echo $newSuppButton->draw();
    }
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
