<?php
	$Qorders = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.Customers c')
	->leftJoin('o.OrdersTotal ot')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('op.OrdersProductsReservation opr')
	->leftJoin('o.OrdersStatus s')
	->andWhere('o.orders_status != ?', sysConfig::get('ORDERS_STATUS_ESTIMATE_ID'))
	->andWhere('pd.language_id = ?', Session::get('languages_id'))
	->andWhereIn('ot.module_type', array('total', 'ot_total'))
	->andWhereIn('opr.rental_state', array('out', 'reserved'));

$f = false;
if (isset($_GET['start_date']) && !empty($_GET['start_date'])){
	$Qorders->andWhere('opr.start_date=?', $_GET['start_date']);
}

if (isset($_GET['event_name']) && !empty($_GET['event_name'])){
	$Qorders->andWhere('opr.event_name = ?', $_GET['event_name']);
}

if (isset($_GET['event_gate']) && !empty($_GET['event_gate'])){
	$Qorders->andWhere('opr.event_gate = ?', $_GET['event_gate']);
}

if(isset($_GET['sortEvent'])){
	$Qorders->orderBy('opr.event_name '.$_GET['sortEvent']);
	$f = true;
}

if(isset($_GET['sortDate'])){
	$Qorders->orderBy('o.date_purchased '.$_GET['sortDate']);
	$f = true;
}

if(isset($_GET['sortDateReserved'])){
	$Qorders->orderBy('opr.start_date '.$_GET['sortDateReserved']);
	$f = true;
}


if(isset($_GET['sortGate'])){
	$Qorders->orderBy('opr.event_gate '.$_GET['sortGate']);
	$f = true;
}

if(isset($_GET['sortLastname'])|| !is_array($_GET)){
	$Qorders->orderBy('c.customers_lastname '.$_GET['sortLastname']);
	$f = true;
}

if(isset($_GET['sortFirstname'])){
	$Qorders->orderBy('c.customers_firstname '.$_GET['sortFirstname']);
	$f = true;
}

if(isset($_GET['sortProduct'])){
	$Qorders->orderBy('pd.products_name '.$_GET['sortProduct']);
	$f = true;
}

if(isset($_GET['sortPrice'])){
	$Qorders->orderBy('ot.value '.$_GET['sortPrice']);
	$f = true;
}

if(isset($_GET['sortQty'])){
	$Qorders->orderBy('op.products_quantity '.$_GET['sortQty']);
	$f = true;
}

if(isset($_GET['sortInsurance'])){
	$Qorders->orderBy('opr.insurance '.$_GET['sortInsurance']);
	$f = true;
}

if($f == false){
	$Qorders->orderBy('opr.start_date '.$_GET['sortDateReserved']);
	$Qorders->orderBy('c.customers_lastname '.$_GET['sortLastname']);
}


$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qorders);

