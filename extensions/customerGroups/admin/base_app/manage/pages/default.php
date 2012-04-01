<?php
	$Qcustomers = Doctrine_Query::create()
	->from('CustomerGroups')
	->orderBy('customer_groups_name asc');
	
	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)

	->setQuery($Qcustomers);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMER_GROUPS_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	
	$customerGroups = &$tableGrid->getResults();
	if ($customerGroups){
		foreach($customerGroups as $cgInfo){
			$cgInfoId = $cgInfo['customer_groups_id'];
		
			if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $cgInfoId))) && !isset($cInfo)){
				$cInfo = new objectInfo($cgInfo);
			}
		
			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $cgInfoId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $cgInfoId);
			if (isset($cInfo) && $cgInfoId == $cInfo->customer_groups_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'action=edit&cID=' . $cgInfoId);
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}
		
			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $cgInfo['customer_groups_name']),
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
				$infoBox->setHeader('<b>' . $cInfo->customer_groups_name . '</b>');
				
				$deleteButton = htmlBase::newElement('button')->usePreset('delete')->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'action=deleteConfirm&cID=' . $cInfo->customer_groups_id));
				$editButton = htmlBase::newElement('button')->usePreset('edit')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $cInfo->customer_groups_id, 'manage', 'new'));
				
				$infoBox->addButton($editButton)->addButton($deleteButton);
				
				$infoBox->addContentRow('<br>' . nl2br($cInfo->customer_groups_credit));
			}
			break;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_GROUPS');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
  <div style="text-align:right;"><?php
  	echo htmlBase::newElement('button')->usePreset('new')->setText(sysLanguage::get('TEXT_NEW_CUSTOMER_GROUPS'))
  	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')), null, 'new', 'SSL'))
  	->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>