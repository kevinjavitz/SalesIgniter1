<?php
	$Qcountries = Doctrine_Query::create()
	->select('countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, address_format_id')
	->from('Countries')
	->orderBy('countries_name');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setQuery($Qcountries);
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->usePreset('new')->addClass('newButton'),
		htmlBase::newElement('button')->usePreset('edit')->addClass('editButton')->disable(),
		htmlBase::newElement('button')->usePreset('delete')->addClass('deleteButton')->disable()
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_COUNTRY_NAME')),
			array('colspan' => '2', 'text' => sysLanguage::get('TABLE_HEADING_COUNTRY_CODES'))
		)
	));
	
	$Countries = &$tableGrid->getResults();
	if ($Countries){
		foreach($Countries as $cInfo){
			$countryId = $cInfo['countries_id'];
			$countryName = $cInfo['countries_name'];
			$isoCode2 = $cInfo['countries_iso_code_2'];
			$isoCode3 = $cInfo['countries_iso_code_3'];
			$addressFormatId = $cInfo['address_format_id'];
			
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-country_id' => $countryId
				),
				'columns' => array(
					array('text' => $countryName),
					array('text' => $isoCode2),
					array('text' => $isoCode3)
				)
			));
		}
	}
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<div class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;margin-left:5px;">
	<div style="margin:5px;"><?php echo $tableGrid->draw();?></div>
</div>