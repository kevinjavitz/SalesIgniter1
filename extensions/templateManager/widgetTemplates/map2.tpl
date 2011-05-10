<script type="text/javascript" src="http://maps.google.com/maps?file=api&v=2&sensor=false&key=<?php echo Session::get('google_key');?>"></script>
<script type="text/javascript">
	var map;
	var geocoder;
	var markers = [];
	var html = [];
	var places = [];
	var i = 0;
	var i1 = 0;
	var inv = new Array();
	var inv2 = new Object();
	function initialize() {
		map = new GMap2(document.getElementById("gMap"));
		map.setCenter(new GLatLng(34, 0), 1);
		map.addControl(new GLargeMapControl());
		geocoder = new GClientGeocoder();


	}

	function displayPoint(marker, msg,i){

		var moveEnd = GEvent.addListener(map, "moveend", function(){
			marker.openInfoWindowHtml(msg);
			GEvent.removeListener(moveEnd);
		});

		//var markerOffset = map.fromLatLngToDivPixel(marker.getLatLng());

		$(".myf option[value="+i+"]").attr("selected","selected");

		map.panTo(marker.getLatLng());
	}


	function showLocation(address, msg,i) {
		geocoder.getLocations(address, function(response){
			if (!response || response.Status.code != 200){
			} else {
				place = response.Placemark[0];

				point = new GLatLng(place.Point.coordinates[1],
				place.Point.coordinates[0]);

				var baseIcon = new GIcon();
				baseIcon.iconSize=new GSize(32,32);
				baseIcon.shadowSize=new GSize(56,32);
				baseIcon.iconAnchor=new GPoint(16,32);
				baseIcon.infoWindowAnchor=new GPoint(16,0);
				var thisicon = new GIcon(baseIcon, "http://itouchmap.com/i/blue-dot.png", null, "http://itouchmap.com/i/msmarker.shadow.png");

				var marker = new GMarker(point, thisicon);

				GEvent.addListener(marker, "click", function(){
					displayPoint(this, msg,i);
				});

				map.addOverlay(marker);
			}
		});
		//alert(html[0]+" "+html[1]);
	}

	function showLocation2(addressx,addressy, msg,i) {



				point = new GLatLng(addressx,
				addressy);

				var baseIcon = new GIcon();
				baseIcon.iconSize=new GSize(32,32);
				baseIcon.shadowSize=new GSize(56,32);
				baseIcon.iconAnchor=new GPoint(16,32);
				baseIcon.infoWindowAnchor=new GPoint(16,0);
				var thisicon = new GIcon(baseIcon, "http://itouchmap.com/i/blue-dot.png", null, "http://itouchmap.com/i/msmarker.shadow.png");

				var marker = new GMarker(point, thisicon);

				GEvent.addListener(marker, "click", function(){
					displayPoint(this, msg,i);
				});

				map.addOverlay(marker);
		//alert(html[0]+" "+html[1]);
	}
</script>
<script type="text/javascript">
$(document).ready(function (){
	initialize();
	i1 = 0;
	var inv = [];
<?php
		$Inventory_centers = Doctrine_Query::create()
		->from('ProductsInventoryCenters')
		->where('inventory_center_stores=?', Session::get('current_store_id'))
		->execute(array(), Doctrine::HYDRATE_ARRAY);
		if($Inventory_centers){
			$i = 0;
			foreach($Inventory_centers as $inv){
				$storeArr = explode(';', $inv['inventory_center_stores']);

				if(in_array(Session::get('current_store_id'), $storeArr)){
					$invent = stripslashes (htmlspecialchars ($inv['inventory_center_address']));
					$invent = str_replace("\r\n", " ", $invent);
					$pointC = unserialize($inv['inventory_center_address_point']);
							$inventX =$pointC['lat'];
							$inventY =$pointC['lng'];
					$invent_det = stripslashes ($inv['inventory_center_details']);
					$invent_det = str_replace("\r\n", " ", $invent_det);

					$invent_name = stripslashes ($inv['inventory_center_name']);
					$invent_name = str_replace("\r\n", " ", $invent_name);

					echo '	inv.push({' . "\n" .
						// '		details: \'' . $invent_det . '\',' . "\n" .
						 '		name: \'' . $invent_name . '<br/><a style="text-decoration:underline;" href="' . itw_app_link('appExt=inventoryCenters&inv_id=' . $inv['inventory_center_id'], 'show_inventory', 'default') . '">More Info</a>\',' . "\n" .
						 //'		address: \'' . $invent . '\',' . "\n" .
						 '		addressX: \'' . $inventX . '\',' . "\n" .
						 '		addressY: \'' . $inventY . '\',' . "\n" .
						 '		func: function(response){' . "\n" .
						 '		}' . "\n" .
						 '	});' . "\n";
					//echo "inv[i] = inv1";
					//echo 'showLocation(inv[' . $i . '].address, inv[' . $i . '].name);' . "\n";
					//echo "i = i + 1;";
					$i++;
				}
			}
		}
?>
	$.each(inv, function (i, el){
		showLocation2(this.addressX, this.addressY, this.name, this.ind);
	});

	$("#message").appendTo(map.getPane(G_MAP_FLOAT_SHADOW_PANE));

});
</script>