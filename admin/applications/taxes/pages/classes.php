<?php
	$QtaxClasses = Doctrine_Query::create()
	->select('tax_class_id, tax_class_title, tax_class_description, last_modified, date_added')
	->from('TaxClass')
	->orderBy('tax_class_title');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setQuery($QtaxClasses);
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->usePreset('new')->addClass('newButton'),
		htmlBase::newElement('button')->usePreset('edit')->addClass('editButton')->disable(),
		htmlBase::newElement('button')->usePreset('delete')->addClass('deleteButton')->disable()
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_TAX_CLASSES')),
			array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
		)
	));
	
	$TaxClasses = &$tableGrid->getResults();
	if ($TaxClasses){
		foreach($TaxClasses as $cInfo){
			$classId = $cInfo['tax_class_id'];
			$classTitle = $cInfo['tax_class_title'];
			$classDescription = $cInfo['tax_class_description'];
			$lastModified = $cInfo['last_modified'];
			$dateAdded = $cInfo['date_added'];
			
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-class_id' => $classId
				),
				'columns' => array(
					array('text' => $classTitle),
					array('align' => 'center', 'text' => htmlBase::newElement('icon')->setType('info')->draw())
				)
			));
			
			$tableGrid->addBodyRow(array(
				'addCls' => 'gridInfoRow',
				'columns' => array(
					array('colspan' => 6, 'text' => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' . 
						'<tr>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_DATE_ADDED') . '</b></td>' . 
							'<td>' . tep_date_short($dateAdded) . '</td>' . 
							'<td><b>' . sysLanguage::get('TEXT_INFO_LAST_MODIFIED') . '</b></td>' . 
							'<td>' . tep_date_short($lastModified) . '</td>' . 
						'</tr>' . 
						'<tr>' . 
							'<td colspan="4"><b>' . sysLanguage::get('TEXT_INFO_CLASS_DESCRIPTION') . '</b></td>' . 
						'</tr>' . 
						'<tr>' . 
							'<td colspan="4">' . $classDescription . '</td>' . 
						'</tr>' . 
					'</table>')
				)
			));
		}
	}
?>    
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_CLASSES');
?></div>
<br />
<div class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;margin-left:5px;">
	<div style="margin:5px;"><?php echo $tableGrid->draw();?></div>
</div>