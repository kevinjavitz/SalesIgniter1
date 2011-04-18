<?php
	$pointOfSale->preProcessOrder();

	$dropArr = array();
	$Qstatus = dataAccess::setQuery('select * from {orders_status} order by orders_status_id')
	->setTable('{orders_status}', TABLE_ORDERS_STATUS);
	while($Qstatus->next() !== false){
		$dropArr[] = array(
			'id'   => $Qstatus->getVal('orders_status_id'),
			'text' => $Qstatus->getVal('orders_status_name')
		);
	}
	
	$commentsTable = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0);

	$commentsTable->addBodyRow(array(
		'columns' => array(
			array(
				'addCls' => 'main',
				'text'   => '<div class="errMsg successMsg" style="font-weight:bold;"></div>'
			)
		)
	));
	
	$commentsTable->addBodyRow(array(
		'columns' => array(
			array(
				'addCls' => 'main',
				'text'   => '<b>Comments</b>'
			)
		)
	));
	
	$commentsTable->addBodyRow(array(
		'columns' => array(
			array(
				'addCls' => 'main',
				'text'   => '<textarea cols="45" rows="5" name="comments" id="comments"></textarea>'
			)
		)
	));
	
	$commentsTable->addBodyRow(array(
		'columns' => array(
			array(
				'addCls' => 'main',
				'text'   => '<b>Status:</b> ' . tep_draw_pull_down_menu('status', $dropArr, '', 'id="status"')
			)
		)
	));
	
	$commentsTable->addBodyRow(array(
		'columns' => array(
			array(
				'addCls' => 'main',
				'text'   => '<input type="checkbox" value="1" name="notify">Notify Customer?'
			)
		)
	));
	
	$paymentTable = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->css('width', '98%');
	
	$paymentTable->addBodyRow(array(
		'colspan' => '2',
		'columns' => array(
			array(
				'addCls'  => 'main',
				'colspan' => '2',
				'text'    => '<b>Amount Due:</b> ' . $currencies->format($order->info['total']) . '<input id="paymentAmount" name="paymentAmount" type="hidden" size="8" value="' . $order->info['total'] . '">'
			)
		)
	));
	
	$paymentTable->addBodyRow(array(
		'columns' => array(
			array(
				'text' => pointOfSaleHTML::outputPaymentMethods(),
				'attr' => array(
					'valign' => 'top'
				)
			),
			array(
				'text' => $commentsTable,
				'attr' => array(
					'valign' => 'top'
				)
			)
		)
	));
	
	EventManager::attachActionResponse($paymentTable->draw(), 'html');
?>