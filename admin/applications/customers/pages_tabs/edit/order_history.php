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
	->andWhere('o.customers_id = ?', $Customer->customers_id)
	->orderBy('o.date_purchased desc');

$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(false)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qorders);

$gridButtons = array(
	htmlBase::newElement('button')->setText('Details')->addClass('detailsButton')->disable(),
	//htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable(),
	//htmlBase::newElement('button')->setText('Cancel')->addClass('cancelButton')->disable(),
	htmlBase::newElement('button')->setText('Invoice')->addClass('invoiceButton')->disable(),
	htmlBase::newElement('button')->setText('Packing Slip')->addClass('packingSlipButton')->disable()
);

$tableGrid->addButtons($gridButtons);

$gridHeaderColumns = array(
	//array('text' => '&nbsp;'),
	array('text' => 'ID'),
	array('text' => sysLanguage::get('TABLE_HEADING_DATE_PURCHASED')),
	array('text' => sysLanguage::get('TABLE_HEADING_STATUS')),
	array('text' => sysLanguage::get('TABLE_HEADING_ORDER_TOTAL'))
);

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
			//array('text' => $htmlCheckbox->draw(), 'align' => 'center'),
			array('text' => $orderId),
			//array('text' => $order['OrdersAddresses']['customer']['entry_name']),
			array('text' => tep_datetime_short($order['date_purchased']), 'align' => 'center'),
			array('text' => $order['OrdersStatus']['OrdersStatusDescription'][Session::get('languages_id')]['orders_status_name'], 'align' => 'center'),
			array('text' => strip_tags($order['order_total']), 'align' => 'right')
		);

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
echo $tableGrid->draw();
