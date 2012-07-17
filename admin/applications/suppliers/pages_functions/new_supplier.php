<?php
	function buildPrintLabelTable(){
		global $labelTypes;
		$printButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_PRINT'))->attr('id', 'printLabels');
		
		$labelTableContainer = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->css('width', '95%');
		
		$labelTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0);
		
		if (!isset($_GET['sID'])){
			$labelTable->disable(true);
		}
		
		$labelTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'colspan' => 2, 'text' => '<b>Print Labels</b>')
			)
		));
		
 		$labelTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Label Type:'),
				array('addCls' => 'main', 'text' => tep_draw_pull_down_menu('label_type', $labelTypes, '', 'id="labelsType"'))
			)
		));
		
		$labelTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'colspan' => 2, 'text' => $printButton)
			)
		));
		
		$labelTableContainer->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => ''),
				array('addCls' => 'main', 'align' => 'right', 'text' => $labelTable)
			)
		));
		return $labelTableContainer;
	}

?>