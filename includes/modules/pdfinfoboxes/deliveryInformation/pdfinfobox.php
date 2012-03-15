<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxDeliveryInformation extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('deliveryInformation');
	}

	public function show(){
			global $appExtension;
			$oID = isset($_GET['oID'])?$_GET['oID']:'';
			$boxWidgetProperties = $this->getWidgetProperties();
		    $htmlText = '';
			if(!empty($oID)){
				$Qorders = Doctrine_Query::create()
				->from('Orders o')
				->leftJoin('o.OrdersAddresses a')
				->where('orders_id=?', $oID)
				->andWhere('a.address_type = ?', 'delivery')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				$Customers = Doctrine_Core::getTable('Customers');

				$Customer = $Customers->find($Qorders[0]['customers_id']);

				if($boxWidgetProperties->company){
					$htmlText .= 'Company: '.$Qorders[0]['OrdersAddresses'][0]['entry_company'].'<br/>';
				}

				$entry_name = explode(' ', $Qorders[0]['OrdersAddresses'][0]['entry_name']);

				if($boxWidgetProperties->firstname){
					$htmlText .= 'First Name: '.(isset($entry_name[0])?$entry_name[0]:'').'<br/>';
				}
				if($boxWidgetProperties->lastname){
					$htmlText .= 'Last Name: '.(isset($entry_name[1])?$entry_name[1]:'').'<br/>';
				}
				if($boxWidgetProperties->name){
					$htmlText .= 'Name: '.$Qorders[0]['OrdersAddresses'][0]['entry_name'].'<br/>';
				}

				if($boxWidgetProperties->cif){
					$htmlText .= 'CIF: '.$Qorders[0]['OrdersAddresses'][0]['entry_cif'].'<br/>';
				}

				if($boxWidgetProperties->vat){
					$htmlText .= 'VAT: '.$Qorders[0]['OrdersAddresses'][0]['entry_vat'].'<br/>';
				}

				if($boxWidgetProperties->gender){
					$htmlText .= 'Gender: '.$Customer->customers_gender.'<br/>';
				}

				if($boxWidgetProperties->dob){
					$htmlText .= 'Date of Birth: '.$Customer->customers_dob.'<br/>';
				}

				if($boxWidgetProperties->fulladdress){

					$htmlText .= 'Address: '.$Qorders[0]['OrdersAddresses'][0]['entry_street_address'].'<br/>'.$Qorders[0]['OrdersAddresses'][0]['entry_city'].', '.$Qorders[0]['OrdersAddresses'][0]['entry_state'].' '.$Qorders[0]['OrdersAddresses'][0]['entry_postcode'].'<br/>';
				}

				if($boxWidgetProperties->street_address){
					$htmlText .= 'Street Address: '.$Qorders[0]['OrdersAddresses'][0]['entry_street_address'].'<br/>';
				}

				if($boxWidgetProperties->city){
					$htmlText .= 'City: '.$Qorders[0]['OrdersAddresses'][0]['entry_city'].'<br/>';
				}

				if($boxWidgetProperties->state){
					$htmlText .= 'State: '.$Qorders[0]['OrdersAddresses'][0]['entry_state'].'<br/>';
				}

				if($boxWidgetProperties->postcode){
					$htmlText .= 'Postcode: '.$Qorders[0]['OrdersAddresses'][0]['entry_postcode'].'<br/>';
				}

				if($boxWidgetProperties->country){
					$htmlText .= 'Country: '.$Qorders[0]['OrdersAddresses'][0]['entry_country'].'<br/>';
				}

				if($boxWidgetProperties->telephone){
					$htmlText .= 'Telephone: '.$Customer->customers_telephone.'<br/>';
				}

				if($boxWidgetProperties->email){
					$htmlText .= 'Email: '.$Customer->customers_email_address.'<br/>';
				}
			}


			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>