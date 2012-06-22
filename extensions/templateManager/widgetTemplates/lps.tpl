<script type="text/javascript">
	var map;
	var geocoder;
	var markers = [];
	var html = [];
	var places = [];
	var i = 0;
	var i1 = 0;
	var lpArr = new Array();
	var inv = new Array();
	var inv2 = new Object();

	function loadAPI()
	{
		var script = document.createElement("script");
		script.src = "http://www.google.com/jsapi?key=&callback=loadMaps";
		script.type = "text/javascript";
		document.getElementsByTagName("head")[0].appendChild(script);
	}

	function loadMaps()
	{
		//AJAX API is loaded successfully. Now lets load the maps api
		google.load("maps", "2", {"callback" : mapLoaded});
	}

	function mapLoaded()
	{
		//here you can be sure that maps api has loaded
		//and you can now proceed to render the map on page
		if (GBrowserIsCompatible())
		{
			initialize();
			i1 = 0;
			var inv = [];
		<?php
		$Inventory_centers = Doctrine_Query::create()
				->from('InventoryCentersLaunchPoints')
				->where('inventory_center_id > ?', '0')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if($Inventory_centers){
			$i = 0;
			foreach($Inventory_centers as $inv){

					$invent = stripslashes (htmlspecialchars ($inv['lp_name']));
					$invent = str_replace("\r\n", " ", $invent);

					$pointC = explode(',',$inv['lp_position']);
					$inventX =trim($pointC[0]);
					$inventY =trim($pointC[1]);
					$invent_det = stripslashes ($inv['lp_desc']);
					$invent_det = str_replace("\r\n", " ", $invent_det);

					$invent_name = stripslashes ($inv['lp_name']);
					$invent_name = str_replace("\r\n", " ", $invent_name);

					$invent_ind = stripslashes ($inv['lp_id']);
					$invent_color = str_replace('#','',stripslashes ($inv['lp_marker_color']));

					if($inventX != 'false' && $inventY != 'false'){
						echo '	inv.push({' . "\n" .
							'		name: \'' . $invent_name . '<br/><a style="text-decoration:underline;" href="' . itw_app_link('appExt=inventoryCenters&lp_id=' . $inv['lp_id'], 'show_launch_point', 'default') . '">More Info</a>\',' . "\n" .
							'		addressX: \'' . $inventX . '\',' . "\n" .
							'		addressY: \'' . $inventY . '\',' . "\n" .
							'		ind: \'' . $invent_ind . '\',' . "\n" .
							'		color: \'' . $invent_color . '\',' . "\n" .
							'		nameo: \'' . $invent_name . '\',' . "\n" .
							'		func: function(response){' . "\n" .
							'		}' . "\n" .
							'	});' . "\n";
					}
					$i++;
			}
		}
		?>
			$.each(inv, function (i, el){
				showLocation2(this.addressX, this.addressY, this.name, this.nameo, this.color);
			});

			$("#message").appendTo(map.getPane(G_MAP_FLOAT_SHADOW_PANE));

		}
	}

	function initialize() {
		map = new GMap2(document.getElementById("gMap"));
		if(typeof lpArr[0] != 'undefined'){
			map.setCenter(new GLatLng(lpArr[0], lpArr[1]), 14);
		}else{
			map.setCenter(new GLatLng(41.551756, -70.558548), 4);
		}
		map.addControl(new GLargeMapControl());
		geocoder = new GClientGeocoder();


	}

	function displayPoint(marker, msg,i){
		var moveEnd = GEvent.addListener(map, "moveend", function(){
			marker.openInfoWindowHtml(msg);
			GEvent.removeListener(moveEnd);
		});
		/*$("#pickupz option[value="+i+"]").removeAttr("selected");
		$("#pickupz option[value="+i+"]").attr("selected","selected");
		$("#pickupz").trigger('change');*/
		//ajax call to set before  inv_id =i
		$ellem = $('#mainppr').find('form');
		/*if($('#pprHome').find('form').find('input[name="pickup"]').size() == 0){
			$ellem.append('<input type="hidden" name="pickup" value="'+i+'"/>');
		}else{
			$('#pprHome').find('form').find('input[name="pickup"]').replaceWith('<input type="hidden" name="pickup" value="'+i+'"/>');
		}*/
		var sendValues = $ellem.serialize();

		//sendValues = sendValues.replace(/&pickup=.*&/i,'&pickup='+i+'&');
		sendValues =  addParameter('url?'+sendValues,'pickup',i);
		sendValues = sendValues.replace('url?','');
		showAjaxLoader($ellem,'xlarge');
		$.ajax({
			type: "post",
			url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=setBefore'),
			data: sendValues+"&rType=ajax",
			success: function(data) {
				$ellem.find('.invCenter').replaceWith(data.data);
			<?php if(sysConfig::get('EXTENSION_INVENTORY_CENTERS_USE_LP') == 'True'){?>
				if(data.excluded_dates != 'null'){
					disabledDays = data.excluded_dates;
					//$(".picker").datepicker("setDate",$(".picker").datepicker("getDate"));
					$('.picker').datepicker( "refresh" );
				}
				var selectedValPickup = $('.pickupz option:selected').val();
				$('.pickupz').html('');
				for (i=0;i<data.lps.length;i++){
					var option = $('<option/>');
					if(data.lps[i] == selectedValPickup){
						option.attr('selected','selected');
					}

					option.val(data.lps[i]).html(data.lps[i]).appendTo('.pickupz');
				}

				if(data.start_time > 0){
					var selectedValhStart = $('.hstart option:selected').val();
					var selectedValhEnd = $('.hend option:selected').val();
					$('.hstart').html('');
					$('.hend').html('');
					var endTime = <?php echo sysConfig::get('EXTENSION_PAY_PER_RENTALS_END_TIME');?>;
					for (i=data.start_time;i<endTime;i++){
						var timeVal = (i % 12) + (i<12?':00 AM':':00 PM');
						if(i == 12){
							timeVal = '12:00 PM';
						}
						var option1 = $('<option/>').val(i).html(timeVal);
						var option2 = $('<option/>').val(i).html(timeVal);
						if(i == selectedValhStart){
							option1.attr('selected','selected');
						}
						if(i == selectedValhEnd){
							option2.attr('selected','selected');
						}
						option1.appendTo('.hstart');
						option2.appendTo('.hend');
					}

				}
				$('#mainppr .mylp1').attr('href',"#");
				$('#mainppr .mylp1').click(function(){
					if(selectedValPickup != 'No Destination Available'){
						link = js_app_link('appExt=inventoryCenters&app=show_launch_point&appPage=default&dialog=true&lp_name='+selectedValPickup);
						popupWindow(link,'400','300');
					}
					return false;
				});

				<?php }?>
				$('.changer').addClass('round_sb');
				$('.changer').sb({useTie:true, fixedWidth: true });
				$ellem.find('.rentbbut').button();
				$ellem.find('.rentbbut').click(function(){
					$ellem.submit();
					return false;
				});
				$ellem.trigger('EventAfterChanger');


				hideAjaxLoader($ellem);
			}
		});
		map.panTo(marker.getLatLng());
	}


	function showLocation(address, msg,i,color) {
		geocoder.getLocations(address, function(response){
			if (!response || response.Status.code != 200){
			} else {
				place = response.Placemark[0];

				point = new GLatLng(place.Point.coordinates[1],
				place.Point.coordinates[0]);

				/*var baseIcon = new GIcon();
				baseIcon.iconSize=new GSize(32,32);
				baseIcon.shadowSize=new GSize(56,32);
				baseIcon.iconAnchor=new GPoint(16,32);
				baseIcon.infoWindowAnchor=new GPoint(16,0);
				var thisicon = new GIcon(baseIcon, "http://itouchmap.com/i/blue-dot.png", null, "http://itouchmap.com/i/msmarker.shadow.png");

				var marker = new GMarker(point, thisicon);*/

				var cafeIcon = new GIcon();
				cafeIcon.image = "http://chart.apis.google.com/chart?chst=d_map_pin_icon&chld=cafe|"+color;
				cafeIcon.shadow = "http://chart.apis.google.com/chart?chst=d_map_pin_shadow";
				cafeIcon.iconSize = new GSize(12, 20);
				cafeIcon.shadowSize = new GSize(22, 20);
				cafeIcon.iconAnchor = new GPoint(6, 20);
				cafeIcon.infoWindowAnchor = new GPoint(5, 1);
				// Set up our GMarkerOptions object literal
				markerOptions = { icon:cafeIcon };
				var marker = new GMarker(point, markerOptions);

				GEvent.addListener(marker, "click", function(){
					displayPoint(this, msg,i);
				});

				map.addOverlay(marker);
			}
		});
	}

	function showLocation2(addressx,addressy, msg,i,color) {
				point = new GLatLng(addressx, addressy);


		var cafeIcon = new GIcon();
		cafeIcon.image = "http://chart.apis.google.com/chart?chst=d_map_pin_icon&chld=camping|"+color;
		cafeIcon.shadow = "http://chart.apis.google.com/chart?chst=d_map_pin_shadow";
		cafeIcon.iconSize = new GSize(21, 34);
		//cafeIcon.shadowSize = new GSize(56, 32);
		cafeIcon.iconAnchor = new GPoint(21, 34);
		cafeIcon.infoWindowAnchor = new GPoint(5, 1);
		// Set up our GMarkerOptions object literal
		markerOptions = { icon:cafeIcon };
		var marker = new GMarker(point, markerOptions);

				GEvent.addListener(marker, "click", function(){
					displayPoint(this, msg,i);
				});

				map.addOverlay(marker);
	}
</script>

<script type="text/javascript">
$(document).ready(function (){
	loadAPI();
});
</script>

 <div id="gMap"></div>