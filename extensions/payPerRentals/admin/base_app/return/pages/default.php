<?php
$centersEnabled = false;
if ($appExtension->isInstalled('inventoryCenters') && $appExtension->isEnabled('inventoryCenters')){
	$extInventoryCenters = $appExtension->getExtension('inventoryCenters');
	$centersEnabled = true;
	$centersStockMethod = $extInventoryCenters->stockMethod;
	if ($centersStockMethod == 'Store'){
		$extStores = $appExtension->getExtension('multiStore');
		$invCenterArray = $extStores->getStoresArray();
	}else{
		$invCenterArray = $extInventoryCenters->getCentersArray();
	}
}
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td><form name="filter" action="<?php echo itw_app_link('appExt=payPerRentals', 'return', 'default');?>" method="get">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
     <tr>
      <td width="50%" class="main" valign="top"><fieldset>
       <legend><?php echo sysLanguage::get('LEGEND_FROM_DATE');?></legend>
       <div type="text" id="DP_startDate"></div><br>
       <input type="text" name="start_date" id="start_date" value="<?php echo date('Y-m-d');?>">
      </fieldset></td>
      <td width="50%" class="main" valign="top"><fieldset>
       <legend><?php echo sysLanguage::get('LEGEND_TO_DATE');?></legend>
       <div type="text" id="DP_endDate"></div><br>
       <input type="text" name="end_date" id="end_date" value="<?php echo date('Y-m-d');?>">
       </fieldset></td>
     </tr>
     <tr>
     <td colspan="2" style="text-align:right">	    <select name="filter_shipping" id="filterShipping">
		    <option value="">All Shipping</option>
		    <?php
		    OrderShippingModules::loadModules();
			foreach(OrderShippingModules::getModules() as $Module){
				echo '<option value="'.$Module->getTitle().'">'.$Module->getTitle().'</option>';
			}
		    ?>
	    </select>
<?php
    $categorySelect = htmlBase::newElement('selectbox')
    ->setId('filterCategory')
	->setName('filter_category');

   	function addCategoryTreeToGrid($parentId, &$categorySelect, $namePrefix = ''){
		global $allGetParams, $cInfo;
		$Qcategories = Doctrine_Query::create()
		->select('c.*, cd.categories_name')
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('cd.language_id = ?', Session::get('languages_id'))
		->andWhere('c.parent_id = ?', $parentId)
		->orderBy('c.sort_order, cd.categories_name');

		EventManager::notify('CategoryListingQueryBeforeExecute', &$Qcategories);

		$ResultC = $Qcategories->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if (count($ResultC) > 0){
			foreach($ResultC as $Category){

				$categorySelect->addOption($Category['categories_id'], $namePrefix. $Category['CategoriesDescription'][0]['categories_name']);
				addCategoryTreeToGrid($Category['categories_id'], &$categorySelect, '&nbsp;&nbsp;&nbsp;' . $namePrefix);
			}
		}
	}
    $categorySelect->addOption('', 'All Categories');
	addCategoryTreeToGrid(0, $categorySelect,'');
    if(isset($_GET['filter_category']) && $_GET['filter_category'] != ''){
	    $categorySelect->selectOptionByValue($_GET['filter_category']);
    }
    echo $categorySelect->draw();
?><br><input type="checkbox" name="include_returned" value="1"> Include Returned Reservations
</td>
     </tr>
     <tr>
      <td colspan="2" align="right"><?php
      echo htmlBase::newElement('button')
      ->setType('submit')
      ->setName('filter_apply')
      ->usePreset('continue')
      ->setText('Apply Filter')
      ->draw();
      if (isset($_GET['start_date'])){
  	    echo htmlBase::newElement('button')
  	    ->setName('filter_reset')
  	    ->usePreset('cancel')
  	    ->setText(sysLanguage::get('TEXT_BUTTON_RESET_FILTER'))
  	    ->setHref(itw_app_link())
  	    ->draw();
      }
      echo htmlBase::newElement('button')
      ->setType('submit')
      ->setName('action')
      ->val('exportData')
      ->usePreset('continue')
      ->setText('Export')
      ->draw();
      ?></td>
     </tr>
    </table>
