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
		/*$("#pickupz option[value="+i+"]").removeAttr("selected");
		$("#pickupz option[value="+i+"]").attr("selected","selected");
		$("#pickupz").trigger('change');*/
		//ajax call to set before  inv_id =i
		$ellem = $('#pprHome').find('form');
		/*if($('#pprHome').find('form').find('input[name="pickup"]').size() == 0){
			$ellem.append('<input type="hidden" name="pickup" value="'+i+'"/>');
		}else{
			$('#pprHome').find('form').find('input[name="pickup"]').replaceWith('<input type="hidden" name="pickup" value="'+i+'"/>');
		}*/
		var sendValues = $ellem.serialize();
		sendValues = sendValues.replace(/&pickup=.*&/i,'&pickup='+i+'&');
		showAjaxLoader($ellem,'xlarge');
		$.ajax({
			type: "post",
			url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=setBefore'),
			data: sendValues+"&rType=ajax",
			success: function(data) {
				$ellem.find('.invCenter').replaceWith(data.data);
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
	}

	function showLocation2(addressx,addressy, msg,i) {
				point = new GLatLng(addressx, addressy);

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
</script>

<script type="text/javascript">
$(document).ready(function (){
	initialize();
	i1 = 0;
	var inv = [];
<?php
		$Inventory_centers = Doctrine_Query::create()
		->from('ProductsInventoryCenters') ;
		if(Session::exists('current_store_id')){
			$Inventory_centers->where('inventory_center_stores=?', Session::get('current_store_id'));
		}
		$Inventory_centers = $Inventory_centers->execute(array(), Doctrine::HYDRATE_ARRAY);
		if($Inventory_centers){
			$i = 0;
			foreach($Inventory_centers as $inv){
				$f = true;
				if(isset($inv['inventory_center_stores']) && Session::exists('current_store_id')){
					$f = false;
	         		$storeArr = explode(';', $inv['inventory_center_stores']);
					if(in_array(Session::get('current_store_id'), $storeArr)){
						$f = true;
					}
				}


				if($f){
					$invent = stripslashes (htmlspecialchars ($inv['inventory_center_address']));
					$invent = str_replace("\r\n", " ", $invent);

					$pointC = unserialize($inv['inventory_center_address_point']);
					$inventX =$pointC['lat'];
					$inventY =$pointC['lng'];
					$invent_det = stripslashes ($inv['inventory_center_details']);
					$invent_det = str_replace("\r\n", " ", $invent_det);

					$invent_name = stripslashes ($inv['inventory_center_name']);
					$invent_name = str_replace("\r\n", " ", $invent_name);

					$invent_ind = stripslashes ($inv['inventory_center_id']);

		            if($inventX != 'false' && $inventY != 'false'){
						echo '	inv.push({' . "\n" .
							 '		name: \'' . $invent_name . '<br/><a style="text-decoration:underline;" href="' . itw_app_link('appExt=inventoryCenters&inv_id=' . $inv['inventory_center_id'], 'show_inventory', 'default') . '">More Info</a>\',' . "\n" .
							 '		addressX: \'' . $inventX . '\',' . "\n" .
							 '		addressY: \'' . $inventY . '\',' . "\n" .
							 '		ind: \'' . $invent_ind . '\',' . "\n" .
							 '		func: function(response){' . "\n" .
							 '		}' . "\n" .
							 '	});' . "\n";
					}
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

 <div id="gMap"></div>