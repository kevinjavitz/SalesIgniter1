<?php
	$Qreports = Doctrine_Query::create()
	->from('CompareReports')
	->orderBy('date_added DESC');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(false)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qreports);
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('Back To Snapshots')->setHref(itw_app_link(null, 'server_manager', 'default')),
		htmlBase::newElement('button')->setText('View Diffs')->addClass('viewButton')->disable()
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => 'Compared'),
			array('text' => 'Number Of Diffs'),
			array('text' => 'Date Compared')
		)
	));
	
	$Reports = &$tableGrid->getResults();
	if ($Reports){
		foreach($Reports as $rInfo){
			$Qdiffs = Doctrine_Query::create()
			->select('count(*) as total')
			->from('CompareReportsDiffs')
			->where('report_id = ?', $rInfo['report_id'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			
			$leftSide = $rInfo['left_side'];
			if ($leftSide == sysConfig::getDirFsCatalog()){
				$leftSide = 'Store Directory';
			}else{
				$leftSide = str_replace(sysConfig::getDirFsCatalog(), '', $rInfo['left_side']);
			}
			
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-report_id' => $rInfo['report_id']
				),
				'columns' => array(
					array('text' => 'Left Side: ' . $leftSide . '<br>-<br>Right Side: ' . str_replace(sysConfig::getDirFsCatalog(), '', $rInfo['right_side'])),
					array('text' => $Qdiffs[0]['total']),
					array('text' => tep_datetime_short($rInfo['date_added']))
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