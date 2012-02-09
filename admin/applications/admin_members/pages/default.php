<?php

$Qadmin = Doctrine_Query::create()
	->from('Admin a')
	->leftJoin('a.AdminGroups ag.')
	->orderBy('a.admin_firstname');

$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qadmin);

$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('New Member')->addClass('newButton'),
		htmlBase::newElement('button')->setText('View Groups')->addClass('groupsButton')->setHref(itw_app_link(null, 'admin_members', 'groups')),
		htmlBase::newElement('button')->setText('Edit')->addClass('editButton passProtect')->disable(),
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton passProtect')->disable()
	));

$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_EMAIL')),
			array('text' => sysLanguage::get('TABLE_HEADING_GROUPS')),
			array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
		)
	));

$infoBoxes = array();
$allGetParams = tep_get_all_get_params(array('mID', 'action'));

$admin = &$tableGrid->getResults();
if ($admin){
	foreach($admin as $aInfo){
		$adminId = $aInfo['admin_id'];
		$adminFirstName = $aInfo['admin_firstname'];
		$adminLastName = $aInfo['admin_lastname'];
		$adminEmail = $aInfo['admin_email_address'];
		$adminGroupName = $aInfo['AdminGroups']['admin_groups_name'];
		$adminLogNum = $aInfo['admin_lognum'];
		$adminDateCreated = tep_date_short($aInfo['admin_created']);
		$adminDateModified = tep_date_short($aInfo['admin_modified']);
		$adminDateLastLogin = tep_date_short($aInfo['admin_logdate']);

		$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-admin_id' => $adminId
				),
				'columns' => array(
					array('text' => $adminFirstName . '&nbsp;' . $adminLastName),
					array('text' => $adminEmail),
					array('text' => $adminGroupName),
					array('text' => htmlBase::newElement('icon')->setType('info')->draw(), 'align' => 'right')
				)
			));

		$tableGrid->addBodyRow(array(
				'addCls' => 'gridInfoRow',
				'columns' => array(
					array('colspan' => 4, 'text' => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' .
						'<tr>' .
						'<td><b>' . sysLanguage::get('TEXT_INFO_FULLNAME') . '</b></td>' .
						'<td>' . $adminFirstName . ' ' . $adminLastName . '</td>' .
						'<td><b>' . sysLanguage::get('TEXT_INFO_EMAIL') . '</b></td>' .
						'<td>' . $adminEmail . '</td>' .
						'</tr>' .
						'<tr>' .
						'<td><b>' . sysLanguage::get('TEXT_INFO_GROUP') . '</b></td>' .
						'<td>' . $adminGroupName . '</td>' .
						'<td><b>' . sysLanguage::get('TEXT_INFO_LOGNUM') . '</b></td>' .
						'<td>' . $adminLogNum . '</td>' .
						'</tr>' .
						'<tr>' .
						'<td><b>' . sysLanguage::get('TEXT_INFO_CREATED') . '</b></td>' .
						'<td>' . $adminDateCreated . '</td>' .
						'<td><b>' . sysLanguage::get('TEXT_INFO_LOGDATE') . '</b></td>' .
						'<td>' . $adminDateLastLogin . '</td>' .
						'</tr>' .
						'<tr>' .
						'<td><b>' . sysLanguage::get('TEXT_INFO_MODIFIED') . '</b></td>' .
						'<td>' . $adminDateModified . '</td>' .
						'<td></td>' .
						'<td></td>' .
						'</tr>' .
						'</table>')
				)
			));
	}
}
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />
<div class="gridContainer">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		</div>
	</div>
</div>
