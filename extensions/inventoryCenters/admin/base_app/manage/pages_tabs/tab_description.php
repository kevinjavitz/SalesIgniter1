<?php
	$Providers = Doctrine_Query::create()
	->select('customers_id, customers_firstname, customers_lastname')
	->from('Customers')
	->where('is_provider = ?', '1')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$content_selectbox = htmlBase::newElement('selectbox')
	->setName('provider');

	$content_selectbox->addOption('0', 'Select Provider', false);

	foreach($Providers as $cp){
		$content_selectbox->addOption($cp['customers_id'], $cp['customers_firstname'] . ' ' . $cp['customers_lastname']);
	}


	if (isset($_GET['cID'])){
		$name = $Inventory->inventory_center_name;
		$address = $Inventory->inventory_center_address;
		$saddress = $Inventory->inventory_center_specific_address;
		$details = $Inventory->inventory_center_details;
		$comission = $Inventory->inventory_center_comission;
		$minRentalDays = $Inventory->inventory_center_min_rental_days;
		$provider = $Inventory->inventory_center_customer;
		$delivery_instructions = $Inventory->inventory_center_delivery_instructions;
		$continent = $Inventory->inventory_center_continent;
		$country = $Inventory->inventory_center_country;
		$state = $Inventory->inventory_center_state;
		$city = $Inventory->inventory_center_city;
		$content_selectbox->selectOptionByValue($provider);
		$sortOrder = $Inventory->inventory_center_sort_order;
		if (tep_not_null($Inventory->gmaps_polygon)){
			$polygon = unserialize($Inventory->gmaps_polygon);
			$script = '<script>$(document).ready(function (){';
			for($i=0, $n=sizeof($polygon); $i<$n; $i++){
				$script .= 'leftClick(poly, new GLatLng(' . $polygon[$i]['lat'] . ', ' . $polygon[$i]['lng'] . ', true));';
			}
			$script .= '});</script>';
		}
	}else{
		$name = '';
		$details = '';
		$comission = '0';
		$minRentalDays = '0';
		$delivery_instructions = '';
		$continent = '';
		$country = sysConfig::get('STORE_COUNTRY');
		$state = '';
		$city = '';
		$sortOrder = '0';
		$saddress = '';
		$address = sysConfig::get('STORE_NAME_ADDRESS');
		$script = '<script>$(document).ready(function (){';
		$script .= '});</script>';
	}
	if (sysConfig::get('EXTENSION_INVENTORY_CENTERS_SHIPPING_PER_INVENTORY') == 'True'){
		$methods = array();
		if (isset($Inventory ->inventory_center_shipping)){
			$methods = explode(',', $Inventory ->inventory_center_shipping);
		}

		$module = OrderShippingModules::getModule('inventorycenter');
		if(isset($module) && is_object($module)){
			$quotes = $module->quote();
			for($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++){
				$shippingInputs[] = array(
					'value' => $quotes['methods'][$i]['id'],
					'label' => 'Shipping: ' . $quotes['methods'][$i]['title'],
					'labelPosition' => 'after'
				);
			}

			$shippingGroup = htmlBase::newElement('checkbox')->addGroup(array(
				'separator' => '<br />',
				'name' => 'inventory_center_shipping[]',
				'checked' => $methods,
				'data' => $shippingInputs
			));
		}

	}

?>

 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_INVENTORY_NAME'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('inventory_center_name', $name); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
