<?php
	$Qupdates = Doctrine_Query::create()
	->from('SesUpdates')
	->where('update_status = ?', 1)
	->orderBy('update_date DESC');
	
	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qupdates);

	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_CHECK_UPDATES'))->addClass('checkButton')/*,
		htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_VIEW_DETAILS'))->addClass('viewButton')->disable()*/
	));
	
	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_DATE_INSTALLED')),
			array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
		)
	));
	
	$Updates = &$tableGrid->getResults();
	if ($Updates){
		foreach($Updates as $uInfo){
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-update_id' => $uInfo['update_id']
				),
				'columns' => array(
					array('text' => $uInfo['update_name']),
					array('text' => $uInfo['update_date']),
					array('text' => htmlBase::newElement('icon')->setType('info')->draw(), 'align' => 'center')
				)
			));
			
			$tableGrid->addBodyRow(array(
				'addCls' => 'gridInfoRow',
				'columns' => array(
					array('colspan' => 6, 'text' => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' . 
						'<tr>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_UPDATE_DESCRIPTION') . '</b></td>' . 
						'</tr>' . 
						'<tr>' . 
							'<td>'  . $uInfo['update_description'] . '</td>' . 
						'</tr>' . 
					'</table>')
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