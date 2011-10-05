<?php
if(!isset($_GET['type']) || $_GET['type'] == 'rental'){
	$Qorders = Doctrine_Query::create()
	->from('RentedProducts rp')
	->leftJoin('rp.Customers c')
	->leftJoin('rp.Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('rp.ProductsInventoryBarcodes pib');

	$f = false;
	if (isset($_GET['start_date'])){
		$Qorders->andWhere('rp.shipment_date=?', $_GET['start_date']);
	}

	if (isset($_GET['end_date'])){
		$Qorders->andWhere('rp.return_date=?', $_GET['end_date']);
	}

	if(isset($_GET['sortDateSent'])){
		$Qorders->orderBy('rp.shipment_date '.$_GET['sortDateSent']);
		$f = true;
	}
	if(isset($_GET['sortDateReturned'])){
		$Qorders->orderBy('rp.return_date '.$_GET['sortDateReturned']);
		$f = true;
	}

	if(isset($_GET['sortName'])){
		$Qorders->orderBy('CONCAT(c.customers_firstname, c.customers_lastname)  '.$_GET['sortName']);
		$f = true;
	}

	if(isset($_GET['sortProduct'])){
		$Qorders->orderBy('pd.products_name '.$_GET['sortProduct']);
		$f = true;
	}

	if(isset($_GET['sortBarcode'])){
		$Qorders->orderBy('pib.barcode '.$_GET['sortBarcode']);
		$f = true;
	}

}else{
	$Qorders = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.Customers c')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('op.OrdersProductsReservation opr')
	->leftJoin('opr.ProductsInventoryBarcodes pib')
	->leftJoin('o.OrdersStatus s')
	->andWhere('o.orders_status != ?', sysConfig::get('ORDERS_STATUS_ESTIMATE_ID'))
	->andWhere('pd.language_id = ?', Session::get('languages_id'))
	->andWhereIn('opr.rental_state', array('reserved'));

	$f = false;
	if (isset($_GET['start_date']) && !empty($_GET['start_date'])){
		$Qorders->andWhere('opr.start_date=?', $_GET['start_date']);
	}

	if (isset($_GET['end_date'])&& !empty($_GET['end_date'])){
		$Qorders->andWhere('opr.end_date=?', $_GET['end_date']);
	}

	if(isset($_GET['sortDateSent'])){
		$Qorders->orderBy('opr.date_shipped '.$_GET['sortDateSent']);
		$f = true;
	}
	if(isset($_GET['sortDateReturned'])){
		$Qorders->orderBy('opr.date_returned '.$_GET['sortDateReturned']);
		$f = true;
	}

	if(isset($_GET['sortName'])){
		$Qorders->orderBy('CONCAT(c.customers_firstname, c.customers_lastname)  '.$_GET['sortName']);
		$f = true;
	}

	if(isset($_GET['sortProduct'])){
		$Qorders->orderBy('pd.products_name '.$_GET['sortProduct']);
		$f = true;
	}

	if(isset($_GET['sortBarcode'])){
		$Qorders->orderBy('pib.barcode '.$_GET['sortBarcode']);
		$f = true;
	}

}
$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qorders);

