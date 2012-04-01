<?php
	$QtaxRates = Doctrine_Query::create()
	->select('r.tax_rates_id, z.geo_zone_id, z.geo_zone_name, tc.tax_class_title, tc.tax_class_id, r.tax_priority, r.tax_rate, r.tax_description, r.date_added, r.last_modified')
	->from('TaxRates r')
	->leftJoin('r.TaxClass tc')
	->leftJoin('r.GeoZones z')
	->orderBy('z.geo_zone_name');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setQuery($QtaxRates);
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->usePreset('new')->addClass('newButton'),
		htmlBase::newElement('button')->usePreset('edit')->addClass('editButton')->disable(),
		htmlBase::newElement('button')->usePreset('delete')->addClass('deleteButton')->disable()
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_TAX_RATE_PRIORITY')),
			array('text' => sysLanguage::get('TABLE_HEADING_TAX_CLASS_TITLE')),
			array('text' => sysLanguage::get('TABLE_HEADING_COUNTRY_ZONE')),
			array('text' => sysLanguage::get('TABLE_HEADING_TAX_RATE')),
			array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
		)
	));
	
	$TaxRates = &$tableGrid->getResults();
	if ($TaxRates){
		foreach($TaxRates as $rInfo){
			$rateId = $rInfo['tax_rates_id'];
			$priority = $rInfo['tax_priority'];
			$classTitle = $rInfo['TaxClass']['tax_class_title'];
			$zoneName = $rInfo['GeoZones']['geo_zone_name'];
			$taxRate = $rInfo['tax_rate'];
			$taxDescription = $rInfo['tax_description'];
			$lastModified = $rInfo['last_modified'];
			$dateAdded = $rInfo['date_added'];
			
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-rate_id' => $rateId
				),
				'columns' => array(
					array('text' => $priority),
					array('text' => $classTitle),
					array('text' => $zoneName),
					array('text' => tep_display_tax_value($taxRate) . '%'),
					array('align' => 'center', 'text' => htmlBase::newElement('icon')->setType('info')->draw())
				)
			));
			
			$tableGrid->addBodyRow(array(
				'addCls' => 'gridInfoRow',
				'columns' => array(
					array('colspan' => 6, 'text' => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' . 
						'<tr>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_DATE_ADDED') . '</b></td>' . 
							'<td>' . tep_date_short($dateAdded) . '</td>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_LAST_MODIFIED') . '</b></td>' . 
							'<td>' . tep_date_short($lastModified) . '</td>' . 
						'</tr>' . 
						'<tr>' . 
							'<td colspan="4"><b>' . sysLanguage::get('TEXT_INFO_RATE_DESCRIPTION') . '</b></td>' . 
						'</tr>' . 
						'<tr>' . 
							'<td colspan="4">' . $taxDescription . '</td>' . 
						'</tr>' . 
					'</table>')
				)
			));
		}
	}
?>    
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_RATES');
?></div>
<br />
<div class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;margin-left:5px;">
	<div style="margin:5px;"><?php echo $tableGrid->draw();?></div>
</div>