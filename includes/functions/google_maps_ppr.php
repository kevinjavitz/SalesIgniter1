<?php
  $checkedAddresses = array();
  function getPPRGoogleCoordinates($address){
    global $checkedAddresses, $http;
      $json = new Services_JSON();

      $addressStr = $address['entry_street_address'] . ', ' .
                    $address['entry_city'] . ', ' .
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
          $address = "http://maps.google.com/maps/geo?q=" . $addressStr . "&key=" . GOOGLE_MAPS_API_KEY . "&output=json";
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

  function polygonPPRContains($polygon, $lon, $lat){

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

	function pointOnVertex($polygon, $point) {
        for ($i=0; $i < sizeof($polygon); $i++) {
			$iLat = $polygon[$i]['lat'];
			$iLng = $polygon[$i]['lng'];

			if($iLat == $point['y'] && $iLng = $point['x']){
				return true;
			}
	    }
		return false;
    }

    function getShippingMethodAreas(){
	    $module = OrderShippingModules::getModule('zonereservation');
		$quotes = $module->quote();
		for($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++){
		    $QGoogleZones = Doctrine_Query::create()
				            ->from('GoogleZones')
				            ->where('google_zones_id = ?', $quotes['methods'][$i]['zone'])
		                    ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if(count($QGoogleZones) > 0){
				$decoded = unserialize($QGoogleZones[0]['gmaps_polygon']);
			}else{
				$decoded = 'all';
			}
		    
			$shipAreas[] = array(
				  'id'      => $quotes['methods'][$i]['id'],
				  'name'    => $quotes['methods'][$i]['title'],
				  'decoded' => $decoded
			);
		}

         return $shipAreas;
    }

  function getShippingMethods($lat, $lng){
      $shippingMethodAreas = getShippingMethodAreas();
      $shipMethodsIn = null;      
      for ($i=0; $i<sizeof($shippingMethodAreas); $i++){
	      if ( ($shippingMethodAreas[$i]['decoded'] == 'all') || (polygonPPRContains($shippingMethodAreas[$i]['decoded'], $lng, $lat) === true)){
              $shipMethodsIn[] = $shippingMethodAreas[$i];
          }
      }
    return $shipMethodsIn;
  }

?>