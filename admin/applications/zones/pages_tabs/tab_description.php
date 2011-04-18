<?php



	if (isset($_GET['zID'])){
		$name = $GoogleZones->google_zones_name;
		$address = STORE_NAME_ADDRESS;

		if (tep_not_null($GoogleZones->gmaps_polygon)){
			$address = '';
			$polygon = unserialize($GoogleZones->gmaps_polygon);
			$script = '<script>$(document).ready(function (){';
			for($i=0, $n=sizeof($polygon); $i<$n; $i++){
				if(!empty($polygon[$i]['lat']) || !empty($polygon[$i]['lng'])){
					$script .= 'leftClick(poly, new GLatLng(' . $polygon[$i]['lat'] . ', ' . $polygon[$i]['lng'] . ', true));';
				}
			}
			$script .= 'recenterMap();});</script>';
		}
	}else{
		$name = "";
		$address = STORE_NAME_ADDRESS;
		$script = '<script>$(document).ready(function (){';
		$script .= 'recenterMap();});</script>';
	}

                               
?>



 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_GOOGLE_ZONES_NAME'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('google_zones_name', $name); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_GOOGLE_ZONES_ADDRESS'); ?></td>
   <td class="main"><?php echo tep_draw_textarea_field('google_zones_address','hard',40,3, $address); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_GOOGLE_ZONES_MAP'); ?></td>
	<td class="main"><?php echo $script;?><div id="mapHolder"><div id="googleMap" style="width:650px;height:450px;"></div></div></td>
  </tr>
	 
 </table>

