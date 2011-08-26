<?php

/*
 <?xml version="1.0" ?>
<AccessRequest xml:lang='en-US'>
<AccessLicenseNumber>YOURACCESSLICENSENUMBER</AccessLicenseNumber>
<UserId>YOURUSERID</UserId>
<Password>YOURPASSWORD</Password>
</AccessRequest>
<?xml version="1.0" encoding="UTF-8" ?>
<VoidShipmentRequest>
<Request>
<TransactionReference>
<CustomerContext>Customer Transaction ID</CustomerContext>
<XpciVersion>1.0001</XpciVersion>
</TransactionReference>
<RequestAction>Void</RequestAction>
<RequestOption />
</Request>
<ExpandedVoidShipment>
<ShipmentIdentificationNumber>
1Z12345E2318693258
</ShipmentIdentificationNumber>
<TrackingNumber>1Z12345E0390819985</TrackingNumber>
<TrackingNumber>1Z12345E0193078536</TrackingNumber>
</ExpandedVoidShipment>
</VoidShipmentRequest>

 * */

	EventManager::attachActionResponse(itw_app_link(null,'orders','default'), 'redirect');

?>