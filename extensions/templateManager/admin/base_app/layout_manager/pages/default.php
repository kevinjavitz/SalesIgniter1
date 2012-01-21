<?php
$tableGrid = htmlBase::newElement('newGrid')
->usePagination(false);

$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => 'Template Name'),
	)
));

$tableGrid->addButtons(array(
	htmlBase::newElement('button')->setText('View Layouts')->addClass('layoutsButton')->disable(),
	htmlBase::newElement('button')->setText('New')->addClass('newButton'),
	htmlBase::newElement('button')->setText('Copy')->addClass('copyButton'),
	htmlBase::newElement('button')->setText('Import')->addClass('importButton'),
	htmlBase::newElement('button')->setText('Export')->addClass('exportButton')->disable(),
	htmlBase::newElement('button')->setText('Configure')->addClass('configureButton')->disable(),
	htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable()
));

$Qtemplates = Doctrine_Query::create()
	->select('t.template_id, tc.configuration_value as template_name')
	->from('TemplateManagerTemplates t')
	->leftJoin('t.Configuration tc')
	->where('configuration_key = ?', 'NAME')
	->orderBy('template_name')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

foreach($Qtemplates as $tInfo){
	$tableGrid->addBodyRow(array(
		'rowAttr' => array(
			'data-template_id' => $tInfo['template_id']
		),
		'columns' => array(
			array('text' => '<span class="ui-icon ui-icon-folder-collapsed"></span><span>' . ucfirst($tInfo['template_name']) . '</span>')
		)
	));
}
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
	?></div>
<br />
<div class="gridContainer">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		</div>
	</div>
</div>
