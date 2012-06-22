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
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></td>
				<td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
			</tr>
		</table></td>
	</tr>
     <tr><td><table border="0" width="100%" cellspacing="0" cellpadding="2">
			<tr>
      <td width="50%" class="main" valign="top"><fieldset>
       <legend><?php echo sysLanguage::get('LEGEND_FROM_DATE');?></legend>
       <div type="text" id="DP_startDate"></div><br>
       <input type="text" name="start_date" id="start_date" value="<?php echo (isset($_GET['start_date'])?$_GET['start_date']:date('Y-m-d'));?>">
      </fieldset></td>
      <td width="50%" class="main" valign="top"><fieldset>
       <legend><?php echo sysLanguage::get('LEGEND_TO_DATE');?></legend>
       <div type="text" id="DP_endDate"></div><br>
       <input type="text" name="end_date" id="end_date" value="<?php echo (isset($_GET['end_date'])?$_GET['start_date']:date('Y-m-d'));?>">
       </fieldset></td>
				</tr>
			</table>
		 </td>
     </tr>
     <tr>
     <td style="text-align:right">	    <select name="filter_shipping" id="filterShipping">
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
?>
		 <input type="button" value="<?php echo sysLanguage::get('TEXT_BUTTON_GET_RES');?>" name="get_res" id="get_res">
		 <br /><input type="checkbox" name="include_unsent" id="includeUnsent" value="1"> Include Unsent Reservations <input type="checkbox" name="include_returned" id="includeReturned" value="1"> Include Returned Reservations
	 </td>

	 </tr>
		<tr>
			<td>
				<div id="errMsg"></div>
			</td>
		</tr>
		<tr>
			<td>

				<?php echo tep_draw_separator('pixel_trans.gif', '10', '10');?></td>
		</tr>
		<tr>
			<td><table cellpadding="2" cellspacing="0" border="0" width="100%" id="reservationsTable">
				<thead>
				<tr class="dataTableHeadingRow">
					<td valign="top" class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('TABLE_HEADING_RETURN');?><br><input type="checkbox" id="selectAll" onclick="$('#reservationsTable .returns').each(function (){ this.checked = document.getElementById('selectAll').checked;});"></td>
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
				</thead>
				<tfoot>
				<tr>
					<td colspan="14" align="right">
						<input type="button" value="<?php echo sysLanguage::get('TEXT_BUTTON_RETURN');?>" name="return" id="return">
						<input type="button" value="<?php echo sysLanguage::get('TEXT_BUTTON_EXPORT_RESULTS');?>" name="export_data" id="export_data">
					</td>
				</tr>
				</tfoot>
				<tbody>
				</tbody>
			</table></td>
		</tr>
	</table>
		<div id="ajaxLoader" title="Ajax Operation">Performing An Ajax Operation<br>Please Wait....</div>


