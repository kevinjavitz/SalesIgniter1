<?php
	$QdiffReports = Doctrine_Query::create()
	->from('CompareReportsDiffs')
	->where('report_id = ?', (int) $_GET['rID'])
	->orderBy('diff_id DESC');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(false)

	->setQuery($QdiffReports);
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('Back To Reports')->setHref(itw_app_link(null, 'server_manager', 'compareReport')),
		htmlBase::newElement('button')->setText('View Diffs')->addClass('viewButton')->disable()
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => 'Compared'),
			array('text' => 'Diff Message')
		)
	));
	
	$DiffReports = &$tableGrid->getResults();
	if ($DiffReports){
		foreach($DiffReports as $rInfo){
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-diff_id' => $rInfo['diff_id']
				),
				'columns' => array(
					array('text' => 'Left Side: ' . str_replace(sysConfig::getDirFsCatalog(), '', $rInfo['left_file']) . '<br>-<br>Right Side: ' . str_replace(sysConfig::getDirFsCatalog(), '', $rInfo['right_file'])),
					array('text' => $rInfo['message'])
				)
			));
		}
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