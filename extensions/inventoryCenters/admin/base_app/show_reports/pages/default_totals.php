 <div class="pageHeading"><?php echo "Inventory Centers Comissions ";?></div>
 <br />
	 <div>

	 <?php
	 require(DIR_WS_CLASSES . 'currencies.php');
	 $currencies = new currencies();
	 $searchForm = htmlBase::newElement('form')
	 ->attr('name', 'search')
	 ->attr('action', itw_app_link(tep_get_all_get_params()))
	 ->attr('method', 'get');

	 $startdateField = htmlBase::newElement('input')->setName('start_date')
	 ->setLabel(sysLanguage::get('HEADING_TITLE_START_DATE'))->setLabelPosition('before')->setId('start_date');

	 $enddateField = htmlBase::newElement('input')->setName('end_date')
	 ->setLabel(sysLanguage::get('HEADING_TITLE_START_END'))->setLabelPosition('before')->setId('end_date');

	 $gobut = htmlBase::newElement('button')
	 ->setType('submit')
	 ->setText('Submit');

	 if (isset($_GET['start_date'])){
	 	$startdateField->setValue($_GET['start_date']);
	 }

	 if (isset($_GET['end_date'])){
	 	$enddateField->setValue($_GET['end_date']);
	 }

	 $searchForm->append($startdateField);
	 $searchForm->append($enddateField);
	 $searchForm->append($gobut);
	 echo $searchForm->draw();

?>
	 </div>
<div class="ui-widget ui-widget-content ui-corner-all pageButtonBar">
<table border="0" cellspacing="0" cellpadding="3" width="100%">
<thead>
    <tr>
	 <th class="ui-widget-header" align="center"><b>Total Sales</b></th>
	 <th class="ui-widget-header" align="center"><b>Amount Owed</b></th>
     <th class="ui-widget-header" align="center"><b>Comissions</b></th>
     <th class="ui-widget-header" align="center"><b>Owner</b></th>
    </tr>
</thead>
	   <tbody>
<?php

	$Qorders = Doctrine_Query::create()
	->select('
			o.*,
			SUM(ot.value) as tot,
			op.*,
			s.orders_status_id,
			ops.*
		')
	->from('OrdersProductsReservation ops')
	->leftJoin('ops.OrdersProducts op')
	->leftJoin('op.Orders o')
	->leftJoin('o.OrdersTotal ot')
	->leftJoin('o.OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->andWhereIn('ot.module_type', array('total', 'ot_total'))
	->andWhere('sd.orders_status_name LIKE ?','%pprove%')
	->andWhere('sd.language_id = ?', Session::get('languages_id'))
	->groupBy('ops.inventory_center_pickup');

	if (isset($_GET['start_date']) && tep_not_null($_GET['start_date'])){
		$Qorders->andWhere('o.date_purchased >= ?', $_GET['start_date']);
	}

	if (isset($_GET['end_date']) && tep_not_null($_GET['end_date'])){
		$Qorders->andWhere('o.date_purchased <= ?', $_GET['end_date']);
	}

	$Result = $Qorders->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	if ($Result){

		foreach($Result as $qo){
			$Qinv = Doctrine_Core::getTable('ProductsInventoryCenters')->findOneByInventoryCenterId((int)$qo['inventory_center_pickup']);
			$provider  =Doctrine_Core::getTable('Customers')->findOneByCustomersId((int)$Qinv['inventory_center_customer']);
			$com = $Qinv['inventory_center_comission'];

			echo '<tr class="productListingRow-dater">
			  <td class="maind" align="center">'  . $currencies->format($qo['tot']) . '</td>

			  <td class="maind" align="center">'  . $currencies->format($qo['tot'] - (float)$qo['tot']*$com/100).'</td>
			  <td class="maind" align="center">'  . $currencies->format((float)$qo['tot']*$com/100).'</td>
			  <td class="maind" align="center">'  . $provider['customers_firstname']." ".$provider['customers_lastname'].'</td>
			 </tr>';

		}
	}

?>

</tbody> </table>
</div>