 <div class="pageHeading"><?php echo "Consumption Products Report";?></div>
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
        <th class="ui-widget-header" align="center"><b>Store</b></th>
	 <th class="ui-widget-header" align="center"><b>Product</b></th>
     <th class="ui-widget-header" align="center"><b>Serial</b></th>
     <th class="ui-widget-header" align="center"><b>Time Used</b></th>
	 <th class="ui-widget-header" align="center"><b>Owed</b></th>
    </tr>
</thead>
	   <tbody>
<?php
if(isset($_GET['start_date']) && isset($_GET['end_date'])){
    $seconds_minute = 60;
    $differenceTotal = 0;
    $total  = 0;
    $Qbarcodes = Doctrine_Query::create()
        ->from('ProductsInventoryBarcodes pib')
        ->leftJoin('pib.ProductsInventory pi')
        ->leftJoin('pi.Products p')
        ->leftJoin('p.ProductsPayPerRental ppr')
        ->andWhere('pi.type = ?', 'reservation')
        ->andWhere('ppr.consumption = ?', '1')
        ->andWhere('ppr.commission = ?', '1');

    EventManager::notify('AdminOrdersListingBeforeExecuteReportConsumptionBarcodes', &$Qbarcodes);

    $Barcodes = $Qbarcodes->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    foreach($Barcodes as $bc){


        $Qorders = Doctrine_Query::create()
        ->from('OrdersProductsReservation ops')
        ->leftJoin('ops.OrdersProducts op')
        ->leftJoin('op.Orders o')
        ->andWhere('ops.rental_state = ?', 'returned')
        ->andWhere('ops.start_date >= ?', $_GET['start_date'])
        ->andWhere('ops.end_date <= ?', $_GET['end_date'])
        ->andWhere('ops.barcode_id = ?', $bc['barcode_id']);


        EventManager::notify('AdminOrdersListingBeforeExecuteReportConsumption', &$Qorders);

        $ResultO = $Qorders->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $totalBarcode = 0;
        $differenceBarcode = 0;

            foreach($ResultO as $qo){

                $start_datetime = $qo['start_date'];
                $end_datetime = $qo['end_date'];
                $differenceBarcode += strtotime($qo['end_date']) - strtotime($qo['start_date']);
                $type = Doctrine_Core::getTable('PayPerRentalTypes')->findOneByPayPerRentalTypesId($qo['OrdersProducts']['Orders']['OrdersToStores']['Stores']['commission_type']);
                $unitTime = $differenceBarcode / ($type['minutes'] * $seconds_minute);
                $time = $unitTime * ($qo['OrdersProducts']['Orders']['OrdersToStores']['Stores']['commission'] / 100);
                $totalBarcode += $qo['OrdersProducts']['final_price'] * $time/ $unitTime;
            }

        $differenceTotal += $differenceBarcode;
        $total += $totalBarcode;

        if($differenceBarcode < $seconds_minute * $seconds_minute){
            $hoursDiff = '0';
            $minutesDiffRemainder = round($differenceBarcode / $seconds_minute);
        }
        else{
            $hoursDiff = round($differenceBarcode / $seconds_minute);
            $minutesDiffRemainder = round($differenceBarcode % $seconds_minute);

        }

        if($minutesDiffRemainder < 10)
            $minutesDiffRemainder = '0'.$minutesDiffRemainder;

        $product = Doctrine_Core::getTable('ProductsDescription')->findOneByProductsId($bc['ProductsInventory']['products_id']);

        echo '<tr class="productListingRow-dater">
                  <td class="maind" align="center">'  . $bc['ProductsInventoryBarcodesToStores']['Stores']['stores_name'] . '</td>
                  <td class="maind" align="center">'  . $product['products_name']. '</td>
                  <td class="maind" align="center">'  . $bc['barcode'] . '</td>
                  <td class="maind" align="center">'. $hoursDiff . ':' . $minutesDiffRemainder . '</td>
                  <td class="maind" align="center">'. $currencies->format($totalBarcode).'</td>
                 </tr>';

    }

    if($differenceBarcode < $seconds_minute){
        $hoursDiff = 0;
        // print($hoursDiff);
        $minutesDiffRemainder = round($differenceTotal / $seconds_minute);
    }else{
        $hoursDiff = round($differenceTotal / $seconds_minute);
        $minutesDiffRemainder = round($differenceTotal % $seconds_minute);
    }
    if($minutesDiffRemainder < 10)
        $minutesDiffRemainder = '0'.$minutesDiffRemainder;

    echo '<tr class="productListingRow-dater">
                  <td class="maind" align="right" colspan="5">Total Time:'. $hoursDiff . ':' . $minutesDiffRemainder . '</td>
                 </tr>';
    echo '<tr class="productListingRow-dater">
                  <td class="maind" align="right" colspan="5">Total Amount:'. $currencies->format($total).'</td>
                 </tr>';

}

?>

</tbody> </table>
</div>