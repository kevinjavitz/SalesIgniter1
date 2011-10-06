<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxCustomerInformation extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('customerInformation');
	}

	public function show(){
			global $appExtension;
			if(!isset($_GET['cID'])){
				if(isset($_GET['oID'])){
					$Qorders = Doctrine_Query::create()
						->from('Orders o')
						->where('orders_id=?', $_GET['oID'])
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					$cID = $Qorders[0]['customers_id'];
				}else{
					$cID = '';
				}
			}else{
				$cID = $_GET['cID'];
			}
			$boxWidgetProperties = $this->getWidgetProperties();
		    $htmlText = '';
			if(!empty($cID)){
				$Customers = Doctrine_Core::getTable('Customers');

				$Customer = $Customers->find($cID);

				if($boxWidgetProperties->company){
					$htmlText .= 'Company: '.$Customer->AddressBook[0]->entry_company.'<br/>';
				}

				if($boxWidgetProperties->firstname){
					$htmlText .= 'First Name: '.$Customer->customers_firstname.'<br/>';
				}
				if($boxWidgetProperties->lastname){
					$htmlText .= 'Last Name: '.$Customer->customers_lastname.'<br/>';
				}
				if($boxWidgetProperties->name){
					$htmlText .= 'Name: '.$Customer->customers_firstname.' '.$Customer->customers_lastname.'<br/>';
				}

				if($boxWidgetProperties->cif){
					$htmlText .= 'CIF: '.$Customer->AddressBook[0]->entry_cif.'<br/>';
				}

				if($boxWidgetProperties->vat){
					$htmlText .= 'VAT: '.$Customer->AddressBook[0]->entry_vat.'<br/>';
				}

				if($boxWidgetProperties->gender){
					$htmlText .= 'Gender: '.$Customer->customers_gender.'<br/>';
				}

				if($boxWidgetProperties->dob){
					$htmlText .= 'Date of Birth: '.$Customer->customers_dob.'<br/>';
				}

				if($boxWidgetProperties->fulladdress){

					$htmlText .= 'Address: '.$Customer->AddressBook[0]->entry_street_address.'<br/>'.$Customer->AddressBook[0]->entry_city.', '.$Customer->AddressBook[0]->entry_state.' '.$Customer->AddressBook[0]->entry_postcode.'<br/>';
				}

				if($boxWidgetProperties->street_address){
					$htmlText .= 'Street Address: '.$Customer->AddressBook[0]->entry_street_address.'<br/>';
				}

				if($boxWidgetProperties->city){
					$htmlText .= 'City: '.$Customer->AddressBook[0]->entry_city.'<br/>';
				}

				if($boxWidgetProperties->state){
					$htmlText .= 'State: '.$Customer->AddressBook[0]->entry_state.'<br/>';
				}

				if($boxWidgetProperties->postcode){
					$htmlText .= 'Postcode: '.$Customer->AddressBook[0]->entry_postcode.'<br/>';
				}

				if($boxWidgetProperties->country){
					$htmlText .= 'Country: '.$Customer->AddressBook[0]->entry_country.'<br/>';
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