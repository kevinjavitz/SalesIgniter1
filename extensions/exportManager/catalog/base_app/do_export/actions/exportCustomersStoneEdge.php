<?php

function ProcessCustomerCount()
	{
		$response = '';
		$count = StoneEdgeCount('customers','');
		$response = "SetiResponse: itemcount=$count";
		return $response;
	}

	/**
	 * Download customers from the database to SEOM
	 *
	 * Method will process the posted data and create XML to be displayed.
	 *
	 * @access
	 * @return string XML response to display on the page for customers requested
	 */

	 function DownloadCustomers()
	{
		// set default queries
		//$query = "SELECT * FROM [|PREFIX|]customers ORDER by customerid ASC";
		$Qcustomers = Doctrine_Query::create()
	->from('Customers c')
	->leftJoin('c.CustomersInfo i')
	->leftJoin('c.AddressBook a on (c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id)')
	->leftJoin('a.Countries co')
	->orderBy('i.customers_info_date_account_created desc, c.customers_lastname, c.customers_firstname');
		//$queryCount = "SELECT COUNT(*) FROM [|PREFIX|]customers ORDER by customerid ASC";

		if (isset($_REQUEST['startnum']) && (int)$_REQUEST['startnum'] > 0 && isset($_REQUEST['batchsize']) && (int)$_REQUEST['batchsize'] > 0) {
			$start = (int)$_REQUEST['startnum'] - 1;
			$numresults = (int)$_REQUEST['batchsize'];

			if ($start >= 0 && $numresults > 0) {
				$Qcustomers->limit($numresults)->offset($start);
			}
		}

		$Qcustomers->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcustomers) {
			//we have results, so build the response header
			$xml = new SimpleXMLElement('<?xml version="1.0"?><SETICustomers />');
			$responseNode = $xml->addChild('Response');
			$responseNode->addChild('ResponseCode', 1);
			$responseNode->addChild('ResponseDescription', 'Success');



			//build the content
			foreach($Qcustomers as $customer) {
				$customerNode = $xml->addChild('Customer');
				$customerNode->addChild('WebID', $customer['customers_id']);
				$customerNode->addChild('UserName', $customer['customers_email_address']);
				$BillingAddress = $customerNode->addChild('BillAddr');
				$BillingAddress->addChild('FirstName', $customer['customers_firstname']);
				$BillingAddress->addChild('LastName', $customer['customers_lastname']);

				$BillingAddress->addChild('Phone', $customer['customers_telephone']);
				$BillingAddress->addChild('Email', $customer['customers_email_address']);

				$address = $customer['AddressBook'][0];
				$RealBillingAddress = $BillingAddress->addChild('Address');
				$RealBillingAddress->addChild('Addr1', $address['entry_street_address']);
				$RealBillingAddress->addChild('Addr2', '');
				$RealBillingAddress->addChild('City', $address['entry_city']);
				$RealBillingAddress->addChild('State', $address['entry_state']);
				$RealBillingAddress->addChild('Zip', $address['entry_postcode']);
				$RealBillingAddress->addChild('Country', $customer['Countries']['countries_iso_code_2']); //2 digit code

				$ShippingAddress = $customerNode->addChild('ShipAddr');
				$ShippingAddress->addChild('FirstName', $address['entry_firstname']);
				$ShippingAddress->addChild('LastName', $address['entry_lastname']);
				if (isset($address['entry_company']) && $address['entry_company'] != '') {
					$ShippingAddress->addChild('Company', $address['entry_company']);
				}
				$ShippingAddress->addChild('Phone', $customer['customers_telephone']);
				$ShippingAddress->addChild('Email', $customer['customers_email_address']);
				$RealShippingAddress = $BillingAddress->addChild('Address');
				$RealShippingAddress->addChild('Addr1', $address['entry_street_address']);
				$RealShippingAddress->addChild('Addr2', '');
				$RealShippingAddress->addChild('City', $address['entry_city']);
				$RealShippingAddress->addChild('State', $address['entry_state']);
				$RealShippingAddress->addChild('Zip', $address['entry_postcode']);
				$RealShippingAddress->addChild('Country', $customer['Countries']['countries_iso_code_2']); //2 digit code

				// are there any notes from the customer?
				/*if (isset($address['']) && $address[''] != '') {
					$CustomFields = $customerNode->addChild('CustomFields');
					$CustomField = $CustomFields->addChild('CustomField');
					$CustomField->addChild('FieldName', 'Customer Notes');
					$CustomField->addChild('FieldValue', substr($address[''],0,255));
				}*/
			}

		} else {
			//no results available
			$xml = new SimpleXMLElement('<?xml version="1.0"?><SETICustomers />');
			$responseNode = $xml->addChild('Response');
			$responseNode->addChild('ResponseCode', 2);
			$responseNode->addChild('ResponseDescription', 'Success');

		}

		return $xml->asXML();
	}
?>