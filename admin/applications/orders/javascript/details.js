$(document).ready(function (){
	$('#tabs').tabs();
	var getVars = getUrlVars();
	$('#resendEmail').click(function(){
		$.ajax({
			url: js_app_link('app=orders&appPage=details&action=resendEmail'),
			cache: false,
			dataType: 'json',
			data: 'oID='+getVars['oID']+'&isEstimate='+getVars['isEstimate'],
			type: 'post',
			success: function (data){
				if(data.success == true){
					alert('Confirmation email resent');
				}
			}
		});
		return false;
	});

    $('.trackingButton').click(function(){
        var $Row = $(this).parentsUntil('tbody').last();

        if($Row.find('.trackingInput').val() ==  '') {
            alert('Please enter tracking number');
            return false;
        }
    });

    function encodeParam(name, value)
    {
        if (value)
        {
            if (!value.toString() == "")
            {
                return name + "=" + escape( value ) + "&";
            }
        }
        return "";
    }

    $('#popShipRush').live('click', function (){
        window.location.href = btnGenerateShipURL_onclick();
        return false;
    });

    function btnGenerateShipURL_onclick() {
        var url =
            "nntp://" +
                "ship?" +
                encodeParam("Carrier", "FedEx" ) +
                encodeParam("AutoPrintLabel", false ) +
                encodeParam("ShipmentXML", getShippmentXML() );

        return url;
    }

    function getShippmentXML()
    {
        return '<?xml version = "1.0"?>' +
            '<Request>' +
            '<ShipTransaction>' +
            '<Shipment>' +
            '<UPSServiceType>FedEx Ground></UPSServiceType>' +
            '<ShipmentChgType>PRE</ShipmentChgType>' +
            '<DeliveryAddress>' +
            '<Address>' +
            '<FirstName><![CDATA[' + $('input[name="inputName"]').val() + ']]></FirstName>' +
            '<Company><![CDATA[' + $('input[name="inputCompany"]').val() + ']]></Company>' +
            '<Address1><![CDATA[' + $('input[name="inputStreet"]').val() + ']]></Address1>' +
            '<Address2></Address2>' +
            '<City><![CDATA[' + $('input[name="inputCity"]').val() + ']]></City>' +
            '<State><![CDATA[' + $('input[name="inputZone"]').val() + ']]></State>' +
            '<PostalCode><![CDATA[' + $('input[name="inputPost"]').val() + ']]></PostalCode>' +
            '<Country><![CDATA[' + $('input[name="inputCountry"]').val() + ']]></Country>' +
            '</Address>' +
            '</DeliveryAddress>' +
            '<Package>' +
            '<PackagingType>02></PackagingType>' +
            '<PackageActualWeight></PackageActualWeight>' +
            '<InsuranceAmount><![CDATA[' + $('input[name="inputTotal"]').val() + ']]></InsuranceAmount>' +
            '<PackageReference1></PackageReference1>' +
            '<PkgLength>10</PkgLength>' +
            '<PkgWidth>10</PkgWidth>' +
            '<PkgHeight>5</PkgHeight>' +
            '</Package>' +
            '</Shipment>' +
            '</ShipTransaction>' +
            '</Request>';

    }
});