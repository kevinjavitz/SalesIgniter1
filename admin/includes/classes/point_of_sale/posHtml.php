<?php
class pointOfSaleHTML {
	public static function getProductListing(){
		global $order, $currencies, $typeNames;
		$order->loadProducts();
		$html = '<table cellpadding="3" cellspacing="0" border="0" width="100%">';
		$products = $order->products;
		//print_r($products);
		for ($i=0, $n=sizeof($products); $i<$n; $i++){
			$productClass = $products[$i]['productClass'];
			$taxRate = $productClass->getTaxRate();

			$typeDescription = '<br><small><i> - Purchase Type: ' . $typeNames[$products[$i]['purchase_type']] . '</i></small>';
			if (isset($products[$i]['download_type'])){
				$typeDescription .= '<br><small><i> - View Type: ' . ($products[$i]['download_type'] == 'stream' ? 'Stream' : 'Download') . '</i></small>';
			}

			EventManager::notify('OrderProductAfterProductName', &$products[$i], &$typeDescription);

			if (isset($products[$i]['reservation'])){
				$typeDescription .= '<br>
                   <small><i> - Start Date: ' . $products[$i]['reservation']['start_date'] . '</i></small><br>
                   <small><i> - End Date: ' . $products[$i]['reservation']['end_date'] . '</i></small><br>
                   <small><i> - Insured: ' . ($products[$i]['reservation']['insure'] == 'Y' ? 'Yes' : 'No')  . '</i></small><br>
                   <small><i> - Shipping Method: ' . $products[$i]['reservation']['shipping']['title'] . ' (' . $currencies->format($products[$i]['reservation']['shipping']['cost']) . ')</i></small>';
			}

			$barcode = '';
			if (isset($products[$i]['barcode'])){
				$QbarcodeNumber = tep_db_query('select barcode from ' . TABLE_PRODUCTS_INVENTORY_BARCODES . ' where barcode_id = "' .$products[$i]['barcode']  . '"');
				$barcodeNumber = tep_db_fetch_array($QbarcodeNumber);
				$barcode = $barcodeNumber['barcode'];
			}

			$buttons = tep_image(DIR_WS_IMAGES . 'icons/cross.gif', 'Remove This Product', '', '', 'id="removeProduct"') .
			'&nbsp;<!--' .
			tep_image(DIR_WS_IMAGES . 'edit.gif', 'Edit This Product', '', '', 'id="editProduct"') .
			'-->';

			if (isset($products[$i]['removable']) && $products[$i]['removable'] === false){
				$buttons = '';
			}
			
			if (isset($products[$i]['reservation']) && PAY_PER_RENTAL_PRICE_CALC == 'aggregate'){
				$finalPrice = $currencies->format(tep_add_tax($products[$i]['final_price'], $taxRate));
			}else{
				$finalPrice = $currencies->format(tep_add_tax($products[$i]['final_price'], $taxRate) * $products[$i]['quantity']);
			}

			$html .= '<tr purchase_type="' . $products[$i]['purchase_type'] . '" id="' . $products[$i]['id_string'] . '">
               <td class="main" valign="top">' . $buttons . '</td>
               <td class="main" valign="top" align="right">' . $products[$i]['quantity'] . '&nbsp;x</td>
               <td class="main" valign="top">' . $productClass->getName() . $typeDescription . '</td>
               <td class="main" valign="top">' . $barcode . '</td>
               <td class="main" align="right" valign="top"><b>' . $currencies->format(tep_add_tax($products[$i]['final_price'], $taxRate)) . '</b></td>
               <td class="main" align="right" valign="top"><b>' . $finalPrice . '</b></td>
              </tr>';
		}
		$html .= '</table>';
		return $html;
	}

	public static function getAddressBookEntries($settings){
		$addressesOnFile = '';
		$Qaddresses = dataAccess::setQuery('select address_book_id from {address_book} where customers_id = {customer_id}');
		$Qaddresses->setTable('{address_book}', TABLE_ADDRESS_BOOK);
		$Qaddresses->setValue('{customer_id}', $settings['customerID']);
		while($Qaddresses->next() !== false){
			$addressesOnFile .= pointOfSaleHTML::getAddressBlock(array(
			'customerID'      => $settings['customerID'],
			'addressBookID'   => $Qaddresses->getVal('address_book_id'),
			'shippingChecked' => ($Qaddresses->getVal('address_book_id') == $settings['shippingAddress']),
			'billingChecked'  => ($Qaddresses->getVal('address_book_id') == $settings['billingAddress']),
			'pickupChecked'   => ($Qaddresses->getVal('address_book_id') == $settings['pickupAddress']),
			'showShipping'    => (isset($settings['showShipping']) ? $settings['showShipping'] : true),
			'showBilling'     => (isset($settings['showBilling']) ? $settings['showBilling'] : true),
			'showPickup'      => (isset($settings['showPickup']) ? $settings['showPickup'] : true)
			));
		}
		return $addressesOnFile;
	}

