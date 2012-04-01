<?php
	$QaddressFormat = Doctrine_Query::create()
	->from('AddressFormat')
	->orderBy('address_format_id asc');
	
	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)

	->setQuery($QaddressFormat);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_ADDRESS_FORMAT_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	
	$addressFormat = &$tableGrid->getResults();
	if ($addressFormat){
		foreach($addressFormat as $address){
			$addressId = $address['address_format_id'];
		
			if ((!isset($_GET['fID']) || (isset($_GET['fID']) && ($_GET['fID'] == $addressId))) && !isset($cInfo)){
				$cInfo = new objectInfo($address);
			}
		
			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'fID=' . $addressId));

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'fID=' . $addressId);
			if (isset($cInfo) && $addressId == $cInfo->address_format_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'action=edit&fID=' . $addressId);
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}
		
			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => strip_tags($address['address_summary'])),
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
				$infoBox->setHeader('<b>' . $cInfo->address_summary . '</b>');
				
				$deleteButton = htmlBase::newElement('button')->setType('submit')->usePreset('delete')->setHref(itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'action=deleteConfirm&fID=' . $cInfo->address_format_id));
				$editButton = htmlBase::newElement('button')->setType('submit')->usePreset('edit')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'fID=' . $cInfo->address_format_id, 'address_format', 'new'));
				
				$infoBox->addButton($editButton)->addButton($deleteButton);

			}
			break;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
  <div style="text-align:right;"><?php
  	echo htmlBase::newElement('button')->usePreset('new')->setText(sysLanguage::get('TEXT_ADDRESS_FORMAT_NEW'))
  	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'fID')), null, 'new', 'SSL'))
  	->draw();
  ?></div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>