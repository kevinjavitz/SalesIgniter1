<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxInvoiceAdmin extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('invoiceAdmin');
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			$htmlText = '';
			if(isset($_GET['oID'])){
		        $oID = $_GET['oID'];

		        $QOrders = Doctrine_Query::create()
			        ->from('Orders')
			        ->where('orders_id = ?', $oID)
			        ->fetchOne();

		        $QAdmin = Doctrine_Query::create()
			        ->from('Admin')
			        ->where('admin_id = ?', $QOrders->admin_id)
			        ->fetchOne();

				$adminData = '';
		        if($QAdmin){
			        $adminData = sysLanguage::get('SALES_REP'). $QAdmin->admin_firstname. ' '.$QAdmin->admin_lastname;
		        }

				$invDate = $adminData;

				switch($boxWidgetProperties->type){
					case 'top': $htmlText = $boxWidgetProperties->text.'<br/>'.$invDate;
								break;
					case 'bottom': $htmlText = $invDate.'<br/>'.$boxWidgetProperties->text;
								break;
					case 'left': $htmlText = $boxWidgetProperties->text.$invDate;
								break;
					case 'right': $htmlText = $invDate.$boxWidgetProperties->text;
								break;
				}
			}
			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>