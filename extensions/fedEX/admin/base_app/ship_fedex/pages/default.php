<?
    $debug = 0;
	$order = $_GET['oID'];
    if ($order){
        $lastshipment = sysConfig::get('EXTENSION_FED_EX_LASTSHIPMENT');            // Define last shipment time (ex: 17 would be 5pm on your server)
        $send_email_on_shipping = 1;   // Set to 0 to disable, set to 1 to enable automatic email of tracking number
        $thermal_printing = (sysConfig::get('EXTENSION_FED_EX_PRINTING') == 'Thermal') ? 1:0;         // set the printing type, thermal_printing = 0 for laser, thermal_printing = 1 for label printer
        $order = $_GET['oID'];

        $fedex_gateway = sysConfig::get('EXTENSION_FED_EX_SERVER');

        $QOrders = Doctrine_Query::create()
        ->from('Orders o')
        ->leftJoin('o.OrdersProducts op')
        ->leftJoin('o.OrdersTotal ot')
        ->leftJoin('o.OrdersAddresses a')
        ->andWhere('o.orders_id = ?', $order)
        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

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
        $packaging_type = array();
        $packaging_type[] = array('id' => '01', 'text' => 'Your Packaging');
        $packaging_type[] = array('id' => '02', 'text' => 'FedEx Pak');
        $packaging_type[] = array('id' => '03', 'text' => 'FedEx Box');
        $packaging_type[] = array('id' => '04', 'text' => 'FedEx Tube');
        $packaging_type[] = array('id' => '06', 'text' => 'FedEx Envelope');

        $service_type = array();
        $service_type[] = array('id' => '92', 'text' => 'FedEx Ground Service');
        $service_type[] = array('id' => '90', 'text' => 'FedEx Home Delivery');
        $service_type[] = array('id' => '01', 'text' => 'FedEx Priority');
        $service_type[] = array('id' => '03', 'text' => 'FedEx 2day');
        $service_type[] = array('id' => '05', 'text' => 'FedEx Standard Overnight');
        $service_type[] = array('id' => '06', 'text' => 'FedEx First Overnight');
        $service_type[] = array('id' => '20', 'text' => 'FedEx Express Saver');
        //$service_type[] = array('id' => '70', 'text' => 'FedEx 1day Freight');
        //$service_type[] = array('id' => '80', 'text' => 'FedEx 2day Freight');
        //$service_type[] = array('id' => '83', 'text' => 'FedEx 3dayFreight');

        $bill_type = array();
        $bill_type[] = array('id' => '1', 'text' => 'Bill sender (Prepaid)');
        $bill_type[] = array('id' => '2', 'text' => 'Bill recipient');
        $bill_type[] = array('id' => '3', 'text' => 'Bill third party');
        $bill_type[] = array('id' => '4', 'text' => 'Bill credit card');
        $bill_type[] = array('id' => '5', 'text' => 'Bill recipient for FedEx Ground');

        $dropoff_type = array();
        $dropoff_type[] = array('id' => 1, 'text' => 'Regular pickup');
        $dropoff_type[] = array('id' => 2, 'text' => 'Request courier');
        $dropoff_type[] = array('id' => 3, 'text' => 'Drop box');
        $dropoff_type[] = array('id' => 4, 'text' => 'Drop at BSC');
        $dropoff_type[] = array('id' => 5, 'text' => 'Drop at station');

        $oversized = array();
        $oversized[] = array('id' => 0, 'text' => '');
        $oversized[] = array('id' => 1, 'text' => 1);
        $oversized[] = array('id' => 2, 'text' => 2);
        $oversized[] = array('id' => 3, 'text' => 3);

        // array for Saturday delivery
        $saturday_delivery = array();
        $saturday_delivery[] = array('id' => 0, 'text' => 'N');
        $saturday_delivery[] = array('id' => 1, 'text' => 'Y');

        // array for Hold At Fedex Location
        $hold_at_location = array();
        $hold_at_location[] = array('id' => 0, 'text' => 'N');
        $hold_at_location[] = array('id' => 1, 'text' => 'Y');

        // arrays for signature services
        $signature_type = array();
        $signature_type[] = array('id' => '0', 'text' => 'None Required');
        $signature_type[] = array('id' => '2', 'text' => 'Anyone can sign (res only)');
        $signature_type[] = array('id' => '3', 'text' => 'Signature Required');
        $signature_type[] = array('id' => '4', 'text' => 'Adult Signature');

        // arrays for AutoPOD
        $autopod = array();
        $autopod[] = array('id' => 0, 'text' => 'N');
        $autopod[] = array('id' => 1, 'text' => 'Y');

        // get & format tomorrow's date for default pickup date
        $dayofweek = strftime("%A", mktime());

        // get the current timestamp into an array
        $timestamp = time();
        $date_time_array = getdate($timestamp);
        $datehours = $date_time_array['hours'];

        if ($dayofweek == 'Saturday') {
            $default_pickup_date = date('m-d-Y',strtotime('+2 day'));
        } else if ($dayofweek == 'Sunday') {
            $default_pickup_date = date('m-d-Y',strtotime('+1 day'));
        } else if ($datehours > $lastshipment) {
            $default_pickup_date = date('m-d-Y',strtotime('+1 day'));
        } else {
            $default_pickup_date = date('m-d-Y',strtotime('today'));
        }

        // get the shipping method
        if (count($QOrders[0]['OrdersTotal'])){
             foreach($QOrders[0]['OrdersTotal'] as $otInfo){
                 if ($otInfo['module_type'] == 'ot_shipping' || $otInfo['module_type'] == 'shipping'){
                    $shippingMethod = $otInfo['title'];
                 }
             }
        }

        //if they have different shipping methods on the order only one will be shown.
        EventManager::notify('fedexShippingMethod', &$shippingMethod, $QOrders);

        $shipping_method_keywords = array('90' => 'Home Delivery',
                                          '92' => 'Ground Service',
                                          '01' => 'Priority',
                                          '03' => '2 Day Air',
                                          '05' => 'Standard Overnight',
                                          '06' => 'First Overnight',
                                          '20' => 'Express Saver');

        // Detect if company if so change to ground service, else default to home delivery.
        if ($orderInfo['delivery_company']) {
            $shipping_type='92'; // default to Fedex Ground
        } else {
            $shipping_type='90'; // default to Fedex Home
        }
        while (list($shipping_index, $shipping_keyword) = each($shipping_method_keywords)){
            if (false !== strpos($shippingMethod, $shipping_keyword)){
                  $shipping_type=$shipping_index;
                  break 1;
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
                            echo sysLanguage::get('HEADING_TITLE') . '<br/>';
                            echo ', ' . 'Order Number: ' . $order;                           
                            ?></td>
                <td class="pageHeading" align="right"></td>
                <td class="pageHeading" align="right"></td>
              </tr>

              <tr>
              <td colspan="3" class="main" align="left">
			 <?php echo '<form name="ship_fedex" action="'. itw_app_link('appExt=fedEX&action=shipOrders&oID=' . $order,'ship_fedex','default').'" method="post" enctype="multipart/form-data">';
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
                $Qcountry = Doctrine_Query::create()
                ->select('countries_id')
                ->from('Countries')
                ->where('countries_name = ?', $delivery_country)
                ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

                $html = '';
                $Qcheck = Doctrine_Query::create()
                ->select('zone_id, zone_code, zone_name')
                ->from('Zones')
                ->where('zone_country_id = ?', $Qcountry[0]['countries_id'])
                ->orderBy('zone_name')
                ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
                if ($Qcheck) {
                    $htmlField = htmlBase::newElement('selectbox')->setName('delivery_state');
                    //$htmlField->addOption('', 'Please Select');
                    foreach($Qcheck as $zInfo){
                        $htmlField->addOption($zInfo['zone_name'], $zInfo['zone_name']);
                    }
                    $htmlField->selectOptionByValue($orderInfo['delivery_state']);
                } else {
                    $htmlField = htmlBase::newElement('input')->setName('delivery_state')->setValue($orderInfo['delivery_state']);
                }

              echo 'Delivery state:    ' . $htmlField->draw();

              ?><br>&nbsp;
              <?php echo 'Delivery postcode:    ' . tep_draw_input_field('delivery_postcode',$orderInfo['delivery_postcode'],'size="20"'); ?><br>&nbsp;
				  <?php
				   if (!empty($orderInfo['delivery_phone'])){
				    	$phone = $orderInfo['delivery_phone'];
			  		}else{
						if (!empty($orderInfo['customers_telephone'])){
				    		$phone = $orderInfo['customers_telephone'];
						}else{
							$phone = sysConfig::get('EXTENSION_FED_EX_PHONE');
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
                        // if quantity = 1, skip to shipping directly
                        //if ($order_qty == 1){
                        //}else {
                         //   echo '<form name="ship_fedex" action="'. itw_app_link('appExt=fedEX&do=multiWeight&oID=' . $order,'ship_fedex','default').'" method="post" enctype="multipart/form-data" >';
                       // }

                    ?>  <tr><td>
                        <input type="hidden" name="order_item_html" value='<?php echo urlencode(serialize($order_item_html)); ?>'/>
                        <input type="hidden" name="item_weights" value='<?php echo urlencode(serialize($item_weights)); ?>'/>
                        <input type="hidden" name="fedex_gateway" value="<? echo $fedex_gateway; ?>"/>
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
                                <td class="main" align="right">Oversized?</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_pull_down_menu('oversized',$oversized); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">Packaging Type ("other" for ground shipments):</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_pull_down_menu('packaging_type',$packaging_type); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">Type of Service:</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_pull_down_menu('service_type',$service_type, $shipping_type); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">Payment Type:</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_pull_down_menu('bill_type',$bill_type); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">Dropoff Type:</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_pull_down_menu('dropoff_type',$dropoff_type); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">Pickup date (yyyymmdd):</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_input_field('pickup_date',$default_pickup_date,'size="9"'); ?></td>
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
                                <td class="main" align="right" valign="top">Saturday Delivery?</td>
                                <td class="main">&nbsp;</td>
                                <td class="main">

                                                    <?php echo tep_draw_pull_down_menu('saturday_delivery',$saturday_delivery); ?>&nbsp;&nbsp;<div class="smallText">
                                                    <font color="#d02727">Only Priority or 2Day Shipping Service Allowed For Saturday Delivery</font></div>
                                            </td>
                            </tr>
                            <tr>
                                <td class="main" align="right">Invoice #:</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_input_field('package_invoice',$order,'size="33" maxlength="30"'); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">Reference #:</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_input_field('package_reference',$order,'size="33" maxlength="30"'); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">Purchase Order #:</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_input_field('package_po','','size="33" maxlength="30"'); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">Department Name:</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_input_field('package_department','','size="10" maxlength="10"'); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">Signature Options:</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_pull_down_menu('signature_type',$signature_type); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">Hold At Fedex Location (HAL):</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_pull_down_menu('hold_at_location',$hold_at_location); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">HAL Address:</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_input_field('hal_address','','size="33"'); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">HAL City:</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_input_field('hal_city','','size="33"'); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">HAL State:</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_input_field('hal_state','','size="33"'); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">HAL Postal Code:</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_input_field('hal_postcode','','size="33"'); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">HAL Phone #:</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_input_field('hal_phone','','size="33"'); ?></td>
                            </tr>

                            <tr>
                                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '15'); ?></td>
                            </tr>
                            <tr>
                                <td class="main" align="right"><b><u>Fedex Ground Fields</u></b></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="main" align="right">Automatic Proof of Delivery?</td>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_pull_down_menu('autopod',$autopod); ?></td>
                            </tr>

                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><?php
                                   $htmlButton = htmlBase::newElement('button')
                                                    ->setType('submit')
                                                    ->usePreset('save')
                                                    ->setText('Submit');
                                echo $htmlButton->draw();
                                ?></td>
                            <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>

                        </table>
    <?
            echo '</form>';
        }/*else if (isset($_GET['do']) && $_GET['do'] == 'multiWeight') {

            $order_item_html = unserialize(urldecode($_POST['order_item_html']));
            $item_weights = unserialize(urldecode($_POST['item_weights']));
    ?>


              <tr>
                <td class="pageHeading"><?php
                            echo sysLanguage::get('HEADING_TITLE');
                            if ($order) {
                                echo ', ' . ORDER_NUMBER . $order;
                                }
                            elseif (!$order) {
                                echo ERROR_NO_ORDER_SPECIFIED;
                                }
                            ?></td>
                <td class="pageHeading" align="right"></td>
                <td class="pageHeading" align="right"></td>
                <?php echo $order_item_html;?>
              </tr>
            </table></td>
          </tr>
          <tr>
                    <td>
                        <?php echo '<form name="ship_fedex" action="'. itw_app_link('appExt=fedEX&action=shipOrders&oID=' . $order,'ship_fedex','default').'" method="post" enctype="multipart/form-data">';?>
                        <input type="hidden" name="fedex_gateway" value="<? echo $fedex_gateway; ?>"/>
                        <input type="hidden" name="package_num" value = "<?php echo $package_num; ?>"/>
                        <input type="hidden" name="oversized" value="<?php echo $_POST['oversized']; ?>"/>
                        <input type="hidden" name="saturday_delivery" value="<?php echo $_POST['saturday_delivery']; ?>"/>
                        <input type="hidden" name="signature_type" value="<?php echo $_POST['signature_type']; ?>" />
                        <input type="hidden" name="hold_at_location" value="<?php echo $_POST['hold_at_location']; ?>"/>
                        <input type="hidden" name="hal_address" value="<?php echo $_POST['hal_address']; ?>"/>
                        <input type="hidden" name="hal_city" value="<?php echo $_POST['hal_city']; ?>"/>
                        <input type="hidden" name="hal_state" value="<?php echo $_POST['hal_state']; ?>"/>
                        <input type="hidden" name="hal_postcode" value="<?php echo $_POST['hal_postcode']; ?>"/>
                        <input type="hidden" name="hal_phone" value="<?php echo $_POST['hal_phone']; ?>"/>
                        <input type="hidden" name="dim_height" value="<?php echo $_POST['dim_height']; ?>"/>
                        <input type="hidden" name="dim_width" value="<?php echo $_POST['dim_width']; ?>"/>
                        <input type="hidden" name="dim_length" value="<?php echo $_POST['dim_length']; ?>"/>
                        <input type="hidden" name="residential" value="<?php echo $_POST['residential']; ?>"/>
                        <input type="hidden" name="packaging_type" value="<?php echo $_POST['packaging_type']; ?>"/>
                        <input type="hidden" name="service_type" value="<?php echo $_POST['service_type']; ?>"/>
                        <input type="hidden" name="payment_type" value="<?php echo $_POST['payment_type']; ?>"/>
                        <input type="hidden" name="bill_type" value="<?php echo $_POST['bill_type']; ?>"/>
                        <input type="hidden" name="dropoff_type" value="<?php echo $_POST['dropoff_type']; ?>"/>
                        <input type="hidden" name="pickup_date" value="<?php echo $_POST['pickup_date']; ?>"/>
                        <input type="hidden" name="package_invoice" value="<?php echo $_POST['package_invoice']; ?>"/>
                        <input type="hidden" name="package_reference" value="<?php echo $_POST['package_reference']; ?>"/>
                        <input type="hidden" name="package_po" value="<?php echo $_POST['package_po']; ?>"/>
                        <input type="hidden" name="package_department" value="<?php echo $_POST['package_department']; ?>"/>
                        <input type="hidden" name="autopod" value="<?php echo $_POST['autopod']; ?>"/>
                        <table width="70%" border="0" cellspacing="0" cellpadding="2">
                <tr>
                  <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '15'); ?></td>
                </tr>
                            <tr>
                                <td class="main" align="right">
    <?php
                            if ($package_num > 1) {
                                echo sysLanguage::get('TOTAL_WEIGHT') . '</td>';
                                }
                            else {
                                echo sysLanguage::get('PACKAGE_WEIGHT') . '</td>';
                                }
    ?>
                                <td class="main">&nbsp;</td>
                                <td class="main"><?php echo tep_draw_input_field('package_weight','','size="2"'); ?></td>
                            </tr>

    <?php
                            if ($package_num > 1) {
                                echo '<tr>';
                                for ($i = 1; $i <= $package_num; $i++) {
                                    echo '<td class="main" align="right">Package #' . $i . ' Weight:</td>';
                                    echo '<td class="main">&nbsp;</td>';
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
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><?php
                                     $htmlButton = htmlBase::newElement('button')
                                                    ->setType('submit')
                                                    ->usePreset('save')
                                                    ->setText('Submit');
                                echo $htmlButton->draw(); ?></td>
                            <tr>
                        </table>
                <?php
                       echo '</form>';
            ?>
                    		</td>
			</tr>
            </table></td>
        </tr>
    </table>
            <?php
                }  */
        }
    }else{
        $link = itw_app_link(null,'orders','default');
        echo '<script type="text/javascript">$(document).ready(function (){window.location.href="'.$link.'";});</script>';
    }
?>

