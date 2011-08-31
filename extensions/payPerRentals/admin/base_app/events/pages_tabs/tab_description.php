<?php
	if (isset($_GET['eID'])){
		$name = $Event->events_name;
		$date = $Event->events_date;
		$days = $Event->events_days;
		$details = $Event->events_details;
		$shipping = $Event->shipping;
		$countryId = $Event->events_country_id;
		$zoneId = $Event->events_zone_id;
		$state = $Event->events_state;
	    $gates = $Event->gates;
	    $default_gate = $Event->default_gate;
	}else{
		$name = "";
		$details = "";
		$date =date("Y-m-d");
		$shipping = '';
		$gates = '';
		$state = '';
		$default_gate = 0;
		$days = 0;
		$countryId = '223';
	}
	$methods = explode(',', $shipping);
    $gatesChecked = explode(',', $gates);
	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
		$Module = OrderShippingModules::getModule('zonereservation');
	} else{
		$Module = OrderShippingModules::getModule('upsreservation');
	}
    $shippingInputs = array();
	if(isset($Module) && is_object($Module)){
		$quotes = $Module->quote();
		foreach($quotes['methods'] as $mInfo){
			$shippingInputs[] = array(
				'value' => $mInfo['id'],
				'label' => $mInfo['title'],
				'labelPosition' => 'after'
			);
		}
	}
	$shippingGroup = htmlBase::newElement('checkbox')->addGroup(array(
		'separator' => '<br />',
		'name' => 'ppr_shipping[]',
		'checked' => $methods,
		'data' => $shippingInputs
	));
	if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
		$gatesInputs = array();

		$QGate = Doctrine_Query::create()
		->from('PayPerRentalGates')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		foreach($QGate as $gInfo){
			$gatesInputs[] = array(
					'value' => $gInfo['gates_id'],
					'label' => $gInfo['gate_name'].'&nbsp;&nbsp <input type="radio" name="default_gate[]" value="'.$gInfo['gates_id'].'" '.(($default_gate == $gInfo['gates_id'])?'checked="checked"':'').'/>',
					'labelPosition' => 'after'
			);
		}

		$gatesGroup = htmlBase::newElement('checkbox')->addGroup(array(
				'separator' => '<br />',
				'name' => 'ppr_gates[]',
				'checked' => $gatesChecked,
				'data' => $gatesInputs
		));
	}
?>
 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_EVENTS_NAME'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('events_name', $name); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_EVENTS_DATE'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('events_date', $date,'id="events_date"'); ?></td>
 </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
	 <td class="main"><?php echo sysLanguage::get('TEXT_EVENTS_DAYS'); ?></td>
	 <td class="main"><?php echo tep_draw_input_field('events_days', $days); ?></td>
  </tr>
 <tr>
	 <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo sysLanguage::get('TEXT_EVENTS_SHIPPING'); ?></td>
   <td class="main"><?php echo $shippingGroup->draw(); ?></td>
 </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_EVENTS_DETAILS'); ?></td>
   <td class="main"><?php echo tep_draw_textarea_field('events_details', 'soft', 30, 5, $details, 'class="makeFCK"'); ?></td>
  </tr>
<?php
if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
?>
	<tr>
		 <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_EVENTS_GATES'); ?></td>
		 <td class="main"><?php echo $gatesGroup->draw(); ?></td>
	 </tr>
	 <?php
}
	?>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>



  <tr>
   <td class="main" valign="top">&nbsp;</td>
   <td class="main">
   <?php
       $checkAddressBox = htmlBase::newElement('contentbox')
               ->setHeader('Event Address')               
               ->setButtonBarAlign('right');

       $checkAddressBox->addContentBlock('<table border="0" cellspacing="2" cellpadding="2" id="addressEntry">' .

           '<tr>' .
               '<td>' . sysLanguage::get('ENTRY_COUNTRY') . '</td>' .
               '<td>' . tep_get_country_list('events_country', $countryId, 'id="countryDrop"') . '</td>' .
           '</tr>' .
            '<tr>' .
               '<td>' . sysLanguage::get('ENTRY_STATE') . '</td>' .
               '<td id="stateCol">' . tep_draw_input_field('events_state', $state,'id="ezone"') . '</td>' .
           '</tr>' .
       '</table>');
        echo $checkAddressBox->draw();
