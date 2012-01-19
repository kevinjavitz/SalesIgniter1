<?php
	$Qorders = Doctrine_Query::create()
	->select('o.orders_id, a.entry_name, o.date_purchased, o.customers_id, o.last_modified, o.currency, o.currency_value, s.orders_status_id, sd.orders_status_name, ot.text as order_total, o.payment_module')
	->from('Orders o')
	->leftJoin('o.OrdersTotal ot')
	->leftJoin('o.OrdersAddresses a')
	->leftJoin('o.OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->where('sd.language_id = ?', Session::get('languages_id'))
	->andWhere('o.orders_status != ?', sysConfig::get('ORDERS_STATUS_ESTIMATE_ID'))
	->andWhereIn('ot.module_type', array('total', 'ot_total'))
	->andWhere('a.address_type = ?', 'customer')
	->orderBy('o.date_purchased desc');
	
	EventManager::notify('AdminOrdersListingBeforeExecute', &$Qorders);
	
	if (isset($_GET['cID'])){
		$Qorders->andWhere('o.customers_id = ?', (int)$_GET['cID']);
	}elseif (isset($_GET['status']) && is_numeric($_GET['status']) && $_GET['status'] > 0){
		$Qorders->andWhere('s.orders_status_id = ?', (int)$_GET['status']);
	}

	if (isset($_GET['start_date']) && !empty($_GET['start_date'])){
        $datetime = date('Y-m-d h:i:s', strtotime($_GET['start_date']));            
		$Qorders->andWhere('o.date_purchased >= ?', $datetime);
	}

    if (isset($_GET['end_date']) && !empty($_GET['end_date'])){
        $datetime = date('Y-m-d h:i:s', strtotime($_GET['end_date']));
		$Qorders->andWhere('o.date_purchased < ?', $datetime);
	}

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qorders);

	$gridButtons = array(
		htmlBase::newElement('button')->setText('Details')->addClass('detailsButton')->disable(),
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable(),
		htmlBase::newElement('button')->setText('Cancel')->addClass('cancelButton')->disable(),
		htmlBase::newElement('button')->setText('Invoice')->addClass('invoiceButton')->disable()
	);
	if(sysConfig::get('SHOW_PACKING_SLIP_BUTTONS') == 'true'){
		$gridButtons[] = htmlBase::newElement('button')->setText('Packing Slip')->addClass('packingSlipButton')->disable();
	}
	
	EventManager::notify('OrdersGridButtonsBeforeAdd', &$gridButtons);
	
	$tableGrid->addButtons($gridButtons);

	$gridHeaderColumns = array(
		array('text' => '&nbsp;'),
		array('text' => 'ID'),
		array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMERS')),
		array('text' => sysLanguage::get('TABLE_HEADING_ORDER_TOTAL')),
		array('text' => sysLanguage::get('TABLE_HEADING_DATE_PURCHASED')),
		array('text' => sysLanguage::get('TABLE_HEADING_STATUS'))
	);

	EventManager::notify('OrdersListingAddGridHeader', &$gridHeaderColumns);

	$gridHeaderColumns[] = array('text' => 'info');
	
	$tableGrid->addHeaderRow(array(
		'columns' => $gridHeaderColumns
	));

	$orders = &$tableGrid->getResults();
	$noOrders = false;
	if ($orders){
		foreach($orders as $order){
			$orderId = $order['orders_id'];

			$arrowIcon = htmlBase::newElement('icon')->setType('info');

			$htmlCheckbox = htmlBase::newElement('checkbox')
			->setName('selectedOrder[]')
			->addClass('selectedOrder')
			->setValue($orderId);

			$gridBodyColumns = array(
				array('text' => $htmlCheckbox->draw(), 'align' => 'center'),
				array('text' => $orderId),
				array('text' => $order['OrdersAddresses']['customer']['entry_name']),
				array('text' => strip_tags($order['order_total']), 'align' => 'right'),
				array('text' => tep_datetime_short($order['date_purchased']), 'align' => 'center'),
				array('text' => $order['OrdersStatus']['OrdersStatusDescription'][Session::get('languages_id')]['orders_status_name'], 'align' => 'center')
			);

			EventManager::notify('OrdersListingAddGridBody', &$order, &$gridBodyColumns);

			$gridBodyColumns[] = array('text' => $arrowIcon->draw(), 'align' => 'right');

			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-order_id' => $orderId
				),
				'columns' => $gridBodyColumns
			));
			
			$tableGrid->addBodyRow(array(
				'addCls' => 'gridInfoRow',
				'columns' => array(
					array(
						'colspan' => sizeof($gridBodyColumns),
						'text' => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' . 
							'<tr>' . 
								'<td><b>' . sysLanguage::get('TEXT_DATE_ORDER_CREATED') . '</b></td>' . 
								'<td> ' . tep_date_short($order['date_purchased']) . '</td>' . 
								'<td><b>' . sysLanguage::get('TEXT_DATE_ORDER_LAST_MODIFIED') . '</b></td>' . 
								'<td>' . tep_date_short($order['last_modified']) . '</td>' .
								'<td></td>' .
							'</tr>' . 
							'<tr>' . 
								'<td><b>' . sysLanguage::get('TEXT_INFO_PAYMENT_METHOD') . '</b></td>' . 
								'<td>'  . $order['payment_module'] . '</td>' . 
							'</tr>' .
						'</table>'
					)
				)
			));
		}
	}
	switch ($action) {
		case 'delete':
			$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE_ORDER') . '</b>');
			$infoBox->setForm(array(
				'name'   => 'orders',
				'action' => itw_app_link(tep_get_all_get_params(array('action', 'oID')) . 'action=deleteConfirm&oID=' . $oInfo->orders_id)
			));

			$deleteButtonReservationRestock = htmlBase::newElement('button')
											->setType('submit')
											->usePreset('delete')
											->setText('Delete');

			$checkBoxDeleteReservation = htmlBase::newElement('checkbox')
				            ->setName('deleteReservationRestock')
			 				->setLabel('Delete reservations')
							->setChecked(true)
				            ->setValue('1');
			$checkBoxDeleteRestock = htmlBase::newElement('checkbox')
				            ->setName('deleteRestockNoReservation')
			 				->setLabel('Restock quantity based inventory')
							->setChecked(true)
				            ->setValue('1');

			$cancelButton = htmlBase::newElement('button')->setType('submit')->usePreset('cancel')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'oID')) . 'oID=' . $oInfo->orders_id));

			$infoBox->addButton($deleteButtonReservationRestock)
					->addButton($cancelButton);


			$oID = $_GET['oID'];
			$QOrdersQuery = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.OrdersAddresses oa')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsReservation opr')
			->leftJoin('opr.ProductsInventoryBarcodes ib')
			->leftJoin('ib.ProductsInventory ibi')
			->leftJoin('opr.ProductsInventoryQuantity iq')
			->leftJoin('iq.ProductsInventory iqi')
			->where('o.orders_id = ?', $oID)
			->andWhere('oa.address_type = ?', 'customer')
			->andWhere('parent_id IS NULL');

			$Qorders = $QOrdersQuery->execute();
			$isreservation = false;
			$isquantity = false;
			foreach($Qorders as $oInfo){
				foreach($oInfo->OrdersProducts as $opInfo){

					foreach($opInfo->OrdersProductsReservation as $oprInfo){
						$isreservation = true;
					}

					if ($opInfo['purchase_type'] == 'new ' || $opInfo['purchase_type'] == 'used'){
						$isquantity = true;
					}
				}

			}

			if ($isreservation){
				$infoBox->addContentRow($checkBoxDeleteReservation->draw());
			}

			if($isquantity){
				$infoBox->addContentRow($checkBoxDeleteRestock->draw());
			}

			$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_DELETE_INTRO'));
			$infoBox->addContentRow('<b>' . $oInfo->OrdersAddresses['customer']['entry_name'] . '</b>');
			//$infoBox->addContentRow(tep_draw_checkbox_field('restock') . ' ' . sysLanguage::get('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY'));
			break;
	}

	$searchForm = htmlBase::newElement('form')
	->attr('name', 'search')
	->attr('id', 'searchFormOrders')
	->attr('action', itw_app_link(null,'orders', 'default', 'SSL'))
	->attr('method', 'get');

	$startdateField = htmlBase::newElement('input')
	->setName('start_date')
	->setLabel('Start Date: ')
	->setLabelPosition('before')
	->setId('start_date');

	if (isset($_GET['start_date']) && !empty($_GET['start_date'])){
		$startdateField->val($_GET['start_date']);
	}

	$enddateField = htmlBase::newElement('input')
	->setName('end_date')
	->setLabel('End Date: ')
	->setLabelPosition('before')
	->setId('end_date');

	if (isset($_GET['end_date']) && !empty($_GET['end_date'])){
		$enddateField->val($_GET['end_date']);
	}

	$htmlSelectAll = htmlBase::newElement('checkbox')
	->setName('select_all')
	->setId('selectAllOrders')
	->setLabel('Select All')
	->setLabelPosition('after');

	$statusField = htmlBase::newElement('selectbox')
	->setName('status')
	->setLabel('Status: ')
	->setLabelPosition('before');
	$statusField->addOption('0', 'All');

	$QOrdersStatus = Doctrine_Query::create()
	->from('OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->where('sd.language_id = ?', (int)Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($QOrdersStatus as $iStatus){
		$statusField->addOption($iStatus['orders_status_id'], $iStatus['OrdersStatusDescription'][0]['orders_status_name']);
	}
	if(isset($_GET['status'])){
		$statusField->selectOptionByValue($_GET['status']);
	}

	$limitField = htmlBase::newElement('selectbox')
	->setName('limit')
	->setLabel('Orders per Page: ')
	->setLabelPosition('before');

	$limitField->addOption('25','25');
	$limitField->addOption('100','100');
	$limitField->addOption('250','250');

	if (isset($_GET['limit']) && !empty($_GET['limit'])){
		$limitField->selectOptionByValue($_GET['limit']);
	}

	$gotoOrderField = htmlBase::newElement('input')
	->setName('oID')
	->attr('size','4')
	->setLabel('Go to order: ')
	->setLabelPosition('before')
	->setId('orderIdField');

	$submitButtonGoto = htmlBase::newElement('button')
	->setType('submit')
	->usePreset('save')
	->setText('Go');

	$submitButton = htmlBase::newElement('button')
	->setType('submit')
	->usePreset('save')
	->setText('Search');

	$searchOrderForm = htmlBase::newElement('form')
	->attr('name', 'searchOrder')
	->attr('id', 'searchOrdersGoto')
	->attr('action', itw_app_link(null,'orders', 'details', 'SSL'))
	->attr('method', 'get');

	$searchOrderForm->append($gotoOrderField)->append($submitButtonGoto);

	$searchForm
	->append($limitField)
	->append($startdateField)
	->append($enddateField)
	->append($statusField);
	EventManager::notify('AdminOrdersListingSearchForm', $searchForm);
	$searchForm->append($submitButton);

 	$htmlSelectFieldsButton = '<a href="#" id="showFields"><img src="'.sysConfig::getDirWsCatalog().'images/addbut.png"/></a>';

 	$htmlSelectFieldsDiv = htmlBase::newElement('div')
 	->css(array(
 		'margin-top' => '.5em'
 	))
	->attr('id','csvFieldsTable');

  	$fieldsArray =	array(
		'v_orders_id',
		'v_orders_customers_name',
        'v_orders_customers_company',
        'v_orders_customers_email_address',
        'v_orders_customers_telephone',
        'v_orders_billing_name',
        'v_orders_billing_address',
        'v_orders_billing_city',
        'v_orders_billing_state',
        'v_orders_billing_country',
        'v_orders_billing_postcode',
        'v_orders_shipping_name',
        'v_orders_shipping_address',
        'v_orders_shipping_city',
        'v_orders_shipping_state',
        'v_orders_shipping_country',
        'v_orders_shipping_postcode',
        'v_orders_subtotal',
		'v_orders_total',
        'v_orders_tax',
        'v_orders_payment_method',
        'v_orders_status',
        'v_orders_shipping_price',
		'v_orders_shipping_method',
		'v_orders_date_purchased',
		'v_orders_products_name',
        'v_orders_products_model',
        'v_orders_products_price',
        'v_orders_products_tax',
        'v_orders_products_finalprice',
        'v_orders_products_qty',
        'v_orders_products_barcode',
        'v_orders_products_purchasetype'
	  );

	EventManager::notify('AdminOrdersListingExportFields', &$fieldsArray);

 	$i = 1;
 	$fieldsTable = htmlBase::newElement('table')
 	->setCellSpacing(0)
 	->setCellPadding(1);

 	$fieldsTable->addHeaderRow(array(
		'columns' => array(
			array('colspan' => 5, 'text' => 'Uncheck to exclude from export')
		)
	));
	foreach($fieldsArray as $field){
		$br = htmlBase::newElement('br');
		$fieldName = explode('_', $field);
		unset($fieldName[0]);
		$fieldName = ucwords(implode(' ',$fieldName));
		
		$fieldCheckbox = htmlBase::newElement('checkbox')
		->setName($field)
		->setChecked(true)
		->setLabel($fieldName)
		->setLabelPosition('after');
		
		$columns[] = array('text' => $fieldCheckbox->draw());
		if (sizeof($columns) == 5){
			$fieldsTable->addBodyRow(array(
				'columns' => $columns
			));
			$columns = array();
		}
    }
	if (sizeof($columns) > 0){
		$fieldsTable->addBodyRow(array(
			'columns' => $columns
		));
	}
	$htmlSelectFieldsDiv->append($fieldsTable);


	$csvButton = htmlBase::newElement('button')
	->setType('submit')
	->usePreset('save')
	->setText('Save CSV');
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />
<div style="width:100%"><?php
	echo $searchForm->draw().$searchOrderForm->draw();
?></div>
<form action="<?php echo itw_app_link('action=exportOrders','orders','default');?>" method="post">
	<div style="width:100%;float:left;">
		<div style="margin-left:30px;display:block;"><?php echo $htmlSelectAll->draw();?></div>
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		</div>
	</div>
	<?php
		echo $htmlSelectFieldsDiv->draw();
		echo $htmlSelectFieldsButton;
 		echo $csvButton->draw();
		EventManager::notify('AdminOrdersAfterTableDraw');
	?>
</form>