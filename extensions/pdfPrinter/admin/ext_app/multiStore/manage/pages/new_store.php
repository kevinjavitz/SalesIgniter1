<?php
/*
	Product Designer Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class pdfPrinter_admin_multiStore_manage_new_store extends Extension_pdfPrinter {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'NewStoreAddTab'
		), null, $this);
	}
	
	public function NewStoreAddTab(&$tabsObj){
		if(isset($_GET['sID'])){
			$QInvLayouts = Doctrine_Query::create()
				->select('invoice_layout, estimate_layout')
				->from('Stores')
				->where('stores_id=?', $_GET['sID'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$invLayout = $QInvLayouts[0]['invoice_layout'];
			$estLayout = $QInvLayouts[0]['estimate_layout'];

		}else{
			$invLayout = 0;
			$estLayout = 0;
		}
		$switcher = htmlBase::newElement('selectbox')
			->setName('invoice_layout')
			->selectOptionByValue($invLayout);
		$QLayouts = Doctrine_Query::create()
			->select('layout_id, layout_name, layout_type')
			->from('PDFTemplateManagerLayouts')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($QLayouts){
			foreach($QLayouts as $lInfo){
				$switcher->addOption($lInfo['layout_id'], ucfirst($lInfo['layout_name']));
			}
		}

		$switcherEstimate = htmlBase::newElement('selectbox')
			->setName('estimate_layout')
			->selectOptionByValue($estLayout);
		if ($QLayouts){
			foreach($QLayouts as $lInfo){
				$switcherEstimate->addOption($lInfo['layout_id'], ucfirst($lInfo['layout_name']));
			}
		}

		$mainTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'colspan' => '2', 'text' => '<b><u></u></b>')
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Invoice Layout:'),
				array('addCls' => 'main', 'text' => $switcher->draw()),
			)
		));

		$mainTable->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => 'Estimate Layout:'),
					array('addCls' => 'main', 'text' => $switcherEstimate->draw()),
				)
			));

		
		$tabsObj->addTabHeader('tab_' . $this->getExtensionKey(), array('text' => sysLanguage::get('TAB_PDF_PRINTER')))
		->addTabPage('tab_' . $this->getExtensionKey(), array('text' => $mainTable->draw()));
	}
}
?>