<?php
/*
	Late Fees Extension Version 1.0

	Sales Ingiter E-Commerce System v2
	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2011 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_pdfPrinter extends ExtensionBase {

	public function __construct(){
		parent::__construct('pdfPrinter');
	}

	public function init(){
		global $App, $appExtension, $Template;
		if ($this->isEnabled() === false) return;

		EventManager::attachEvents(array(
			'AdminOrderDefaultInfoBoxAddButton',
			'AdminOrderDetailsAddButton',
			'AdminOrderCreatorAddButton',
			'OrdersGridButtonsBeforeAdd',
			'OrderCreatorBeforeSendNewEmail',
			'OrderBeforeSendEmail'
			), null, $this);
	}

	public function OrderBeforeSendEmail(&$order, &$emailEvent, &$products_ordered, &$sendVariables){
		$oID = $order['orderID'];
		$file = '';
		if(sysConfig::get('EXTENSION_PDF_INVOICE_ATTACH_TO_ORDER_EMAIL') == 'True'){
			$file = (itw_catalog_app_link('appExt=pdfPrinter&suffix='.$oID.'&oID=' . $oID, 'generate_pdf', 'default','NONSSL'));
			$sendVariables['attach'] = 'temp/pdf/invoice_'.$oID.'.pdf';
		}elseif(sysConfig::get('EXTENSION_PDF_AGREEMENT_ATTACH_TO_ORDER_EMAIL') == 'True'){
			$file = (itw_catalog_app_link('appExt=pdfPrinter&type=a&suffix='.$oID.'&oID=' . $oID, 'generate_pdf', 'default','NONSSL'));
			$sendVariables['attach'] = 'temp/pdf/agreement_'.$oID.'.pdf';
		}
		if(!empty($file)){
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL, $file);
			curl_exec($ch);
			curl_close($ch);
		}

	}

	public function OrderCreatorBeforeSendNewEmail(&$order, &$emailEvent, &$products_ordered, &$sendVariables, $isEstimate = 0){
		$oID =  $order->orders_id;
		$file = '';
		$est = '';
		if($isEstimate == 1){
			$est = '&isEstimate=1';
		}
		if(sysConfig::get('EXTENSION_PDF_INVOICE_ATTACH_TO_ORDER_EMAIL') == 'True'){
			$file = (itw_catalog_app_link('appExt=pdfPrinter&suffix='.$oID.'&oID=' . $oID.$est, 'generate_pdf', 'default','NONSSL'));
			$sendVariables['attach'] = 'temp/pdf/invoice_'.$oID.'.pdf';
		}elseif(sysConfig::get('EXTENSION_PDF_AGREEMENT_ATTACH_TO_ORDER_EMAIL') == 'True'){
			$file = (itw_catalog_app_link('appExt=pdfPrinter&type=a&suffix='.$oID.'&oID=' . $oID.$est, 'generate_pdf', 'default','NONSSL'));
			$sendVariables['attach'] = 'temp/pdf/agreement_'.$oID.'.pdf';
		}

		if(!empty($file)){
			$ch=curl_init();
			curl_setopt($ch,CURLOPT_URL, $file);
			curl_exec($ch);
			curl_close($ch);
		}

	}
	
	public function AdminOrderDefaultInfoBoxAddButton(&$oInfo, &$infoBox){
		if(isset($_GET['isEstimate'])){
			$est = '&isEstimate=1';
		}else{
			$est = '';
		}
		$pdfinvoiceButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_INVOICE_PDF'))
		->setHref(itw_catalog_app_link('appExt=pdfPrinter&oID=' . $oInfo->orders_id.$est, 'generate_pdf', 'default'));

		$infoBox->addButton($pdfinvoiceButton);

	}

	public function OrdersGridButtonsBeforeAdd(&$gridButtons){
		$sClass = '';
		if(isset($_GET['isEstimate'])){
			$sClass = ' isEstimate';
		}
		$gridButtons[] = htmlBase::newElement('button')->setText('PDF Invoice')->addClass('pdfinvoiceButton'.$sClass)->disable();
	}

	public function AdminOrderDetailsAddButton($oID, &$infoBox){

		$pdfinvoiceButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_INVOICE_PDF'))
		->setHref(itw_catalog_app_link('appExt=pdfPrinter&oID=' . $oID.(isset($_GET['isEstimate'])?'&isEstimate=1':''), 'generate_pdf', 'default'));

		$infoBox->append($pdfinvoiceButton);
	}

	public function AdminOrderCreatorAddButton(&$infoBox){
		if(isset($_GET['oID'])){
			$pdfinvoiceButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_INVOICE_PDF'))
			->setHref(itw_catalog_app_link('appExt=pdfPrinter&oID=' . $_GET['oID'].(isset($_GET['isEstimate'])?'&isEstimate=1':''), 'generate_pdf', 'default'));

			$infoBox->append($pdfinvoiceButton);
		}
	}

}


function tep_cfg_get_layout_name($layoutName){
	$QLayouts = Doctrine_Query::create()
		->select('layout_id, layout_name, layout_type')
		->from('PDFTemplateManagerLayouts')
	    ->where('layout_id = ?', $layoutName)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if(isset($QLayouts[0])){
		return $QLayouts[0]['layout_name'];
	}else{
		return '';
	}
}

function tep_cfg_pull_down_layout_list($layoutName, $key = '') {
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
	$switcher = htmlBase::newElement('selectbox')
		->setName($name)
		->selectOptionByValue($layoutName);
	$QLayouts = Doctrine_Query::create()
		->select('layout_id, layout_name, layout_type')
		->from('PDFTemplateManagerLayouts')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($QLayouts){
		foreach($QLayouts as $lInfo){
			$switcher->addOption($lInfo['layout_id'], ucfirst($lInfo['layout_name']));
		}
	}
	return $switcher->draw();
}
