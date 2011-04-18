<?php
$tableGrid = htmlBase::newElement('newGrid')
->usePagination(false);

$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => 'Layout Name'),
	)
));

$tableGrid->addButtons(array(
	htmlBase::newElement('button')->setText('Back To Templates')->addClass('backButton'),
	htmlBase::newElement('button')->setText('New')->addClass('newButton'),
	htmlBase::newElement('button')->setText('Duplicate')->addClass('duplicateButton')->disable(),
	htmlBase::newElement('button')->setText('Configure')->addClass('configureButton')->disable(),
	htmlBase::newElement('button')->setText('Edit Layout Template')->addClass('editButton')->disable(),
	htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable()
));

$QLayouts = Doctrine_Query::create()
->select('layout_id, layout_name')
->from('TemplateManagerLayouts')
->where('template_id = ?', $_GET['tID'])
->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
if ($QLayouts){
	foreach($QLayouts as $lInfo){
		$tableGrid->addBodyRow(array(
			'rowAttr' => array(
				'data-layout_id' => $lInfo['layout_id']
			),
			'columns' => array(
				array('text' => ucfirst($lInfo['layout_name']))
			)
		));
	}
}
?>
<script type="text/javascript">
	var tID = '<?php echo $_GET['tID'];?>';
</script>
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
