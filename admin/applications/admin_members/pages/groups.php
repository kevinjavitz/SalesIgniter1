<?php
	$Qgroups = Doctrine_Query::create()
	->from('AdminGroups')
	->orderBy('admin_groups_name');

	$TableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setQuery($Qgroups);

$TableGrid->addButtons(array(
	htmlBase::newElement('button')->addClass('backButton')->usePreset('back')->setHref(itw_app_link(null, 'admin_members', 'default')),
	htmlBase::newElement('button')->addClass('newButton')->usePreset('new'),
	htmlBase::newElement('button')->addClass('editButton')->usePreset('edit')->disable(),
	htmlBase::newElement('button')->addClass('permissionsButton')->setIcon('lockClosed')->setText(sysLanguage::get('TEXT_BUTTON_FILE_PERMISSION'))->disable(),
	htmlBase::newElement('button')->addClass('deleteButton')->usePreset('delete')->disable()
));

$TableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TABLE_HEADING_GROUPS_NAME')),
		array('text' => sysLanguage::get('TABLE_CUSTOMER_LOGIN_ALLOWED')),
		array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
	)
));

$Groups = &$TableGrid->getResults();
if ($Groups){
	foreach($Groups as $group){
		$TableGrid->addBodyRow(array(
			'rowAttr' => array(
				'data-group_id' => $group['admin_groups_id']
			),
			'columns' => array(
				array('text' => $group['admin_groups_name']),
				array('text' => ($group['customer_login_allowed'] == '1' ? 'Yes' : 'No')),
				array(
					'text'  => htmlBase::newElement('icon')->setType('info'),
					'align' => 'right'
				)
			)
		));
	}
}
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_GROUPS');?></div>
<br />
<div class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;margin-left:5px;">
	<div style="margin:5px;">
		<?php echo $TableGrid->draw();?>
	</div>
</div>
