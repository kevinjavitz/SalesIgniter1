 <div class="pageHeading"><?php echo "Rental Orders History ";?></div>
 <br />
	 <div>

	 <?php
	 require(DIR_WS_CLASSES . 'currencies.php');
	 $currencies = new currencies();
	 $searchForm = htmlBase::newElement('form')
	 ->attr('name', 'search')
	 ->attr('action', itw_app_link(tep_get_all_get_params()))
	 ->attr('method', 'get');

	/* $startdateField = htmlBase::newElement('input')->setName('start_date')
	 ->setLabel(sysLanguage::get('HEADING_TITLE_START_DATE'))->setLabelPosition('before')->setId('start_date');

	 $enddateField = htmlBase::newElement('input')->setName('end_date')
	 ->setLabel(sysLanguage::get('HEADING_TITLE_START_END'))->setLabelPosition('before')->setId('end_date');*/

	 /*$cntProviderField = htmlBase::newElement('input')->setName('cnt_provider')
	 ->setLabel(sysLanguage::get('HEADING_TITLE_CNT_PROVIDER'))->setLabelPosition('before')->setId('cnt_provider');
	 */

	$barcode_list = htmlBase::newElement('selectbox')
			->setName('barcode_id')
			->attr('id','barcode_list');
	$barcode_list->addOption('0','Select Barcode');

	if(isset($_GET['product_id'])){

		$Qbarcodes = Doctrine_Query::create()
					->select('i.inventory_id, b.barcode_id, b.barcode')
					->from('ProductsInventory i')
					->leftJoin('i.ProductsInventoryBarcodes b')
					->where('i.products_id = ?', $_GET['product_id'])
					->andWhere('i.type = "reservation"')
					->andWhere('i.track_method = "barcode"');

					/* If at some point filters for stores or Inventory centers will be added
					if (isset($storeId)){
						$Qbarcodes->leftJoin('b.ProductsInventoryBarcodesToStores b2s')
						->andWhere('b2s.inventory_store_id = ?', $storeId);
					}
					if (isset($inventoryCenterId)){
						$Qbarcodes->leftJoin('b.ProductsInventoryBarcodesToInventoryCenters b2c')
						->andWhere('b2c.inventory_center_id = ?', $inventoryCenterId);
					}*/

		$ResultB = $Qbarcodes->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			foreach ($ResultB as $pr){
				foreach($pr['ProductsInventoryBarcodes'] as $bar){
					$barcode_list->addOption($bar['barcode_id'],$bar['barcode']);
				}
			}		
	}

	$product_list = htmlBase::newElement('selectbox')
			->setName('product_id')
			->attr('id','product_list');

	$product_list->addOption('0','Select Product');

	$lID = (int)Session::get('languages_id');

	$Qproducts = Doctrine_Query::create()
	->select('p.products_id, pd.products_name')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->where('pd.language_id = ?', $lID)
	->andWhere('p.products_in_box = ?', '0')
	->orderBy('p.products_featured desc, pd.products_name asc, p.products_id desc');

	EventManager::notify('ProductListingQueryBeforeExecute', &$Qproducts);

	$Result = $Qproducts->execute(array(), Doctrine_Core::HYDRATE_ARRAY);


		foreach($Result as $pr){
			
			if (tep_not_null($pr['products_model'])){
				$product_list->addOption($pr['products_model']);
			}else{
				$product_list->addOption($pr['products_id'],$pr['ProductsDescription'][0]['products_name']);
			}
		}	

		 if(isset($_GET['product_id'])){
			 $product_list->selectOptionByValue($_GET['product_id']);
		 }

		 if(isset($_GET['barcode_id'])){
			 $barcode_list->selectOptionByValue($_GET['barcode_id']);
		 }

	 $gobut = htmlBase::newElement('button')
	 ->setType('submit')
	 ->setText('Submit');

	/* if (isset($_GET['start_date'])){
	 	$startdateField->setValue($_GET['start_date']);
	 }

	 if (isset($_GET['end_date'])){
	 	$enddateField->setValue($_GET['end_date']);
	 }
*/
	 /*if (isset($_GET['cnt_provider'])){
	 $cntProviderField->setValue($_GET['cnt_provider']);
	 }*/

	// $searchForm->append($startdateField);
	// $searchForm->append($enddateField);
	 //$searchForm->append($cntProviderField);

	$searchForm->append($product_list)->append($barcode_list);
	$searchForm->append($gobut);

	 echo $searchForm->draw();
?>
	 </div>
<div class="ui-widget ui-widget-content ui-corner-all pageButtonBar">
<table border="0" cellspacing="0" cellpadding="3" width="100%">
<thead>
    <tr>
	 <th class="ui-widget-header" align="center"><b>Customer</b></th>
     <th class="ui-widget-header" align="center"><b>Dates</b></th>
     <th class="ui-widget-header" align="center"><b>Order</b></th>
	 <th class="ui-widget-header" align="center"><b>Amount Paid</b></th>
    </tr>
</thead>
	   <tbody>
<?php
if(isset($_GET['product_id']) && isset($_GET['barcode_id'])){
	$Qorders = Doctrine_Query::create()
	->from('OrdersProductsReservation ops')
	->leftJoin('ops.OrdersProducts op')
	->leftJoin('op.Orders o');

	if(isset($_GET['product_id'])){
		$Qorders->andWhere('op.products_id = ?', $_GET['product_id']);
	}

	if(isset($_GET['barcode_id'])){
		$Qorders->andWhere('ops.barcode_id = ?', $_GET['barcode_id']);
	}

	EventManager::notify('OrdersListingBeforeExecute', &$Qorders);

	/*if (isset($_GET['start_date']) && tep_not_null($_GET['start_date'])){
		$Qorders->andWhere('o.date_purchased >= ?', $_GET['start_date']);
	}

	if (isset($_GET['end_date']) && tep_not_null($_GET['end_date'])){
		$Qorders->andWhere('o.date_purchased <= ?', $_GET['end_date']);
	}*/

	$ResultO = $Qorders->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	/*if (isset($_GET['provider']) && tep_not_null($_GET['provider']) ){
	$cntp = Doctrine_Query::create()
	->from('Customers')
	->where('concat(customers_firstname, customers_lastname) LIKE ?',"%".$_GET['provider']."%" )
	->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

	$Qorders->andWhere('p.provider in ?', $cntp[0]['customers_id']);
	}*/

	$total  = 0;
		foreach($ResultO as $qo){

			$customer = Doctrine_Core::getTable('Customers')->findOneByCustomersId($qo['OrdersProducts']['Orders']['customers_id']);
			$total += $qo['OrdersProducts']['products_price'];
			echo '<tr class="productListingRow-dater">
			  <td class="maind" align="center">'  . $customer['customers_firstname']." ".$customer['customers_lastname'] . '</td>
			  <td class="maind" align="center">Start Date:'  . tep_date_short($qo['start_date'])."<br>End Date:".tep_date_short($qo['start_date']) .'</td>
			  <td class="maind" align="center"><a href="'  . itw_app_link('oID='. $qo['OrdersProducts']['Orders']['orders_id'],'orders','details') .'">View Order</a></td>
			  <td class="maind" align="center">'  . $currencies->format($qo['OrdersProducts']['products_price']).'</td>
			 </tr>';

		}
			echo '<tr class="productListingRow-dater">
			  <td class="maind" align="right" colspan="4">Total:'. $currencies->format($total).'</td>
			 </tr>';
}

?>

</tbody> </table>
</div>