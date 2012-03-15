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
	<div class="" style="font-weight: bold; color:red;text-align: justify;margin-left:200px;">
		Please check that your product barcodes are according with the barcode type choosed.<br/>
		For Code128B, Code 39 Extended and QR is no need to check since most characters are allowed.<br/>
		Code 25 and Code 25 Interleaved allow only numbers and Code 39 allow only numbers and uppercase chars.<br/>
	</div>
<div class="hideForPrint main" style="text-align:right;margin:.5em;"><?php
	$selectBox = htmlBase::newElement('selectbox')
	->setName('filter')
	->setId('filter')
	->setLabel(sysLanguage::get('TEXT_ENTRY_PRINT_FOR'))
	->setLabelPosition('before');

	$selectBox->addOption('all', sysLanguage::get('TEXT_ALL'));
	$selectBox->addOption('rental', sysLanguage::get('TEXT_RENTAL'));
	
	if (defined('EXTENSION_PAY_PER_RENTALS_ENABLED') && sysConfig::get('EXTENSION_PAY_PER_RENTALS_ENABLED') == 'True'){
		$selectBox->addOption('onetime', sysLanguage::get('TEXT_PAY_PER_RENTAL'));
	}
	echo $selectBox->draw();
?></div>
<?php
	if (defined('EXTENSION_INVENTORY_CENTERS_ENABLED') && sysConfig::get('EXTENSION_INVENTORY_CENTERS_ENABLED') == 'True'){
		$selectBox = htmlBase::newElement('selectbox')
		->setName('invCenter')
		->setId('invCenter')
		->setLabel(sysLanguage::get('TEXT_ENTRY_INVENTORY_CENTER'))
		->setLabelPosition('before');
		
		$selectBox->addOption('', sysLanguage::get('TEXT_ALL_CENTERS'));
		
		$QinventoryCenter = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from products_inventory_centers order by inventory_center_name');
		foreach($QinventoryCenter as $inventoryCenter){
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

<div class="ui-widget ui-widget-content ui-corner-all middleTable" style="margin:.5em;padding:.5em;">
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
	  <?php
	  if(sysConfig::get('EXTENSION_CUSTOM_FIELDS_SHOW_DOWNLOAD_FILE_COLUMN') == 'True'){
	  ?>
	        <th class="ui-widget-header" style="text-align:left;border-left:none;"><?php echo sysLanguage::get('TABLE_HEADING_DOWNLOAD_FILE');?></th>
		  <?php
		}
?>
  </tr>
 </thead>
 <tbody>
 </tbody>
</table>
</div>

<div class="hideForPrint main" style="text-align:right;margin:.5em;"><?php
	echo htmlBase::newElement('button')->setName('genLabels')->setId('genLabels')->setText(sysLanguage::get('TEXT_BUTTON_GENERATE_LABELS'))->draw();
	echo htmlBase::newElement('button')->setName('printPage')->setId('printPage')->setText(sysLanguage::get('TEXT_BUTTON_PRINT_PAGE'))->draw();
?></div>