	public static function getAddressBlock($settings){
		$aID = $settings['addressBookID'];
		$cID = $settings['customerID'];

		if (!isset($settings['showShipping'])) $settings['showShipping'] = true;
		if (!isset($settings['showBilling'])) $settings['showBilling'] = true;
		if (!isset($settings['showPickup'])) $settings['showPickup'] = true;

		$html = '<div address_id="' . $aID . '" style="border:1px solid black; padding:5px; float:left;margin:2px;">
           <div style="height:80px;">
            ' . tep_address_label($cID, $aID, true, '', '<br>') . '
           </div>
           <div>';

		if ($settings['showShipping'] === true){
			$html .= tep_draw_radio_field('sendTo', $aID, (isset($settings['shippingChecked']) && $settings['shippingChecked'] === true), false, 'id="sendTo_' . $aID . '"') . '<label for="sendTo_' . $aID . '">Use As Shipping</label><br>';
		}

		if ($settings['showBilling'] === true){
			$html .= tep_draw_radio_field('billTo', $aID, (isset($settings['billingChecked']) && $settings['billingChecked'] === true), false, 'id="billTo_' . $aID . '"') . '<label for="billTo_' . $aID . '">Use As Payment</label><br>';
		}

		if ($settings['showPickup'] === true){
			$html .= tep_draw_radio_field('pickupFrom', $aID, (isset($settings['pickupChecked']) && $settings['pickupChecked'] === true), false, 'id="pickupFrom_' . $aID . '"') . '<label for="pickupFrom_' . $aID . '">Use As Pick Up</label><br>';
		}

		$html .= '</div></div>';

		return $html;
	}

