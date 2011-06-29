<?php
	$Qorders = Doctrine_Query::create()
	->select('o.orders_id, a.entry_name, o.date_purchased, o.customers_id, o.last_modified, o.currency, o.currency_value, s.orders_status_id, sd.orders_status_name, ot.text as order_total, o.payment_module')
	->from('Orders o')
	->leftJoin('o.OrdersTotal ot')
	->leftJoin('o.OrdersAddresses a')
	->leftJoin('o.OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->where('sd.language_id = ?', Session::get('languages_id'))
	->andWhereIn('ot.module_type', array('total', 'ot_total'))
	->andWhere('a.address_type = ?', 'customer')
	->andWhere('o.orders_status = ?', sysConfig::get('ORDERS_STATUS_ESTIMATE_ID'))
	->orderBy('o.date_purchased desc');
	
	EventManager::notify('AdminEstimatesListingBeforeExecute', &$Qorders);
	
	if (isset($_GET['cID'])){
		$Qorders->andWhere('o.customers_id = ?', (int)$_GET['cID']);
	}

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qorders);

	$gridButtons = array(
		htmlBase::newElement('button')->setText('Details')->addClass('detailsButton')->disable(),
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable()
	);
	
	EventManager::notify('OrdersGridButtonsBeforeAdd', &$gridButtons);
	
	$tableGrid->addButtons($gridButtons);

	$gridHeaderColumns = array(
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

			$gridBodyColumns = array(
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
				'action' => itw_app_link(tep_get_all_get_params(array('action', 'oID')) . 'action=deleteConfirmEstimates&oID=' . $oInfo->orders_id)
			));

			$deleteButtonEstimate = htmlBase::newElement('button')
			->setType('submit')
			->usePreset('delete')
			->setText('Delete');

			$infoBox->addButton($deleteButtonEstimate);
			$oID = $_GET['oID'];

			$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_DELETE_INTRO'));
			$infoBox->addContentRow('<b>' . $oInfo->OrdersAddresses['customer']['entry_name'] . '</b>');
			break;
	}

?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />

	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		</div>
	</div>
	<?php

		EventManager::notify('AdminOrdersAfterTableDraw');
	?>