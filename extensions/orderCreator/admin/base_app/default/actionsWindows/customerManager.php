<div>
	<div class="ui-widget ui-widget-content ui-corner-all" style="height:300px;overflow:auto"><?php
		$CustomersTable = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0)
		->css(array(
			'width' => '100%'
		))
		->addClass('customersTable');
		
		$CustomersTable->addHeaderRow(array(
			'columns' => array(
				array('text' => 'Id'),
				array('text' => 'First Name'),
				array('text' => 'Last Name'),
				array('text' => 'Email Address')
			)
		));
		
		$Qcustomers = Doctrine_Query::create()
		->from('Customers')
		->orderBy('customers_firstname')
		->execute();
		$tableRows = array();
		if ($Qcustomers->count() > 0){
			foreach($Qcustomers->toArray() as $cInfo){
				$CustomersTable->addBodyRow(array(
					'rowAttr' => array(
						'data-customer_id' => $cInfo['customers_id']
					),
					'columns' => array(
						array('text' => $cInfo['customers_id']),
						array('text' => $cInfo['customers_firstname']),
						array('text' => $cInfo['customers_lastname']),
						array('text' => $cInfo['customers_email_address'])
					)
				));
			}
		}
		
		echo $CustomersTable->draw();
	?></div>
	<div class="ui-widget ui-widget-content ui-corner-all"><?php
		echo htmlBase::newElement('button')->setText('Add To Order')->draw();
		echo htmlBase::newElement('button')->setText('New Customer')->draw();
	?></div>
</div>

<?php
	EventManager::attachActionResponse('', 'html');
?>