?>
   </td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
	 <tr>
		 <td class="main"><?php echo sysLanguage::get('TEXT_EVENTS_QTY'); ?></td>
		 <td class="main"><?php

			 $Qcheck = Doctrine_Query::create()
				 ->select('MAX(product_event_id) as nextId')
				 ->from('ProductQtyToEvents')
				 ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			 $TableEventsProducts = htmlBase::newElement('table')
				 ->setCellPadding(3)
				 ->setCellSpacing(0)
				 ->addClass('ui-widget ui-widget-content EventsProductsTable')
				 ->css(array(
					 'width' => '100%'
				 ))
				 ->attr('data-next_id', $Qcheck[0]['nextId'] + 1)
				 ->attr('language_id', Session::get('languages_id'));

			 $TableEventsProducts->addHeaderRow(array(
					 'addCls' => 'ui-state-hover EventsProductsTableHeader',
					 'columns' => array(
						 array('text' => '<div style="float:left;width:80px;">' .sysLanguage::get('TABLE_HEADING_PRODUCT_MODEL').'</div>'.
							 '<div style="float:left;width:150px;">'.sysLanguage::get('TABLE_HEADING_QTY').'</div>'.
							 '<div style="float:left;width:40px;">'.htmlBase::newElement('icon')->setType('insert')->addClass('insertIconHidden')->draw().
							 '</div><br style="clear:both"/>'
						 )
					 )
				 ));

			 $deleteIcon = htmlBase::newElement('icon')->setType('delete')->addClass('deleteIconHidden')->draw();
			 $hiddenList = htmlBase::newElement('list')
			 ->addClass('hiddenList');

			 if(isset($_GET['eID'])){
				 $QProductEvents = Doctrine_Query::create()
					 ->from('ProductQtyToEvents')
					 ->where('events_id=?', $_GET['eID'])
					 ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				 foreach($QProductEvents as $iprodev){
					 $prodevid = $iprodev['product_event_id'];

					  $htmlProductsModel = htmlBase::newElement('input')
						 ->addClass('ui-widget-content prod_model')
						 ->setName('event_products[' . $prodevid . '][products_model]')
						 ->attr('size', '15')
						 ->val($iprodev['products_model']);

					 $htmlQty = htmlBase::newElement('input')
						 ->addClass('ui-widget-content')
						 ->setName('event_products[' . $prodevid . '][qty]')
						 ->attr('size', '15')
						 ->val($iprodev['qty']);

					 $divLi1 = '<div style="float:left;width:80px;">'.$htmlProductsModel->draw().'</div>';
					 $divLi2 = '<div style="float:left;width:80px;">'.$htmlQty->draw().'</div>';
					 $divLi5 = '<div style="float:left;width:40px;">'.$deleteIcon.'</div>';

					 $liObj = new htmlElement('li');
					 $liObj->css(array(
							 'font-size' => '.8em',
							 'list-style' => 'none',
							 'line-height' => '1.1em',
							 'border-bottom' => '1px solid #cccccc',
							 'cursor' => 'crosshair'
						 ))
						 ->html($divLi1.$divLi2.$divLi5.'<br style="clear:both;"/>');
					 $hiddenList->addItemObj($liObj);
				 }
			 }
			 $TableEventsProducts->addBodyRow(array(
					 'columns' => array(
						 array('align' => 'center', 'text' => $hiddenList->draw(),'addCls' => 'eventsProduct')
					 )
				 ));
			  echo $TableEventsProducts->draw();
			 ?></td>
	 </tr>


 </table>

