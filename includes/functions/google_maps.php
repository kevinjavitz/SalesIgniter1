<?php
  $checkedAddresses = array();
  function getGoogleCoordinates($address){
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
          $address = "http://maps.google.com/maps/geo?q=" . $addressStr . "&key=" . EXTENSION_INVENTORY_CENTERS_GOOGLE_MAPS_API_KEY . "&output=json";
          $page = file_get_contents($address);

          if (tep_not_null($page)){
              $addressArr = $json->decode($page);
              if (isset($addressArr->Placemark)){
                  $point = $addressArr->Placemark[0]->Point->coordinates;
              }
              if (isset($point) && is_array($point)){
                  $pointCoordinates['lng'] = $point[0];
                  $pointCoordinates['lat'] = $point[1];
              }
          }
          $checkedAddresses[$addressStr] = $pointCoordinates;
      }
     return $checkedAddresses[$addressStr];
  }

  function polygonContains($polygon, $lon, $lat){
      $j=0;
      $oddNodes = false;
      $x = $lon;
      $y = $lat;
      for ($i=0; $i < sizeof($polygon); $i++) {
          $j++;
          if ($j == sizeof($polygon)) {$j = 0;}

          $iLat = $polygon[$i]['lat'];
          $iLng = $polygon[$i]['lng'];
          $jLat = $polygon[$j]['lat'];
          $jLng = $polygon[$j]['lng'];

          if (($iLat < $y && $jLat >= $y) || ($jLat < $y && $iLat >= $y)){
              if (($iLng + ($y - $iLat) / ($jLat-$iLat) * ($jLng - $iLng)) < $x){
                  $oddNodes = !$oddNodes;
              }
          }
      }
    return $oddNodes;
  }

  function getServiceAreas(){
      $QserviceAreas = tep_db_query('select * from ' . TABLE_PRODUCTS_INVENTORY_CENTERS);
      $serviceAreas = array();
      while($serviceArea = tep_db_fetch_array($QserviceAreas)){
          $serviceAreas[] = array(
              'id'      => $serviceArea['inventory_center_id'],
              'name'    => $serviceArea['inventory_center_name'],
              'address' => $serviceArea['inventory_center_address'],
              'decoded' => unserialize($serviceArea['gmaps_polygon'])
          );
      }
    return $serviceAreas;
  }

  function getServiceAreaName($lat, $lng){
      $serviceAreas = getServiceAreas();
      for ($i=0; $i<sizeof($serviceAreas); $i++){
          if (polygonContains($serviceAreas[$i]['decoded'], $lng, $lat) === true){
              return $serviceAreas[$i]['name'];
              break;
          }
      }
    return false;
  }

  function getServiceAreaID($lat, $lng){
      $serviceAreas = getServiceAreas();
      for ($i=0; $i<sizeof($serviceAreas); $i++){
          if (polygonContains($serviceAreas[$i]['decoded'], $lng, $lat) === true){
              return $serviceAreas[$i]['id'];
              break;
          }
      }
    return false;
  }
?>