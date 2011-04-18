 <?php
 	$Qcustomers = Doctrine_Query::create()
	->select('c.customers_id, concat(c.customers_firstname, " ", c.customers_lastname) as full_name, c.customers_email_address')
	->from('Customers c')
	->leftJoin('c.CustomersMembership cm')
	->where('cm.ismember = ?', 'M')
	->andWhere('cm.activate = ?', 'Y')
	->orderBy('c.customers_id');

	$tableGrid = htmlBase::newElement('newGrid')
	->setQuery($Qcustomers);
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('View Rentals')->addClass('rentedButton')->disable()
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_ID')),
			array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMER')),
			array('text' => sysLanguage::get('TABLE_HEADING_EMAIL')),
			array('text' => sysLanguage::get('TABLE_HEADING_TITLES_RENTED_OUT')),
			array('text' => sysLanguage::get('TABLE_HEADING_TITLES_ALLOWED_OUT')),
			array('text' => sysLanguage::get('TABLE_HEADING_RENTAL_QUEUE'))
		)
	));
	
	$Customers = &$tableGrid->getResults();
	if ($Customers){
		foreach($Customers as $cInfo){
			$Qrented = Doctrine_Query::create()
			->select('COUNT(customers_id) as total')
			->from('RentedQueue')
			->where('customers_id = ?', $cInfo['customers_id'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			
			$QnumTitles = Doctrine_Query::create()
			->select('cm.plan_id, m.no_of_titles')
			->from('CustomersMembership cm')
			->leftJoin('cm.Membership m')
			->where('cm.customers_id = ?', $cInfo['customers_id'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
      
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-customer_id' => $cInfo['customers_id']
				),
				'columns' => array(
					array('text' => $cInfo['customers_id']),
					array('text' => $cInfo['full_name']),
					array('text' => $cInfo['customers_email_address']),
					array('text' => $Qrented[0]['total']),
					array('text' => $QnumTitles[0]['Membership']['no_of_titles']),
					array('align' => 'center', 'text' => '<a href="'. itw_app_link('cID=' . $cInfo['customers_id'], 'rental_queue', 'return').'">View</a>')
				)
			));
		}
	}
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_RENTED');
?></div>
<br />
<div class="gridContainer">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		</div>
	</div>
</div>