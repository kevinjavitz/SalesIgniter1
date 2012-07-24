<?php
$Qadmin = Doctrine_Query::create()
	->from('AdminFavorites a')
	->orderBy('a.admin_favs_name');

$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qadmin);

$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('Edit')->addClass('editButton')->disable(),
		htmlBase::newElement('button')->setText('Load As My Set ')->addClass('loadSetButton')->disable(),
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable()
	));

$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_NAME_FAVORITES'))
		)
	));

$infoBoxes = array();
$allGetParams = tep_get_all_get_params(array('mID', 'action'));

$admin = &$tableGrid->getResults();
if ($admin){
	foreach($admin as $aInfo){
		$adminId = $aInfo['admin_favs_id'];
		$adminName = $aInfo['admin_favs_name'];

		$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-admin_id' => $adminId
				),
				'columns' => array(
					array('text' => $adminName),
				)
			));
	}
}
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_FAVORITES');?></div>
<br />
<div class="gridContainerNew">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		</div>
	</div>
</div>
