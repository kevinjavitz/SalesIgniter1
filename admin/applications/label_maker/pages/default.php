<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<div class="hideForPrint main"><?php
	echo sysLanguage::get('TEXT_INFO_SELECT_DATES');
?></div>
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="hideForPrint">
 <tr>
  <td width="50%" class="main" valign="top"><fieldset>
   <legend><?php echo sysLanguage::get('TEXT_LEGEND_FROM_DATE');?></legend>
   <div type="text" id="DP_startDate"></div>
   <br /><input type="text" name="start_date" id="start_date" value="<?php echo date('Y-m-d');?>">
  </fieldset></td>
  <td width="50%" class="main" valign="top"><fieldset>
   <legend><?php echo sysLanguage::get('TEXT_LEGEND_TO_DATE');?></legend>
   <div type="text" id="DP_endDate"></div>
   <br /><input type="text" name="end_date" id="end_date" value="<?php echo date('Y-m-d');?>">
  </fieldset></td>
 </tr>
</table>
<div class="hideForPrint main" style="text-align:right;margin:.5em;"><?php
	$selectBox = htmlBase::newElement('selectbox')
	->setName('filter')
	->setId('filter')
	->setLabel(sysLanguage::get('TEXT_ENTRY_PRINT_FOR'))
	->setLabelPosition('before');

	$selectBox->addOption('all', sysLanguage::get('TEXT_ALL'));
	$selectBox->addOption('rental', sysLanguage::get('TEXT_RENTAL'));
	
	if (defined('EXTENSION_PAY_PER_RENTALS_ENABLED') && EXTENSION_PAY_PER_RENTALS_ENABLED == 'True'){
		$selectBox->addOption('onetime', sysLanguage::get('TEXT_PAY_PER_RENTAL'));
	}
	echo $selectBox->draw();
?></div>
<?php
	if (defined('EXTENSION_INVENTORY_CENTERS_ENABLED') && EXTENSION_INVENTORY_CENTERS_ENABLED == 'True'){
		$selectBox = htmlBase::newElement('selectbox')
		->setName('invCenter')
		->setId('invCenter')
		->setLabel(sysLanguage::get('TEXT_ENTRY_INVENTORY_CENTER'))
		->setLabelPosition('before');
		
		$selectBox->addOption('', sysLanguage::get('TEXT_ALL_CENTERS'));
		
		$QinventoryCenter = tep_db_query('select * from ' . TABLE_PRODUCTS_INVENTORY_CENTERS . ' order by inventory_center_name');
		while($inventoryCenter = tep_db_fetch_array($QinventoryCenter)){
			$selectBox->addOption($inventoryCenter['inventory_center_id'], $inventoryCenter['inventory_center_name']);
		}
		echo '<div class="hideForPrint main" style="text-align:right;margin:.5em;">' . 
			$selectBox->draw() . 
		'</div>';
	}
?>
<div class="hideForPrint main" style="text-align:right;margin:.5em;"><?php
	echo htmlBase::newElement('button')->setName('genList')->setId('genList')->setText(sysLanguage::get('TEXT_BUTTON_GENERATE_LIST'))->draw();
?></div>

<div class="ui-widget ui-widget-content ui-corner-all" style="margin:.5em;padding:.5em;">
<table cellpadding="2" cellspacing="0" border="0" id="reservations" width="100%">
 <thead>
  <tr>
   <th class="ui-widget-header" style="text-align:left;border-right:none;"><input type="checkbox" class="checkAll"></th>
   <th class="ui-widget-header" style="text-align:left;border-left:none;border-right:none;"><?php echo sysLanguage::get('TABLE_HEADING_NAME');?></th>
   <th class="ui-widget-header" style="text-align:left;border-left:none;border-right:none;"><?php echo sysLanguage::get('TABLE_HEADING_ADDRESS');?></th>
   <th class="ui-widget-header" style="text-align:left;border-left:none;border-right:none;"><?php echo sysLanguage::get('TABLE_HEADING_PRODUCT');?></th>
   <th class="ui-widget-header" style="text-align:left;border-left:none;border-right:none;"><?php echo sysLanguage::get('TABLE_HEADING_BARCODE');?></th>
   <th class="ui-widget-header" style="text-align:left;border-left:none;border-right:none;"><?php echo sysLanguage::get('TABLE_HEADING_INVENTORY_CENTER');?></th>
   <th class="ui-widget-header" style="text-align:left;border-left:none;border-right:none;"><?php echo sysLanguage::get('TABLE_HEADING_TYPE');?></th>
   <th class="ui-widget-header" style="text-align:left;border-left:none;"><?php echo sysLanguage::get('TABLE_HEADING_DATE_SENT');?></th>
  </tr>
 </thead>
 <tbody>
 </tbody>
</table>
</div>

<div class="hideForPrint main" style="text-align:right;margin:.5em;"><?php
	$selectBox = htmlBase::newElement('selectbox')
	->setName('label_type')
	->setId('label_type');
	
	$selectBox->addOption('avery_5160', 'Avery 5160 Shipping Labels');
	$selectBox->addOption('avery_5164', 'Avery 5164 Product Info Labels');
	$selectBox->addOption('avery_5160_html', 'Shipping Labels HTML Version');
	$selectBox->addOption('avery_5164_html', 'Product Info Labels HTML Version');
	
	echo $selectBox->draw();
?></div>

<div class="hideForPrint main" style="text-align:right;margin:.5em;"><?php
	echo htmlBase::newElement('button')->setName('genLabels')->setId('genLabels')->setText(sysLanguage::get('TEXT_BUTTON_GENERATE_LABELS'))->draw();
	echo htmlBase::newElement('button')->setName('printPage')->setId('printPage')->setText(sysLanguage::get('TEXT_BUTTON_PRINT_PAGE'))->draw();
?></div>
