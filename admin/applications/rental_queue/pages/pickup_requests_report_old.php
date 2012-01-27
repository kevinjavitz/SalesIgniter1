<?php
	$Qorders = Doctrine_Query::create()
	->from('RentedProductsToPickupRequests rptpr')
	->leftJoin('rptpr.RentedQueue rq')
	->leftJoin('rptpr.PickupRequests pr')
	->leftJoin('pr.PickupRequestsTypes prt')
	->leftJoin('rq.Customers c');


if(isset($_GET['sortDate'])){
	$Qorders->orderBy('pr.start_date '.$_GET['sortDate']);
}


if(isset($_GET['sortFirstname'])){
	$Qorders->orderBy('c.customers_firstname '.$_GET['sortFirstname'].',c.customers_lastname '.$_GET['sortFirstname']);
}

$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qorders);

$gridHeaderColumns = array(

	array('text' => '<a href="'.itw_app_link('sortDate='.(isset($_GET['sortDate'])?($_GET['sortDate'] == 'ASC'?'DESC':'ASC'):'ASC'),null,null).'">'.sysLanguage::get('TABLE_HEADING_DATE').'</a>'),
	array('text' => '<a href="'.itw_app_link('sortFirstname='.(isset($_GET['sortFirstname'])?($_GET['sortFirstname'] == 'ASC'?'DESC':'ASC'):'ASC'),null,null).'">'.sysLanguage::get('TABLE_HEADING_FIRSTNAME').'</a>'),
	array('text' => sysLanguage::get('TABLE_HEADING_ADDRESS')),
	array('text' => sysLanguage::get('TABLE_HEADING_VIEW'))
);

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

$searchForm = htmlBase::newElement('form')
	->attr('name', 'search')
	->attr('id', 'searchForm')
	->attr('action', itw_app_link(null,'rental_queue', 'pickup_requests_report'))
	->attr('method', 'get');


$submitButton = htmlBase::newElement('button')
	->setType('submit')
	->usePreset('save')
	->setText('Search');

$searchForm
	->append($limitField)
	->append($submitButton);

$tableGrid->addHeaderRow(array(
		'columns' => $gridHeaderColumns
	));

$rentedProd = array();

$pickupr = &$tableGrid->getResults();
if ($pickupr){
	foreach($pickupr as $pickup){
		$pickupId = $pickup['customers_pickup_requests_id'];

		if(isset($pickup['RentedQueue']['Customers']['customers_delivery_address_id'])){
			$deliveryAdress = $pickup['RentedQueue']['Customers']['customers_delivery_address_id'];
		}else{
			$deliveryAdress = $pickup['RentedQueue']['Customers']['customers_default_address_id'];
		}

		$QAddress = Doctrine_Query::create()
		->from('AddressBook')
		->where('address_book_id = ?', $deliveryAdress)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$gridBodyColumns = array(
				array('text' => $pickup['PickupRequests']['start_date'].' '.$pickup['PickupRequests']['PickupRequestsTypes']['type_name']),
				array('text' => $pickup['RentedQueue']['Customers']['customers_firstname'].' '.$pickup['RentedQueue']['Customers']['customers_lastname']),
				array('text' => tep_address_format(tep_get_address_format_id($QAddress[0]['entry_country_id']), $QAddress[0],'','','','short')),
				array('text' => '<div class="address" address="'.tep_address_format(tep_get_address_format_id($QAddress[0]['entry_country_id']), $QAddress[0],'','','','short').'">View</div>')
		);
		$tableGrid->addBodyRow(array(
					'rowAttr' => array(
						'data-order_id' => $pickupId
					),
					'columns' => $gridBodyColumns
		));

	}
}

?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<div style="width:100%"><?php
	echo $searchForm->draw();
	?></div>
<br />
<div style="width:100%;float:left;">
	<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
		<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		<br style="clear:both;"/> <br/>
	</div>
</div>
<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=<?php echo sysConfig::get('GOOGLE_MAPS_API_KEY');?>"></script>
