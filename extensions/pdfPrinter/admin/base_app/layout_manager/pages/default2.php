<?php
$tableGrid = htmlBase::newElement('newGrid')
->usePagination(false);

$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => 'Template Name')
	)
));

$tableGrid->addButtons(array(
	htmlBase::newElement('button')->setText('View Layouts')->addClass('layoutsButton')->disable(),
	htmlBase::newElement('button')->setText('New')->addClass('newButton'),
	htmlBase::newElement('button')->setText('Import')->addClass('importButton'),
	htmlBase::newElement('button')->setText('Export')->addClass('exportButton')->disable(),
	htmlBase::newElement('button')->setText('Configure')->addClass('configureButton')->disable(),
	htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable()
));

$Templates = Doctrine_Core::getTable('TemplateManagerTemplates')
	->findAll();
foreach($Templates as $Template){
	$tableGrid->addBodyRow(array(
		'rowAttr' => array(
			'data-template_id' => $Template->template_id
		),
		'columns' => array(
			array('text' => '<span class="ui-icon ui-icon-folder-collapsed"></span><span>' . ucfirst($Template->Configuration['NAME']->configuration_value) . '</span>')
		)
	));
}
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_TEMPLATES');
	?></div>
<br />
<div class="gridContainer">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		</div>
	</div>
</div>
