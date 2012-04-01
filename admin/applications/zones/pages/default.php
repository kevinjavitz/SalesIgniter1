<?php
$Qzones = Doctrine_Query::create()
	->from('GoogleZones')
	->orderBy('google_zones_name asc');

$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setQuery($Qzones);

$tableGrid->addButtons(array(
	htmlBase::newElement('button')->usePreset('new')->addClass('newButton'),
	htmlBase::newElement('button')->usePreset('edit')->addClass('editButton')->disable(),
	htmlBase::newElement('button')->usePreset('delete')->addClass('deleteButton')->disable()
));

$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TABLE_HEADING_GOOGLE_ZONE_NAME'))
	)
));

$zones = &$tableGrid->getResults();
if ($zones){
	foreach($zones as $zone){
		$tableGrid->addBodyRow(array(
			'rowAttr' => array(
				'data-zone_id' => $zone['google_zones_id']
			),
			'columns' => array(
				array('text' => $zone['google_zones_name'])
			)
		));
	}
}
?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=<?php echo sysConfig::get('GOOGLE_MAPS_API_KEY');?>" type="text/javascript"></script>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />
<div>
	<div class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;margin-left:5px;">
		<div style="margin:5px;"><?php echo $tableGrid->draw();?></div>
	</div>
</div>
