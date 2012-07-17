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

function recenterMap(){
	if($('textarea[name="google_zones_address"]').val()){
		geocoder = new GClientGeocoder();
		geocoder.getLocations($('textarea[name="google_zones_address"]').val(), geocodeResult);
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
	points = [];
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
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
	});

	$('.editButton').click(function () {
		var zoneId = $('.gridBodyRow.state-active').data('zone_id');
		gridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link('rType=ajax&app=zones&appPage=default&action=getActionWindow&window=new&zID=' + zoneId),
			onShow: function (ui) {
				var self = this;
                markers = [];
                points = [];
                count = 0;
                poly = false;
				map = new GMap2($(self).find('#googleMap')[0]);
				//     map.setCenter(new GLatLng(37.4419, -122.1419), 13);
				map.setUIToDefault();
				GEvent.addListener(map, "click", leftClick);

				$(self).find('.cancelButton').click(function () {
					$(self).effect('fade', {
						mode: 'hide'
					}, function () {
						$('.gridContainer').effect('fade', {
							mode: 'show'
						}, function () {
							$(self).remove();
						});
					});
				});

				$(self).find('.saveButton').click(function () {
					$.ajax({
						cache: false,
						url: js_app_link('rType=ajax&app=zones&appPage=default&action=save&zID=' + zoneId),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data) {
							js_redirect(js_app_link('app=zones&appPage=default'));
						}
					});
				});

				$(self).find('textarea[name="google_zones_address"]').blur(function(){
					recenterMap();
				});
				$(self).find('.makeFCK').each(function (){
					CKEDITOR.replace(this, {
						filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
					});
				});

				windowOnLoad.apply(this);
			}
		});
	});

	$('.newButton').click(function () {
		gridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link('rType=ajax&app=zones&appPage=default&action=getActionWindow&window=new'),
			onShow: function (ui) {
				var self = this;
                markers = [];
                points = [];
                count = 0;
                poly = false;
				map = new GMap2($(self).find('#googleMap')[0]);
				//     map.setCenter(new GLatLng(37.4419, -122.1419), 13);
				map.setUIToDefault();
				GEvent.addListener(map, "click", leftClick);

				$(self).find('.cancelButton').click(function () {
					$(self).effect('fade', {
						mode: 'hide'
					}, function () {
						$('.gridContainer').effect('fade', {
							mode: 'show'
						}, function () {
							$(self).remove();
						});
					});
				});

				$(self).find('.saveButton').click(function () {
					$.ajax({
						cache: false,
						url: js_app_link('rType=ajax&app=zones&appPage=default&action=save'),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data) {
							js_redirect(js_app_link('app=zones&appPage=default'));
						}
					});
				});

				$(self).find('textarea[name="google_zones_address"]').blur(function(){
					recenterMap();
				});
				$(self).find('.makeFCK').each(function (){
					CKEDITOR.replace(this, {
						filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
					});
				});

				windowOnLoad.apply(this);
			}
		});
	});

	$('.gridButtonBar').find('.deleteButton').click(function (){
		var zoneId = $('.gridBodyRow.state-active').data('zone_id');

		confirmDialog({
			confirmUrl: js_app_link('app=zones&appPage=default&action=deleteConfirm&zID=' + zoneId),
			title: 'Confirm Zone Delete',
			content: 'Are you sure you want to delete this zone?',
			errorMessage: 'This zone could not be deleted.',
			success: function () {
				$('.gridBodyRow.state-active').remove();
				$('.gridBodyRow').first().trigger('click');
			}
		});
	});
});