<?php
/*
		 * this event expects an array having two elements: label and content | i.e. (array(label=>'', content=>''))
		 * -BEGIN-
		 */
		$contents_middle = array();
		EventManager::notify('InventoryCentersFormMiddle', $Inventory, &$contents_middle);

		if (is_array($contents_middle)) {
			foreach($contents_middle as $element){
				if (is_array($element)) {

					if (!isset($element['label'])) $element['label'] = 'no_defined';
					if (!isset($element['content'])) $element['content'] = 'no_defined';
					?>
					<tr>
   <td class="main"><?php echo $element['label']; ?></td>
   <td class="main"><?php echo $element['content']; ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
	<?php
				}
				else { ?>
										<tr>
   <td colspan="2" class="main"><?php echo $element['content']; ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
					<?php
				}
			}
		}
		/* -END- */
	 ?>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_INVENTORY_ADDRESS'); ?></td>
   <td class="main"><?php echo tep_draw_textarea_field('inventory_center_address','hard',40,3, $address); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>

  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_INVENTORY_SPECIFIC_ADDRESS'); ?></td>
   <td class="main"><?php echo tep_draw_textarea_field('inventory_center_specific_address','hard',40,3, $saddress); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>

 <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_INVENTORY_PROVIDERS'); ?></td>
   <td class="main"><?php echo $content_selectbox->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_INVENTORY_MIN_DAYS'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('inventory_center_min_rental_days', $minRentalDays); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_INVENTORY_COMISSION'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('inventory_center_comission', $comission); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_INVENTORY_DELIVERY_INSTRUCTIONS'); ?></td>
   <td class="main"><?php echo tep_draw_textarea_field('inventory_center_delivery_instructions', 'hard', 30, 5, $delivery_instructions, 'class="makeFCK"'); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_INVENTORY_DETAILS'); ?></td>
   <td class="main"><?php echo tep_draw_textarea_field('inventory_center_details', 'hard', 30, 5, $details, 'class="makeFCK"'); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 <?php
 if(sysConfig::get('EXTENSION_INVENTORY_CENTERS_SHIPPING_PER_INVENTORY') == 'True' && isset($module) && is_object($module)){
	 ?>
  <tr>
        <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_INVENTORY_SHIPPING'); ?></td>
        <td class="main"><?php echo $shippingGroup->draw(); ?></td>
  </tr>
  <tr>
        <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>

<?php
 }
?>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_INVENTORY_SORT_ORDER'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('inventory_center_sort_order', $sortOrder); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
	 <?php
  $htmlContinent = htmlBase::newElement('selectbox')
  ->setName('continent');
  $htmlContinent->selectOptionByValue($continent);

  $htmlContinent->addOption('Africa', 'Africa');
  $htmlContinent->addOption('Asia', 'Asia');
  $htmlContinent->addOption('Australasia', 'Australasia');
  $htmlContinent->addOption('Caribbean Islands', 'Caribbean Islands');
  $htmlContinent->addOption('Central America', 'Central America');
  $htmlContinent->addOption('Europe', 'Europe');
  $htmlContinent->addOption('North America', 'North America');
  $htmlContinent->addOption('Pacific Islands', 'Pacific Islands');
  $htmlContinent->addOption('South America', 'South America');

  $countries = tep_get_countriesArray();
  $htmlCountries = htmlBase::newElement('selectbox')
  ->setName('country')
  ->attr('id','countryDrop');
  $htmlCountries->selectOptionByValue($country);
  for ($i = 0, $n = sizeof($countries); $i < $n; $i++){
	$htmlCountries->addOption($countries[$i]['countries_id'], $countries[$i]['countries_name']);
  }

  echo  '<tr>' .
			'<td>' . sysLanguage::get('ENTRY_CITY') . '</td>' .
			'<td>' . tep_draw_input_field('city', $city) . '</td>' .
		'</tr>' .
		'<tr>' .
			'<td>' . sysLanguage::get('ENTRY_STATE') . '</td>' .
			'<td id="stateCol">' . tep_draw_input_field('state', $state) . '</td>' .
		'</tr>' .
		'<tr>' .
			'<td>' . sysLanguage::get('ENTRY_COUNTRY') . '</td>' .
			'<td>' .$htmlCountries->draw() . '</td>' .
		'</tr>'.
		 '<tr>' .
			'<td>' . sysLanguage::get('ENTRY_CONTINENT') . '</td>' .
			'<td>' . $htmlContinent->draw() . '</td>' .
		'</tr>';
?>

	 <?php
	$contents = EventManager::notifyWithReturn('InventoryCentersAfterDescription', (isset($Inventory)?$Inventory:null));
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
	 ?>


 <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_INVENTORY_MAP'); ?></td>
	<td class="main"><?php echo $script;?><div id="mapHolder"><div id="googleMap" style="width:100%;height:750px;"></div></div></td>
  </tr>

	 
 </table>