<?php
    echo tep_draw_hidden_field(session_name(), session_id());

    $startDefault = (date('d') - 7);
    if ($startDefault <= 0){
    	$startDefault = '1';
    }

    $endDefault = (date('d') + 7);
    $lastDay = date('d', mktime(0, 0, 0, date("m")+1, 0, date("Y")));
    if ($endDefault > $lastDay){
    	$endDefault = $lastDay;
    }
    ?>
    <script language="javascript">
    $(document).ready(function (){
    	<?php
    	if (isset($_GET['filter_apply'])){
    		$startDefault = (int)date('d', strtotime($_GET['start_date']));
    		$endDefault = (int)date('d', strtotime($_GET['end_date']));
    		if (date('m', strtotime($_GET['start_date'])) != date('m')){
    			?>
    			$('#cal_Start').attr('curMonth', '<?php echo date('m', strtotime($_GET['start_date']));?>');
    			$('#cal_Start').attr('curYear', '<?php echo date('Y', strtotime($_GET['start_date']));?>');
    			$('#cal_Start').trigger('changeDate', false, function (){
    				setTimeout(function (){
    					$('td[day="<?php echo $startDefault;?>"]', $('#cal_Start')).trigger('click');
    				}, 500);
    			});
    			<?php
    		}else{
    			?>
    			$('td[day="<?php echo $startDefault;?>"]', $('#cal_Start')).trigger('click');
    			<?php
    		}
    		if (date('m', strtotime($_GET['end_date'])) != date('m')){
    			?>
    			$('#cal_End').attr('curMonth', '<?php echo date('m', strtotime($_GET['end_date']));?>');
    			$('#cal_End').attr('curYear', '<?php echo date('Y', strtotime($_GET['end_date']));?>');
    			$('#cal_End').trigger('changeDate', false, function (){
    				setTimeout(function (){
    					$('td[day="<?php echo $endDefault;?>"]', $('#cal_End')).trigger('click');
    				}, 500);
    			});
    			<?php
    		}else{
    			?>
    			$('td[day="<?php echo $endDefault;?>"]', $('#cal_End')).trigger('click');
    			<?php
    		}
    	}else{
    		?>
    		$('td[day="<?php echo $startDefault;?>"]', $('#cal_Start')).trigger('click');
    		$('td[day="<?php echo $endDefault;?>"]', $('#cal_End')).trigger('click');
    		<?php
    	}
    	?>
    });
    </script>
    </form></td>
   </tr>
   <tr>
    <td><form name="send_rentals" action="<?php echo itw_app_link('appExt=payPerRentals&action=return', 'return', 'default');?>" method="post"><table border="0" width="100%" cellspacing="0" cellpadding="0">
     <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '10');?></td>
     </tr>
     <tr>
      <td class="main"><?php echo sysLanguage::get('TEXT_INFO_CHECK_RETURNS');?></td>
     </tr>
     <tr>
      <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="3">
       <tr class="dataTableHeadingRow">
        <td valign="top" class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('TABLE_HEADING_RETURN');?><br><input type="checkbox" id="selectAll" onclick="$('.dataTableRow input[type=checkbox]').each(function (){ this.checked = document.getElementById('selectAll').checked;});"></td>
        <td valign="top" class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_CUSTOMERS_NAME');?></td>
        <td valign="top" class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_PRODUCTS_NAME');?></td>
        <td valign="top" class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_INV_NUM');?></td>
        <?php
        if ($centersEnabled){
        	if ($centersStockMethod == 'Store'){
        		echo '<td valign="top" class="dataTableHeadingContent">Store</td>';
        	}else{
        		echo '<td valign="top" class="dataTableHeadingContent">Inventory Center</td>';
        	}
        }
        ?>
        <td valign="top" class="dataTableHeadingContent"><?php echo 'Dates';?></td>
        <td valign="top" class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_DAYS_LATE');?></td>
        <!--<td valign="top" class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_ADD_LATE_FEE');?></td>-->
	   <td class="dataTableHeadingContent"><?php echo 'Shipping Method';?></td>
        <td valign="top" class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_COMMENTS');?></td>
        <td valign="top" class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('TABLE_HEADING_ITEM_DMG');?></td>
        <td valign="top" class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('TABLE_HEADING_ITEM_LOST');?></td>
       </tr>
