<?php
$tableGrid = htmlBase::newElement('grid');

$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => 'Model'),
		array('text' => 'Status'),
		array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
	)
));

$Models = Doctrine_Core::getLoadedModels();
sort($Models);
foreach($Models as $mInfo){
	$ModelCheck = checkModel($mInfo);
	$tableGrid->addBodyRow(array(
		'columns' => array(
			array('text' => $mInfo),
			array('align' => 'center', 'text' => '<span class="statusIcon ui-icon ui-icon-circle-' . ($ModelCheck['isOk'] === false ? 'close' : 'check') . '"></span>'),
			array('align' => 'center', 'text' => ($ModelCheck['isOk'] === false ? htmlBase::newElement('button')->addClass('resButton')->setText('Fix Problems')->setHref($ModelCheck['resolution'])->draw() : ''))
		)
	));
}
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />
<div style="width:100%;float:left;">
	<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
		<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
	</div>
</div>
