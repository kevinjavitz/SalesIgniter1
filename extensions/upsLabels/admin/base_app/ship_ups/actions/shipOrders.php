<?php
//check order for tracking number

	if(isset($_GET['oID'])){
	$QOrders = Doctrine_Query::create()
	->from('Orders o')
	->andWhere('o.orders_id = ?', $_GET['oID'])
	->fetchOne();

	$xmlRequest1 = '<?xml version="1.0"?>
	<AccessRequest xml:lang="en-US">
	<AccessLicenseNumber>' . sysConfig::get('EXTENSION_UPSLABELS_ACCESS_KEY') . '</AccessLicenseNumber>
	<UserId>'.sysConfig::get('EXTENSION_UPSLABELS_USERNAME').'</UserId>
	<Password>'.sysConfig::get('EXTENSION_UPSLABELS_PASSWORD').'</Password>
	</AccessRequest>
	<?xml version="1.0"?>
	<ShipmentConfirmRequest xml:lang="en-US">
	<Request>
	<TransactionReference>
	<CustomerContext/>
	<XpciVersion/>
	</TransactionReference>
	<RequestAction>ShipConfirm</RequestAction>
	<RequestOption>validate</RequestOption>
	</Request>
	<LabelSpecification>
	<LabelPrintMethod>
	<Code>GIF</Code>
	<Description>gif file</Description>
	</LabelPrintMethod>
	<HTTPUserAgent>Mozilla/4.5</HTTPUserAgent>
	<LabelImageFormat>
	<Code>GIF</Code>
	<Description>gif</Description>
	</LabelImageFormat>
	</LabelSpecification>
	<Shipment>
	<Description/>
	<Shipper>
	<Name>'.sysConfig::get('EXTENSION_UPSLABELS_SHIPPER_NAME').'</Name>
	<PhoneNumber>'.sysConfig::get('EXTENSION_UPSLABELS_PHONE').'</PhoneNumber>
	<ShipperNumber>'.sysConfig::get('EXTENSION_UPSLABELS_ACCOUNT_NUMBER').'</ShipperNumber>
	<TaxIdentificationNumber/>
	<Address>
	<AddressLine1>'.sysConfig::get('EXTENSION_UPSLABELS_ADDRESS1').'</AddressLine1>
	<City>'.sysConfig::get('EXTENSION_UPSLABELS_CITY').'</City>
	<StateProvinceCode>'.sysConfig::get('EXTENSION_UPSLABELS_STATE').'</StateProvinceCode>
	<PostalCode>'.sysConfig::get('EXTENSION_UPSLABELS_POSTAL').'</PostalCode>
	<PostcodeExtendedLow/>
	<CountryCode>'.sysConfig::get('EXTENSION_UPSLABELS_COUNTRY').'</CountryCode>
	</Address>
	</Shipper>
	<ShipTo>';

	if(!empty($_POST['delivery_company'])){
		$xmlRequest1 .= '<CompanyName>'.$_POST['delivery_company'].'</CompanyName>';
	}else{
		$xmlRequest1 .= '<CompanyName>'.$_POST['delivery_name'].'</CompanyName>';
	}

	$xmlRequest1 .= '<AttentionName>'.$_POST['delivery_name'].'</AttentionName>';

	if(!empty($_POST['delivery_phone'])){
		$xmlRequest1 .= '<PhoneNumber>'.$_POST['delivery_phone'].'</PhoneNumber>';
	}
	$xmlRequest1 .= '<Address>
	<AddressLine1>'.$_POST['delivery_address'].'</AddressLine1>
	<City>'.$_POST['delivery_city'].'</City>
	<StateProvinceCode>'.$_POST['zone_code'].'</StateProvinceCode>
	<PostalCode>'.$_POST['delivery_postcode'].'</PostalCode>
	<CountryCode>'.  $_POST['country_code'].'</CountryCode>
	</Address>
	</ShipTo>
	<ShipFrom>
	<CompanyName>'.sysConfig::get('EXTENSION_UPSLABELS_COMPANY_NAME').'</CompanyName>
	<AttentionName>'.sysConfig::get('EXTENSION_UPSLABELS_SHIPPER_NAME').'</AttentionName>
	<PhoneNumber>'.sysConfig::get('EXTENSION_UPSLABELS_PHONE').'</PhoneNumber>
	<TaxIdentificationNumber/>
	<Address>
	<AddressLine1>'.sysConfig::get('EXTENSION_UPSLABELS_ADDRESS1').'</AddressLine1>
	<City>'.sysConfig::get('EXTENSION_UPSLABELS_CITY').'</City>
	<StateProvinceCode>'.sysConfig::get('EXTENSION_UPSLABELS_STATE').'</StateProvinceCode>
	<PostalCode>'.sysConfig::get('EXTENSION_UPSLABELS_POSTAL').'</PostalCode>
	<CountryCode>'.sysConfig::get('EXTENSION_UPSLABELS_COUNTRY').'</CountryCode>
	</Address>
	</ShipFrom>
	<PaymentInformation>
	<Prepaid>
	<BillShipper>
	<AccountNumber>'.sysConfig::get('EXTENSION_UPSLABELS_ACCOUNT_NUMBER').'</AccountNumber>
	</BillShipper>
	</Prepaid>
	</PaymentInformation>
	<Service>
	<Code>'.$_POST['service_type'].'</Code>
	<Description/>
	</Service>
	<Package>
	<PackagingType>
	<Code>'.$_POST['packaging_type'].'</Code>
	<Description/>
	</PackagingType>';
	if(!empty($_POST['dim_width'])){
	$xmlRequest1 .= '<Dimensions>
		<UnitOfMeasurement>
		<Code>'.sysConfig::get('EXTENSION_UPSLABELS_PACKAGE_SIZE').'</Code>
		</UnitOfMeasurement>
		<Length>'.(!empty($_POST['dim_length'])?$_POST['dim_length']:'0').'</Length>
		<Width>'.(!empty($_POST['dim_length'])?$_POST['dim_width']:'0').'</Width>
		<Height>'.(!empty($_POST['dim_length'])?$_POST['dim_height']:'0').'</Height>
		</Dimensions>';
	}
	$xmlRequest1 .= '
	<Description/>
	<PackageWeight>
	<UnitOfMeasurement>
	<Code>'.sysConfig::get('EXTENSION_UPSLABELS_WEIGHT').'</Code>
	</UnitOfMeasurement>
	<Weight>'.$_POST['package_weight'].'</Weight>
	</PackageWeight>
	<AdditionalHandling>0</AdditionalHandling>
	</Package>
	</Shipment>
	</ShipmentConfirmRequest>';

	if(sysConfig::get('EXTENSION_UPSLABELS_SERVER') == 'test'){
		$urlC = 'https://wwwcie.ups.com/ups.app/xml/ShipConfirm';
		$urlA = 'https://wwwcie.ups.com/ups.app/xml/ShipAccept';
	}else{
		$urlC = 'https://onlinetools.ups.com/ups.app/xml/ShipConfirm';
		$urlA = 'https://onlinetools.ups.com/ups.app/xml/ShipAccept';
		//https://onlinetools.ups.com/ups.app/xml/ShipConfirm

	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $urlC);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3600);

	$xmlResponse = curl_exec ($ch); // SHIP CONFORMATION RESPONSE

	$xml = $xmlResponse;

	$shipDigest = '';

	preg_match_all( "/\<ShipmentConfirmResponse\>(.*?)\<\/ShipmentConfirmResponse\>/s", $xml, $bookblocks );
	foreach( $bookblocks[1] as $block ){
		preg_match_all( "/\<ShipmentDigest\>(.*?)\<\/ShipmentDigest\>/", $block, $author ); // SHIPPING DIGEST
		preg_match_all( "/\<ErrorDescription\>(.*?)\<\/ErrorDescription\>/", $block, $error ); // SHIPPING DIGEST
		$shipDigest = $author[1][0];
	}

	if(isset($error[1][0])){
		$messageStack->addSession('pageStack',$error[1][0]);
	}


	   //add to order shipdigest
	//echo $shipDigest;
	$xmlRequest1 = '<?xml version="1.0"?>
	<AccessRequest xml:lang="en-US">
	<AccessLicenseNumber>' . sysConfig::get('EXTENSION_UPSLABELS_ACCESS_KEY') . '</AccessLicenseNumber>
	<UserId>'.sysConfig::get('EXTENSION_UPSLABELS_USERNAME').'</UserId>
	<Password>'.sysConfig::get('EXTENSION_UPSLABELS_PASSWORD').'</Password>
	</AccessRequest>
	<?xml version="1.0"?>
	<ShipmentAcceptRequest>
	<Request>
	<TransactionReference>
	<CustomerContext/>
	<XpciVersion/>
	</TransactionReference>
	<RequestAction>ShipAccept</RequestAction>
	</Request>
	<ShipmentDigest>'.$shipDigest.'</ShipmentDigest>
	</ShipmentAcceptRequest>';


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $urlA);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3600);

	$xmlResponse = curl_exec ($ch); // SHIP ACCEPT RESPONSE

	$xml = $xmlResponse;

	preg_match_all("/\<ShipmentAcceptResponse\>(.*?)\<\/ShipmentAcceptResponse\>/s",$xml, $bookblocks );
	$img = '';
	$trakingNumber = '';
	foreach( $bookblocks[1] as $block ){
		preg_match_all( "/\<GraphicImage\>(.*?)\<\/GraphicImage\>/", $block, $author ); // GET LABEL
		preg_match_all( "/\<TrackingNumber\>(.*?)\<\/TrackingNumber\>/", $block, $tracking ); // GET TRACKING NUMBER
		preg_match_all( "/\<ShipmentIdentificationNumber\>(.*?)\<\/ShipmentIdentificationNumber\>/", $block, $ident ); // GET TRACKING NUMBER
		preg_match_all( "/\<ErrorDescription\>(.*?)\<\/ErrorDescription\>/", $block, $error ); // SHIPPING DIGEST
		$img = $author[1][0];
		$trakingNumber = $tracking[1][0];
		$identificationNumber = $ident[1][0];
		$QOrders->ups_track_num = $trakingNumber;
		$QOrders->ups_ship_identification = $identificationNumber;
		$QOrders->save();
		$newOrdersStatusHistory = new OrdersStatusHistory();
		$newOrdersStatusHistory->orders_id = $_GET['oID'];
		$newOrdersStatusHistory->orders_status_id = sysConfig::get('ORDERS_STATUS_SHIPPED_ID');
		$newOrdersStatusHistory->customer_notified = 0;
		$newOrdersStatusHistory->comments = 'Order Shipped';
		$newOrdersStatusHistory->save();
	}
	if(isset($error[1][0])){
		$messageStack->addSession('pageStack',$error[1][0]);
	}
	//add to order shiptracking number, identification number

	$image=imagecreatefromgif( 'data://text/plain;base64,'.$img );
	$image=imagerotate($image, 270, 0); // Note: rotateImage turns our background to blue!
	$blue = imagecolorallocate($image, 0, 0, 255);
	imagecolortransparent($image, $blue);    // make blue transparent, so image isn't goofy
	imagepng($image, sysConfig::getDirFsCatalog().'extensions/upsLabels/tracking/'. $trakingNumber.".png");
	imagedestroy($image);


	$xmlRequest1 = '<?xml version="1.0"?>
	<AccessRequest xml:lang="en-US">
	<AccessLicenseNumber>' . sysConfig::get('EXTENSION_UPSLABELS_ACCESS_KEY') . '</AccessLicenseNumber>
	<UserId>'.sysConfig::get('EXTENSION_UPSLABELS_USERNAME').'</UserId>
	<Password>'.sysConfig::get('EXTENSION_UPSLABELS_PASSWORD').'</Password>
	</AccessRequest>
	<?xml version="1.0"?>
	<ShipmentConfirmRequest xml:lang="en-US">
	<Request>
	<TransactionReference>
	<CustomerContext/>
	<XpciVersion/>
	</TransactionReference>
	<RequestAction>ShipConfirm</RequestAction>
	<RequestOption>validate</RequestOption>
	</Request>
	<LabelSpecification>
	<LabelPrintMethod>
	<Code>GIF</Code>
	<Description>gif file</Description>
	</LabelPrintMethod>
	<HTTPUserAgent>Mozilla/4.5</HTTPUserAgent>
	<LabelImageFormat>
	<Code>GIF</Code>
	<Description>gif</Description>
	</LabelImageFormat>
	</LabelSpecification>
	<Shipment>
	<Description/>
	<ReturnService>
	<Code>9</Code>
	</ReturnService>
	<Shipper>
	<Name>'.sysConfig::get('EXTENSION_UPSLABELS_SHIPPER_NAME').'</Name>
	<PhoneNumber>'.sysConfig::get('EXTENSION_UPSLABELS_PHONE').'</PhoneNumber>
	<ShipperNumber>'.sysConfig::get('EXTENSION_UPSLABELS_ACCOUNT_NUMBER').'</ShipperNumber>
	<TaxIdentificationNumber/>
	<Address>
	<AddressLine1>'.sysConfig::get('EXTENSION_UPSLABELS_ADDRESS1').'</AddressLine1>
	<City>'.sysConfig::get('EXTENSION_UPSLABELS_CITY').'</City>
	<StateProvinceCode>'.sysConfig::get('EXTENSION_UPSLABELS_STATE').'</StateProvinceCode>
	<PostalCode>'.sysConfig::get('EXTENSION_UPSLABELS_POSTAL').'</PostalCode>
	<PostcodeExtendedLow/>
	<CountryCode>'.sysConfig::get('EXTENSION_UPSLABELS_COUNTRY').'</CountryCode>
	</Address>
	</Shipper>
	<ShipTo>
	<CompanyName>'.sysConfig::get('EXTENSION_UPSLABELS_COMPANY_NAME').'</CompanyName>
	<AttentionName>'.sysConfig::get('EXTENSION_UPSLABELS_SHIPPER_NAME').'</AttentionName>
	<PhoneNumber>'.sysConfig::get('EXTENSION_UPSLABELS_PHONE').'</PhoneNumber>
	<TaxIdentificationNumber/>
	<Address>
	<AddressLine1>'.sysConfig::get('EXTENSION_UPSLABELS_ADDRESS1').'</AddressLine1>
	<City>'.sysConfig::get('EXTENSION_UPSLABELS_CITY').'</City>
	<StateProvinceCode>'.sysConfig::get('EXTENSION_UPSLABELS_STATE').'</StateProvinceCode>
	<PostalCode>'.sysConfig::get('EXTENSION_UPSLABELS_POSTAL').'</PostalCode>
	<CountryCode>'.sysConfig::get('EXTENSION_UPSLABELS_COUNTRY').'</CountryCode>
	</Address>
	</ShipTo>
	<ShipFrom>';
	if(!empty($_POST['delivery_company'])){
		$xmlRequest1 .= '<CompanyName>'.$_POST['delivery_company'].'</CompanyName>';
	}else{
		$xmlRequest1 .= '<CompanyName>'.$_POST['delivery_name'].'</CompanyName>';
	}

	$xmlRequest1 .= '<AttentionName>'.$_POST['delivery_name'].'</AttentionName>';

	if(!empty($_POST['delivery_phone'])){
		$xmlRequest1 .= '<PhoneNumber>'.$_POST['delivery_phone'].'</PhoneNumber>';
	}
	$xmlRequest1 .= '<Address>
	<AddressLine1>'.$_POST['delivery_address'].'</AddressLine1>
	<City>'.$_POST['delivery_city'].'</City>
	<StateProvinceCode>'.$_POST['zone_code'].'</StateProvinceCode>
	<PostalCode>'.$_POST['delivery_postcode'].'</PostalCode>
	<CountryCode>'.  $_POST['country_code'].'</CountryCode>
	</Address>';
	$xmlRequest1 .= '</ShipFrom>
	<PaymentInformation>
	<Prepaid>
	<BillShipper>
	<AccountNumber>'.sysConfig::get('EXTENSION_UPSLABELS_ACCOUNT_NUMBER').'</AccountNumber>
	</BillShipper>
	</Prepaid>
	</PaymentInformation>
	<Service>
	<Code>'.$_POST['service_type'].'</Code>
	<Description>return</Description>
	</Service>
	<Package>
	<PackagingType>
	<Code>'.$_POST['packaging_type'].'</Code>
	<Description>return</Description>
	</PackagingType>';
	if(!empty($_POST['dim_width'])){
		$xmlRequest1 .= '<Dimensions>
		<UnitOfMeasurement>
		<Code>'.sysConfig::get('EXTENSION_UPSLABELS_PACKAGE_SIZE').'</Code>
		</UnitOfMeasurement>
		<Length>'.(!empty($_POST['dim_length'])?$_POST['dim_length']:'0').'</Length>
		<Width>'.(!empty($_POST['dim_length'])?$_POST['dim_width']:'0').'</Width>
		<Height>'.(!empty($_POST['dim_length'])?$_POST['dim_height']:'0').'</Height>
		</Dimensions>';
	}
	$xmlRequest1 .= '
	<Description>return</Description>
	<PackageWeight>
	<UnitOfMeasurement>
	<Code>'.sysConfig::get('EXTENSION_UPSLABELS_WEIGHT').'</Code>
	</UnitOfMeasurement>
	<Weight>'.$_POST['package_weight'].'</Weight>
	</PackageWeight>
	<AdditionalHandling>0</AdditionalHandling>
	</Package>
	</Shipment>
	</ShipmentConfirmRequest>';

	if(sysConfig::get('EXTENSION_UPSLABELS_SERVER') == 'test'){
		$urlC = 'https://wwwcie.ups.com/ups.app/xml/ShipConfirm';
		$urlA = 'https://wwwcie.ups.com/ups.app/xml/ShipAccept';
	}else{
		$urlC = 'https://onlinetools.ups.com/ups.app/xml/ShipConfirm';
		$urlA = 'https://onlinetools.ups.com/ups.app/xml/ShipAccept';
		//https://onlinetools.ups.com/ups.app/xml/ShipConfirm

	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $urlC);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3600);

	$xmlResponse = curl_exec ($ch); // SHIP CONFORMATION RESPONSE

	$xml = $xmlResponse;
	$shipDigest = '';

	preg_match_all( "/\<ShipmentConfirmResponse\>(.*?)\<\/ShipmentConfirmResponse\>/s", $xml, $bookblocks );
	foreach( $bookblocks[1] as $block ){
		preg_match_all( "/\<ShipmentDigest\>(.*?)\<\/ShipmentDigest\>/", $block, $author ); // SHIPPING DIGEST
		preg_match_all( "/\<ErrorDescription\>(.*?)\<\/ErrorDescription\>/", $block, $error ); // SHIPPING DIGEST
		$shipDigest = $author[1][0];
	}
	if(isset($error[1][0])){
		$messageStack->addSession('pageStack',$error[1][0]);
	}
	/*accept request*/

	//echo $shipDigest;
	$xmlRequest1 = '<?xml version="1.0"?>
	<AccessRequest xml:lang="en-US">
	<AccessLicenseNumber>' . sysConfig::get('EXTENSION_UPSLABELS_ACCESS_KEY') . '</AccessLicenseNumber>
	<UserId>'.sysConfig::get('EXTENSION_UPSLABELS_USERNAME').'</UserId>
	<Password>'.sysConfig::get('EXTENSION_UPSLABELS_PASSWORD').'</Password>
	</AccessRequest>
	<?xml version="1.0"?>
	<ShipmentAcceptRequest>
	<Request>
	<TransactionReference>
	<CustomerContext/>
	<XpciVersion/>
	</TransactionReference>
	<RequestAction>ShipAccept</RequestAction>

	</Request>
	<ShipmentDigest>'.$shipDigest.'</ShipmentDigest>
	</ShipmentAcceptRequest>';


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $urlA);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3600);

	$xmlResponse = curl_exec ($ch); // SHIP ACCEPT RESPONSE

	$xml = $xmlResponse;

	preg_match_all("/\<ShipmentAcceptResponse\>(.*?)\<\/ShipmentAcceptResponse\>/s",$xml, $bookblocks );
	$img = '';
	$trakingNumber = '';
	foreach( $bookblocks[1] as $block ){
		preg_match_all( "/\<GraphicImage\>(.*?)\<\/GraphicImage\>/", $block, $author ); // GET LABEL
		preg_match_all( "/\<TrackingNumber\>(.*?)\<\/TrackingNumber\>/", $block, $tracking ); // GET TRACKING NUMBER
		preg_match_all( "/\<ShipmentIdentificationNumber\>(.*?)\<\/ShipmentIdentificationNumber\>/", $block, $ident ); // GET TRACKING NUMBER
		preg_match_all( "/\<ErrorDescription\>(.*?)\<\/ErrorDescription\>/", $block, $error ); // SHIPPING DIGEST
		$img = $author[1][0];
		$trakingNumber = $tracking[1][0];
		$identificationNumber = $ident[1][0];
		$QOrders->ups_track_num2 = $trakingNumber;
		$QOrders->ups_return_ship_identification = $identificationNumber;
		$QOrders->save();
	}

	if(isset($error[1][0])){
		$messageStack->addSession('pageStack',$error[1][0]);
	}
	$image=imagecreatefromgif( 'data://text/plain;base64,'.$img );
	$image=imagerotate($image, 270, 0); // Note: rotateImage turns our background to blue!
	$blue = imagecolorallocate($image, 0, 0, 255);
	imagecolortransparent($image, $blue);    // make blue transparent, so image isn't goofy
	imagepng($image, sysConfig::getDirFsCatalog().'extensions/upsLabels/tracking/'. $trakingNumber.".png");
	imagedestroy($image);
	}


EventManager::attachActionResponse(itw_app_link('appExt=upsLabels&oID='.$_GET['oID'],'ship_ups','default'), 'redirect');
?>
