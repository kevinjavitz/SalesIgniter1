<?php
	$tableGrid = htmlBase::newElement('newGrid');

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

	$Qcustomers = Doctrine_Query::create()
	->from('Customers c')
	->leftJoin('c.CustomersMembership cm')
	->where('cm.ismember = ?', 'M')
	->andWhere('cm.activate = ?', 'Y')
	->andWhere('DATEDIFF(cm.next_bill_date,CURDATE()) > ?', sysConfig::get('RENTAL_DAYS_CUSTOMER_PAST_DUE'))
	->orderBy('c.customers_id');

	if(isset($_GET['pickupRequest'])){
		$Qcustomers->leftJoin('c.CustomersToPickupRequests cpr')
		->leftJoin('cpr.PickupRequests pr')
		->andwhere('pr.start_date = ?', date('Y-m-d H:i:s', strtotime($_GET['pickupRequest'])));
	}

	$Qcustomers = $Qcustomers->execute(array(), Doctrine_Core::HYDRATE_ARRAY);


	$QPickupRequests = Doctrine_Query::create()
	->from('PickupRequests pr')
	->leftJoin('pr.PickupRequestsTypes prt')
	->where('pr.start_date >= ?', date('Y-m-d'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($Qcustomers as $iCustomer){

		$Qrented = Doctrine_Query::create()
		->select('count(customers_id) as rented')
		->from('RentedQueue')
		->where('customers_id = ?', $iCustomer['customers_id'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$QnumberOfTitles = Doctrine_Query::create()
		->from('CustomersMembership cm')
		->leftJoin('cm.Membership m')
		->where('cm.customers_id = ?', $iCustomer['customers_id'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$position++;
		$rent_array[$position] = array(
			'customers_id'            => $iCustomer['customers_id'],
			'customers_firstname'     => $iCustomer['customers_firstname'],
			'customers_lastname'      => $iCustomer['customers_lastname'],
			'customers_email_address' => $iCustomer['customers_email_address'],
			'movies_rented'           => $Qrented[0]['rented'],
			'no_of_titles'            => $QnumberOfTitles[0]['Membership']['no_of_titles'],
			'titles_to_send'          => $QnumberOfTitles[0]['Membership']['no_of_titles'] - $Qrented[0]['rented']
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
<?php
if($usePickupRequest){
	$pickupReq = htmlBase::newElement('selectbox')
		->setName('pickupRequest')
		->setId('pickupRequest');
	$pickupReq->addOption('-1','Any Date');

	if(isset($_GET['pickupRequest'])){
   	    $pickupReq->selectOptionByValue($_GET['pickupRequest']);
	}
	$dateArr = array();
	foreach($QPickupRequests as $pick){
		if(!isset($dateArr[$pick['start_date']])){
			$pickupReq->addOption(tep_date_short($pick['start_date']), tep_date_short($pick['start_date']));
			$dateArr[$pick['start_date']] = 1;
		}
	}

	$filterButton = htmlBase::newElement('button')
	->setType('submit')
	->setText('Filter');

?>
	<div class="" align="left"><form name="filter" method="get" action="<?php echo itw_app_link(null, null,null);?>">
		Filter customers with pickup request date:<?php echo $pickupReq->draw().'&nbsp;&nbsp;'.$filterButton->draw();?>
	</form>
	</div>
	<?php
}
	?>
 <div class="main" align="right" style="font-size:.8em;"><?php
    $pickupRequestDate = '';
	if(isset($_GET['pickupRequest'])){
	   $pickupRequestDate = '&pickupRequestDate='.date('Y-m-d H:i:s', strtotime($_GET['pickupRequest']));
    }
  $autoSendButton = htmlBase::newElement('button')->setText('Auto Send Rentals')
  ->setHref(itw_app_link('action=autoSendRentals'.$pickupRequestDate));
  echo $autoSendButton->draw();?></div>
 <br />
 <div style="width:100%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
 </div>