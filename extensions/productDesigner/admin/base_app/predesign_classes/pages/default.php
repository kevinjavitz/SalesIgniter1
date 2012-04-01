<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Qclasses = Doctrine_Query::create()
	->from('ProductDesignerPredesignClasses')
	->orderBy('class_name');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 0))
	->setQuery($Qclasses);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_CLASSES')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$designClasses = &$tableGrid->getResults();
	$infoBoxes = array();
	if (!empty($action)){
		$saveButton = htmlBase::newElement('button')->usePreset('save')->setType('submit');
		$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
		->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, null, 'SSL'));
	}
	
	$allGetParams = tep_get_all_get_params(array('action', 'cID'));
	if ($designClasses){
		$deleteButton = htmlBase::newElement('button')->usePreset('delete');
		$editButton = htmlBase::newElement('button')->usePreset('edit');
		foreach($designClasses as $classes){
			$classId = $classes['class_id'];
			if ((!isset($_GET['cID']) || $_GET['cID'] == $classId) && !isset($cInfo) && (substr($action, 0, 3) != 'new')){
				$cInfo = new objectInfo($classes);
			}

			$arrowIcon = htmlBase::newElement('icon')->setType('info')
			->setHref(itw_app_link($allGetParams . 'cID=' . $classId, null, null, 'SSL'));

			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'infobox_id' => $classId
				),
				'columns' => array(
					array('text' => $classes['class_name']),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));
			
			$editButton->setHref(itw_app_link($allGetParams . 'action=edit&cID=' . $classId, null, null, 'SSL'));
			$deleteButton->setHref(itw_app_link($allGetParams . 'action=delete_activity&cID=' . $classId, null, null, 'SSL'));
			$infoBox = htmlBase::newElement('infobox');
			$infoBox->setButtonBarLocation('top');
			$infoBox->setHeader('<b>' . $classes['class_name'] . '</b>');
			$infoBox->addButton($editButton)->addButton($deleteButton);
			
			$infoBoxes[$classId] = $infoBox->draw();
			unset($infoBox);
		}
	}

	if (!empty($action)){
		$infoBox = htmlBase::newElement('infobox');
		switch ($action) {
			case 'delete_class':
				$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE_CLASS') . '</b>');
				$infoBox->setForm(array(
					'name' => 'classes',
					'action' => itw_app_link(tep_get_all_get_params(array('action')) . 'action=deleteClassConfirm', null, null, 'SSL')
				));

				$deleteSubmitButton = htmlBase::newElement('button')->setType('submit')->usePreset('delete');

				$infoBox->addButton($deleteSubmitButton)->addButton($cancelButton);

				$infoBox->addContentRow(sysLanguage::get('TEXT_DELETE_CLASS_INTRO') . tep_draw_hidden_field('class_id', $cInfo->class_id));
				$infoBox->addContentRow('<b>' . $cInfo->class_name . '</b>');
				
				$infoBoxes[$cInfo->class_id] = $infoBox->draw();
				break;
			case 'new':
			case 'edit':
				if ($action == 'edit'){
					$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_EDIT_CLASS') . '</b>');
				}else{
					$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_NEW_CLASS') . '</b>');
				}
				$infoBox->setForm(array(
					'name' => 'classes',
					'action' => itw_app_link(tep_get_all_get_params(array('action')) . 'action=saveClass', null, null, 'SSL')
				));

				$infoBox->addButton($saveButton)->addButton($cancelButton);
			
				$className = htmlBase::newElement('input')->setName('class_name');
				if (isset($aInfo)){
					$className->val($cInfo->class_name);
				}
			
				$infoBox->addContentRow(sysLanguage::get('TEXT_EDIT_CLASS_NAME') . $className->draw());

				if (isset($cInfo)){
					$infoBoxId = $cInfo->class_id;
				}else{
					$infoBoxId = 'new';
				}
				$infoBoxes[$infoBoxId] = $infoBox->draw();
				break;
		}
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />

 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   </div>
  </div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td align="right" class="smallText"><?php
   	$newClassButton = htmlBase::newElement('button')->usePreset('install')->setText(sysLanguage::get('TEXT_BUTTON_NEW_CLASS'))
   	->setHref(itw_app_link($allGetParams . 'action=new', null, null, 'SSL'));

   	echo $newClassButton->draw();
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php
 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $cID => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $cID . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>