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
	geocoder = new GClientGeocoder();
	geocoder.getLocations($('textarea[name="inventory_center_address"]').val().replace(/(\r\n|\n|\r)/gm," "), geocodeResult);
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
					break;
				}
			}

			// Shorten array of markers and adjust counter
			markers.splice(n, 1);
			if(markers.length == 0) {
				count = 0;
			} else {
				count = markers[markers.length-1].content;
				drawOverlay();
			}
		});

		drawOverlay();
	}
}

function uploadManagerField($el){
	if ($el.attr('type') == 'file'){
		alert($el.attr('name') + ' cannot be an upload manager field because it is not a text input field.');
		return;
	}

	var isMulti = false;
	var autoUpload = true;
	var hasPreviewContainer = false;
	var $debugger = $('#' + $el.attr('id') + '_uploadDebugOutput').addClass('uploadDebugger');

	if ($el.attr('data-is_multi')){
		isMulti = ($el.attr('data-is_multi') == 'true');
	}

	if ($el.attr('data-has_preview')){
		hasPreviewContainer = true;
		var $previewContainer = $('#' + $el.attr('id') + '_previewContainer');
	}

	if ($el.attr('data-auto_upload')){
		autoUpload = ($el.attr('data-auto_upload') == 'true');
	}

	var fileType = $el.attr('data-file_type');
	$el.uploadify({
		uploader: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/uploadify.swf',
		script: 'application.php',
		method: 'GET',
		multi: isMulti,
		scriptData: {
			'app': thisApp,
			'appPage': thisAppPage,
			'action': 'uploadFile',
			'rType': 'ajax',
			'osCAdminID': sessionId,
			'fileType': fileType
		},
		cancelImg: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/images/cancel.png',
		auto: autoUpload,
		onError: function (event, queueID, fileObj, errorObj){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nError Uploading: " + errorObj.type + " :: " + errorObj.info);
			}else{
				alert("Error Uploading: " + errorObj.type + " :: " + errorObj.info);
			}
		},
		onAllComplete: function (){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nAll Uploads Completed!");
			}
		},
		onOpen: function (event, queueID, fileObj){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nBeginning Upload: " + fileObj.name);
			}
		},
		onProgress: function (event, queueID, fileObj, data){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nUpload Speed: " + data.speed + ' KB/ps');
			}
		},
		onComplete: function (event, queueID, fileObj, resp, data){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nUpload Completed\nJson Response: " + resp);
			}

			var theResp = eval('(' + resp + ')');

			if (theResp.success == true){
				if (isMulti){
					if ($el.val() != ''){
						$el.val($el.val() + ';' + theResp.image_name);
					}else{
						$el.val(theResp.image_name);
					}
				}else{
					$el.val(theResp.image_name);
				}

				if (hasPreviewContainer === true){
					var $deleteIcon = $('<a></a>')
						.addClass('ui-icon ui-icon-closethick');

					var $zoomIcon = $('<a></a>')
						.addClass('ui-icon ui-icon-zoomin');

					var $fancyBox = $('<a></a>')
						.addClass('fancyBox')
						.attr('href', theResp.image_path);

					var $img = $('<img></img>')
						.attr('src', theResp.thumb_path)
						.appendTo($fancyBox);

					var $thumbHolder = $('<div></div>')
						.css('text-align', 'center')
						.append($fancyBox)
						.append($zoomIcon)
						.append($deleteIcon);

					var $theBox = $('<div>').css({
						'float'  : 'left',
						'width'  : '80px',
						'height' : '100px',
						'border' : '1px solid #cccccc',
						'margin' : '.5em'
					}).append($thumbHolder);

					if (isMulti){
						$previewContainer.append($theBox);
					}else{
						$previewContainer.html($theBox);
					}
					$('.fancyBox', $theBox).trigger('loadBox');
				}
			}else{
				alert("Error Uploading: " + theResp.errorMsg);
			}
		}
	});
}


$(document).ready(function (){

	$('#tab_container').tabs();

	$('.makeFCK').each(function (){
			CKEDITOR.replace(this, {
				filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
			});
	});
	$('#countryDrop').change(function (){
		var $stateColumn = $('#stateCol');
		showAjaxLoader($stateColumn, 'icon', 'append');

		$.ajax({
			cache: true,
			url: js_app_link('appExt=inventoryCenters&app=manage&appPage=new&rType=ajax&action=getCountryZones'),
			data: 'cID=' + $(this).val() + '&zName='+$('#stateCol input').val(),
			dataType: 'html',
			success: function (data){
				removeAjaxLoader($stateColumn);
				$('#stateCol').html(data);
			}
		});
	});

	$('.uploadManagerInput').each(function (){
		uploadManagerField($(this));
	});

	$('#countryDrop').trigger('change');
	map = new GMap2(document.getElementById('googleMap'));
	map.setUIToDefault();
	GEvent.addListener(map, "click", leftClick);
	$('textarea[name="inventory_center_address"]').blur(function(){
		recenterMap();
	});
	recenterMap();

});