	public static function getShippingQuotesTable(){
		global $ShoppingCart, $order, $currencies, $shippingModules, $total_count;
		$html = '';
		$total_count = $ShoppingCart->countContents();

		if ($order->hasReservation() === true){
			$products = $order->products;
			$onlyReservations = true;
			$hasShipping = false;
			for ($i=0, $n=sizeof($products); $i<$n; $i++) {
				if (!isset($products[$i]['reservation'])){
					$onlyReservations = false;
					break;
				}
			}
		}else{
			$onlyReservations = false;
		}

		if ($onlyReservations === true){
			$html = '<div style="text-align:center;">No Shipping Required, Only Reservation Products On Order</div>';
		}else{
			$pointOfSale = &Session::getReference('pointOfSale');
			$shipping = $pointOfSale->order['info']['shipping'];
			$quotes = $shippingModules->quote();

			if (!isset($shipping['id']) || empty($shipping['id'])){
				$shipping = $shippingModules->cheapest();
				$pointOfSale->order['info']['shipping'] = $shipping;
			}

			for($i=0, $n=sizeof($quotes); $i<$n; $i++){
				if (isset($quotes[$i]['id']) && $quotes[$i]['id'] == 'reservation') continue;
				$html .= '<div class="main"><b>' . $quotes[$i]['module'] . '</b></div><table cellpadding="0" cellspacing="0" border="0" width="100%">';
				$radio_buttons = 0;
				if (isset($quotes[$i]['error'])) {
					$html .= '<tr>
                       <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>
                       <td class="main" colspan="3">' . $quotes[$i]['error'] . '</td>
                       <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>
                      </tr>';
				} else {
					foreach($quotes[$i]['methods'] as $method){
						$html .= '<tr>
                           <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>
                           <td class="main" width="75%">' . $method['title'] . '</td>
                           <td class="main">' . $currencies->format(tep_add_tax($method['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))) . '</td>
                           <td class="main" align="right">' . tep_draw_radio_field('shipping', $quotes[$i]['id'] . '_' . $method['id'], $shipping['id'] == $quotes[$i]['id'] . '_' . $method['id']) . '</td>
                           <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>
                          </tr>';
					}
				}
				$html .= '</table><br>';
			}
		}
		return $html;
	}

	public static function outputPaymentMethods(){
		global $order, $paymentModules;
		$html = '';
		$fieldDivs = '';
		$pointOfSale = &Session::getReference('pointOfSale');
		$dropArr = $pointOfSale->getMethods('payment', true);
		foreach($dropArr as $dInfo){
			if (tep_not_null($dInfo['id'])){
				$module = $paymentModules->getModule($dInfo['id']);
				$selection = $module->selection();
				if (!empty($selection['fields'])){
					$fieldDivs .= '<div id="' . $module->code . 'Fields" class="paymentFields"><table cellpadding="1" cellspacing="0" border="0">';
					for ($i=0, $n=sizeof($selection['fields']); $i<$n; $i++) {
						$fieldDivs .= '<tr>
                           <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>
                           <td class="ui-widget" style="font-size:.8em">' . $selection['fields'][$i]['title'] . '</td>
                           <td>' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>
                           <td class="ui-widget" style="font-size:.8em">' . $selection['fields'][$i]['field'] . '</td>
                           <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>
                          </tr>';
					}
					$fieldDivs .= '</table></div>';
				}
			}
		}

		$html = '<div class="main"><b>Payment Method: </b>' .
		tep_draw_pull_down_menu('payment', $dropArr, '', 'id="paymentMethod"') .
		'</div>' .
		$fieldDivs;

		return $html;
	}

	public static function _getStateSelector($name, $countryID = false, $zoneID = false, $defaultZoneName = ''){
		$userAccount = &Session::getReference('userAccount');
		$addressBook =& $userAccount->plugins['addressBook'];
		if ($countryID !== false){
			$zonesArray = $addressBook->getCountryZones($countryID);
		}

		if ($countryID !== false && $zoneID !== false){
			$customersZoneName = tep_get_zone_name($countryID, $zoneID, $defaultZoneName);
		}

		if ($zonesArray !== false){
			if (!isset($checked) && isset($customersZoneName)){
				$zone_query = tep_db_query("select distinct zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$countryID . "' and (zone_name like '" . tep_db_input($customersZoneName) . "%' or zone_code like '%" . tep_db_input($customersZoneName) . "%')");
				if (tep_db_num_rows($zone_query) == 1) {
					$zone = tep_db_fetch_array($zone_query);
					$checked = $zone['zone_name'];
				}
			}

			$stateSelector = tep_draw_pull_down_menu($name, $zonesArray, $checked);
		} else {
			$stateSelector = tep_draw_input_field($name, $customersZoneName);
		}

		if (!isset($stateSelector)){
			$stateSelector = tep_draw_input_field($name);
		}
		return $stateSelector;
	}

	public static function getAddressTable($addressID = false, $newCustomer = false, $includeAddressBook = true){
		$pointOfSale = &Session::getReference('pointOfSale');
		if ($addressID !== false){
			$Qaddress = dataAccess::setQuery('select * from {address_book} where address_book_id = {address_id}');
			$Qaddress->setTable('{address_book}', TABLE_ADDRESS_BOOK);
			$Qaddress->setValue('{address_id}', $addressID);
			$Qaddress->runQuery();

			$stateSelector = pointOfSaleHTML::_getStateSelector(
			'state',
			$Qaddress->getVal('entry_country_id'),
			$Qaddress->getVal('entry_zone_id'),
			$Qaddress->getVal('entry_state')
			);
		}

		if (!isset($stateSelector)){
			$stateSelector = pointOfSaleHTML::_getStateSelector('state', STORE_COUNTRY, STORE_ZONE);
		}

		$html = '<table border="0" width="100%" cellspacing="0" cellpadding="2" id="addressEntryTable">';

		if ($newCustomer === true){
			$html .= '<tr>
               <td class="main" valign="top" style="font-weight:bold;" colspan="2"><u>Customer Info</u></td>
              </tr>
              <tr>
               <td class="main">Email Address:</td>
               <td class="main"><input type="text" name="customer_email" id="customer_email"></td>
              </tr>
              <tr>
               <td class="main">Password:</td>
               <td class="main"><input type="password" name="customer_password" id="customer_password"><input type="checkbox" id="passAutoGen" name="passAutoGen" checked="checked">Auto Generate</td>
              </tr>
              <tr>
               <td class="main">Telephone:</td>
               <td class="main"><input type="text" name="customer_telephone" id="customer_telephone"></td>
              </tr>
              <tr>
               <td class="main" valign="top" style="font-weight:bold;" colspan="2"><u>Address Info</u></td>
              </tr>';
		}

		$countries = tep_get_countries();
		foreach($countries as $country){
			$countryArray[] = array(
			'id'   => $country['countries_id'],
			'text' => $country['countries_name']
			);
		}

		$html .= (ACCOUNT_GENDER == 'true' ? '
           <tr>
            <td class="main">' . sysLanguage::get('ENTRY_GENDER') . '</td>
            <td class="main">' . 
		tep_draw_radio_field('gender', 'm', (isset($Qaddress) && $Qaddress->getVal('entry_gender') == 'm')) . '&nbsp;&nbsp;' . sysLanguage::get('MALE') . '&nbsp;&nbsp;' .
		tep_draw_radio_field('gender', 'f', (isset($Qaddress) && $Qaddress->getVal('entry_gender') != 'm')) . '&nbsp;&nbsp;' . sysLanguage::get('FEMALE') .
		'</td>
           </tr>
           ' : '') . '
           <tr>
            <td class="main">' . sysLanguage::get('ENTRY_FIRST_NAME') . '</td>
            <td class="main">' . tep_draw_input_field('firstname', (isset($Qaddress) ? $Qaddress->getVal('entry_firstname') : '')) . '</td>
           </tr>
           <tr>
            <td class="main">' . sysLanguage::get('ENTRY_LAST_NAME') . '</td>
            <td class="main">' . tep_draw_input_field('lastname', (isset($Qaddress) ? $Qaddress->getVal('entry_lastname') : '')) . '</td>
           </tr>
           <tr>
            <td colspan="2">' . tep_draw_separator('pixel_trans.gif', '100%', '10') . '</td>
           </tr>
           ' . (ACCOUNT_COMPANY == 'true' ? '
           <tr>
            <td class="main">' . sysLanguage::get('ENTRY_COMPANY') . '</td>
            <td class="main">' . tep_draw_input_field('company', (isset($Qaddress) ? $Qaddress->getVal('entry_company') : '')) . '</td>
           </tr>
           <tr>
            <td colspan="2">' . tep_draw_separator('pixel_trans.gif', '100%', '10') . '</td>
           </tr>
           ' : '') . '
           <tr>
            <td class="main">' . sysLanguage::get('ENTRY_STREET_ADDRESS') . '</td>
            <td class="main">' . tep_draw_input_field('street_address', (isset($Qaddress) ? $Qaddress->getVal('entry_street_address') : '')) . '</td>
           </tr>
           ' . (ACCOUNT_SUBURB == 'true' ? '
           <tr>
            <td class="main">' . sysLanguage::get('ENTRY_SUBURB') . '</td>
            <td class="main">' . tep_draw_input_field('suburb', (isset($Qaddress) ? $Qaddress->getVal('entry_suburb') : '')) . '</td>
           </tr>
           ' : '') . '
           <tr>
            <td class="main">' . sysLanguage::get('ENTRY_POST_CODE') . '</td>
            <td class="main">' . tep_draw_input_field('postcode', (isset($Qaddress) ? $Qaddress->getVal('entry_postcode') : '')) . '</td>
           </tr>
           <tr>
            <td class="main">' . sysLanguage::get('ENTRY_CITY') . '</td>
            <td class="main">' . tep_draw_input_field('city', (isset($Qaddress) ? $Qaddress->getVal('entry_city') : '')) . '</td>
           </tr>
           ' . (ACCOUNT_STATE == 'true' ? '
           <tr>
            <td class="main">' . sysLanguage::get('ENTRY_STATE') . '</td>
            <td class="main">' . $stateSelector . '</td>
           </tr>
           ' : '') . '
           <tr>
            <td class="main">' . sysLanguage::get('ENTRY_COUNTRY') . '</td>
            <td class="main">' . tep_draw_pull_down_menu('country', $countryArray, (isset($Qaddress) ? $Qaddress->getVal('entry_country_id') : STORE_COUNTRY)) . '</td>
           </tr>';

		if ($addressID !== false){
			$html .= '<tr>
               <td colspan="2" class="main"><input type="hidden" name="address_book_id" value="' . $addressID . '"><input type="hidden" name="customers_id" value="' . $Qaddress->getVal('customers_id') . '"><br><input type="checkbox" name="updateAddressBook" value="1">Update Address Book Entry?</td>
              </tr>';
		}else{
			$html .= '<tr>
               <td colspan="2" class="main"><input type="hidden" name="address_book_id" value="false"></td>
              </tr>';
		}

		$html .= '</table>';

		if ($newCustomer === false && $includeAddressBook === true){
			$settings = array(
			'customerID'      => (isset($Qaddress) ? $Qaddress->getVal('customers_id') : ''),
			'shippingAddress' => $pointOfSale->sendTo,
			'billingAddress'  => $pointOfSale->billTo,
			'pickupAddress'   => $pointOfSale->pickupFrom
			);

			if (isset($_GET['address_type'])){
				$settings['showShipping'] = ($_GET['address_type'] == 'shipping' ? true : false);
				$settings['showBilling'] = ($_GET['address_type'] == 'billing' ? true : false);
				$settings['showPickup'] = ($_GET['address_type'] == 'pickup' ? true : false);
			}

			$html .= '<hr><table cellpadding="3" cellspacing="0" border="0" width="98%">
               <tr>
                <td class="main">Addresses On File</td>
               </tr>
               <tr>
                <td class="main" id="addressesOnFile" width="100%">' . pointOfSaleHTML::getAddressBookEntries($settings) . '</td>
               </tr>
              </table>';
		}

		return $html;
		//require('../includes/languages/' . $_SESSION['language'] . '.php');
		//require('../includes/languages/' . $_SESSION['language'] . '/address_book_process.php');
		//require('../includes/modules/address_book_details.php');
	}
}
?>