<?php
	$tableGrid = htmlBase::newElement('grid');

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_ID')),
			array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMER')),
			array('text' => sysLanguage::get('TABLE_HEADING_EMAIL')),
			array('text' => sysLanguage::get('TABLE_HEADING_TITLES_RENTED_OUT')),
			array('text' => sysLanguage::get('TABLE_HEADING_TITLES_ALLOWED_OUT')),
			array('text' => sysLanguage::get('TABLE_HEADING_TITLES_TO_SEND')),
			array('text' => sysLanguage::get('TABLE_HEADING_RENTAL_QUEUE'))
		)
	));

	$position = 0;
	$major = -1;
	$rent_array = array();

	$Qcustomers = dataAccess::setQuery('select c.customers_id, c.customers_firstname, c.customers_lastname, c.customers_email_address from {customers} c left join {customers_membership} cm using(customers_id) where cm.ismember = "M" and cm.activate = "Y" order by c.customers_id')
	->setTable('{customers}', TABLE_CUSTOMERS)
	->setTable('{customers_membership}', 'customers_membership');
	while($Qcustomers->next() !== false){
		$Qrented = dataAccess::setQuery('select count(customers_id) as rented from {rented_queue} where customers_id = {customer_id}')
		->setTable('{rented_queue}', TABLE_RENTED_QUEUE)
		->setValue('{customer_id}', $Qcustomers->getVal('customers_id'))
		->runQuery();

		$QnumberOfTitles = dataAccess::setQuery('select cm.plan_id, m.no_of_titles as num from {customers_membership} cm, {membership} m where m.plan_id = cm.plan_id and cm.customers_id = {customer_id}')
		->setTable('{customers_membership}', 'customers_membership')
		->setTable('{membership}', TABLE_MEMBER)
		->setValue('{customer_id}', $Qcustomers->getVal('customers_id'))
		->runQuery();

		$position++;
		$rent_array[$position] = array(
			'customers_id'            => $Qcustomers->getVal('customers_id'),
			'customers_firstname'     => $Qcustomers->getVal('customers_firstname'),
			'customers_lastname'      => $Qcustomers->getVal('customers_lastname'),
			'customers_email_address' => $Qcustomers->getVal('customers_email_address'),
			'movies_rented'           => $Qrented->getVal('rented'),
			'no_of_titles'            => $QnumberOfTitles->getVal('num'),
			'titles_to_send'          => $QnumberOfTitles->getVal('num') - $Qrented->getVal('rented')
		);
		if ($rent_array[$position]['titles_to_send'] > $major) $major = $rent_array[$position]['titles_to_send'];
	}

	$actual = $major;
	while($actual > -1){
		foreach($rent_array as $pos => $qInfo){
			if ($qInfo['titles_to_send'] == $actual){
				$tableGrid->addBodyRow(array(
					'columns' => array(
						array('text' => $qInfo['customers_id']),
						array('text' => $qInfo['customers_firstname'] . ' ' . $qInfo['customers_lastname']),
						array('text' => $qInfo['customers_email_address']),
						array('text' => $qInfo['movies_rented'], 'align' => 'center'),
						array('text' => $qInfo['no_of_titles'], 'align' => 'center'),
						array('text' => $qInfo['titles_to_send'], 'align' => 'center'),
						array('text' => '<a href="'. itw_app_link(tep_get_all_get_params(array('action', 'cID', 'app', 'appPage')) . 'cID=' . $qInfo['customers_id'], 'rental_queue', 'details').'">View</a>', 'align' => 'center')
					)
				));
			}
		}
		$actual--;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div class="main" align="right" style="font-size:.8em;"><?php
  $autoSendButton = htmlBase::newElement('button')->setText('Auto Send Rentals')
  ->setHref(itw_app_link('action=autoSendRentals'));
  echo $autoSendButton->draw();?></div>
 <br />
 <div style="width:100%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
 </div>