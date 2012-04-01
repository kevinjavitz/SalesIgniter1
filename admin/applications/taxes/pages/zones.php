<?php
	$QtaxZones = Doctrine_Query::create()
	->select('geo_zone_id, geo_zone_name, geo_zone_description, last_modified, date_added')
	->from('GeoZones')
	->orderBy('geo_zone_name');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setQuery($QtaxZones);
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->usePreset('new')->addClass('newButton'),
		htmlBase::newElement('button')->usePreset('edit')->addClass('editButton')->disable(),
		htmlBase::newElement('button')->usePreset('delete')->addClass('deleteButton')->disable()
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_TAX_ZONES')),
			array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
		)
	));
	
	$TaxZones = &$tableGrid->getResults();
	if ($TaxZones){
		foreach($TaxZones as $zInfo){
			$zoneId = $zInfo['geo_zone_id'];
			$zoneName = $zInfo['geo_zone_name'];
			$zoneDescription = $zInfo['geo_zone_description'];
			$dateAdded = $zInfo['date_added'];
			$lastModified = $zInfo['last_modified'];
			
			$QnumZones = Doctrine_Query::create()
			->select('count(*) as total')
			->from('ZonesToGeoZones')
			->where('geo_zone_id = ?', (int)$zoneId)
			->groupBy('geo_zone_id')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-zone_id' => $zoneId
				),
				'columns' => array(
					array('text' => $zoneName),
					array('align' => 'center', 'text' => htmlBase::newElement('icon')->setType('info')->draw())
				)
			));
			
			$tableGrid->addBodyRow(array(
				'addCls' => 'gridInfoRow',
				'columns' => array(
					array('colspan' => 6, 'text' => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' . 
						'<tr>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_ZONE_DESCRIPTION') . '</b></td>' . 
							'<td>' . $zoneDescription . '</td>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_NUMBER_ZONES') . '</b></td>' . 
							'<td>' . $QnumZones[0]['total'] . '</td>' . 
						'</tr>' . 
						'<tr>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_DATE_ADDED') . '</b></td>' . 
							'<td>' . tep_date_short($dateAdded) . '</td>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_LAST_MODIFIED') . '</b></td>' . 
							'<td>' . tep_date_short($lastModified) . '</td>' . 
						'</tr>' . 
					'</table>')
				)
			));
		}
	}
?>
<script language="javascript">
<?php
	$Qcountries = Doctrine_Query::create()
	->select('zone_country_id')
	->from('Zones')
	->orderBy('zone_country_id')
	->groupBy('zone_country_id')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	echo 'var zones = [];' . "\n";
	foreach($Qcountries as $cInfo){
		echo 'zones[' . $cInfo['zone_country_id'] . '] = [' . "\n";
		
		$Qzones = Doctrine_Query::create()
		->select('zone_name, zone_id')
		->from('Zones')
		->where('zone_country_id = ?', $cInfo['zone_country_id'])
		->orderBy('zone_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qzones){
			$zoneArr = array(
				'	["", "' . sysLanguage::get('PLEASE_SELECT') . '"]'
			);
			foreach($Qzones as $zInfo){
				$zoneArr[] = '	["' . $zInfo['zone_id'] . '", "' . $zInfo['zone_name'] . '"]';
			}
			echo implode(",\n", $zoneArr);
		}
		echo "\n" . '];' . "\n\n";
	}
?>
</script>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_ZONES');
?></div>
<br />
<div class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;margin-left:5px;">
	<div style="margin:5px;"><?php echo $tableGrid->draw();?></div>
</div>
<div class="addAssociationWindow" style="display:none;"><table cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td><?php echo sysLanguage::get('TEXT_INFO_COUNTRY');?></td>
		<td><?php echo tep_draw_pull_down_menu('zone_country_id', tep_get_countries(sysLanguage::get('TEXT_ALL_COUNTRIES')));?></td>
	</tr>
	<tr>
		<td><?php echo sysLanguage::get('TEXT_INFO_COUNTRY_ZONE');?></td>
		<td><?php echo tep_draw_pull_down_menu('zone_id', tep_prepare_country_zones_pull_down());?></td>
	</tr>
</table></div>