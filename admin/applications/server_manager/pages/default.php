<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<?php
	$archiveArr = array();
	$Snapshots = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'snapshots/');
	foreach($Snapshots as $archive){
		if ($archive->isDot() || $archive->isDir()) continue;
		$archiveArr[$archive->getMTime()] = array(
			'fileName' => $archive->getBasename('.zip'),
			'mTime' => $archive->getMTime()
		);
	}
	ksort($archiveArr);
	
	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(false);

	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('New Snapshot')->addClass('snapshotButton')/*->setHref(itw_app_link('action=generateSnapshot', 'server_manager', 'default', 'SSL'))*/,
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable(),
		htmlBase::newElement('button')->setText('Compare')->addClass('compareButton')->disable(),
		htmlBase::newElement('button')->setText('Roll Back')->addClass('restoreButton')->disable()
	));
	
	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => 'File Name'),
			array('text' => 'Date Taken')/*,
			array('text' => 'Info')*/
		)
	));
	
	if ($archiveArr){
		foreach($archiveArr as $sInfo){
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-file_name' => $sInfo['fileName']
				),
				'columns' => array(
					array('text' => $sInfo['fileName']),
					array('text' => strftime(sysLanguage::getDateTimeFormat(), $sInfo['mTime']))/*,
					array('text' => htmlBase::newElement('icon')->setType('info')->draw(), 'align' => 'center')*/
				)
			));
			
			/*$tableGrid->addBodyRow(array(
				'addCls' => 'gridInfoRow',
				'columns' => array(
					array('colspan' => 6, 'text' => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' . 
						'<tr>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_LANGUAGE_NAME') . '</b></td>' . 
							'<td> ' . $lInfo['name'] . '</td>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_LANGUAGE_CODE') . '</b></td>' . 
							'<td>' . $lInfo['code'] . '</td>' .
						'</tr>' . 
						'<tr>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_LANGUAGE_DIRECTORY') . '</b></td>' . 
							'<td>'  . $lInfo['directory'] . '</td>' . 
						'</tr>' . 
					'</table>')
				)
			));*/
		}
	}
?>
<div style="width:100%;float:left;">
	<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
		<div style="width:99%;margin:5px;"><?php
			echo $tableGrid->draw();
		?></div>
	</div>
</div>