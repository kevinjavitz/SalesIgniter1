<?php

$tableGrid = htmlBase::newElement('newGrid')

->usePagination(false);



$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => 'Layout Name'),
		array('text' => 'Display Type')
	)
));

$buttArr = array(
	htmlBase::newElement('button')->usePreset('back')->setText('Back To Templates')->addClass('backButton'),
	htmlBase::newElement('button')->usePreset('new')->addClass('newButton'),
	htmlBase::newElement('button')->usePreset('copy')->setText('Duplicate')->addClass('duplicateButton')->disable(),
	htmlBase::newElement('button')->usePreset('edit')->addClass('configureButton')->disable(),
	htmlBase::newElement('button')->usePreset('edit')->setText('Edit Layout Template')->addClass('editButton')->disable(),
	htmlBase::newElement('button')->usePreset('delete')->addClass('deleteButton')->disable()
);

$iTemplate = Doctrine_Query::create()
	->from('TemplateManagerTemplates')
	->where('template_id = ?', $_GET['tID'])
	->fetchOne();

if($iTemplate->Configuration['NAME']->configuration_value == 'codeGeneration'){
	$buttArr[] = htmlBase::newElement('button')->setText('GenerateCode')->addClass('generateCode')->disable();

}


$tableGrid->addButtons($buttArr);

$QLayouts = Doctrine_Query::create()
->select('layout_id, layout_name, layout_type')
->from('TemplateManagerLayouts')
->where('template_id = ?', $_GET['tID'])
->andWhere('backupof_layout_id = ?','0')
->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

if ($QLayouts){

	foreach($QLayouts as $lInfo){

		$tableGrid->addBodyRow(array(

			'rowAttr' => array(

				'data-layout_id' => $lInfo['layout_id']

			),

			'columns' => array(

				array('addCls' => 'layoutName', 'text' => ucfirst($lInfo['layout_name'])),

				array('addCls' => 'layoutType', 'text' => ucfirst($lInfo['layout_type']))

			)

		));

	}

}

?>

<script type="text/javascript">

	var tID = '<?php echo $_GET['tID'];?>';

</script>

<div class="pageHeading"><?php

	echo sysLanguage::get('HEADING_TITLE_LAYOUTS');

	?></div>

<br />

<div class="gridContainerElem">

	<div style="width:100%;float:left;">

		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">

			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>

		</div>

	</div>

</div>

