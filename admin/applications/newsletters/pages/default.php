<?php
	$Qnewsletters = Doctrine_Query::create()
	->select('newsletters_id, title, LENGTH(content) as content_length, module, date_added, date_sent, status, locked')
	->from('Newsletters')
	->orderBy('date_added DESC');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qnewsletters);
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('New')->addClass('newButton'),
		htmlBase::newElement('button')->setText('Edit')->addClass('editButton')->disable(),
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable(),
		htmlBase::newElement('button')->setText('Preview')->addClass('previewButton')->disable(),
		htmlBase::newElement('button')->setText('Send')->addClass('sendButton')->disable(),
		htmlBase::newElement('button')->setText('Lock')->addClass('lockButton')->disable(),
		htmlBase::newElement('button')->setText('Unlock')->addClass('unlockButton')->disable()
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_NEWSLETTERS')),
			array('text' => sysLanguage::get('TABLE_HEADING_SIZE')),
			array('text' => sysLanguage::get('TABLE_HEADING_MODULE')),
			array('text' => sysLanguage::get('TABLE_HEADING_SENT')),
			array('text' => sysLanguage::get('TABLE_HEADING_STATUS')),
			array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
		)
	));
	
	$Newsletters = &$tableGrid->getResults();
	if ($Newsletters){
		$allGetParams = tep_get_all_get_params(array('nID', 'action', 'app', 'appPage'));
		foreach($Newsletters as $nInfo){
			$id = $nInfo['newsletters_id'];
			$title = $nInfo['title'];
			$contentLength = $nInfo['content_length'];
			$module = $nInfo['module'];
			$dateAdded = $nInfo['date_added'];
			$dateSent = $nInfo['date_sent'];
			$status = $nInfo['status'];
			$locked = $nInfo['locked'];
			
			if ($status == '1'){
				$statusIcon = tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK);
			}else{
				$statusIcon = tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS);
			}
			
			if ($locked > 0) {
				$lockedIcon = tep_image(DIR_WS_ICONS . 'locked.gif', ICON_LOCKED);
			}else{
				$lockedIcon = tep_image(DIR_WS_ICONS . 'unlocked.gif', ICON_UNLOCKED);
			}
			
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-newsletter_id' => $id,
					'data-locked' => ($locked > 0 ? 'true' : 'false')
				),
				'columns' => array(
					array('text' => '<a href="' . itw_app_link($allGetParams . 'nID=' . $id, 'newsletters', 'preview') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $title),
					array('align' => 'right', 'text' => number_format($contentLength) . ' bytes'),
					array('align' => 'center', 'text' => $module),
					array('align' => 'center', 'text' => $statusIcon),
					array('align' => 'center', 'text' => $lockedIcon),
					array('align' => 'center', 'text' => htmlBase::newElement('icon')->setType('info')->draw())
				)
			));
			
			$tableGrid->addBodyRow(array(
				'addCls' => 'gridInfoRow',
				'columns' => array(
					array('colspan' => 6, 'text' => '<table cellpadding="1" cellspacing="0" border="0">' . 
						'<tr>' . 
							'<td><b>' . sysLanguage::get('TEXT_NEWSLETTER_DATE_ADDED') . '</b></td>' . 
							'<td>' . tep_date_short($dateAdded) . '</td>' . 
						'</tr>' . 
						($status == '1' ? '<tr>' . 
							'<td><b>' . sysLanguage::get('TEXT_NEWSLETTER_DATE_SENT') . '</b></td>' . 
							'<td>' . tep_date_short($dateSent) . '</td>' . 
						'</tr>' : '') . 
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