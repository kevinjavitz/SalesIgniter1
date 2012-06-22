<?php
$html = '';
ob_start();
$Qreservations = Doctrine_Query::create()
		->from('Orders o')
		->leftJoin('o.OrdersAddresses oa')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('op.OrdersProductsReservation opr')
		->leftJoin('opr.ProductsInventoryBarcodes ib')
		->leftJoin('ib.ProductsInventory i')
		->leftJoin('opr.ProductsInventoryQuantity iq')
		->leftJoin('iq.ProductsInventory i2')
		->where('oa.address_type = ?', 'delivery')
		->andWhere('opr.parent_id IS NULL')
		->orderBy('opr.end_date');
if (isset($_GET['include_returned']) && isset($_GET['include_unsent'])){
	$Qreservations->andWhereIn('opr.rental_state', array('returned','reserved', 'out'));
}elseif (isset($_GET['include_returned'])){
	$Qreservations->andWhereIn('opr.rental_state', array('returned', 'out'));
}elseif (isset($_GET['include_unsent'])){
	$Qreservations->andWhereIn('opr.rental_state', array('reserved', 'out'));
}else{
	$Qreservations->andWhere('opr.rental_state = ?', 'out');
}

if (isset($_GET['start_date']) || isset($_GET['end_date'])){
	//$Qreservations->andWhere('opr.start_date between "' . $_GET['start_date'] . '" and "' . $_GET['end_date'] . '" OR opr.end_date between "' . $_GET['start_date'] . '" and "' . $_GET['end_date'] . '"');
	$Qreservations->andWhere('opr.end_date between "' . $_GET['start_date'] . '" and "' . $_GET['end_date'] . '"');
}
if (isset($_GET['filter_shipping']) && !empty($_GET['filter_shipping'])){
	$Qreservations->andWhere('o.shipping_module LIKE ?', '%' . $_GET['filter_shipping'] . '%');
}
if (isset($_GET['filter_category']) && !empty($_GET['filter_category'])){
	$Qreservations->leftJoin('op.Products p')
			->leftJoin('p.ProductsToCategories ptc')
			->andWhere('ptc.categories_id = ?', $_GET['filter_category']);
}


if ($centersEnabled === true){
	if ($centersStockMethod == 'Store'){
		$Qreservations->leftJoin('ib.ProductsInventoryBarcodesToStores b2s')
				->leftJoin('b2s.Stores s');
	}else{
		$Qreservations->leftJoin('ib.ProductsInventoryBarcodesToInventoryCenters b2c')
				->leftJoin('b2c.ProductsInventoryCenters ic');
	}
}

EventManager::notify('OrdersListingBeforeExecute', &$Qreservations);

