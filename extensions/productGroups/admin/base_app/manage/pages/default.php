<?php
	$QProductGroups = Doctrine_Query::create()
	->from('ProductsGroups');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)

	->setQuery($QProductGroups);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_PRODUCT_GROUPS')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$pGroups = &$tableGrid->getResults();

	if ($pGroups){
		foreach($pGroups as $pgroup){
			$gId = $pgroup['product_group_id'];

			if ((!isset($_GET['gID']) || (isset($_GET['gID']) && ($_GET['gID'] == $gId))) && !isset($cInfo)){
				$cInfo = new objectInfo($pgroup);
			}

			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $gId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $gId);
			if (isset($cInfo) && $gId == $cInfo->product_group_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link($allGetParams . 'gID=' . $gId, null, 'new');
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}

			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $pgroup['product_group_name']),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));
		}
	}

	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setButtonBarLocation('top');

	switch ($action){

		default:
			if (isset($cInfo) && is_object($cInfo)) {
				$infoBox->setHeader('<b>' . $cInfo->product_group_name . '</b>');

				$deleteButton = htmlBase::newElement('button')->usePreset('delete')->setHref(itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'action=deleteConfirm&gID=' . $cInfo->product_groups_id));
				$editButton = htmlBase::newElement('button')->usePreset('edit')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $cInfo->product_group_id,null,'new'));

				$infoBox->addButton($editButton)->addButton($deleteButton);
			}
			break;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_PRODUCT_GROUPS');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
  <div style="text-align:right;"><?php
  	echo htmlBase::newElement('button')->usePreset('new')->setText('New Product Group')
  	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'gID')), null, 'new', 'SSL'))
  	->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>