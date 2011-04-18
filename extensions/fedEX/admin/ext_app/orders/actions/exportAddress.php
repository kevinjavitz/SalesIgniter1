<?php
 if (isset($_POST['selectedOrder']) && is_array($_POST['selectedOrder'])){
    $dataExport = new dataExport();
	$dataExport->setHeaders(array(
		'Nickname',
		'FullName',
        'FirstName',
        'LastName',
        'Title',
        'Company',
        'Department',
        'AddressOne',
        'AddressTwo',
        'City',
        'State',
        'Zip',
        'PhoneNumber',
        'ExtensionNumber',
        'FAXNumber',
        'PagerNumber',
        'MobilePhoneNumber',
        'CountryCode',
		'EmailAddress',
        'VerifiedFlag',
        'AcceptedFlag',
        'ValidFlag',
        'ResidentialFlag',
		'CustomsIDEIN',
        'ReferenceDescription',
        'ServiceTypeCode',
        'PackageTypeCode',
        'CollectionMethodCode',
        'BillCode',
        'BillAccountNumber',
        'DutyBillCode',
        'DutyBillAccountNumber',
        'CurrencyTypeCode',
        'InsightIDNumber',
        'GroundReferenceDescription',
        'ShipmentNotificationRecipientEmail',
        'RecipientEmailLanguage',
        'RecipientEmailShipmentnotification',
        'RecipientEmailExceptionnotification',
        'RecipientEmailDeliverynotification',
        'PartnerTypeCodes',
        'NetReturnBillAccountNumber',
        'CustomsIDTypeCode',
        'AddressTypeCode',
        'ShipmentNotificationSenderEmail',
        'SenderEmailLanguage',
        'SenderEmailShipmentnotification',
        'SenderEmailExceptionnotification',
        'SenderEmailDeliverynotification',
        'RecipientEmailPickupnotification',
        'SenderEmailPickupnotification',
        'OpCoTypeCd',
        'BrokerAccounttID',
        'BrokerTaxID',
        'DefaultBrokerID'
	));

	EventManager::notify('FedexExportQueryFileLayoutHeader', &$dataExport);

	$QfileLayout = Doctrine_Query::create()
	->from('Orders o')
    ->leftJoin('o.OrdersProducts op')
	->leftJoin('o.OrdersTotal ot')
	->leftJoin('o.OrdersAddresses a')
	->leftJoin('o.OrdersStatus s')
	->where('s.language_id = ?', Session::get('languages_id'))
	->orderBy('o.date_purchased desc')
    ->whereIn('o.orders_id', $_POST['selectedOrder']);

	EventManager::notify('FedexExportQueryBeforeExecute', &$QfileLayout);

	$Result = $QfileLayout->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$dataRows = array();
	foreach($Result as $oInfo){

		$pInfo['v_orders_id'] = $oInfo['orders_id'];
        $Qcustomer = Doctrine_Query::create()
                    ->from('Customers')
                    ->where('customers_id = ?', $oInfo['customers_id'])
                    ->fetchOne();
        $pInfo['v_orders_customers_name'] = $Qcustomer->customers_firstname . ' ' . $Qcustomer->customers_lastname;
        $pInfo['v_orders_customers_email_address'] = $Qcustomer->customers_email_address;
        $pInfo['v_orders_customers_telephone'] = $Qcustomer->customers_telephone;

        foreach($oInfo['OrdersAddresses'] as $oaInfo){
            if($oaInfo['address_type'] == 'billing'){
		        $pInfo['v_orders_billing_name'] = $oaInfo['entry_name'];
                $pInfo['v_orders_billing_company'] = $oaInfo['entry_company'];
                $pInfo['v_orders_billing_address'] = $oaInfo['entry_street_address'];
                $pInfo['v_orders_billing_city'] = $oaInfo['entry_city'];
                $pInfo['v_orders_billing_state'] = $oaInfo['entry_state'];
                $pInfo['v_orders_billing_country'] = $oaInfo['entry_country'];
                $pInfo['v_orders_billing_postcode'] = $oaInfo['entry_postcode'];
            }else if($oaInfo['address_type'] == 'shipping'){
		        $pInfo['v_orders_shipping_name'] = $oaInfo['entry_name'];
                $pInfo['v_orders_shipping_company'] = $oaInfo['entry_company'];
                $pInfo['v_orders_shipping_address'] = $oInfo['entry_street_address'];
                $pInfo['v_orders_shipping_city'] = $oaInfo['entry_city'];
                $pInfo['v_orders_shipping_state'] = $oaInfo['entry_state'];
                $pInfo['v_orders_shipping_country'] = $oaInfo['entry_country'];
                $pInfo['v_orders_shipping_postcode'] = $oaInfo['entry_postcode'];
            }
        }

		EventManager::notify('FedexExportBeforeFileLineCommit', &$pInfo, &$oInfo);

		$dataRows[] = $pInfo;
	}

	$dataExport->setExportData($dataRows);
	$dataExport->output(true);
}
?>