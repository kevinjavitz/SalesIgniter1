<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td width="100%">
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_RETURN'); ?></td>
					<td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
				</tr>
				<tr>
					<td>
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top">
									<table border="0" width="100%" cellspacing="0" cellpadding="2">
										<tr class="dataTableHeadingRow">
											<td valign="top" class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_TITLE'); ?></td>
											<td valign="top" class="dataTableHeadingContent">
												<table cellspacing='0' cellpadding='0'>
													<tr>
														<td valign="top">
															<a href="<?php echo itw_app_link((isset($_GET['cID'])
																	? 'sort_by=customer&cID=' . $_GET['cID']
																	: 'sort_by=customer'), 'rental_queue', 'return')?>" class="headerLink"><?php echo sysLanguage::get('TABLE_HEADING_CUSTOMER');?></a>
														</td>
														<?php if ($sort_by == 'customer'){ ?>
														<td valign="top"><?php echo tep_image('images/down.gif');?></td>
														<?php } ?>
													</tr>
												</table>
											</td>
											<td valign="top" class="dataTableHeadingContent">
												<table cellspacing="0" cellpadding="0">
													<tr>
														<td valign="top">
															<a href="<?php echo itw_app_link((isset($_GET['cID'])
																	? 'sort_by=barcode&cID=' . $_GET['cID']
																	: 'sort_by=barcode'), 'rental_queue', 'return')?>" class="headerLink"><?php echo sysLanguage::get('TABLE_HEADING_BARCODE');?></a>
														</td>
														<?php if ($sort_by == 'barcode'){ ?>
														<td valign="top"><?php echo tep_image('images/down.gif');?></td>
														<?php } ?>
													</tr>
												</table>
											</td>
											<td valign="top" class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_BARCODE_IMG'); ?></td>
											<?php if (defined('EXTENSION_INVENTORY_CENTERS_ENABLED') && EXTENSION_INVENTORY_CENTERS_ENABLED == 'True'){ ?>
											<td valign="top" class="dataTableHeadingContent"><?php echo 'Inventory Center'; ?></td>
											<?php } ?>
											<td valign="top" class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_COMMENTS'); ?></td>
											<td valign="top" class="dataTableHeadingContent" align="center"><?php echo 'Action&nbsp;'; ?></td>
										</tr>
<?php
	if (isset($_GET['cID'])){
		$order_by = ' AND r.customers_id = "' . $_GET['cID'] . '"' . $order_by;
	}

	if (sysConfig::exists('EXTENSION_INVENTORY_CENTERS_ENABLED') && sysConfig::get('EXTENSION_INVENTORY_CENTERS_ENABLED') == 'True'){
		$invCenterArray = array();
		$QinvCenters = Doctrine_Query::create()
			->select('inventory_center_id, inventory_center_name')
			->from('InventoryCenters')
			->orderBy('inventory_center_name')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($QinvCenters as $invCenter){
			$invCenterArray[] = array(
				'id' => $invCenter['inventory_center_id'],
				'text' => $invCenter['inventory_center_name']
			);
		}
	}

	$Qrented = Doctrine_Query::create()
		->select('r.customers_queue_id, r.customers_id, r.products_id, p.products_name, r.date_added, r.products_barcode, concat(c.customers_firstname, " ", c.customers_lastname) as full_name')
		->from('RentedQueue r')
		->leftJoin('r.ProductsDescription p ON p.products_id = r.products_id')
		->leftJoin('r.Customers c ON r.customers_id = c.customers_id')
		->where('p.language_id = ?', Session::get('languages_id'));
	if (isset($_GET['cID'])){
		$Qrented->andWhere('r.customers_id = ?', (int) $_GET['cID']);
	}

	if (isset($_GET['sort_by'])){
		$sort_by = $_GET['sort_by'];
		if ($sort_by == 'customer'){
			$Qrented->orderBy('c.customers_firstname, c.customers_lastname');
		}
		elseif ($sort_by == 'barcode') {
			$Qrented->orderBy('r.products_barcode');
		}
	}
	$Result = $Qrented->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if (!$Result){
		echo '<tr>
       <td colspan="7" class="messageStackError">' . sysLanguage::get('TEXT_RENTED_QUEUE_EMPTY') . '</td>
      </tr>';
	}
	else {
		foreach($Result as $rented){
			$Qbarcode = Doctrine_Query::create()
				->from('ProductsInventory i')
				->leftJoin('i.ProductsInventoryBarcodes ib')
				->where('ib.barcode_id = ?', $rented['products_barcode'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			?>
			<tr class="dataTableRow">
				<td class="main"><?php echo $rented['ProductsDescription'][0]['products_name']; ?></td>
				<td class="main"><?php echo $rented['full_name']; ?></td>
				<td class="main"><?php echo $Qbarcode[0]['barcode']; ?></td>
				<td class="main">
					<img src="showBarcode.php?code=<?php echo $rented['products_barcode'];?>">
				</td>
				<?php if (sysConfig::exists('EXTENSION_INVENTORY_CENTERS_ENABLED') && sysConfig::get('EXTENSION_INVENTORY_CENTERS_ENABLED') == 'True'){ ?>
				<td class="main"><?php
	                if ($Qbarcode[0]['use_center'] == '1'){
						$QinvCenter = Doctrine_Query::create()
							->select('i.inventory_center_id')
							->from('ProductsInventoryCenters i')
							->leftJoin('i.ProductsInventoryBarcodesToInventoryCenters b2c')
							->where('b2c.barcode_id = ?', $rented['products_barcode'])
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						echo tep_draw_pull_down_menu('inventory_center', $invCenterArray, $QinvCenter[0]['inventory_center_id'], 'defaultValue="' . $QinvCenter[0]['inventory_center_id'] . '" id="inventory_center"');
					}
				?></td>
				<?php } ?>
				<td class="main"><?php echo  tep_draw_textarea_field('comments', 'soft', 35, 5, '', 'id="comments"'); ?></td>
				<td class="main" align="center"><?php
	                echo htmlBase::newElement('button')->addClass('returnOk')->setText('Return OK')->draw() . '<br>' .
						htmlBase::newElement('button')->addClass('returnBroken')->setText('Return Broken')->draw() . '<br>' .
						htmlBase::newElement('button')->addClass('appendComments')->setText('Just Comments')->draw() . '<br>' .
						'<input type="hidden" name="queue_id" id="queue_id" value="' . $rented['customers_queue_id'] . '">';
				?></td>
			</tr>
			<?php
		}
	}
?>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>