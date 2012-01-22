<?
$debug = 0;
$order = $_GET['oID'];
if ($order){
	$order = $_GET['oID'];

	$QOrders = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('o.OrdersTotal ot')
	->leftJoin('o.OrdersAddresses a')
	->andWhere('o.orders_id = ?', $order)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	if(empty($QOrders[0]['ups_track_num']) && empty($QOrders[0]['ups_track_num2'])){

	if(count($QOrders)){
		$orderInfo['customers_telephone'] = $QOrders[0]['customers_telephone'];
		$orderInfo['customers_email_address'] = $QOrders[0]['customers_email_address'];
		foreach($QOrders[0]['OrdersAddresses'] as $oaInfo){
			if($oaInfo['address_type'] == 'billing'){
				$orderInfo['customers_name'] = $oaInfo['entry_name'];
				$orderInfo['customers_company'] = $oaInfo['entry_company'];
				$orderInfo['customers_address'] = $oaInfo['entry_street_address'];
				$orderInfo['customers_city'] = $oaInfo['entry_city'];
				$orderInfo['customers_state'] = $oaInfo['entry_state'];
				$orderInfo['customers_country'] = $oaInfo['entry_country'];
				$orderInfo['customers_postcode'] = $oaInfo['entry_postcode'];
			}else if($oaInfo['address_type'] == 'delivery'){
				$orderInfo['delivery_name'] = $oaInfo['entry_name'];
				$orderInfo['delivery_company'] = $oaInfo['entry_company'];
				$orderInfo['delivery_address'] = $oaInfo['entry_street_address'];
				$orderInfo['delivery_city'] = $oaInfo['entry_city'];
				$orderInfo['delivery_state'] = $oaInfo['entry_state'];
				$orderInfo['delivery_country'] = $oaInfo['entry_country'];
				$orderInfo['delivery_postcode'] = $oaInfo['entry_postcode'];
			}
		}

		$delivery_country = $orderInfo['delivery_country'];
		$service_type = array();
		$service_type[] = array('id' => '01', 'text' => 'UPS Next Day Air');
		$service_type[] = array('id' => '02', 'text' => 'UPS Second Day Air');
		$service_type[] = array('id' => '03', 'text' => 'UPS Ground');
		$service_type[] = array('id' => '12', 'text' => 'UPS Three-Day Select');
		$service_type[] = array('id' => '13', 'text' => 'UPS Next Day Air Saver');
		$service_type[] = array('id' => '14', 'text' => 'UPS Next Day Air Early A.M. SM');
		$service_type[] = array('id' => '59', 'text' => 'UPS Second Day Air A.M.');
		$service_type[] = array('id' => '65', 'text' => 'UPS Saver');

        $packaging_type = array();
        $packaging_type[] = array('id' => '01', 'text' => 'UPS Letter');
        $packaging_type[] = array('id' => '02', 'text' => 'Customer Supplied Package');
        $packaging_type[] = array('id' => '03', 'text' => 'Tube');
        $packaging_type[] = array('id' => '04', 'text' => 'PAK');
        $packaging_type[] = array('id' => '21', 'text' => 'UPS Express Box');
        $packaging_type[] = array('id' => '24', 'text' => 'UPS 25KG Box');
        $packaging_type[] = array('id' => '25', 'text' => 'UPS 10KG Box');
		$packaging_type[] = array('id' => '30', 'text' => 'Pallet');
		$packaging_type[] = array('id' => '2a', 'text' => 'Small Express Box');
		$packaging_type[] = array('id' => '2b', 'text' => 'Medium Express Box');
		$packaging_type[] = array('id' => '2c', 'text' => 'Large Express Box');

        /*$bill_type = array();
        $bill_type[] = array('id' => '1', 'text' => 'Bill sender (Prepaid)');
        $bill_type[] = array('id' => '2', 'text' => 'Bill recipient');
        $bill_type[] = array('id' => '3', 'text' => 'Bill third party');
        $bill_type[] = array('id' => '4', 'text' => 'Bill credit card');
        $bill_type[] = array('id' => '5', 'text' => 'Bill recipient for FedEx Ground');

        $oversized = array();
        $oversized[] = array('id' => 0, 'text' => '');
        $oversized[] = array('id' => 1, 'text' => 1);
        $oversized[] = array('id' => 2, 'text' => 2);
        $oversized[] = array('id' => 3, 'text' => 3);
        */

        // get the shipping method
		$shippingMethod = '';
        if (count($QOrders[0]['OrdersTotal'])){
	        foreach($QOrders[0]['OrdersTotal'] as $otInfo){
		        if ($otInfo['module_type'] == 'ot_shipping' || $otInfo['module_type'] == 'shipping'){
			        $shippingMethod = $otInfo['title'];
		        }
	        }
        }


        // get the order qty and item weights
          ?>
             <table width="100%" style="" align="center">
    <?php
        if (!isset($_GET['do']) || $_GET['do'] != 'multiWeight') {
			$order_qty_query = tep_db_query("select * from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $order . "'");
			$order_qty = 0;
			$order_weight = 0;
			$order_item_html = '';
			if (tep_db_num_rows($order_qty_query)) {
				while ($order_qtys = tep_db_fetch_array($order_qty_query)){
					$order_item_html = $order_item_html . '          <tr>' . "\n" .
						'            <td class="smallText" align="left"><b>Product:</b><br>' . $order_qtys['products_quantity'] . ' * ' .
						$order_qtys['products_name'] . '</td>' . "\n" .
						'            <td class="smallText" align="left">';
					$order_qty = $order_qty + $order_qtys['products_quantity'];
					$products_id = $order_qtys['products_id'];
					$products_weight_query = tep_db_query("select * from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
					if (tep_db_num_rows($products_weight_query)) {
						$products_weights = tep_db_fetch_array($products_weight_query);
						$order_weight = $order_weight + ($order_qtys['products_quantity'] * ($products_weights['products_weight']));
						$item_weights[] = $products_weights['products_weight'];
					}
				}

				$order_weight_tar = $order_weight + (float)sysConfig::get('SHIPPING_BOX_WEIGHT');
				$order_weight_percent = ($order_weight * ((float)sysConfig::get('SHIPPING_BOX_PADDING') / 100 + 1));

				if ($order_weight_percent < $order_weight_tar) {
					$order_weight = $order_weight_tar;
				} else {
					$order_weight = $order_weight_percent;
				}

				$order_weight = round($order_weight,1);
				$order_weight = sprintf("%01.1f", $order_weight);
			}
			if (isset($_GET['pkg'])){
				$package_num = $_GET['pkg'];
			}else{
				$package_num = $order_qty;
			}

			?>

		<tr>
			<td class="pageHeading"><?php

				echo 'Ship UPS ' . 'Order Number: ' . $order;
				?></td>
			<td class="pageHeading" align="right"></td>
			<td class="pageHeading" align="right"></td>
		</tr>

		<tr>
			<td colspan="3" class="main" align="left">
				<?php echo '<form name="ship_fedex" action="'. itw_app_link('appExt=upsLabels&action=shipOrders&oID=' . $order,'ship_ups','default').'" method="post" enctype="multipart/form-data">';
				?>
				<table border=0 cellpadding=0 cellspacing=0 width="700">
					<tr>
						<td class="main" align="left" valign="top" width="30%">
							<b>Sold To:</b><br>
							<?php echo $orderInfo['delivery_company']; ?><br>&nbsp;
							<?php echo $orderInfo['customers_name']; ?><br>&nbsp;
							<?php echo $orderInfo['customers_address']; ?><br>&nbsp;
							<?php echo $orderInfo['customers_city']; ?><br>&nbsp;
							<?php echo $orderInfo['customers_state']; ?><br>&nbsp;
							<?php echo $orderInfo['customers_postcode']; ?><br>&nbsp;
							<?php echo $orderInfo['customers_telephone']; ?><br>&nbsp;
						<td class="main" align="left" valign="top" width="70%">
							<b>Shipping To:</b><br>
							<?php echo 'Delivery company:       ' . tep_draw_input_field('delivery_company',$orderInfo['delivery_company'],'size="20"');  ?><br>&nbsp;
							<?php echo 'Delivery name:    ' . tep_draw_input_field('delivery_name',$orderInfo['delivery_name'],'size="20"'); ?><br>&nbsp;
							<?php echo 'Delivery address:    ' . tep_draw_input_field('delivery_address',$orderInfo['delivery_address'],'size="50"'); ?><br>&nbsp;
							<?php echo 'Delivery city:     ' . tep_draw_input_field('delivery_city',$orderInfo['delivery_city'],'size="20"'); ?><br>&nbsp;
							        <?php

							$htmlField = htmlBase::newElement('input')->setName('zone_code')->setValue($orderInfo['delivery_state']);


							$Qcountries = Doctrine_Query::create()
							->from('Countries')
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						$htmlCountries = htmlBase::newElement('selectbox')->setName('country_code')->attr('id', 'countryDrop');
						$mycountry = '';
						foreach($Qcountries as $iCountry){
							$htmlCountries->addOption($iCountry['countries_iso_code_2'], $iCountry['countries_name']);
							if($iCountry['countries_name'] == $orderInfo['delivery_country']){
								$mycountry = $iCountry['countries_iso_code_2'];
							}
						}
						$htmlCountries->selectOptionByValue($mycountry);
							echo 'Delivery country:     ' . $htmlCountries->draw().'<br/>';
							echo 'Delivery state:    <div id="stateCol">' . $htmlField->draw().'</div>';

							?><br>&nbsp;
							<?php echo 'Delivery postcode:    ' . tep_draw_input_field('delivery_postcode',$orderInfo['delivery_postcode'],'size="20"'); ?><br>&nbsp;
							        <?php
	   				   if (!empty($orderInfo['delivery_phone'])){
							$phone = $orderInfo['delivery_phone'];
						}else{
							if (!empty($orderInfo['customers_telephone'])){
								$phone = $orderInfo['customers_telephone'];
							}else{
								$phone = sysConfig::get('EXTENSION_UPSLABELS_PHONE');
							}
						}
							?>
							<?php echo 'Delivery phone:    ' . tep_draw_input_field('delivery_phone',$phone,'size="20"'); ?><br>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="main" align="left"><b>Shipping Method:</b><br><?php echo $shippingMethod ?><br>&nbsp;</td>
		</tr>
			<?php echo $order_item_html;?>

							        <?php
			/*$QtCountry = Doctrine_Query::create()
			->from('Countries')
			->where('countries_name = ?',$orderInfo['delivery_country'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$QtZones = Doctrine_Query::create()
			->from('Zones')
			->where('zone_name = ?',$orderInfo['delivery_state'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			<input type="hidden" id="countryid" name="country_code" value="<? echo $QtCountry[0]['countries_iso_code_2']; ?>"/>
			<input type="hidden" id="zoneid" name="zone_code" value="<? echo $QtZones[0]['zone_code']; ?>"/>
			*/
			?>  <tr><td>
         <input type="hidden" name="order_item_html" value='<?php echo urlencode(serialize($order_item_html)); ?>'/>
		<input type="hidden" name="item_weights" value='<?php echo urlencode(serialize($item_weights)); ?>'/>
		<input type="hidden" id="oID" name="myorder" value="<? echo $order; ?>"/>

		<table width="70%" border="0" cellspacing="0" cellpadding="2">
		<tr>
			<td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '15'); ?></td>
		</tr>
		<tr>
			<td class="main" align="right"><b><u>Required Fields</u></b></td>
			<td></td>
			<td></td>
		</tr>

		<tr>
			<td class="main" align="right">Number of Packages:</td>
			<td class="main">&nbsp;</td>
			<td class="main"><?php echo tep_draw_input_field('package_num',$package_num,'size="2" id="pkg"'); ?>
							        <?php
	   								$packageButton = htmlBase::newElement('button')
					->setId('packageButton')
					->setText('Change');
				echo $packageButton->draw();
				?>
			</td>
		</tr>
		<tr>
			<td class="main" align="right">Packaging Type:</td>
			<td class="main">&nbsp;</td>
			<td class="main"><?php echo tep_draw_pull_down_menu('packaging_type',$packaging_type); ?></td>
		</tr>
			<tr>
				<td class="main" align="right">Type of Service:</td>
				<td class="main">&nbsp;</td>
				<td class="main"><?php echo tep_draw_pull_down_menu('service_type',$service_type, $shippingMethod); ?></td>
			</tr>



							        <?php
	                               // get the transaction value
			$value_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $order . "' and (module_type='ot_subtotal' or module_type='subtotal')");
			$order_value = tep_db_fetch_array($value_query);
			$order_value = round($order_value['value'], 0);
			?>
		<tr>
			<td class="main" align="right">Declared Value ($):</td>
			<td class="main">&nbsp;</td>
			<td class="main"><?php echo tep_draw_input_field('declare_value', (string) $order_value, 'size="2"'); ?></td>
		</tr>
		<tr>
							        <?php
	                               $declare_value = round($declare_value, 0);

								        if ($package_num > 1) {
									        echo '<td class="main" align="right">' . sysLanguage::get('TOTAL_WEIGHT') . '</td>';
								        }
								        else {
									        echo '<td class="main" align="right">' . sysLanguage::get('PACKAGE_WEIGHT') . '</td>';
								        }
								        ?>
								        <td class="main">&nbsp;</td>
								        <td class="main"><?php echo tep_draw_input_field('package_weight',(string) $order_weight,'size="2"'); ?></td>
								        <td></td>
		</tr>
		<tr>
			<td class="main" colspan="3"><table align="center">
							        <?php
	                               if ($package_num > 1) {
								        echo '<tr>';
								        for ($i = 1; $i <= $package_num; $i++) {
									        echo '<td class="main" align="right">Package #' . $i . ' Weight:</td>';
									        $item_weight_rounded = sprintf("%01.1f", array_pop($item_weights));
									        echo '<td class="main">' . tep_draw_input_field('package_' . $i . '_weight', $item_weight_rounded, 'size="2"') . '</td>';
									        $div = $i/3;
									        if (is_int($div) && ($i != $package_num)) {
										        echo '</tr><tr>';
									        }
								        }
								        echo '</tr>';
							        }
								        ?>
			</table></td></tr>
		<tr>
			<td class="main">&nbsp;</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td class="main" align="right"><b><u>Optional Fields</u></b></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td class="main" align="right">Your Package Dimensions:</td>
			<td class="main">&nbsp;</td>
			<td class="main">Length:&nbsp;<?php echo tep_draw_input_field('dim_length','','size="5" maxlength="30"'); ?><br/>Width:&nbsp;<?php echo tep_draw_input_field('dim_width','','size="5" maxlength="30"'); ?><br/>Height:&nbsp;<?php echo tep_draw_input_field('dim_height','','size="5" maxlength="30"'); ?></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><?php
                $htmlButton = htmlBase::newElement('button')
				->setType('submit')
				->setName('shipOrder')
				->usePreset('save')
				->setText('Ship Order and Generate labels');
				/*$htmlButton2 = htmlBase::newElement('button')
					->setType('submit')
					->setName('returnOrder')
					->usePreset('save')
					->setText('Return Order and Generate labels');*/
				echo $htmlButton->draw();

				?></td>
		<tr>
			<td></td>
			<td></td>
			<td></td>

		</table>
			<?
			echo '</form>';
		}
        }
	}else{

		    echo '<h1>Send Labels:</h1>';
			$sendTrackArr = explode(',', $QOrders[0]['ups_track_num']);
			foreach($sendTrackArr as $trackingNumber){
				echo '<img src="'.sysConfig::getDirWsCatalog().'extensions/upsLabels/tracking/'.$trackingNumber.'.png">';
			}

			echo '<h1>Return Labels:</h1>';
			$sendTrackArr = explode(',', $QOrders[0]['ups_track_num2']);
			foreach($sendTrackArr as $trackingNumber){
				echo '<img src="'.sysConfig::getDirWsCatalog().'extensions/upsLabels/tracking/'.$trackingNumber.'.png">';
			}
		//echo '<img src="'.sysConfig::getDirWsCatalog().'extensions/upsLabels/tracking/'.$QOrders[0]['ups_track_num2'].'.png">';
	}
}else{
	$link = itw_app_link(null,'orders','default');
	echo '<script type="text/javascript">$(document).ready(function (){window.location.href="'.$link.'";});</script>';
}

/*
 		<tr>
			<td class="main" align="right">Oversized?</td>
			<td class="main">&nbsp;</td>
			<td class="main"><?php echo tep_draw_pull_down_menu('oversized',$oversized); ?></td>
		</tr>


			<tr>
				<td class="main" align="right">Payment Type:</td>
				<td class="main">&nbsp;</td>
				<td class="main"><?php echo tep_draw_pull_down_menu('bill_type',$bill_type); ?></td>
			</tr>


 * */
?>

