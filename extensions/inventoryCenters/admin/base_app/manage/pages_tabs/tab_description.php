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
		$shortdetails = $Inventory->inventory_center_short_details;
		$centerImage = $Inventory->inventory_center_image;
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
		if (($Inventory->gmaps_polygon != '')){
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
		$shortdetails = '';
		$centerImage = '';
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
   <td class="main"><?php echo tep_draw_textarea_field('inventory_center_specific_address','hard',40,3, $saddress, 'class="makeFCK"'); ?></td>
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
	<td class="main" valign="top"><?php echo sysLanguage::get('TEXT_INVENTORY_SHORT_DETAILS'); ?></td>
	<td class="main"><?php echo tep_draw_textarea_field('inventory_center_short_details', 'hard', 30, 5, $shortdetails, 'class="makeFCK"'); ?></td>
 </tr>
	 <tr>
		 <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_INVENTORY_IMAGE'); ?></td>
		 <td class="main"><?php
			 $InventoryImage = htmlBase::newElement('uploadManagerInput')
				 ->setName('inventory_center_image')
				 ->setFileType('image')
				 ->autoUpload(true)
				 ->showPreview(true)
				 ->showMaxUploadSize(true);
			$InventoryImage->setPreviewFile($centerImage);
		echo $InventoryImage->draw();
		?></td>
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
  <tr>
	  <td class="main"><?php echo sysLanguage::get('TEXT_INVENTORY_LAUNCH_POINTS'); ?></td>
	  <td class="main"><?php
		  $Qcheck = Doctrine_Query::create()
			  ->select('MAX(lp_id) as nextId')
			  ->from('InventoryCentersLaunchPoints')
			  ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		  $TableLaunchPoints = htmlBase::newElement('table')
			  ->setCellPadding(3)
			  ->setCellSpacing(0)
			  ->addClass('ui-widget ui-widget-content LaunchPointsTable')
			  ->css(array(
				  'width' => '100%'
			  ))
			  ->attr('data-next_id', $Qcheck[0]['nextId'] + 1)
			  ->attr('language_id', Session::get('languages_id'));
		  $TableLaunchPoints->addHeaderRow(array(
				  'addCls' => 'ui-state-hover LaunchPointsTableHeader',
				  'columns' => array(
					  array('text' => '<div style="float:left;width:280px;">' .sysLanguage::get('TABLE_HEADING_LAUNCH_POINT_NAME').'</div>'.
						  '<div style="float:left;width:80px;">' .sysLanguage::get('TABLE_HEADING_LAUNCH_POINT_MARKER_COLOR').'</div>'.
						  '<div style="float:left;width:120px;">' .sysLanguage::get('TABLE_HEADING_LAUNCH_POINT_POSITION').'</div>'.
						  '<div style="float:left;width:380px;">' .sysLanguage::get('TABLE_HEADING_LAUNCH_POINT_DESC').'</div>'.
						  '<div style="float:left;width:40px;">'.htmlBase::newElement('icon')->setType('insert')->addClass('insertIconHidden')->draw().
						  '</div><br style="clear:both"/>'
					  )
				  )
			  ));
		  $deleteIcon = htmlBase::newElement('icon')->setType('delete')->addClass('deleteIconHidden')->draw();
		  $hiddenList = htmlBase::newElement('list')
			  ->addClass('hiddenList');
		  if(isset($_GET['cID'])){
			  $QLP = Doctrine_Query::create()
				  ->from('InventoryCentersLaunchPoints')
				  ->where('inventory_center_id = ?', $_GET['cID'])
				  ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			  foreach($QLP as $ilp){
				  $lpid = $ilp['lp_id'];
				  $htmlLPName = htmlBase::newElement('input')
				  ->addClass('ui-widget-content lp_name')
				  ->setName('lp[' . $lpid . '][lp_name]')
				  ->attr('size', '30')
				  ->val($ilp['lp_name']);
				  $htmlLPMarkerColor = htmlBase::newElement('input')
				  ->addClass('ui-widget-content lp_marker_color')
				  ->setName('lp[' . $lpid . '][lp_marker_color]')
				  ->attr('size', '10')
				  ->val($ilp['lp_marker_color']);
				  $htmlLPPosition = htmlBase::newElement('input')
				  ->addClass('ui-widget-content lp_position')
				  ->setName('lp[' . $lpid . '][lp_position]')
				  ->attr('size', '15')
				  ->val($ilp['lp_position']);
				  $htmlLPDesc = htmlBase::newElement('textarea')
				  ->addClass('ui-widget-content lp_desc')
				  ->setName('lp[' . $lpid . '][lp_desc]')
				  ->attr('rows','15')
				  ->attr('cols','5')
				  ->val($ilp['lp_desc']);
				  $divLi1 = '<div style="float:left;width:280px;">'.$htmlLPName->draw().'</div>';
				  $divLi2 = '<div style="float:left;width:80px;">'.$htmlLPMarkerColor->draw().'</div>';
				  $divLi3 = '<div style="float:left;width:120px;">'.$htmlLPPosition->draw().'</div>';
				  $divLi4 = '<div style="float:left;width:380px;">'.$htmlLPDesc->draw().'</div>';
				  $divLi5 = '<div style="float:left;width:40px;">'.$deleteIcon.'</div>';
				  $liObj = new htmlElement('li');
				  $liObj->css(array(
						  'font-size' => '.8em',
						  'list-style' => 'none',
						  'line-height' => '1.1em',
						  'border-bottom' => '1px solid #cccccc',
						  'cursor' => 'crosshair'
					  ))
				  ->html($divLi1.$divLi2.$divLi3.$divLi4.$divLi5.'<br style="clear:both;"/>');
				  $hiddenList->addItemObj($liObj);
			  }
		  }
		  $TableLaunchPoints->addBodyRow(array(
				  'columns' => array(
					  array('align' => 'center', 'text' => $hiddenList->draw(),'addCls' => 'launchPoints')
				  )
			  ));
		  echo $TableLaunchPoints->draw();
		  ?></td>
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