$Result = $Qreservations->execute();
if ($Result->count() > 0){
	//echo '<pre>';print_r($Result->toArray(true));
	foreach($Result->toArray(true) as $oInfo){
		$orderId = $oInfo['orders_id'];
		$customersName = $oInfo['OrdersAddresses']['delivery']['entry_name'];
		foreach($oInfo['OrdersProducts'] as $opInfo){
			$productName = $opInfo['products_name'];
			foreach($opInfo['OrdersProductsReservation'] as $oprInfo){
				$shippingMethod = $oInfo['shipping_module'];
				$rentalState = $oprInfo['rental_state'];
				$trackMethod = $oprInfo['track_method'];
				$reservationId = $oprInfo['orders_products_reservations_id'];
				$useCenter = 0;

				$startArr = date_parse($oprInfo['start_date']);
				$endArr = date_parse($oprInfo['end_date']);

				$resStart = tep_date_short($oprInfo['start_date']);
				$resEnd = tep_date_short($oprInfo['end_date']);

				$padding_days_before = $oprInfo['shipping_days_before'];
				$padding_days_after = $oprInfo['shipping_days_after'];

				$shipOn = tep_date_short(date('Y-m-d', mktime(0,0,0,$startArr['month'],$startArr['day'] - $padding_days_before,$startArr['year'])));
				$dueBack = tep_date_short(date('Y-m-d', mktime(0,0,0,$endArr['month'],$endArr['day'] + $padding_days_after,$endArr['year'])));

				if ($trackMethod == 'barcode'){
					$barcodeInfo = $oprInfo['ProductsInventoryBarcodes'];
					$barcodeId = $barcodeInfo['barcode_id'];
					$invDescription = $barcodeInfo['barcode'];

					if ($centersEnabled === true){
						if (isset($barcodeInfo['ProductsInventory'])){
							$useCenter = $barcodeInfo['ProductsInventory']['use_center'];
						}else{
							$useCenter = 0;
						}
						if ($useCenter == '1'){
							if ($centersStockMethod == 'Store'){
								if (isset($barcodeInfo['ProductsInventoryBarcodesToStores'])){
									$invCenterId = $barcodeInfo['ProductsInventoryBarcodesToStores']['inventory_store_id'];
								}else{
									$invCenterId = 0;
								}
							}else{
								if (isset($barcodeInfo['ProductsInventoryBarcodesToInventoryCenters'])){
									$invCenterId = $barcodeInfo['ProductsInventoryBarcodesToInventoryCenters']['inventory_center_id'];
								}else{
									$invCenterId = 0;
								}
							}
						}
					}
				}elseif ($trackMethod == 'quantity'){
					$quantityInfo = $oprInfo['ProductsInventoryQuantity'];
					$quantityId = $quantityInfo['quantity_id'];
					$invDescription = 'Quantity Tracking';

					if ($centersEnabled === true){
						$useCenter = $quantityInfo['ProductsInventory']['use_center'];
						if ($useCenter == '1'){
							if ($centersStockMethod == 'Store'){
								$invCenterId = $quantityInfo['Stores']['stores_id'];
							}else{
								$invCenterId = $quantityInfo['InventoryCenters']['inventory_center_id'];
							}
						}
					}
				}
				?>
			<tr class="dataTableRow">
				<td class="main" align="center"><?php
					if ($rentalState == 'returned'){
						echo 'Returned';
					}elseif ($rentalState == 'reserved'){
						echo 'Reserved';
					}else{
						echo tep_draw_checkbox_field('rental[' . $reservationId . ']', $reservationId,false,'','class="returns"');
					}?></td>
				<td class="main"><?php echo $customersName;?></td>
				<td class="main"><?php echo $productName;?></td>
				<td class="main"><?php echo $invDescription;?></td>
				<?php if ($centersEnabled === true){ ?>
				<td class="main"><?php
					if ($useCenter == '1'){
						$selectBox = htmlBase::newElement('selectbox')
								->setId('inventory_center')
								->setName('inventory_center[' . $reservationId . ']')
								->attr('defaultValue', $invCenterId);
						foreach($invCenterArray as $invInfo){
							if ($centersStockMethod == 'Store'){
								$selectBox->addOption($invInfo['stores_id'], $invInfo['stores_name']);
							}else{
								$selectBox->addOption($invInfo['inventory_center_id'], $invInfo['inventory_center_name']);
							}
						}
						$selectBox->selectOptionByValue($invCenterId);
						echo $selectBox->draw();
					}
					?></td>
				<?php } ?>
				<td class="main"><table cellpadding="2" cellspacing="0" border="0">
					<tr>
						<td class="main"><?php echo 'Ship On: ';?></td>
						<td class="main"><?php echo $shipOn;?></td>
					</tr>
					<tr>
						<td class="main"><?php echo 'Res Start: ';?></td>
						<td class="main"><?php echo $resStart;?></td>
					</tr>
					<tr>
						<td class="main"><?php echo 'Res End: ';?></td>
						<td class="main"><?php echo $resEnd;?></td>
					</tr>
					<tr>
						<td class="main"><?php echo 'Due Back: ';?></td>
						<td class="main"><?php echo $dueBack;?></td>
					</tr>
				</table></td>
				<td class="main"><?php
					if ($rentalState == 'returned' || $rentalState == 'reserved'){
					}else{
						$days = (mktime(0,0,0,$endArr['month'],$endArr['day'],$endArr['year']) - mktime(0,0,0)) / (60 * 60 * 24);
						if ($days > 0){
							echo 'Not Due';
						}elseif ($days == 0){
							echo 'Due Today';
						}else{
							echo abs($days);
						}
					}
					?></td>
				<td class="main"><?php echo $shippingMethod;?></td>
				<td class="main" align="center"><?php
					if ($rentalState == 'returned' || $rentalState == 'reserved'){
						echo '';
					}else{
						echo tep_draw_textarea_field('comment[' . $reservationId . ']', true, 30, 2);
					}
					?></td>
				<td class="main" align="center"><?php
					if ($rentalState == 'returned' || $rentalState == 'reserved'){
						echo '';
					}else{
						echo tep_draw_checkbox_field('damaged[' . $reservationId . ']', $reservationId);
					}
					?></td>
				<td class="main" align="center"><?php
					if ($rentalState == 'returned' || $rentalState == 'reserved'){
						echo '';
					}else{
						echo tep_draw_checkbox_field('lost[' . $reservationId . ']', $reservationId);
					}
					?></td>
			</tr>
			<?php
				if (isset($invCenterId)) unset($invCenterId);
			}
		}
	}
}
$html = ob_get_contents();
ob_end_clean();

EventManager::attachActionResponse($html, 'html');
?>