<?php
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
	if (isset($_GET['include_returned'])){
		$Qreservations->andWhereIn('opr.rental_state', array('returned', 'out'));
	}else{
		$Qreservations->andWhere('opr.rental_state = ?', 'out');
	}

if (isset($_GET['start_date']) || isset($_GET['end_date'])){
	$Qreservations->andWhere('opr.start_date between "' . $_GET['start_date'] . '" and "' . $_GET['end_date'] . '"');
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
        }else{
        	echo tep_draw_checkbox_field('rental[' . $reservationId . ']', $reservationId);
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
        if ($rentalState == 'returned'){
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
         if ($rentalState == 'returned'){
        	echo '';
        }else{
			 echo tep_draw_textarea_field('comment[' . $reservationId . ']', true, 30, 2);
        }
        ?></td>
        <td class="main" align="center"><?php
         if ($rentalState == 'returned'){
        	echo '';
        }else{
         echo tep_draw_checkbox_field('damaged[' . $reservationId . ']', $reservationId);
        }
         ?></td>
        <td class="main" align="center"><?php
         if ($rentalState == 'returned'){
        	echo '';
        }else{
         echo tep_draw_checkbox_field('lost[' . $reservationId . ']', $reservationId);
        }
         ?></td>
       </tr>
<?php
if (isset($invCenterId)) unset($invCenterId);

if (isset($oprInfo['Packaged'])){
	foreach($oprInfo['Packaged'] as $opprInfo){
		if ($check['track_method'] == 'barcode'){
			if ($centersEnabled === true){
				$invDescription = $opprInfo['ProductsInventory']['ProductsInventoryBarcodes']['barcode'];
				$invCenterId = $opprInfo['ProductsInventory']['ProductsInventoryBarcodes']['inventory_center_id'];
				$useCenter = ($opprInfo['ProductsInventory']['use_center'] == '1');
			}else{
				$invDescription = $opprInfo['ProductsInventoryBarcodes']['barcode'];
				$useCenter = false;
			}
		}elseif ($check['track_method'] == 'quantity'){
			$invDescription = 'Quantity Tracking';
			if ($centersEnabled === true){
				$invCenterId = $opprInfo['ProductsInventory']['ProductsInventoryQuantity']['inventory_center_id'];
				$useCenter = ($opprInfo['ProductsInventory']['use_center'] == '1');
			}else{
				$useCenter = false;
			}
		}
		$resId = $opprInfo['orders_products_reservations_id'];
?>
       <tr class="dataTableRow">
        <td class="main" align="center"></td>
        <td class="main" align="center">|_</td>
        <td class="main"><?php echo $opprInfo['OrdersProducts']['products_name'];?></td>
        <td class="main"><?php echo $invDescription;?></td>
	    <?php if ($useCenter === true){ ?>
	    <td class="main"><?php
	    echo tep_draw_pull_down_menu('inventory_center[' . $resId . ']', $invCenterArray, $invCenterId, 'defaultValue="' . $invCenterId . '" id="inventory_center"');
	    ?></td>
	    <?php }else{ ?>
	    <td class="main"></td>
	    <?php }
	    }?>
        <td class="main"></td>
        <td class="main"></td>
        <td class="main" align="center"><?php echo tep_draw_input_field('comment[' . $resId . ']');?></td>
        <td class="main" align="center"><?php echo tep_draw_checkbox_field('damaged[' . $resId . ']', $resId);?></td>
        <td class="main" align="center"><?php echo tep_draw_checkbox_field('lost[' . $resId . ']', $resId);?></td>
       </tr>
<?php
}
			}
		}
	}
}
?>
      </table></td>
     </tr>
     <tr>
      <td align="right" height="35" valign="middle"><?php
      echo htmlBase::newElement('button')
      ->setType('submit')
      ->setName('return')
      ->usePreset('save')
      ->setText(sysLanguage::get('TEXT_BUTTON_RETURN_RENTALS'))
      ->draw();
      ?></td>
     </tr>
    </table></form></td>
   </tr>
  </table>