$gridHeaderColumns = array(
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&type='.(isset($_GET['type'])?$_GET['type']:'rental').'&sortName='.(isset($_GET['sortName'])?($_GET['sortName'] == 'ASC'?'DESC':'ASC'):'ASC'),null,null).'">'.sysLanguage::get('TABLE_HEADING_NAME').'</a>'),
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&type='.(isset($_GET['type'])?$_GET['type']:'rental').'&sortProduct='.(isset($_GET['sortProduct'])?($_GET['sortProduct'] == 'ASC'?'DESC':'ASC'):'ASC'),null,null).'">'.sysLanguage::get('TABLE_HEADING_PRODUCT').'</a>'),
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&type='.(isset($_GET['type'])?$_GET['type']:'rental').'&sortBarcode='.(isset($_GET['sortBarcode'])?($_GET['sortBarcode'] == 'ASC'?'DESC':'ASC'):'ASC'),null,null).'">'.sysLanguage::get('TABLE_HEADING_BARCODE').'</a>'),
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&type='.(isset($_GET['type'])?$_GET['type']:'rental').'&sortDateSent='.(isset($_GET['sortDateSent'])?($_GET['sortDateSent'] == 'ASC'?'DESC':'ASC'):'ASC'),null,null).'">'.sysLanguage::get('TABLE_HEADING_DATE_SENT').'</a>'),
	array('text' => '<a href="'.itw_app_link('appExt=payPerRentals&type='.(isset($_GET['type'])?$_GET['type']:'rental').'&sortDateReturned='.(isset($_GET['sortDateReturned'])?($_GET['sortDateReturned'] == 'ASC'?'DESC':'ASC'):'ASC'),null,null).'">'.sysLanguage::get('TABLE_HEADING_DATE_RETURNED').'</a>'),
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
	->attr('action', itw_app_link('appExt=payPerRentals','sendreturn_reports', 'default'))
	->attr('method', 'get');

$startdateField = htmlBase::newElement('input')
	->setName('start_date')
	->setLabel(sysLanguage::get('HEADING_TITLE_START_DATE'))
	->setLabelPosition('before')
	->setId('start_date');

if (isset($_GET['start_date'])){
	$startdateField->setValue($_GET['start_date']);
}

$enddateField = htmlBase::newElement('input')
	->setName('end_date')
	->setLabel(sysLanguage::get('HEADING_TITLE_END_DATE'))
	->setLabelPosition('before')
	->setId('end_date');

if (isset($_GET['end_date'])){
	$enddateField->setValue($_GET['end_date']);
}


$typeSelect = htmlBase::newElement('selectbox')
	->setName('type')
	->setLabel('Type: ')
	->setLabelPosition('before');

/*$startdateField = htmlBase::newElement('input')
	->setName('start_date')
	->setLabel('Event Date: ')
	->setLabelPosition('before')
	->setId('start_date');

if (isset($_GET['start_date']) && !empty($_GET['start_date'])){
	$startdateField->val($_GET['start_date']);
} */
if (isset($_GET['type']) && !empty($_GET['type'])){
	$typeSelect->selectOptionByValue($_GET['type']);
}

$typeSelect->addOption('rental','Rental');
$typeSelect->addOption('reservation','Reservation');

$submitButton = htmlBase::newElement('button')
->setType('submit')
->usePreset('save')
->setText('Search');

$searchForm
->append($limitField)
->append($startdateField)
->append($enddateField)
->append($typeSelect)
->append($submitButton);

$tableGrid->addHeaderRow(array(
		'columns' => $gridHeaderColumns
));

$orders = &$tableGrid->getResults();
$total = 0;
if ($orders){
	foreach($orders as $order){
		if(!isset($_GET['type']) || $_GET['type'] == 'rental'){
			$vId = $order['rented_products_id'];
			//foreach($order['Products'] as $orderp){
				$gridBodyColumns = array(
					array('text' => $order['Customers']['customers_firstname'] .' '.$order['Customers']['customers_lastname']),
					array('text' => $order['Products']['ProductsDescription'][Session::get('languages_id')]['products_name']),
					array('text' => $order['ProductsInventoryBarcodes']['barcode']),
					array('text' => $order['shipment_date']),
					array('text' => $order['return_date'])

				);
				$tableGrid->addBodyRow(array(
						'rowAttr' => array(
							'data-order_id' => $vId
						),
						'columns' => $gridBodyColumns
				));
			//}
		}else{
			$vId = $order['orders_id'];

			foreach($order['OrdersProducts'] as $orderp) {
				foreach($orderp['OrdersProductsReservation'] as $ores){
					$gridBodyColumns = array(
						array('text' => $order['Customers']['customers_firstname'] .' '.$order['Customers']['customers_lastname']),
						array('text' => $orderp['Products']['ProductsDescription'][Session::get('languages_id')]['products_name']),
						array('text' => $ores['ProductsInventoryBarcodes']['barcode']),
						array('text' => $ores['date_shipped']),
						array('text' => $ores['date_returned'])

					);
					$tableGrid->addBodyRow(array(
							'rowAttr' => array(
								'data-order_id' => $vId
							),
							'columns' => $gridBodyColumns
					));
				}
			}
		}

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