$gridHeaderColumns = array(
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&sortEvent='.(isset($_GET['sortEvent'])?($_GET['sortEvent'] == 'ASC'?'DESC':'ASC'):'ASC').(isset($_GET['event_name'])&&!empty($_GET['event_name'])?'&event_name='.$_GET['event_name']:'').(isset($_GET['start_date'])&&!empty($_GET['start_date'])?'&start_date='.$_GET['start_date']:'').(isset($_GET['event_gate'])&&!empty($_GET['event_gate'])?'&event_gate='.$_GET['event_gate']:''),null,null).'">'.sysLanguage::get('TABLE_HEADING_EVENT').'</a>'),
	//array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&sortDate='.(isset($_GET['sortDate'])?($_GET['sortDate'] == 'ASC'?'DESC':'ASC'):'ASC').(isset($_GET['event_name'])?'&event_name='.$_GET['event_name']:''),null,null).'">'.sysLanguage::get('TABLE_HEADING_DATE').'</a>'),
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&sortDateReserved='.(isset($_GET['sortDateReserved'])?($_GET['sortDateReserved'] == 'ASC'?'DESC':'ASC'):'ASC').(isset($_GET['event_name'])&&!empty($_GET['event_name'])?'&event_name='.$_GET['event_name']:'').(isset($_GET['start_date'])&&!empty($_GET['start_date'])?'&start_date='.$_GET['start_date']:'').(isset($_GET['event_gate'])&&!empty($_GET['event_gate'])?'&event_gate='.$_GET['event_gate']:''),null,null).'">'.sysLanguage::get('TABLE_HEADING_DATE_RESERVED').'</a>'),
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&sortGate='.(isset($_GET['sortGate'])?($_GET['sortGate'] == 'ASC'?'DESC':'ASC'):'ASC').(isset($_GET['event_name'])&&!empty($_GET['event_name'])?'&event_name='.$_GET['event_name']:'').(isset($_GET['start_date'])&&!empty($_GET['start_date'])?'&start_date='.$_GET['start_date']:'').(isset($_GET['event_gate'])&&!empty($_GET['event_gate'])?'&event_gate='.$_GET['event_gate']:''),null,null).'">'.sysLanguage::get('TABLE_HEADING_GATE').'</a>'),
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&sortLastname='.(isset($_GET['sortLastname'])?($_GET['sortLastname'] == 'ASC'?'DESC':'ASC'):'ASC').(isset($_GET['event_name'])&&!empty($_GET['event_name'])?'&event_name='.$_GET['event_name']:'').(isset($_GET['start_date'])&&!empty($_GET['start_date'])?'&start_date='.$_GET['start_date']:'').(isset($_GET['event_gate'])&&!empty($_GET['event_gate'])?'&event_gate='.$_GET['event_gate']:''),null,null).'">'.sysLanguage::get('TABLE_HEADING_LASTNAME').'</a>'),
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&sortFirstname='.(isset($_GET['sortFirstname'])?($_GET['sortFirstname'] == 'ASC'?'DESC':'ASC'):'ASC').(isset($_GET['event_name'])&&!empty($_GET['event_name'])?'&event_name='.$_GET['event_name']:'').(isset($_GET['start_date'])&&!empty($_GET['start_date'])?'&start_date='.$_GET['start_date']:'').(isset($_GET['event_gate'])&&!empty($_GET['event_gate'])?'&event_gate='.$_GET['event_gate']:''),null,null).'">'.sysLanguage::get('TABLE_HEADING_FIRSTNAME').'</a>'),
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&sortProduct='.(isset($_GET['sortProduct'])?($_GET['sortProduct'] == 'ASC'?'DESC':'ASC'):'ASC').(isset($_GET['event_name'])&&!empty($_GET['event_name'])?'&event_name='.$_GET['event_name']:'').(isset($_GET['start_date'])&&!empty($_GET['start_date'])?'&start_date='.$_GET['start_date']:'').(isset($_GET['event_gate'])&&!empty($_GET['event_gate'])?'&event_gate='.$_GET['event_gate']:''),null,null).'">'.sysLanguage::get('TABLE_HEADING_PRODUCT_NAME').'</a>'),
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&sortQty='.(isset($_GET['sortQty'])?($_GET['sortQty'] == 'ASC'?'DESC':'ASC'):'ASC').(isset($_GET['event_name'])&&!empty($_GET['event_name'])?'&event_name='.$_GET['event_name']:'').(isset($_GET['start_date'])&&!empty($_GET['start_date'])?'&start_date='.$_GET['start_date']:'').(isset($_GET['event_gate'])&&!empty($_GET['event_gate'])?'&event_gate='.$_GET['event_gate']:''),null,null).'">'.sysLanguage::get('TABLE_HEADING_QUANTITY').'</a>'),
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&sortInsurance='.(isset($_GET['sortInsurance'])?($_GET['sortInsurance'] == 'ASC'?'DESC':'ASC'):'ASC').(isset($_GET['event_name'])&&!empty($_GET['event_name'])?'&event_name='.$_GET['event_name']:'').(isset($_GET['start_date'])&&!empty($_GET['start_date'])?'&start_date='.$_GET['start_date']:'').(isset($_GET['event_gate'])&&!empty($_GET['event_gate'])?'&event_gate='.$_GET['event_gate']:''),null,null).'">'.sysLanguage::get('TABLE_HEADING_INSURANCE').'</a>'),
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&sortPrice='.(isset($_GET['sortPrice'])?($_GET['sortPrice'] == 'ASC'?'DESC':'ASC'):'ASC').(isset($_GET['event_name'])&&!empty($_GET['event_name'])?'&event_name='.$_GET['event_name']:'').(isset($_GET['start_date'])&&!empty($_GET['start_date'])?'&start_date='.$_GET['start_date']:'').(isset($_GET['event_gate'])&&!empty($_GET['event_gate'])?'&event_gate='.$_GET['event_gate']:''),null,null).'">'.sysLanguage::get('TABLE_HEADING_PRICE').'</a>')
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
	->attr('id', 'searchFormOrders')
	->attr('action', itw_app_link('appExt=payPerRentals','event_reports', 'default'))
	->attr('method', 'get');

$startdateField = htmlBase::newElement('input')->setName('start_date')
	->setLabel(sysLanguage::get('HEADING_TITLE_START_DATE'))->setLabelPosition('before')->setId('start_date');

if (isset($_GET['start_date']) && !empty($_GET['start_date'])){
	$startdateField->setValue($_GET['start_date']);
}

$eventSelect = htmlBase::newElement('selectbox')
	->setName('event_name')
	->setLabel('Event Name: ')
	->setLabelPosition('before');

$gateSelect = htmlBase::newElement('selectbox')
	->setName('event_gate')
	->setLabel('Event Gate: ')
	->setLabelPosition('before');

/*$startdateField = htmlBase::newElement('input')
	->setName('start_date')
	->setLabel('Event Date: ')
	->setLabelPosition('before')
	->setId('start_date');

if (isset($_GET['start_date']) && !empty($_GET['start_date'])){
	$startdateField->val($_GET['start_date']);
} */
if (isset($_GET['event_name']) && !empty($_GET['event_name'])){
	$eventSelect->selectOptionByValue($_GET['event_name']);
	$QgatesSelected = Doctrine_Query::create()
		->from('PayPerRentalEvents')
		->where('events_name = ?', $_GET['event_name'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if(isset($QgatesSelected[0])){
		$gatesSelected = explode(',',$QgatesSelected[0]['gates']);
	}
}

if (isset($_GET['event_gate']) && !empty($_GET['event_gate'])){
	$gateSelect->selectOptionByValue($_GET['event_gate']);
}

$Qevents = Doctrine_Query::create()
->from('PayPerRentalEvents')
->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$eventSelect->addOption('','all');
foreach($Qevents as $iEvent){
	$eventSelect->addOption($iEvent['events_name'], $iEvent['events_name']);
}

$Qgates = Doctrine_Query::create()
	->from('PayPerRentalGates');
if(isset($gatesSelected)){
	$Qgates->whereIn('gates_id', $gatesSelected);
}
$Qgates = $Qgates->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$gateSelect->addOption('','all');
foreach($Qgates as $iGate){
	$gateSelect->addOption($iGate['gate_name'], $iGate['gate_name']);
}

$submitButton = htmlBase::newElement('button')
	->setType('submit')
	->usePreset('save')
	->setText('Search');

$searchForm
->append($limitField)
->append($eventSelect)
->append($gateSelect)
->append($startdateField)
->append($submitButton);

$tableGrid->addHeaderRow(array(
		'columns' => $gridHeaderColumns
	));

$rentedProd = array();

$orders = &$tableGrid->getResults();
$total = 0;
if ($orders){
	foreach($orders as $order){
		$orderId = $order['orders_id'];

		foreach($order['OrdersProducts'] as $orderp) {
			//foreach($orderp['OrdersProductsReservation'] as $ores){
				$ores = $orderp['OrdersProductsReservation'][0];
				$gridBodyColumns = array(
					array('text' => $ores['event_name']),
					//array('text' => $order['date_purchased']),
					array('text' => $ores['start_date']),
					array('text' => $ores['event_gate']),
					array('text' => $order['Customers']['customers_lastname']),
					array('text' => $order['Customers']['customers_firstname']),
					array('text' => $orderp['Products']['ProductsDescription'][Session::get('languages_id')]['products_name']),
					array('text' => $orderp['products_quantity']),
					array('text' => $ores['insurance']),
					array('text' => $currencies->format($orderp['final_price']*$orderp['products_quantity']))

				);
				$total += $orderp['final_price']*$orderp['products_quantity'];
				if(!isset($rentedProd[$orderp['Products']['products_model']])){
					$rentedProd[$orderp['Products']['products_model']][$ores['event_name']] = 0;
				}
				$rentedProd[$orderp['Products']['products_model']][$ores['event_name']] += $orderp['products_quantity'];
				$tableGrid->addBodyRow(array(
						'rowAttr' => array(
							'data-order_id' => $orderId
						),
						'columns' => $gridBodyColumns
					));
			//}
		}

	}
}

if (isset($_GET['event_name']) && !empty($_GET['event_name'])){
	$Qevents = Doctrine_Query::create()
	->from('PayPerRentalEvents')
	->where('events_name=?', $_GET['event_name'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);



	$avail = '';
	foreach($rentedProd as $model => $qty){
		$QProductEvents = Doctrine_Query::create()
		->from('ProductQtyToEvents')
		->where('events_id = ?', $Qevents[0]['events_id'])
		->andWhere('products_model = ?', $model)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if($QProductEvents){
			$avail .= '<b>Availability for model: '. $model .' is '. ($QProductEvents[0]['qty']-$qty[$Qevents[0]['events_name']]) .' items</b><br/>';
		}
	}
}else{
	$Qevents = Doctrine_Query::create()
		->from('PayPerRentalEvents')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);



	$avail = '';
	foreach($rentedProd as $model => $qty){
		foreach($Qevents as $iEvent){
			$QProductEvents = Doctrine_Query::create()
				->from('ProductQtyToEvents')
				->where('events_id = ?', $iEvent['events_id'])
				->andWhere('products_model = ?', $model)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if($QProductEvents){
				$avail .= '<b>Availability for model: '. $model .' for event: "'.$iEvent['events_name'].'" is '. ($QProductEvents[0]['qty']-$qty[$iEvent['events_name']]) .' items</b><br/>';
			}
		}
	}
}

$gridBodyColumns = array(
	array('text' => ''),
	array('text' => ''),
	array('text' => ''),
	array('text' => ''),
	array('text' => ''),
	array('text' => ''),
	array('text' => ''),
	array('text' => ''),
	array('text' =>'<b>Total for the day:</b>'. $currencies->format($total))

);
$tableGrid->addBodyRow(array(
		'columns' => $gridBodyColumns
));

?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<div style="width:100%"><?php
	echo $searchForm->draw();
	?></div>
<br />
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
			<div style="margin-left:30px;margin-top:10px;"><?php echo $avail;?></div>
			<br style="clear:both;"/> <br/>
		</div>
	</div>

