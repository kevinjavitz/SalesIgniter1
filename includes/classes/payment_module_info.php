<?php
  class paymentModuleInfo {
    var $payment_code, $keys;

// class constructor
    function paymentModuleInfo($pmInfo_array) {
      $this->payment_code = $pmInfo_array['payment_code'];

      for ($i = 0, $n = sizeof($pmInfo_array) - 1; $i < $n; $i++) {
          $Qvalue = dataAccess::setQuery('select configuration_title, configuration_value, configuration_description from {configuration} where configuration_key = {key}');
          $Qvalue->setTable('{configuration}', TABLE_CONFIGURATION);
          $Qvalue->setTable('{key}', $pmInfo_array[$i]);
          $Qvalue->runQuery();

        $this->keys[$pmInfo_array[$i]]['title'] = $Qvalue->getVal('configuration_title');
        $this->keys[$pmInfo_array[$i]]['value'] = $Qvalue->getVal('configuration_value');
        $this->keys[$pmInfo_array[$i]]['description'] = $Qvalue->getVal('configuration_description');
      }
    }
  }
?>