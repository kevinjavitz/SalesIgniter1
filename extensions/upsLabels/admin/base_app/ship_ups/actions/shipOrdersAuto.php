<?php
//check order for tracking number

	if(isset($_GET['oID'])){
	$QOrders = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('o.OrdersTotal ot')
	->leftJoin('o.OrdersAddresses a')
	->andWhere('o.orders_id = ?', $_GET['oID'])
	->fetchOne();

if(empty($QOrders->ups_track_num) && empty($QOrders->ups_track_num2)){
	$orderInfo['customers_telephone'] = $QOrders->customers_telephone;
	$orderInfo['customers_email_address'] = $QOrders->customers_email_address;
	foreach($QOrders->OrdersAddresses as $oaInfo){
		if($oaInfo['address_type'] == 'billing'){
			$orderInfo['customers_name'] = $oaInfo['entry_name'];
			$orderInfo['customers_company'] = $oaInfo['entry_company'];
			$orderInfo['customers_address'] = $oaInfo['entry_street_address'];
			$orderInfo['customers_city'] = $oaInfo['entry_city'];
			$orderInfo['customers_state'] = $oaInfo['entry_state'];
			$orderInfo['customers_country'] = $oaInfo['entry_country'];
			$orderInfo['customers_postcode'] = $oaInfo['entry_postcode'];
		}else if($oaInfo['address_type'] == 'delivery'){
			$orderInfo['delivery_name'] = $oaInfo['entry_name'];
			$orderInfo['delivery_company'] = $oaInfo['entry_company'];
			$orderInfo['delivery_address'] = $oaInfo['entry_street_address'];
			$orderInfo['delivery_city'] = $oaInfo['entry_city'];
			$orderInfo['delivery_state'] = $oaInfo['entry_state'];
			$orderInfo['delivery_country'] = $oaInfo['entry_country'];
			$orderInfo['delivery_postcode'] = $oaInfo['entry_postcode'];
		}
	}

	$delivery_country = $orderInfo['delivery_country'];

	$Qcountry = Doctrine_Query::create()
		->select('countries_id')
		->from('Countries')
		->where('countries_name = ?', $delivery_country)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$html = '';
	if(isset($Qcountry[0])){
		$orderInfo['delivery_country_code'] = $Qcountry[0]['countries_iso_code_2'];

		$Qcheck = Doctrine_Query::create()
		->select('zone_id, zone_code, zone_name')
		->from('Zones')
		->where('zone_country_id = ?', $Qcountry[0]['countries_id'])
		->andWhere('zone_name = ?', $orderInfo['delivery_state'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}

	if(isset($Qcheck[0])){
		$orderInfo['zone_code'] = $Qcheck[0]['zone_code'];
	}

	if (!empty($orderInfo['delivery_phone'])){
		$phone = $orderInfo['delivery_phone'];
	}else{
		if (!empty($orderInfo['customers_telephone'])){
			$phone = $orderInfo['customers_telephone'];
		}else{
			$phone = sysConfig::get('EXTENSION_UPSLABELS_PHONE');
		}
	}

	$order_qty_query = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select * from orders_products where orders_id = '" . $_GET['oID']. "'");
	$order_qty = 0;
	$order_weight = 0;
	$order_item_html = '';
	if (sizeof($order_qty_query) > 0) {
		foreach ($order_qty_query as $order_qtys){
			$order_item_html = $order_item_html . '          <tr>' . "\n" .
				'            <td class="smallText" align="left"><b>Product:</b><br>' . $order_qtys['products_quantity'] . ' * ' .
				$order_qtys['products_name'] . '</td>' . "\n" .
				'            <td class="smallText" align="left">';
			$order_qty = $order_qty + $order_qtys['products_quantity'];
			$products_id = $order_qtys['products_id'];
			$products_weight_query = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc("select * from products where products_id = '" . $products_id . "'");
			if (sizeof($products_weight_query) > 0) {
				$products_weights = $products_weight_query[0];
				$order_weight = $order_weight + ($order_qtys['products_quantity'] * ($products_weights['products_weight']));
				$item_weights[] = $products_weights['products_weight'];
			}
		}

		$order_weight_tar = $order_weight + (float)sysConfig::get('SHIPPING_BOX_WEIGHT');
		$order_weight_percent = ($order_weight * ((float)sysConfig::get('SHIPPING_BOX_PADDING') / 100 + 1));

		if ($order_weight_percent < $order_weight_tar) {
			$order_weight = $order_weight_tar;
		} else {
			$order_weight = $order_weight_percent;
		}

		$order_weight = round($order_weight,1);
		$order_weight = sprintf("%01.1f", $order_weight);
	}
	$package_num = $order_qty;


	$nrPackages = (int)($order_weight/sysConfig::get('SHIPPING_MAX_WEIGHT'));
	$packageWeight = sysConfig::get('SHIPPING_MAX_WEIGHT');
	if($nrPackages < 1){
		$nrPackages = 1;
		$packageWeight = $order_weight;
	}
	$sendTracker = array();
	$returnTracker = array();
	for($i = 0; $i < $nrPackages;$i++){


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

		if(!empty($orderInfo['delivery_company'])){
			$xmlRequest1 .= '<CompanyName>'.$orderInfo['delivery_company'].'</CompanyName>';
		}else{
			$xmlRequest1 .= '<CompanyName>'.$orderInfo['delivery_name'].'</CompanyName>';
		}

		$xmlRequest1 .= '<AttentionName>'.$orderInfo['delivery_name'].'</AttentionName>';

		if(!empty($phone)){
			$xmlRequest1 .= '<PhoneNumber>'.$phone.'</PhoneNumber>';
		}
		$xmlRequest1 .= '<Address>
		<AddressLine1>'.$orderInfo['delivery_address'].'</AddressLine1>
		<City>'.$orderInfo['delivery_city'].'</City>
		<StateProvinceCode>'.$orderInfo['zone_code'].'</StateProvinceCode>
		<PostalCode>'.$orderInfo['delivery_postcode'].'</PostalCode>
		<CountryCode>'.  $orderInfo['delivery_country_code'].'</CountryCode>
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
		<Code>'.sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_SERVICE_TYPE').'</Code>
		<Description/>
		</Service>';

			$xmlRequest1 .= '<Package>
			<PackagingType>
			<Code>'.sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_PACKAGING_TYPE').'</Code>
			<Description/>
			</PackagingType>';
			if(sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_WIDTH') != ''){
			$xmlRequest1 .= '<Dimensions>
				<UnitOfMeasurement>
				<Code>'.sysConfig::get('EXTENSION_UPSLABELS_PACKAGE_SIZE').'</Code>
				</UnitOfMeasurement>
				<Length>'.((sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_LENGTH') != '')?sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_LENGTH'):'0').'</Length>
				<Width>'.((sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_WIDTH') != '')?sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_WIDTH'):'0').'</Width>
				<Height>'.((sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_HEIGHT') != '')?sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_HEIGHT'):'0').'</Height>
				</Dimensions>';
			}
			$xmlRequest1 .= '
			<Description/>
			<PackageWeight>
			<UnitOfMeasurement>
			<Code>'.sysConfig::get('EXTENSION_UPSLABELS_WEIGHT').'</Code>
			</UnitOfMeasurement>
			<Weight>'.$packageWeight.'</Weight>
			</PackageWeight>
			<AdditionalHandling>0</AdditionalHandling>
			</Package>';

		$xmlRequest1 .= '</Shipment>
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
			$sendTracker[] = $trakingNumber;
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
		if(!empty($orderInfo['delivery_company'])){
			$xmlRequest1 .= '<CompanyName>'.$orderInfo['delivery_company'].'</CompanyName>';
		}else{
			$xmlRequest1 .= '<CompanyName>'.$orderInfo['delivery_name'].'</CompanyName>';
		}

		$xmlRequest1 .= '<AttentionName>'.$orderInfo['delivery_name'].'</AttentionName>';

		if(!empty($orderInfo['delivery_phone'])){
			$xmlRequest1 .= '<PhoneNumber>'.$orderInfo['delivery_phone'].'</PhoneNumber>';
		}
		$xmlRequest1 .= '<Address>
		<AddressLine1>'.$orderInfo['delivery_address'].'</AddressLine1>
		<City>'.$orderInfo['delivery_city'].'</City>
		<StateProvinceCode>'.$orderInfo['zone_code'].'</StateProvinceCode>
		<PostalCode>'.$orderInfo['delivery_postcode'].'</PostalCode>
		<CountryCode>'.  $orderInfo['delivery_country_code'].'</CountryCode>
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
		<Code>'.sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_SERVICE_TYPE').'</Code>
		<Description>return</Description>
		</Service>';

			$xmlRequest1 .= '<Package>
			<PackagingType>
			<Code>'.sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_PACKAGING_TYPE').'</Code>
			<Description>return</Description>
			</PackagingType>';
			if(sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_WIDTH') != ''){
				$xmlRequest1 .= '<Dimensions>
				<UnitOfMeasurement>
				<Code>'.sysConfig::get('EXTENSION_UPSLABELS_PACKAGE_SIZE').'</Code>
				</UnitOfMeasurement>
				<Length>'.((sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_LENGTH') != '')?sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_LENGTH'):'0').'</Length>
				<Width>'.((sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_WIDTH') != '')?sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_WIDTH'):'0').'</Width>
				<Height>'.((sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_HEIGHT') != '')?sysConfig::get('EXTENSION_UPSLABELS_DEFAULT_DIM_HEIGHT'):'0').'</Height>
				</Dimensions>';
			}

			$xmlRequest1 .= '
			<Description>return</Description>
			<PackageWeight>
			<UnitOfMeasurement>
			<Code>'.sysConfig::get('EXTENSION_UPSLABELS_WEIGHT').'</Code>
			</UnitOfMeasurement>
			<Weight>'.$packageWeight.'</Weight>
			</PackageWeight>
			<AdditionalHandling>0</AdditionalHandling>
			</Package>';

		$xmlRequest1 .= '</Shipment>
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
			$returnTracker[] = $trakingNumber;
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

	$QOrders->ups_track_num = implode(',', $sendTracker);
	$QOrders->ups_track_num2 = implode(',', $returnTracker);
	//$QOrders->ups_return_ship_identification = $identificationNumber;
	$QOrders->save();

}
}


EventManager::attachActionResponse(itw_app_link('appExt=upsLabels&oID='.$_GET['oID'],'ship_ups','default'), 'redirect');
?>
