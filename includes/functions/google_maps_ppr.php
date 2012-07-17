<?php
  $checkedAddresses = array();
  function getPPRGoogleCoordinates($address){
    global $checkedAddresses, $http;
      $json = new Services_JSON();

      $addressStr = //$address['entry_street_address'] . ', ' .
                    //$address['entry_city'] . ', ' .
                    $address['entry_postcode'];
                                          
      if (isset($address['entry_state'])){
          $addressStr .= ', ' . $address['entry_state'];
      }

      if (isset($address['entry_country_name'])){
          $addressStr .= ', ' . $address['entry_country_name'];
      }
      
      $addressStr = str_replace(' ', '+', $addressStr);

      if (!isset($checkedAddresses[$addressStr])){
          $pointCoordinates = array(
              'lng' => 'false',
              'lat' => 'false'
          );
          $address = "http://maps.google.com/maps/geo?q=" . $addressStr . "&key=" . Session::get('google_key') . "&output=json";
          $page = file_get_contents($address);

          if (tep_not_null($page)){
              $addressArr = $json->decode($page);
              if (isset($addressArr->Placemark)){
                  $point = $addressArr->Placemark[0]->Point->coordinates;
              }
              if (is_array($point)){
                  $pointCoordinates['lng'] = $point[0];
                  $pointCoordinates['lat'] = $point[1];
              }
          }
          $checkedAddresses[$addressStr] = $pointCoordinates;
      }
     return $checkedAddresses[$addressStr];
  }

/*function pointInPolygon($point, $polygon) {

    	$point['y'] = $lon;
        $point['x'] = $lat;

        // Check if the point sits exactly on a vertex
        if (pointOnVertex($polygon, $point) == true) {
            return true;//vertex
        }

        // Check if the point is inside the polygon or on the boundary
        $intersections = 0;
        $polygonCount = count($polygon);

        for ($i=1; $i < $polygonCount; $i++) {

	        $vertex1['y'] = $polygon[$i-1]['lat'];
            $vertex1['x'] = $polygon[$i-1]['lng'];
            $vertex2['y'] = $polygon[$i]['lat'];
            $vertex2['x'] = $polygon[$i]['lng'];

            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) { // Check if point is on an horizontal polygon boundary
                return true;//boundary
            }

            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) {
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
                if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                    return true;
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections++;
                }
            }
        }
        // If the number of edges we passed through is even, then it's in the polygon.
        if ($intersections % 2 != 0) {
            return true;
        } else {
            return false;
        }
  }

function pointOnVertex($point, $vertices) {
	foreach($vertices as $vertex) {
		if ($point == $vertex) {
			return true;
		}
	}
} */
  function polygonPPRContains($polygonNotOrdered, $lat, $lon){
      $point['y'] = (float)$lat;
      $point['x'] = (float)$lon;
	  $polygon = array();
	  foreach($polygonNotOrdered as $iPoly){
		$polygon[] = $iPoly;
	  }
        // Check if the point sits exactly on a vertex
        /*if (pointOnVertex($polygon, $point) == false) {
            return false;//vertex
        } */
        // Check if the point is inside the polygon or on the boundary
        $intersections = false;
        $polygonCount = count($polygon);
	    $j = $polygonCount -1;
        for ($i=0; $i < $polygonCount; $i++) {
	        $vertex1['y'] = (float)$polygon[$i]['lat'];
            $vertex1['x'] = (float)$polygon[$i]['lng'];
            $vertex2['y'] = (float)$polygon[$j]['lat'];
            $vertex2['x'] = (float)$polygon[$j]['lng'];
            /*if (!($vertex1['y'] == $vertex2['y'] && $vertex1['y'] == $point['y'] && $point['x'] > min($vertex1['x'], $vertex2['x']) && $point['x'] < max($vertex1['x'], $vertex2['x']))) { // Check if point is on an horizontal polygon boundary
                return false;//boundary
            } */
            /*if ($point['y'] > min($vertex1['y'], $vertex2['y']) && $point['y'] <= max($vertex1['y'], $vertex2['y']) && $point['x'] <= max($vertex1['x'], $vertex2['x']) && $vertex1['y'] != $vertex2['y']) {
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
                if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                    return true;
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections = !$intersections;
                }
			}
            */
			if ($vertex1['x'] < $point['x'] && $vertex2['x'] >= $point['x'] || $vertex2['x'] < $point['x'] && $vertex1['x'] >= $point['x'])	 {
				if ($vertex1['y'] + ($point['x'] - $vertex1['x']) / ($vertex2['x'] - $vertex1['x']) * ($vertex2['y'] - $vertex1['y']) < $point['y']) {
					$intersections = !$intersections;
				}
			}
			$j = $i;
        }
        // If the number of edges we passed through is even, then it's in the polygon.
       /* if ($intersections % 2 != 0) {
            return true;
        } else {
            return false;
        }*/
	  return $intersections;
  }
	function pointOnVertex($polygon, $point) {
        for ($i=0; $i < sizeof($polygon); $i++) {
			$iLat = $polygon[$i]['lat'];
			$iLng = $polygon[$i]['lng'];

			if($iLat == $point['y'] && $iLng == $point['x']){
				return true;
			}
	    }
		return false;
    }

    function getShippingMethodAreas($methods = false){
		if($methods == false){
	    $module = OrderShippingModules::getModule('zonereservation');
		$quotes = $module->quote();
			$methods = $quotes['methods'];
		}
		$shipAreas = array();
		foreach($methods as $methodId => $val){
		    $QGoogleZones = Doctrine_Query::create()
				            ->from('GoogleZones')
				            ->where('google_zones_id = ?', $val['zone'])
		                    ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if(count($QGoogleZones) > 0){
				$decoded = unserialize($QGoogleZones[0]['gmaps_polygon']);
			}else{
				$decoded = 'all';
			}
			//if($val['text'] == 'Whitby'){
			$shipAreas[] = array(
				  'id'      => $methodId,
				  'name'    => $val['text'],
				  'decoded' => $decoded
			);
			//}
		}

         return $shipAreas;
    }

  function getShippingMethods($lng, $lat, $methods = false){
      $shippingMethodAreas = getShippingMethodAreas($methods);
      $shipMethodsIn = null;      
      for ($i=0; $i<sizeof($shippingMethodAreas); $i++){
	      if ( ($shippingMethodAreas[$i]['decoded'] == 'all') || (polygonPPRContains($shippingMethodAreas[$i]['decoded'], $lat, $lng))){
              $shipMethodsIn[] = $shippingMethodAreas[$i];
          }
      }
    return $shipMethodsIn;
  }

?>