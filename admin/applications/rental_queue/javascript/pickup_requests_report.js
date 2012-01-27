var map;
var geocoder;
var marker;
var poly;
var count = 0;
var points = new Array();
var markers = new Array();
var icon_url ="http://labs.google.com/ridefinder/images/";
var tooltip;
var lineColor = "#0000af";
var fillColor = "#335599";
var lineWeight = 3;
var lineOpacity = .8;
var fillOpacity = .2;

function geocodeResult(response) {
	if (response.Status.code == '200') {
		place = response.Placemark[0];
		$('#addLon').html(place.Point.coordinates[0]);
		$('#addLat').html(place.Point.coordinates[1]);
		map.setCenter(new GLatLng(place.Point.coordinates[1], place.Point.coordinates[0]), 15);
	} else {
		alert("Geocode was not successful for the following reason: " + status);
	}
}

function recenterMap(address){
	if(address != ''){
		geocoder = new GClientGeocoder();
		geocoder.getLocations(address, geocodeResult);
	}else{
		map.setCenter(points[0], 15);
	}
}

function addIcon(icon) { // Add icon attributes
	icon.shadow= icon_url + "mm_20_shadow.png";
	icon.iconSize = new GSize(12, 20);
	icon.shadowSize = new GSize(22, 20);
	icon.iconAnchor = new GPoint(6, 20);
	icon.infoWindowAnchor = new GPoint(5, 1);
}

function drawOverlay(){
	if(poly) { map.removeOverlay(poly); }
	points.length = 0;
	for(i = 0; i < markers.length; i++) {
		var markerLatLng = markers[i].getLatLng();
		points.push(markerLatLng);
		$('input[name="poly_point[' + i + '][lat]"]').val(markerLatLng.lat());
		$('input[name="poly_point[' + i + '][lng]"]').val(markerLatLng.lng());
	}

	// Polygon mode
	points.push(markers[0].getLatLng());
	poly = new GPolygon(points, lineColor, lineWeight, lineOpacity, fillColor, fillOpacity);
	map.addOverlay(poly);
}

function leftClick(overlay, point) {
	if(point) {
		count++;

		if(count%2 != 0) {
			// Light blue marker icons
			var icon = new GIcon();
			icon.image = icon_url +"mm_20_blue.png";
			addIcon(icon);
		}else {
			// Purple marker icons
			var icon = new GIcon();
			icon.image = icon_url +"mm_20_purple.png";
			addIcon(icon);
		}

		// Make markers draggable
		var marker = new GMarker(point, {icon:icon, draggable:true, bouncy:false, dragCrossMove:true});
		map.addOverlay(marker);
		marker.content = count;
		markers.push(marker);

		$('#mapHolder').append('<input type="hidden" name="poly_point[' + (count-1) + '][lat]" value=""><input type="hidden" name="poly_point[' + (count-1) + '][lng]" value="">');

		// Drag listener
		GEvent.addListener(marker, "drag", function() {
			drawOverlay();
		});

		// Second click listener
		GEvent.addListener(marker, "click", function() {
			// Find out which marker to remove
			for(var n = 0; n < markers.length; n++) {
				if(markers[n] == marker) {
					map.removeOverlay(markers[n]);
					//remove polypoint
					$('input[name="poly_point['+n+'][lat]"]').remove();
					$('input[name="poly_point['+n+'][lng]"]').remove();
					for(var p = n;p < markers.length - 1; p++) {
						$('input[name="poly_point['+(p+1)+'][lat]"]').first().attr('name','poly_point['+p+'][lat]' );
						$('input[name="poly_point['+(p+1)+'][lng]"]').first().attr('name','poly_point['+p+'][lng]' );
					}
					break;
				}
			}

			// Shorten array of markers and adjust counter
			markers.splice(n, 1);
			if(markers.length == 0) {
				count = 0;
			} else {
				count--;
				drawOverlay();
			}
		});

		drawOverlay();
	}
}

$(document).ready(function (){


	$('#start_date').datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$('.address').click(function(){
		var address = $(this).attr('address');
		$( '<div id="dialog-mesage" title="Map Location"><div id="googleMap" style="width:550px;height:500px;"></div></div>' ).dialog({
			modal: true,
			width:600,
			height:550,
			close: function (e, ui){
				$(this).dialog('destroy').remove();
			},
			open: function (e, ui){
				var lines = jQuery.trim(address).split('<br />');
				map = new GMap2(document.getElementById('googleMap'));
				//     map.setCenter(new GLatLng(37.4419, -122.1419), 13);
				map.setUIToDefault();
				//GEvent.addListener(map, "click", leftClick);
				recenterMap(lines[1]+' '+lines[2]+' '+lines[3]);
			},
			buttons: {
				Ok: function() {
					$(this).dialog('destroy').remove();
				}
			}
		});
	});

});