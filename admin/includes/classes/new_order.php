<?php
 class newOrder {
     
      function newOrder(){
          $QorderTotals = tep_db_query('select configuration_value as cfgValue from ' . TABLE_CONFIGURATION . ' where configuration_key = "MODULE_ORDER_TOTAL_INSTALLED"');
          $orderTotals = tep_db_fetch_array($QorderTotals);
          $this->order_total_modules = explode(';', $orderTotals['cfgValue']);

          $QshippingMethods = tep_db_query('select configuration_value as cfgValue from ' . TABLE_CONFIGURATION . ' where configuration_key = "MODULE_SHIPPING_INSTALLED"');
          $shippingMethods = tep_db_fetch_array($QshippingMethods);
          $this->shipping_modules = explode(';', $shippingMethods['cfgValue']);

          $QpaymentMethods = tep_db_query('select configuration_value as cfgValue from ' . TABLE_CONFIGURATION . ' where configuration_key = "MODULE_PAYMENT_INSTALLED"');
          $paymentMethods = tep_db_fetch_array($QpaymentMethods);
          $this->payment_modules = explode(';', $paymentMethods['cfgValue']);

          $this->reset();
      }
      
      function loadOrder($oID){
        global $currencies;
          $this->orderID = $oID;
          $QoInfo = tep_db_query('select * from ' . TABLE_ORDERS . ' where orders_id = "' . $this->orderID . '"');
          $oInfo = tep_db_fetch_array($QoInfo);
          
          $this->info = array(
              'currency'           => $oInfo['currency'],
              'currency_value'     => $oInfo['currency_value'],
              'payment_method'     => $oInfo['payment_method'],
              'payment_module'     => $oInfo['payment_module'],
              'shipping_module'    => $oInfo['shipping_module'],
              'cc_type'            => $oInfo['cc_type'],
              'cc_owner'           => $oInfo['cc_owner'],
              'cc_number'          => $oInfo['cc_number'],
              'cc_expires'         => $oInfo['cc_expires'],
              'date_purchased'     => $oInfo['date_purchased'],
              'orders_status'      => $oInfo['orders_status'],
//Package Tracking Plus BEGIN
              'usps_track_num'     => $oInfo['usps_track_num'],
              'usps_track_num2'    => $oInfo['usps_track_num2'],
              'ups_track_num'      => $oInfo['ups_track_num'],
              'ups_track_num2'     => $oInfo['ups_track_num2'],
              'fedex_track_num'    => $oInfo['fedex_track_num'],
              'fedex_track_num2'   => $oInfo['fedex_track_num2'],
              'dhl_track_num'      => $oInfo['dhl_track_num'],
              'dhl_track_num2'     => $oInfo['dhl_track_num2'],
//Package Tracking Plus END
              'shipping_signature' => $oInfo['signature'],
              'last_modified'      => $oInfo['last_modified']
          );
          
          $QorderCustomer = tep_db_query('select customers_id, customers_name, customers_company as company, customers_street_address as street_address, customers_suburb as suburb, customers_city as city, customers_postcode as postcode, customers_state as state, customers_country as country_name, customers_telephone as telephone, customers_email_address as email_address, customers_address_format_id as format_id from ' . TABLE_ORDERS . ' where orders_id = "' . $this->orderID . '"');
          $orderCustomer = tep_db_fetch_array($QorderCustomer);
           
          $QorderBilling = tep_db_query('select billing_name as customers_name, billing_company as company, billing_street_address as street_address, billing_suburb as suburb, billing_city as city, billing_postcode as postcode, billing_state as state, billing_country as country_name, billing_address_format_id as format_id from ' . TABLE_ORDERS . ' where orders_id = "' . $this->orderID . '"');
          $orderBilling = tep_db_fetch_array($QorderBilling);
           
          $QorderDelivery = tep_db_query('select delivery_name as customers_name, delivery_company as company, delivery_street_address as street_address, delivery_suburb as suburb, delivery_city as city, delivery_postcode as postcode, delivery_state as state, delivery_country as country_name, delivery_address_format_id as format_id from ' . TABLE_ORDERS . ' where orders_id = "' . $this->orderID . '"');
          $orderDelivery = tep_db_fetch_array($QorderDelivery);

          $this->addCustomerInfo('customer', $orderCustomer);
          $this->addCustomerInfo('delivery', $orderDelivery);
          $this->addCustomerInfo('billing', $orderBilling);
          
          $this->setPaymentMethod($oInfo['payment_module'], $oInfo['payment_method']);
          
          $ship = explode('_', $oInfo['shipping_module']);
          $this->setShippingMethod($ship[0], $ship[1]);
          
          $Qproducts = tep_db_query('select * from ' . TABLE_ORDERS_PRODUCTS . ' where orders_id = "' . $this->orderID . '"');
          while($products = tep_db_fetch_array($Qproducts)){
              $reservationInfo = newOrder_getOrdersProductsReservation($products['orders_products_id']);
              if (is_array($reservationInfo)){
                  $this->setShippingMethod($reservationInfo['shipping']['module'], $reservationInfo['shipping']['method']);
              }
              
              $attributes = newOrder_getOrdersProductsArrtibutes($products['orders_products_id'], $products['products_id']);
                                     
              $this->add_product($products['products_id'], $products['products_quantity'], $attributes, $reservationInfo);
              $this->updateProductPrice(tep_get_uprid($products['products_id'], $attributes), $products['final_price']);
              $this->contents[tep_get_uprid($products['products_id'], $attributes)]['opID'] = $products['orders_products_id'];
          }
      }
      
      function reset() {
        global $currencies;
          $this->contents = array();
          
          $this->info = array(
              'order_status'    => DEFAULT_ORDERS_STATUS_ID,
              'currency'        => $_SESSION['currency'],
              'currency_value'  => $currencies->currencies[$_SESSION['currency']]['value'],
              'payment_method'  => '',
              'payment_module'  => '',
              'shipping_method' => '',
              'shipping_module' => '',
              'cc_type'         => '',
              'cc_owner'        => '',
              'cc_number'       => '',
              'cc_expires'      => '',
              'shipping_cost'   => 0,
              'subtotal'        => 0,
              'tax'             => 0,
              'total'           => 0,
              'tax_groups'      => array(),
              'comments'        => ''
          );
          
          $this->customer = array();
          $this->delivery = array();
          $this->billing = array();
      }
      
      function addCustomer($cInfo){ $this->addCustomerInfo('customer', $cInfo); }
      function addDelivery($cInfo){ $this->addCustomerInfo('delivery', $cInfo); }
      function addBilling($cInfo){ $this->addCustomerInfo('billing', $cInfo); }
      
      function addCustomerInfo($varName, $cInfo){
          $countryInfo = newOrder_getCountryInfo($cInfo['country_name']);
          $zoneInfo = newOrder_getZoneInfo($cInfo['state']);
          if (isset($cInfo['customers_name'])){
              $name = explode(' ', $cInfo['customers_name']);
              $firstName = $name[0];
              $lastName = $name[1];
          }else{
              $firstName = $cInfo['firstname'];
              $lastName = $cInfo['lastname'];
          }
          $this->$varName = array(
              'id'             => $cInfo['customers_id'],
              'firstname'      => $firstName,
              'lastname'       => $lastName,
              'company'        => $cInfo['company'],
              'street_address' => $cInfo['street_address'],
              'suburb'         => $cInfo['suburb'],
              'city'           => $cInfo['city'],
              'postcode'       => $cInfo['postcode'],
              'state'          => $zoneInfo['zone_name'],
              'zone_id'        => $zoneInfo['zone_id'],
              'country_id'     => $countryInfo['country_id'],
              'country'        => array(
                  'id'         => $countryInfo['country_id'], 
                  'title'      => $countryInfo['country_name'], 
                  'iso_code_2' => $countryInfo['country_iso_code_2'], 
                  'iso_code_3' => $countryInfo['country_iso_code_3']
              ),
              'format_id'      => $cInfo['format_id'],
              'telephone'      => $cInfo['telephone'],
              'email_address'  => $cInfo['email_address']
          );
      }
      
      function addComments($comments){
          if ($this->info['comments'] == ''){
              $this->info['comments'] = addslashes($comments);
          }else{
              if (is_array($this->info['comments'])){
                  $this->info['comments'][] = addslashes($comments);
              }else{
                  $orig = $this->info['comments'];
                  $this->info['comments'] = array();
                  $this->info['comments'][] = $orig;
                  $this->info['comments'][] = addslashes($comments);
              }
          }
      }
      
      function setNotify($value){
          $this->notify = $value;
      }
      
      function setNotifyComments($value){
          $this->notifyComments = $value;
      }
      
      function setPaymentMethod($code, $title){
          $this->info['payment_method'] = $title;
          $this->info['payment_module'] = $code;
      }
      
      function setShippingMethod($module, $method){
          $this->info['shipping_module'] = $module . '_' . $method;
          if (tep_not_null($module) && tep_not_null($method)){
              $quote = newOrder_getShippingQuote($module, $method);
              $this->info['shipping_method'] = $quote['title'];
              $this->info['shipping_cost'] = $quote['cost'];
          }
      }
      
      function hasRemotePayment(){
//          return $this->remotePayment;
          return false;
      }
      
      function setRemotePayment($val){
          $this->remotePayment = $val;
      }
      
      function setOrderStatus($status){
          $this->info['order_status'] = $status;
      }
      
      function add_product($products_id, $qty = '', $attributes = '', $reservationInfo = '') {
          $products_id_string = tep_get_uprid($products_id, $attributes);
          
          if ($this->in_order($products_id_string)) {
              $this->update_quantity($products_id_string, $qty, $attributes, $reservationInfo);
          } else {
              if ($qty == '') $qty = '1'; // if no quantity is supplied, then add '1' to the customers basket
              
              $this->contents[] = array($products_id_string);
              $this->contents[$products_id_string] = array('qty' => $qty);
              
              if (is_array($attributes)) {
                  reset($attributes);
                  while (list($option, $value) = each($attributes)) {
                      $this->contents[$products_id_string]['attributes'][$option] = $value;
                  }
              }

              if (tep_not_null($reservationInfo)){
                  $this->contents[$products_id_string]['reservationInfo'] = $reservationInfo;
              }
          }
          $this->cleanup();
          $this->loadPricing();
      }
      
      function update_quantity($products_id, $quantity = '', $attributes = '', $reservationInfo = '') {
          if ($quantity == '') return true; // nothing needs to be updated if theres no quantity, so we return true..
          if (isset($this->contents[$products_id]['price'])){
              $alteredPrice = $this->contents[$products_id]['price'];
          }
          $this->contents[$products_id] = array('qty' => $quantity);
          if (isset($alteredPrice)){
              $this->contents[$products_id]['price'] = $alteredPrice;
          }
          if (tep_not_null($reservationInfo)){
              $this->contents[$products_id]['reservationInfo'] = $reservationInfo;
          }
          
          if (is_array($attributes)) {
              reset($attributes);
              while (list($option, $value) = each($attributes)) {
                  $this->contents[$products_id]['attributes'][$option] = $value;
              }
          }
      }
      
      function updateProductQuantity($products_id, $quantity = ''){
          if ($quantity == '') return true; // nothing needs to be updated if theres no quantity, so we return true..
          $this->contents[$products_id]['qty'] = $quantity;
      }
      
      function updateProductPrice($products_id, $price = '') {
          if ($price == '') return true; // nothing needs to be updated if theres no quantity, so we return true..
          $this->contents[$products_id]['price'] = $price;
      }
      
      function cleanup() {
          reset($this->contents);
          while (list($key,) = each($this->contents)) {
              if ($this->contents[$key]['qty'] < 1) {
                  unset($this->contents[$key]);
              }
          }
      }
      
      function get_quantity($products_id) {
          if ($this->contents[$products_id]) {
              return $this->contents[$products_id]['qty'];
          } else {
              return 0;
          }
      }
      
      function in_order($products_id) {
          if ($this->contents[$products_id]) {
              return true;
          } else {
              return false;
          }
      }

      function remove($products_id) {
          unset($this->contents[$products_id]);
      }

      function remove_all() {
          $this->reset();
      }
      
      function show_total(){
          $this->calculate();

          return $this->total;
      }
      
      function get_product_id_list() {
          $product_id_list = '';
          if (is_array($this->contents)){
              reset($this->contents);
              while (list($products_id, ) = each($this->contents)) {
                  $product_id_list .= ', ' . $products_id;
              }
          }
          return substr($product_id_list, 2);
      }
      
      function calculate() {
          $this->total = 0;
          $this->weight = 0;
          if (!is_array($this->contents)) return 0;
          
          reset($this->contents);
          while (list($products_id, ) = each($this->contents)) {
              $qty = $this->contents[$products_id]['qty'];
              if (isset($this->contents[$products_id]['price'])){
                  $alteredPrice = $this->contents[$products_id]['price'];
              }
              
              $product_query = tep_db_query("select products_id, products_price, products_tax_class_id, products_weight from " . TABLE_PRODUCTS . " where products_id='" . (int)tep_get_prid($products_id) . "'");
              if ($product = tep_db_fetch_array($product_query)) {
                  $prid = $product['products_id'];
                  $products_tax = tep_get_tax_rate($product['products_tax_class_id']);
                  $products_price = $product['products_price'];
                  $products_weight = $product['products_weight'];
                  
                  $specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$prid . "' and status = '1'");
                  if (tep_db_num_rows ($specials_query)) {
                      $specials = tep_db_fetch_array($specials_query);
                      $products_price = $specials['specials_new_products_price'];
                  }

                  if (isset($this->contents[$products_id]['reservationInfo'])){
                      $productPricing = array(
                          'daily'    => $product['products_price_daily'],
                          'weekly'   => $product['products_price_weekly'],
                          'monthly'  => $product['products_price_monthly'],
                          'insure'   => $this->contents[$products_id]['reservationInfo']['insure'],
                          'shipping' => $this->contents[$products_id]['reservationInfo']['shipping']['cost']
                      );
                      $rentalLength = $this->getRentalLength($this->contents[$products_id]['reservationInfo']['start_date'], $this->contents[$products_id]['reservationInfo']['end_date']);
                      $pricing = $this->getReservationPrice($productPricing, $rentalLength);
                      $products_price = $pricing['price'];
                  }
                  
                  if (isset($alteredPrice)){
                      $products_price = $alteredPrice;
                  }

                  $this->total += tep_add_tax($products_price, $products_tax) * $qty;
                  $this->weight += ($qty * $products_weight);
              }
              
              if (isset($this->contents[$products_id]['attributes'])) {
                  reset($this->contents[$products_id]['attributes']);
                  while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                      $attribute_price_query = tep_db_query("select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$prid . "' and options_id = '" . (int)$option . "' and options_values_id = '" . (int)$value . "'");
                      $attribute_price = tep_db_fetch_array($attribute_price_query);
                      if ($attribute_price['price_prefix'] == '+') {
                          $this->total += $qty * tep_add_tax($attribute_price['options_values_price'], $products_tax);
                      } else {
                          $this->total -= $qty * tep_add_tax($attribute_price['options_values_price'], $products_tax);
                      }
                  }
              }
          }
      }
      
      function attributes_price($products_id) {
          $attributes_price = 0;
          
          if (isset($this->contents[$products_id]['attributes'])) {
              reset($this->contents[$products_id]['attributes']);
              while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                  $attribute_price_query = tep_db_query("select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$products_id . "' and options_id = '" . (int)$option . "' and options_values_id = '" . (int)$value . "'");
                  $attribute_price = tep_db_fetch_array($attribute_price_query);
                  if ($attribute_price['price_prefix'] == '+') {
                      $attributes_price += $attribute_price['options_values_price'];
                  } else {
                      $attributes_price -= $attribute_price['options_values_price'];
                  }
              }
          }
          return $attributes_price;
      }
      
      function get_products($pID = false) {
          if (!is_array($this->contents)) return 0;
          $products_array = array();
          reset($this->contents);
          $index = 0;
          while (list($products_id, ) = each($this->contents)) {
              if ($pID === false || $pID == $products_id){
                  $products_query = tep_db_query("select p.products_id, pd.products_name, p.products_model, p.products_price, p.products_weight, p.products_tax_class_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id='" . (int)tep_get_prid($products_id) . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
                  if ($products = tep_db_fetch_array($products_query)) {
                      $prid = $products['products_id'];
                      $products_price = $products['products_price'];
                      
                      $specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$prid . "' and status = '1'");
                      if (tep_db_num_rows($specials_query)) {
                          $specials = tep_db_fetch_array($specials_query);
                          $products_price = $specials['specials_new_products_price'];
                      }
                      
                      if (isset($this->contents[$products_id]['reservationInfo'])){
                          $productPricing = array(
                              'daily'    => $products['products_price_daily'],
                              'weekly'   => $products['products_price_weekly'],
                              'monthly'  => $products['products_price_monthly'],
                              'insure'   => $this->contents[$products_id]['reservationInfo']['insure'],
                              'shipping' => $this->contents[$products_id]['reservationInfo']['shipping']['cost']
                          );
                          $rentalLength = $this->getRentalLength($this->contents[$products_id]['reservationInfo']['start_date'], $this->contents[$products_id]['reservationInfo']['end_date']);
                          $pricing = $this->getReservationPrice($productPricing, $rentalLength);
                          $products_price = $pricing['price'];
                      }
                  
                      if (isset($this->contents[$products_id]['price'])){
                          $products_price = $this->contents[$products_id]['price'];
                      }
                  
                      $products_array[$index] = array(
                          'id'           => $products_id,
                          'opID'         => (isset($this->contents[$products_id]['opID']) ? $this->contents[$products_id]['opID'] : false),
                          'name'         => $products['products_name'],
                          'model'        => $products['products_model'],
                          'price'        => $products_price,
                          'quantity'     => $this->contents[$products_id]['qty'],
                          'weight'       => $products['products_weight'],
                          'final_price'  => ($products_price + $this->attributes_price($products_id)),
                          'tax_class_id' => $products['products_tax_class_id'],
                          'attributes'   => (isset($this->contents[$products_id]['attributes']) ? $this->contents[$products_id]['attributes'] : ''));
                      if (isset($this->contents[$products_id]['reservationInfo'])){
                          $products_array[$index]['reservation'] = $this->contents[$products_id]['reservationInfo'];
                      }
                      $index++;
                  }
              }
          }
          return $products_array;
      }
      
      function resetPricing(){
          $this->total = 0;
          $this->weight = 0;
          $this->info['subtotal'] = 0;
          $this->info['tax'] = 0;
          $this->info['tax_groups'] = array();
      }
      
      function loadPricing(){
          $this->resetPricing();
          $products = $this->get_products();
          $index = 0;
          for ($i=0, $n=sizeof($products); $i<$n; $i++) {
              $this->products[$index] = array(
                  'qty'             => $products[$i]['quantity'],
                  'name'            => $products[$i]['name'],
                  'model'           => $products[$i]['model'],
                  'tax'             => tep_get_tax_rate($products[$i]['tax_class_id'], $this->billing['country']['id'], $this->billing['zone_id']),
                  'tax_description' => tep_get_tax_description($products[$i]['tax_class_id'], $this->billing['country']['id'], $this->billing['zone_id']),
                  'price'           => $products[$i]['price'],
                  'final_price'     => $products[$i]['price'] + $this->attributes_price($products[$i]['id']),
                  'weight'          => $products[$i]['weight'],
                  'id'              => $products[$i]['id'],
                  'opID'            => $products[$i]['opID']
              );
              
              if ($products[$i]['attributes']) {
                  $subindex = 0;
                  reset($products[$i]['attributes']);
                  while (list($option, $value) = each($products[$i]['attributes'])) {
                      $attributes_query = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . (int)$products[$i]['id'] . "' and pa.options_id = '" . (int)$option . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . (int)$value . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . (int)$_SESSION['languages_id'] . "' and poval.language_id = '" . (int)$_SESSION['languages_id'] . "'");
                      $attributes = tep_db_fetch_array($attributes_query);
                      
                      $this->products[$index]['attributes'][$subindex] = array(
                          'option'    => $attributes['products_options_name'],
                          'value'     => $attributes['products_options_values_name'],
                          'option_id' => $option,
                          'value_id'  => $value,
                          'prefix'    => $attributes['price_prefix'],
                          'price'     => $attributes['options_values_price']
                      );
                      
                      $subindex++;
                  }
              }
              
              $shown_price = tep_add_tax($this->products[$index]['final_price'], $this->products[$index]['tax']) * $this->products[$index]['qty'];
              $this->info['subtotal'] += $shown_price;
              
              $products_tax = $this->products[$index]['tax'];
              $products_tax_description = $this->products[$index]['tax_description'];
              if (DISPLAY_PRICE_WITH_TAX == 'true') {
                  $this->info['tax'] += $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
                  if (isset($this->info['tax_groups']["$products_tax_description"])) {
                      $this->info['tax_groups']["$products_tax_description"] += $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
                  } else {
                      $this->info['tax_groups']["$products_tax_description"] = $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
                  }
              } else {
                  $this->info['tax'] += ($products_tax / 100) * $shown_price;
                  if (isset($this->info['tax_groups']["$products_tax_description"])) {
                      $this->info['tax_groups']["$products_tax_description"] += ($products_tax / 100) * $shown_price;
                  } else {
                      $this->info['tax_groups']["$products_tax_description"] = ($products_tax / 100) * $shown_price;
                  }
              }
              $index++;
          }
          
          $this->calculate();
          if (isset($this->info['shipping_module'])){
              $method = explode('_', $this->info['shipping_module']);
              $this->getShippingQuotes($method[0], $method[1]);
          }
          
          if (DISPLAY_PRICE_WITH_TAX == 'true') {
              $this->info['total'] = $this->info['subtotal'] + $this->info['shipping_cost'];
          } else {
              $this->info['total'] = $this->info['subtotal'] + $this->info['tax'] + $this->info['shipping_cost'];
          }
      }
      
      function get_content_type() {
          $this->content_type = false;
          if ( (DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0) ) {
              reset($this->contents);
              while (list($products_id, ) = each($this->contents)) {
                  if (isset($this->contents[$products_id]['attributes'])) {
                      reset($this->contents[$products_id]['attributes']);
                      while (list(, $value) = each($this->contents[$products_id]['attributes'])) {
                          $virtual_check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad where pa.products_id = '" . (int)$products_id . "' and pa.options_values_id = '" . (int)$value . "' and pa.products_attributes_id = pad.products_attributes_id");
                          $virtual_check = tep_db_fetch_array($virtual_check_query);
                          
                          if ($virtual_check['total'] > 0) {
                              switch ($this->content_type) {
                                  case 'physical':
                                      $this->content_type = 'mixed';
                                      return $this->content_type;
                                  break;
                                  default:
                                      $this->content_type = 'virtual';
                                  break;
                              }
                          } else {
                              switch ($this->content_type) {
                                  case 'virtual':
                                      $this->content_type = 'mixed';
                                      return $this->content_type;
                                  break;
                                  default:
                                      $this->content_type = 'physical';
                                  break;
                              }
                          }
                      }
                  } else {
                      switch ($this->content_type) {
                          case 'virtual':
                              $this->content_type = 'mixed'; 
                              return $this->content_type;
                          break;
                          default:
                              $this->content_type = 'physical';
                          break;
                      }
                  }
              }
          } else {
              $this->content_type = 'physical';
          }
          return $this->content_type;
      }
      
      function unserialize($broken) {
          for(reset($broken);$kv=each($broken);) {
              $key=$kv['key'];
              if (gettype($this->$key)!="user function") $this->$key=$kv['value'];
          }
      }
      
      function getProductsListing(){
        global $currencies;
          $products = $this->get_products();
          $return = '';
          for ($i=0, $n=sizeof($products); $i<$n; $i++){
              $taxClass = $products[$i]['tax_class_id'];
              $taxRate = tep_get_tax_rate($taxClass, $this->billing['country']['id'], $this->billing['zone_id']);
              $return .= '          <tr id="' . $products[$i]['id'] . '">' . "\n" .
                         '            <td class="main" valign="top">' . tep_image(DIR_WS_IMAGES . 'icons/cross.gif', 'Remove This Product', '', '', 'id="removeProduct"') . '&nbsp;' . tep_image(DIR_WS_IMAGES . 'edit.gif', 'Edit This Product', '', '', 'id="editProduct"') . '</td>' . "\n" .
                         '            <td class="main" valign="top" align="right">' . $products[$i]['quantity'] . '&nbsp;x</td>' . "\n" .
                         '            <td class="main" valign="top">' . $products[$i]['name'];
                
              $attributes = $products[$i]['attributes'];
              if ($attributes != '') {
                  while (list($option, $value) = each($attributes)) {
                      $Qattrib = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . (int)$products[$i]['id'] . "' and pa.options_id = '" . (int)$option . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . (int)$value . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . (int)$_SESSION['languages_id'] . "' and poval.language_id = '" . (int)$_SESSION['languages_id'] . "'");
                      $attrib = tep_db_fetch_array($Qattrib);
                      $return .= '<br><nobr><small>&nbsp;<i> - ' . $attrib['products_options_name'] . ': ' . $attrib['products_options_values_name'];
                      if ($attrib['options_values_price'] != '0') $return .= ' (' . $attrib['price_prefix'] . $currencies->format($attrib['options_values_price']) . ')';
                      $return .= '</i></small></nobr>';
                  }
              }

              if (isset($products[$i]['reservation'])){
                  $return .= '<br><small><b><i><u>Reservation Info (<a href="' . tep_href_link(FILENAME_CUSTOMERS, 'cID=' . $this->customer['id'] . '&action=edit&rID=' . $products[$i]['reservation']['id']) . '" target="_blank">Edit Reservation</a>)</u></i></b></small>' . 
                             '<br><small><i> - Start Date: ' . $products[$i]['reservation']['start_date'] . '</i></small>' . 
                             '<br><small><i> - End Date: ' . $products[$i]['reservation']['end_date'] . '</i></small>' .
                             '<br><small><i> - Shipping Method: ' . $products[$i]['reservation']['shipping']['title'] . ' (' . $currencies->format($products[$i]['reservation']['shipping']['cost']) . ')</i></small>';
              }
                            
              $return .= '            </td>' . "\n" .
                         '            <td class="main" valign="top"></td>' . "\n" .
                         '            <td class="main" valign="top">' . $products[$i]['model'] . '</td>' . "\n" .
                         //'            <td class="main" align="right" valign="top">' . tep_display_tax_value($taxRate) . '%</td>' . "\n" .
                         //'            <td class="main" align="right" valign="top"><b>' . $currencies->format($products[$i]['final_price']) . '</b></td>' . "\n" .
                         '            <td class="main" align="right" valign="top"><b>' . $currencies->format(tep_add_tax($products[$i]['final_price'], $taxRate)) . '</b></td>' . "\n" .
                         //'            <td class="main" align="right" valign="top"><b>' . $currencies->format($products[$i]['final_price'] * $products[$i]['quantity']) . '</b></td>' . "\n" .
                         '            <td class="main" align="right" valign="top"><b>' . $currencies->format(tep_add_tax($products[$i]['final_price'], $taxRate) * $products[$i]['quantity']) . '</b></td>' . "\n" . 
                         '          </tr>' . "\n";
          }
          return '<table>' . $return . '</table>';
      }
      
      function getAttributesBlock($productsID){
        global $currencies;
          $return = 'false';
          if (tep_has_product_attributes($productsID)) {
              $return = '<table border="0" cellspacing="0" cellpadding="2">';
              
              $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$productsID . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$_SESSION['languages_id'] . "' order by popt.products_options_name");
              while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
                  $products_options_array = array();
                  $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . (int)$productsID . "' and pa.options_id = '" . (int)$products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int)$_SESSION['languages_id'] . "'");
                  while ($products_options = tep_db_fetch_array($products_options_query)) {
                      $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
                      if ($products_options['options_values_price'] != '0') {
                          $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .') ';
                      }
                  }
                  $return .= '<tr>' . 
                             '<td class="main">' . $products_options_name['products_options_name'] . ':</td>' . 
                             '<td class="main">' . tep_draw_pull_down_menu('id[' . $productsID . '][' . $products_options_name['products_options_id'] . ']', $products_options_array) . '</td>' . 
                             '</tr>';
              }
              $return .= '</table>';
          }
        return $return;
      }
      
      function completeOrder(){
        global $currencies;
          if ($this->customer['id'] == 'new_customer'){
              $this->_insertCustomer();
          }
          
          $this->newOrderID = $this->_insertOrder();
          $this->_insertOrderTotals();
          $this->_insertOrderStatusHistory();

          if (isset($this->orderID)){
              tep_db_query('delete from ' . TABLE_ORDERS_PRODUCTS . ' where orders_id = "' . $this->orderID . '"');
          }
          for ($i=0, $n=sizeof($this->products); $i<$n; $i++) {
              if (STOCK_LIMITED == 'true') {
                  $this->_doStockCheck($this->products[$i]);
              }
              
              // Update products_ordered (for bestsellers list)
              tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . sprintf('%d', $this->products[$i]['qty']) . " where products_id = '" . tep_get_prid($this->products[$i]['id']) . "'");
              
              $orderProductID = $this->_insertOrderProduct($this->products[$i]);
              
//------insert customer choosen option to order--------
              $attributes_exist = '0';
              $products_ordered_attributes = '';
              if (!empty($this->products[$i]['attributes'])) {
                  $products_ordered_attributes .= $this->_insertOrderProductAttributes($this->products[$i]['id'], $this->products[$i]['attributes'], $orderProductID);
              }
              $products_ordered .= $this->products[$i]['qty'] . ' x ' . $this->products[$i]['name'] . ' (' . $this->products[$i]['model'] . ') = ' . $currencies->display_price($this->products[$i]['final_price'], $this->products[$i]['tax'], $this->products[$i]['qty']) . $products_ordered_attributes . "\n";
          }

          if ($this->notify === true){
              $this->_notifyCustomer($products_ordered);
          }
          
          $this->reset();
      }
      
      function _notifyCustomer($productsOrdered){
        global $currencies, $orders_status_array, $trackingLinks, $orderTotals, $historyLink, $full_name, $orderID, $status, $datePurchased, $orderedProducts;
          $orderedProducts = $productsOrdered;
          // lets start with the email confirmation
          if (tep_not_null($this->info['usps_track_num']) || tep_not_null($this->info['usps_track_num2']) || tep_not_null($this->info['ups_track_num']) || tep_not_null($this->info['ups_track_num2']) || tep_not_null($this->info['fedex_track_num']) || tep_not_null($this->info['fedex_track_num2']) || tep_not_null($this->info['dhl_track_num']) || tep_not_null($this->info['dhl_track_num2'])){
              $trackingLinks = "\n" . EMAIL_TEXT_TRACKING_NUMBER . "\n";
              
              if (tep_not_null($this->info['usps_track_num'])){
                  $trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_USPS1') . ' ' . 
                                    '<a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . str_replace(' ', '', $this->info['usps_track_num']) . '">' . $this->info['usps_track_num'] . '</a>' . "\n";
              }
              
              if (tep_not_null($this->info['usps_track_num2'])){
                  $trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_USPS2') . ' ' . 
                                    '<a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=' . str_replace(' ', '', $this->info['usps_track_num2']) . '">' . $this->info['usps_track_num2'] . '</a>' . "\n";
              }
              
              if (tep_not_null($this->info['ups_track_num'])) {
                  $trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_UPS1') . ' ' . 
                                    '<a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . str_replace(' ', '', $this->info['ups_track_num']) . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package">' . $this->info['ups_track_num'] . '</a>' . "\n";
              }
              
              if (tep_not_null($this->info['ups_track_num2'])) {
                  $trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_UPS2') . ' ' . 
                                    '<a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=' . str_replace(' ', '', $this->info['ups_track_num2']) . '&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package">' . $this->info['ups_track_num2'] . '</a>' . "\n";
              }

              if (tep_not_null($this->info['fedex_track_num'])) {
                  $trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_FEDEX1') . ' ' . 
                                    '<a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=' . str_replace(' ', '', $this->info['fedex_track_num']) . '&action=track&language=english&cntry_code=us">' . $this->info['fedex_track_num'] . '</a>' . "\n";
              }
              
              if (tep_not_null($this->info['fedex_track_num2'])) {
                  $trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_FEDEX2') . ' ' . 
                                    '<a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=' . str_replace(' ', '', $this->info['fedex_track_num2']) . '&action=track&language=english&cntry_code=us">' . $this->info['fedex_track_num2'] . '</a>' . "\n";
              }
              
              if (tep_not_null($this->info['dhl_track_num'])) {
                  $trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_DHL1') . ' ' . 
                                    '<a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . str_replace(' ', '', $this->info['dhl_track_num']) . '&action=track&language=english&cntry_code=us">' . $this->info['dhl_track_num'] . '</a>' . "\n";
              }
              
              if (tep_not_null($this->info['dhl_track_num2'])) {
                  $trackingLinks .= sysLanguage::get('TEXT_INFO_TRACKING_DHL2') . ' ' . 
                                    '<a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=' . str_replace(' ', '', $this->info['dhl_track_num2']) . '&action=track&language=english&cntry_code=us">' . $this->info['dhl_track_num2'] . '</a>' . "\n";
              }
              $trackingLinks .="\n\n";
          }
			          
          $orderTotals = '';
          for ($i = 0, $n = sizeof($this->totals); $i < $n; $i++){
              $orderTotals .= "\n" . $this->totals[$i]['title'] . "\t" . $this->totals[$i]['text'] . "\t" . "\n";
          }
			
          $historyLink = false;
          $full_name = $this->customer['firstname'] . ' ' . $this->customer['lastname'];
          $orderID = $this->newOrderID;
          $status = $orders_status_array[$this->info['order_status']];;
          $datePurchased = tep_date_long($check_status['date_purchased']);
          
          require_once(DIR_WS_CLASSES.'email_events.php');
          $email_event = new email_event(ORDER_UPDATE_EMAIL);
          $email_event->sendEmail(array(
              'email' => $this->customer['email_address'],
              'name'  => $full_name
          ));
      }
      
      function _insertOrder(){
          $sql_data_array = array(
              'customers_id'                => $this->customer['id'],
              'customers_name'              => $this->customer['firstname'] . ' ' . $this->customer['lastname'],
              'customers_company'           => $this->customer['company'],
              'customers_street_address'    => $this->customer['street_address'],
              'customers_suburb'            => $this->customer['suburb'],
              'customers_city'              => $this->customer['city'],
              'customers_postcode'          => $this->customer['postcode'],
              'customers_state'             => $this->customer['state'],
              'customers_country'           => $this->customer['country']['title'],
              'customers_telephone'         => $this->customer['telephone'],
              'customers_email_address'     => $this->customer['email_address'],
              'customers_address_format_id' => $this->customer['format_id'],
              'delivery_name'               => $this->delivery['firstname'] . ' ' . $this->delivery['lastname'],
              'delivery_company'            => $this->delivery['company'],
              'delivery_street_address'     => $this->delivery['street_address'],
              'delivery_suburb'             => $this->delivery['suburb'],
              'delivery_city'               => $this->delivery['city'],
              'delivery_postcode'           => $this->delivery['postcode'],
              'delivery_state'              => $this->delivery['state'],
              'delivery_country'            => $this->delivery['country']['title'],
              'delivery_address_format_id'  => $this->delivery['format_id'],
              'billing_name'                => $this->billing['firstname'] . ' ' . $this->billing['lastname'],
              'billing_company'             => $this->billing['company'],
              'billing_street_address'      => $this->billing['street_address'],
              'billing_suburb'              => $this->billing['suburb'],
              'billing_city'                => $this->billing['city'],
              'billing_postcode'            => $this->billing['postcode'],
              'billing_state'               => $this->billing['state'],
              'billing_country'             => $this->billing['country']['title'],
              'billing_address_format_id'   => $this->billing['format_id'],
              'payment_method'              => $this->info['payment_method'],
              'cc_type'                     => $this->info['cc_type'],
              'cc_owner'                    => $this->info['cc_owner'],
              'cc_number'                   => $this->info['cc_number'],
              'cc_expires'                  => $this->info['cc_expires'],
              'date_purchased'              => 'now()',
              'orders_status'               => $this->info['order_status'],
              'currency'                    => $this->info['currency'],
              'currency_value'              => $this->info['currency_value']
          );
          if (isset($this->orderID)){
              tep_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = "' . $this->orderID . '"');
            return $this->orderID;
          }else{
              tep_db_perform(TABLE_ORDERS, $sql_data_array);
            return tep_db_insert_id();
          }
      }
      
      function _insertOrderTotals(){
        global $currencies;
          if (isset($this->orderID)){
              tep_db_query('delete from ' . TABLE_ORDERS_TOTAL . ' where orders_id = "' . $this->orderID . '"');
          }
          for ($i=0, $n=sizeof($this->order_totals); $i<$n; $i++) {
              if ($this->order_totals[$i]['code'] == 'CustomTotal'){
                  $this->order_totals[$i]['text'] = $currencies->format($this->order_totals[$i]['text']);
              }
              $sql_data_array = array(
                  'orders_id'  => $this->newOrderID,
                  'title'      => $this->order_totals[$i]['title'],
                  'text'       => $this->order_totals[$i]['text'],
                  'value'      => $this->order_totals[$i]['value'],
                  'class'      => $this->order_totals[$i]['code'],
                  'sort_order' => $this->order_totals[$i]['sort_order']
              );
              tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
          }
      }
      
      function _insertOrderStatusHistory(){
          $customer_notification = ($this->notify === true) ? '1' : '0';
          if (is_array($this->info['comments'])){
              for($i=0; $i<sizeof($this->info['comments']); $i++){
                  if (!empty($this->info['comments'][$i])){
                      $sql_data_array = array(
                          'orders_id'         => $this->newOrderID,
                          'orders_status_id'  => $this->info['order_status'],
                          'date_added'        => 'now()',
                          'customer_notified' => $customer_notification,
                          'comments'          => $this->info['comments'][$i]
                      );
                      tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
                  }
              }
          }else{
              $sql_data_array = array(
                  'orders_id'         => $this->newOrderID,
                  'orders_status_id'  => $this->info['order_status'],
                  'date_added'        => 'now()',
                  'customer_notified' => $customer_notification,
                  'comments'          => $this->info['comments']
              );
              tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
          }
      }
      
      function _doStockCheck($product){
          if (DOWNLOAD_ENABLED == 'true') {
              $stock_query_raw = "SELECT products_quantity, pad.products_attributes_filename, pa.products_attributes_id FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa ON p.products_id=pa.products_id LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad ON pa.products_attributes_id=pad.products_attributes_id WHERE p.products_id = '" . tep_get_prid($product['id']) . "'";
              // Will work with only one option for downloadable products
              // otherwise, we have to build the query dynamically with a loop
              $products_attributes = $product['attributes'];
              if (is_array($products_attributes)) {
                  $stock_query_raw .= " AND pa.options_id = '" . $products_attributes[0]['option_id'] . "' AND pa.options_values_id = '" . $products_attributes[0]['value_id'] . "'";
              }
              $stock_query = tep_db_query($stock_query_raw);
          } else {
              $stock_query = tep_db_query("select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . tep_get_prid($product['id']) . "'");
          }
          
          if (tep_db_num_rows($stock_query) > 0) {
              $stock_values = tep_db_fetch_array($stock_query);
              // do not decrement quantities if products_attributes_filename exists
              if ((DOWNLOAD_ENABLED != 'true') || (!$stock_values['products_attributes_filename'])) {
                  $stock_left = $stock_values['products_quantity'] - $product['qty'];
              } else {
                  $stock_left = $stock_values['products_quantity'];
              }
              
              tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = '" . $stock_left . "' where products_id = '" . tep_get_prid($product['id']) . "'");
              if ( ($stock_left < 1) && (STOCK_ALLOW_CHECKOUT == 'false') ) {
                  tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '0' where products_id = '" . tep_get_prid($product['id']) . "'");
              }
          }
      }
      
      function _insertOrderProduct($product){
          $sql_data_array = array(
              'orders_id'         => $this->newOrderID,
              'products_id'       => tep_get_prid($product['id']),
              'products_model'    => $product['model'],
              'products_name'     => $product['name'],
              'products_price'    => $product['price'],
              'final_price'       => $product['final_price'],
              'products_tax'      => $product['tax'],
              'products_quantity' => $product['qty']
          );
          if ($product['opID'] !== false){
              $sql_data_array['orders_products_id'] = $product['opID'];
          }
          tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
        return tep_db_insert_id();
      }
      
      function _insertOrderProductAttributes($productsID_string, $attributes, $orderProductID){
          if (isset($this->orderID)){
              tep_db_query('delete from ' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . ' where orders_id = "' . $this->orderID . '"');
          }
          $products_ordered_attributes = '';
          for ($j=0, $n2=sizeof($attributes); $j<$n2; $j++) {
              if (DOWNLOAD_ENABLED == 'true') {
                  $attributes_query = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pad.products_attributes_maxdays, pad.products_attributes_maxcount, pad.products_attributes_filename from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad on pa.products_attributes_id=pad.products_attributes_id where pa.products_id = '" . $productsID_string . "' and pa.options_id = '" . $attributes[$j]['option_id'] . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $attributes[$j]['value_id'] . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $_SESSION['languages_id'] . "' and poval.language_id = '" . $_SESSION['languages_id'] . "'");
              } else {
                  $attributes_query = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . tep_get_prid($productsID_string) . "' and pa.options_id = '" . $attributes[$j]['option_id'] . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $attributes[$j]['value_id'] . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $_SESSION['languages_id'] . "' and poval.language_id = '" . $_SESSION['languages_id'] . "'"); //rmh M-S_fixes
              }
              $attributes_values = tep_db_fetch_array($attributes_query);
              
              $sql_data_array = array(
                  'orders_id'               => $this->newOrderID,
                  'orders_products_id'      => $orderProductID,
                  'products_options'        => $attributes_values['products_options_name'],
                  'products_options_values' => $attributes_values['products_options_values_name'],
                  'options_values_price'    => $attributes_values['options_values_price'],
                  'price_prefix'            => $attributes_values['price_prefix']
              );
              tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);
                      
              if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
                  $sql_data_array = array(
                      'orders_id'                => $this->newOrderID,
                      'orders_products_id'       => $orderProductID,
                      'orders_products_filename' => $attributes_values['products_attributes_filename'],
                      'download_maxdays'         => $attributes_values['products_attributes_maxdays'],
                      'download_count'           => $attributes_values['products_attributes_maxcount'],
                      'download_type'            => $attributes_values['products_attributes_download_type']
                  );
                  tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
              }
              $products_ordered_attributes .= "\n\t" . $attributes_values['products_options_name'] . ' ' . $attributes_values['products_options_values_name'];
          }
        return $products_ordered_attributes;
      }
      
      function _insertCustomer(){
          $password = '';
          while (strlen($password) < 8) {
              $char = chr(tep_rand(0,255));
              if (eregi('^[a-z0-9]$', $char)) $password .= $char;
          }

          $sql_data_array = array(
              'customers_firstname'     => $this->customer['firstname'],
              'customers_lastname'      => $this->customer['lastname'],
              'customers_email_address' => $this->customer['email_address'],
              'customers_telephone'     => $this->customer['telephone'],
              'customers_fax'           => $this->customer['fax'],
              'customers_newsletter'    => 0,
              'customers_password'      => tep_encrypt_password($password)
          );
          tep_db_perform(TABLE_CUSTOMERS, $sql_data_array);
          $this->customer['id'] = tep_db_insert_id();

          $sql_data_array = array(
              'customers_id'         => $this->customer['id'],
              'entry_firstname'      => $this->customer['firstname'],
              'entry_lastname'       => $this->customer['lastname'],
              'entry_street_address' => $this->customer['street_address'],
              'entry_postcode'       => $this->customer['postcode'],
              'entry_city'           => $this->customer['city'],
              'entry_country_id'     => $this->customer['country']['id'],
              'entry_zone_id'        => $this->customer['zone_id'],
              'entry_state'          => $this->customer['state']
          );
          
          if (ACCOUNT_COMPANY == 'true') $sql_data_array['entry_company'] = $company;
          tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
          $address_id = tep_db_insert_id();

          tep_db_query("update " . TABLE_CUSTOMERS . " set customers_default_address_id = '" . (int)$address_id . "' where customers_id = '" . (int)$this->customer['id'] . "'");
          tep_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_date_of_last_logon, customers_info_number_of_logons, customers_info_date_account_created, customers_info_date_account_last_modified) values ('" . (int)$this->customer['id'] . "', now(), '1', now(), now())");

          $email_text = sprintf(EMAIL_NEW_CUSTOMER_GREET_NONE, $this->customer['firstname']);

          $email_text .= sprintf(EMAIL_NEW_CUSTOMER_WELCOME, $this->info['store_name']) . 
                         EMAIL_NEW_CUSTOMER_TEXT . 
                         sprintf(EMAIL_NEW_CUSTOMER_CONTACT, $this->info['store_owner_email']) . 
                         sprintf(EMAIL_NEW_CUSTOMER_WARNING, $this->info['store_owner_email']);
                         
          tep_mail($this->customer['firstname'] . ' ' . $this->customer['lastname'], $this->customer['email_address'], EMAIL_NEW_CUSTOMER_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      }
      
      function getInstalledModules($type){
          switch($type){
              case 'shipping':
                  return $this->shipping_modules;
              break;
              case 'order_total':
                  return $this->order_total_modules;
              break;
              case 'payment':
                  return $this->payment_modules;
              break;
          }
      }
      
      function getShippingQuotes($module, $method = ''){
        global $currencies, $cart, $order;
          if (strlen($module) <= 0) return;
          $order = $_SESSION['order'];
          // get all available shipping quotes
          if (!class_exists($module)){
              include(DIR_FS_CATALOG_LANGUAGES . $_SESSION['language'] . '/modules/shipping/' . $module . '.php');
              include(DIR_FS_CATALOG_MODULES . 'shipping/' . $module . '.php');
          }
          $class = new $module;
          $quotes = $class->quote();
          
          $free_shipping = false;
          $return = '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
          if ($free_shipping == true) {
              $return .= ' <tr>' . 
                         '  <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="2">' . 
                         '   <tr>' . 
                         '    <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>' . 
                         '    <td class="main" colspan="3"><b>' . sysLanguage::get('FREE_SHIPPING_TITLE') . '</b>&nbsp;' . $quotes[$i]['icon'] . '</td>' . 
                         '    <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>' . 
                         '   </tr>' . 
                         '   <tr id="defaultSelected" class="moduleRowSelected" onMouseOver="rowOverEffect(this)" onMouseOut="rowOutEffect(this)" onClick="selectRowEffect(this, 0)">' . 
                         '    <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>' . 
                         '    <td class="main" width="100%">' . sprintf(sysLanguage::get('FREE_SHIPPING_DESCRIPTION'), $currencies->format(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER)) . tep_draw_hidden_field('shipping', 'free_free') . '</td>' . 
                         '    <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>' . 
                         '   </tr>' . 
                         '  </table></td>' . 
                         ' </tr>';
          } else {
              $radio_buttons = 0;
              $noReturn = false;
              $return .= ' <tr>' . 
                         '  <td>' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>' . 
                         '  <td><table border="0" width="100%" cellspacing="0" cellpadding="2">';
              if (isset($quotes['error'])) {
                  $return .= '   <tr>' . 
                             '    <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>' . 
                             '    <td class="main" colspan="3">' . $quotes['error'] . '</td>' . 
                             '    <td width="10">' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>' . 
                             '   </tr>';
              } else {
                  for ($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++) {
                      if (tep_not_null($method)){
                          $checked = ($module . '_' . $method == $quotes['id'] . '_' . $quotes['methods'][$i]['id']);
                      }else{
                          $checked = (($quotes['id'] . '_' . $quotes['methods'][$i]['id'] == $this->info['shipping_module']) ? true : false);
                      }
                      $return .= '   <tr>' . "\n" . 
                                 '    <td class="main" width="75%">' . $quotes['methods'][$i]['title'] . '</td>' . 
                                 '    <td class="main">' . $currencies->format(tep_add_tax($quotes['methods'][$i]['cost'], (isset($quotes['tax']) ? $quotes['tax'] : 0))) . '</td>' . 
                                 '    <td class="main" align="right">' . tep_draw_radio_field('shipping', $quotes['id'] . '_' . $quotes['methods'][$i]['id'], $checked) . '</td>' . 
                                 '   </tr>';
                  }
              }
              $return .= '  </table></td>' . 
                         '  <td>' . tep_draw_separator('pixel_trans.gif', '10', '1') . '</td>' . 
                         ' </tr>';
          }
          $return .= '</table>';
        return $return;
      }
      
      function getAddressEdit($address){
          $aInfo = '';
          switch($address){
              case 'cAddress':
                  $aInfo = $this->customer;
              break;
              case 'bAddress':
                  $aInfo = $this->billing;
              break;
              case 'dAddress':
                  $aInfo = $this->delivery;
              break;
          }

          $check_query = tep_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . $aInfo['country_id'] . "'");
          $check_value = tep_db_fetch_array($check_query);
          if ($check_value['total'] > 0){
              $zones_array = array();
              $zones_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . $aInfo['country_id'] . "' order by zone_name");
              while ($zones_values = tep_db_fetch_array($zones_query)) {
                  $zones_array[] = array('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
              }
              $stateInput = tep_draw_pull_down_menu('state', $zones_array, $aInfo['state']);
          }else{
              $stateInput = tep_draw_input_field('state', $aInfo['state']);
          }
          
          $table = '<table cellpadding="2" cellspacing="0" border="0">' . 
                    '<tr>' . 
                     '<td><input type="text" name="firstname" value="' . $aInfo['firstname'] . '"> <input type="text" name="lastname" value="' . $aInfo['lastname'] . '"></td>' . 
                    '</tr>' . 
                    '<tr>' . 
                     '<td><input type="text" name="street_address" value="' . $aInfo['street_address'] . '"></td>' . 
                    '</tr>' . 
                    '<tr>' . 
                     '<td><input type="text" name="city" value="' . $aInfo['city'] . '">, ' . $stateInput . ' <input type="text" name="postcode" value="' . $aInfo['postcode'] . '" maxlength="8" size="8"></td>' . 
                    '</tr>' . 
                    '<tr>' . 
                     '<td>' . tep_draw_pull_down_menu('country_id', tep_get_countries(), $aInfo['country_id']) . '</td>' . 
                    '</tr>' . 
                   '</table>';
                   
        return $table;
      }
      
      function updateAddress(){
          $varName = '';
          switch($_GET['address']){
              case 'cAddress':
                  $varName = 'customer';
              break;
              case 'bAddress':
                  $varName = 'billing';
              break;
              case 'dAddress':
                  $varName = 'delivery';
              break;
          }
          
          $this->{$varName}['firstname'] = $_GET['firstname'];
          $this->{$varName}['lastname'] = $_GET['lastname'];
          $this->{$varName}['street_address'] = $_GET['street_address'];
          $this->{$varName}['city'] = $_GET['city'];
          $this->{$varName}['postcode'] = $_GET['postcode'];
          
          $zoneInfo = newOrder_getZoneInfo($_GET['state']);
          $this->{$varName}['state'] = $zoneInfo['zone_name'];
          $this->{$varName}['zone_id'] = $zoneInfo['zone_id'];
          
          $countryInfo = newOrder_getCountryInfo($_GET['country_id']);
          $this->{$varName}['country_id'] = $countryInfo['country_id'];
          $this->{$varName}['country'] = array(
              'id'         => $countryInfo['country_id'], 
              'title'      => $countryInfo['country_name'], 
              'iso_code_2' => $countryInfo['country_iso_code_2'], 
              'iso_code_3' => $countryInfo['country_iso_code_3']
          );
      }
      
      function loadOrderTotals() {
        global $language, $order;
          $order = $_SESSION['order'];
          $shipping = $_SESSION['shipping'];
          $car = $order;
          if (!empty($this->order_total_modules)) {
              reset($this->order_total_modules);
              while (list(, $value) = each($this->order_total_modules)) {
                  include(DIR_FS_CATALOG_LANGUAGES . $_SESSION['language'] . '/modules/order_total/' . $value);
                  include(DIR_FS_CATALOG_MODULES . 'order_total/' . $value);
                  
                  $class = substr($value, 0, strrpos($value, '.'));
                  $GLOBALS[$class] = new $class;
              }
          }
      }

      function orderTotalsProcess() {
        global $cart;
          $cart = $_SESSION['order'];
          $this->order_totals = array();
          $this->loadPricing();
          if (is_array($this->order_total_modules)) {
              reset($this->order_total_modules);
              $totalsIndex = 0;
              while (list(, $value) = each($this->order_total_modules)) {
                  $class = substr($value, 0, strrpos($value, '.'));
                  if ($GLOBALS[$class]->enabled) {
                      $GLOBALS[$class]->process();
                      for ($i=0, $n=sizeof($GLOBALS[$class]->output); $i<$n; $i++) {
                          if (tep_not_null($GLOBALS[$class]->output[$i]['title']) && tep_not_null($GLOBALS[$class]->output[$i]['text'])) {
                              $this->order_totals[$totalsIndex] = array(
                                  'code'       => $GLOBALS[$class]->code,
                                  'title'      => $GLOBALS[$class]->output[$i]['title'],
                                  'text'       => $GLOBALS[$class]->output[$i]['text'],
                                  'value'      => $GLOBALS[$class]->output[$i]['value'],
                                  'sort_order' => $totalsIndex
                              );
                              if ($GLOBALS[$class]->code == 'ot_subtotal'){
                                  $subtotalIndex = $totalsIndex;
                              }
                              if ($GLOBALS[$class]->code == 'ot_total'){
                                  $totalIndex = $totalsIndex;
                              }
                              $totalsIndex++;
                          }
                      }
                  }
              }
          }
          
          $customTotals = array();
          if (!isset($_GET['customTotal'])){
              $Qcheck = tep_db_query('select * from ' . TABLE_ORDERS_TOTAL . ' where orders_id = "' . $this->orderID . '" and class = "CustomTotal"');
              while($check = tep_db_fetch_array($Qcheck)){
                  $customTotals[$check['sort_order']] = array(
                      'code'       => $check['class'],
                      'text'       => $check['title'],
                      'value'      => $check['value'],
                      'sort_order' => $check['sort_order']
                  );
              }
          }else{
              $customTotals = $_GET['customTotal'];
          }
          
          if (sizeof($customTotals) > 0){
              global $currencies;
              if (!empty($this->order_totals)){
                  foreach($customTotals as $index => $array){
                      if (isset($subtotalIndex) && $subtotalIndex >= $index){
                          $this->info['subtotal'] += $array['value'];
                          $this->order_totals[$subtotalIndex]['value'] = $this->info['subtotal'];
                          $this->order_totals[$subtotalIndex]['text'] = $currencies->format($this->info['subtotal'], true, $this->info['currency'], $this->info['currency_value']);
                      }
                      
                      if (isset($totalIndex) && $totalIndex >= $index){
                          $this->info['total'] += $array['value'];
                          $this->order_totals[$totalIndex]['value'] = $this->info['total'];
                          $this->order_totals[$totalIndex]['text'] = '<b>' . $currencies->format($this->info['total'], true, $this->info['currency'], $this->info['currency_value']) . '</b>';
                      }
                  }
                  
                  $newTotals = array();
                  $newIndex = 0;
                  foreach($this->order_totals as $index => $array){
                      if (isset($customTotals[$index])/* && !empty($customTotals[$index]['text']) && !empty($customTotals[$index]['value'])*/){
                          $newTotals[$newIndex] = array(
                              'code'       => 'CustomTotal',
                              'title'      => $customTotals[$index]['text'],
                              'text'       => $customTotals[$index]['value'],
                              'value'      => $customTotals[$index]['value'],
                              'sort_order' => $newIndex
                          );
                          $newIndex++;
                          if ($this->order_totals[$index]['text'] != $customTotals[$index]['text']){
                              $this->order_totals[$index]['sort_order'] = $newIndex;
                              $newTotals[$newIndex] = $this->order_totals[$index];
                              $newIndex++;
                          }
                          unset($customTotals[$index]);
                      }else{
                          $newTotals[$newIndex] = $this->order_totals[$index];
                          $newIndex++;
                      }
                  }
                  if (!empty($customTotals)){
                      foreach($customTotals as $index => $array){
                          //if (!empty($customTotals[$index]['text']) && !empty($customTotals[$index]['value'])){
                              $newTotals[$newIndex] = array(
                                  'code'       => 'CustomTotal',
                                  'title'      => $array['text'],
                                  'text'       => $array['value'],
                                  'value'      => $array['value'],
                                  'sort_order' => $newIndex
                              );
                              $newIndex++;
                         // }
                      }
                  }
                  
                  $this->order_totals = array();
                  $tIndex = 0;
                  reset($newTotals);
                  foreach($newTotals as $tInfo){
                      $this->order_totals[$tIndex] = array(
                          'code'       => $tInfo['code'],
                          'title'      => $tInfo['title'],
                          'text'       => $tInfo['text'],
                          'value'      => $tInfo['value'],
                          'sort_order' => $tIndex
                      );
                      $tIndex++;
                  }
              }
          }
        return $this->order_totals;
      }
      
      function sendOrderEmail(){
        global $currencies;
          $products_ordered_attributes = '';
          for ($j=0, $n2=sizeof($attributes); $j<$n2; $j++) {
              if (DOWNLOAD_ENABLED == 'true') {
                  $attributes_query = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pad.products_attributes_maxdays, pad.products_attributes_maxcount, pad.products_attributes_filename from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad on pa.products_attributes_id=pad.products_attributes_id where pa.products_id = '" . $productsID_string . "' and pa.options_id = '" . $attributes[$j]['option_id'] . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $attributes[$j]['value_id'] . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $_SESSION['languages_id'] . "' and poval.language_id = '" . $_SESSION['languages_id'] . "'");
              } else {
                  $attributes_query = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . tep_get_prid($productsID_string) . "' and pa.options_id = '" . $attributes[$j]['option_id'] . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $attributes[$j]['value_id'] . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $_SESSION['languages_id'] . "' and poval.language_id = '" . $_SESSION['languages_id'] . "'"); //rmh M-S_fixes
              }
              $attributes_values = tep_db_fetch_array($attributes_query);
              
              $products_ordered_attributes .= "\n\t" . $attributes_values['products_options_name'] . ' ' . $attributes_values['products_options_values_name'];
          }
          
          for ($i=0, $n=sizeof($this->products); $i<$n; $i++) {
              $products_ordered .= $this->products[$i]['qty'] . ' x ' . $this->products[$i]['name'] . ' (' . $this->products[$i]['model'] . ') = ' . $currencies->display_price($this->products[$i]['final_price'], $this->products[$i]['tax'], $this->products[$i]['qty']) . $products_ordered_attributes . "\n";
          }
          
          $this->_notifyCustomer($products_ordered);
      }
      
      function orderTotalsOutput() {
          $output_string = '';
          if (is_array($this->order_totals)) {
              foreach($this->order_totals as $index => $output){
                  if ($output['code'] == 'CustomTotal'){
                      $output_string .= '<tr sort_order="' . $index . '">' . "\n" .
                                        ' <td align="right" class="main">' . tep_image(DIR_WS_IMAGES . 'icons/cross.gif', 'Remove Order Total', '', '', 'id="delete"') . tep_image(DIR_WS_IMAGES . 'up.gif', 'Move Up', '', '', 'id="up"') . tep_image(DIR_WS_IMAGES . 'down.gif', 'Move Down', '', '', 'id="down"') . '&nbsp;<input id="customTotalText" name="customTotal[' . $index . '][text]" type="text" value="' . $output['title'] . '"></td>' . "\n" .
                                        ' <td align="right" class="main">$<input size="6" id="customTotalVal" name="customTotal[' . $index . '][value]" type="text" value="' . $output['value'] . '"></td>' . "\n" .
                                        '</tr>';
                  }else{
                      $output_string .= '<tr sort_order="' . $index . '">' . "\n" .
                                        ' <td align="right" class="main">' . $output['title'] . '</td>' . "\n" .
                                        ' <td align="right" class="main">' . $output['text'] . '</td>' . "\n" .
                                        '</tr>';
                  }
              }
          }
        return $output_string;
      }
      
      function getProductEditPage($pID){
          $pInfo = $this->get_products($pID);
          $table = '<table cellpadding="3" cellspacing="0" border="0">' . 
                    '<tr>' . 
                     '<td class="main">Products Name: </td>' . 
                     '<td class="main"><input type="hidden" name="pID" value="' . $pInfo[0]['id'] . '">' . $pInfo[0]['name'] . '</td>' . 
                    '</tr>' . 
                    '<tr>' . 
                     '<td class="main">Products Quantity: </td>' . 
                     '<td><input type="text" name="products_qty" value="' . $pInfo[0]['quantity'] . '"></td>' . 
                    '</tr>' . 
                    '<tr>' . 
                     '<td class="main">Products Price: </td>' . 
                     '<td class="main"><input type="text" name="products_price" value="' . $pInfo[0]['price'] . '"><br>*This is the product price without the attributes added price, if one exists.</td>' . 
                    '</tr>' . 
                    '<tr>' . 
                     '<td class="main" valign="top">Products Attributes: </td>' . 
                     '<td>' . $this->getAttributesBlock($pInfo[0]['id']) . '</td>' . 
                    '</tr>' . 
                   '</table>';
                   
          if (isset($pInfo[0]['reservation'])){
              $table .= '<fieldset style="width:75%">' . 
                         '<legend>Reservation Info</legend>' . 
                         '<table cellpadding="3" cellspacing="0" border="0">' . 
                          '<tr>' . 
                           '<td class="main">Start Date: </td>' . 
                           '<td><input type="text" name="start_date" value="' . $pInfo[0]['reservation']['start_date'] . '"></td>' . 
                          '</tr>' . 
                          '<tr>' . 
                           '<td class="main">End Date: </td>' . 
                           '<td><input type="text" name="end_date" value="' . $pInfo[0]['reservation']['end_date'] . '"></td>' . 
                          '</tr>' . 
                          '<tr>' . 
                           '<td class="main">Insured: </td>' . 
                           '<td><input type="hidden" name="insure_per" value="' . $pInfo[0]['reservation']['insure_per'] . '"><input type="checkbox" name="insure" value="Y"' . ($pInfo[0]['reservation']['insure'] == 'Y' ? ' CHECKED' : '') . '></td>' . 
                          '</tr>' . 
                          '<tr>' . 
                           '<td class="main" valign="top">Shipping Method: </td>' . 
                           '<td>' . $this->getShippingQuotes('reservation', $pInfo[0]['reservation']['shipping']['id']) . '</td>' . 
                          '</tr>' . 
                         '</table>' . 
                        '</fieldset>';
          }
                   
        return $table;
      }
      
    function calcPrice($price, $length){
        return ($price * $length);
    }
    
    function findBestPrice($productPricing, $rentalLength, $scheme){
        if ($rentalLength == $scheme['begin'] || $rentalLength == $scheme['end']){
            $returnPrice = $this->calcPrice($productPricing['weekly'], ($rentalLength/7));
            $return = array(
                'price' => $returnPrice,
                'weeks' => ($rentalLength/7),
                'days' => '0'
            );
        }else{
            $returnPrice = $this->calcPrice($productPricing['daily'], $rentalLength);
            $return = array(
                'price' => $returnPrice,
                'weeks' => '0',
                'days' => $rentalLength
            );
            if ($rentalLength > $scheme['begin'] && $rentalLength < $scheme['end']){
                $weeks = floor($rentalLength/7);
                $days = ($rentalLength - ($weeks*7));
                $testPrice = $this->calcPrice($productPricing['daily'], $days);
                $testPrice += $this->calcPrice($productPricing['weekly'], $weeks);
                if ($testPrice < $returnPrice){
                    $returnPrice = $testPrice;
                    $return = array(
                        'price' => $returnPrice,
                        'weeks' => $weeks,
                        'days'  => $days
                    );
                }
            }
         }
         
       return $return;
    }
    
    function addRentalDays($date, $days){
        $strTime = strtotime($date);
        $day = (60 * 60 * 24);
        if ($days < 0){
            while($days < 0){
                $strTime -= $day;
                $days++;
            }
        }else{
            while($days > 0){
                $strTime += $day;
                $days--;
            }
        }
      return date('Y-m-d', $strTime);
    }
    
    function getRentalLength($start_date, $end_date){
        return ((strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24));
    }
    
    function getReservationPrice($productPricing, $rentalLength){
        if ($rentalLength < 7){
            $scheme = array(
                'begin' => 1,
                'end'   => 7,
                'weeks' => 1
            );
            $returnPrice = $this->findBestPrice($productPricing, $rentalLength, $scheme);
        }elseif ($rentalLength >= 7 && $rentalLength < 14){
            $scheme = array(
                'begin' => 7,
                'end'   => 14,
                'weeks' => 1
            );
            $returnPrice = $this->findBestPrice($productPricing, $rentalLength, $scheme);
        }elseif ($rentalLength >= 14 && $rentalLength < 21){
            $scheme = array(
                'begin' => 14,
                'end'   => 21,
                'weeks' => 1
            );
            $returnPrice = $this->findBestPrice($productPricing, $rentalLength, $scheme);
        }elseif ($rentalLength >= 21 && $rentalLength < 28){
            $scheme = array(
                'begin' => 21,
                'end'   => 28,
                'weeks' => 2
            );
            $returnPrice = $this->findBestPrice($productPricing, $rentalLength, $scheme);
        }else{
            $returnPrice = 'More Than 28 Days Is Not Currently Supported';
        }

        if (is_array($returnPrice)){
            if (isset($productPricing['insure']) && $productPricing['insure'] == 'Y'){
                $returnPrice['price'] += ($returnPrice['price'] * ((float)RENTAL_ONE_TIME_INSURANCE_PER/100));
            }
            
            if (isset($productPricing['shipping'])){
                $returnPrice['price'] += $productPricing['shipping'];
            }
        }
        
      return $returnPrice;
    }      
 }

////
// Return a formatted address
// TABLES: customers, address_book
  function tep_address_label($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {
    $address_query = tep_db_query("select entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customers_id . "' and address_book_id = '" . (int)$address_id . "'");
    $address = tep_db_fetch_array($address_query);

    $format_id = tep_get_address_format_id($address['country_id']);

    return tep_address_format($format_id, $address, $html, $boln, $eoln);
  }
  
////
// Returns the address_format_id for the given country
// TABLES: countries;
  function tep_get_address_format_id($country_id) {
    $address_format_query = tep_db_query("select address_format_id as format_id from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$country_id . "'");
    if (tep_db_num_rows($address_format_query)) {
      $address_format = tep_db_fetch_array($address_format_query);
      return $address_format['format_id'];
    } else {
      return '1';
    }
  }

////
// Check if product has attributes
  function tep_has_product_attributes($products_id) {
    $attributes_query = tep_db_query("select count(*) as count from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$products_id . "'");
    $attributes = tep_db_fetch_array($attributes_query);

    if ($attributes['count'] > 0) {
      return true;
    } else {
      return false;
    }
  }
  
  function tep_get_address_country($id){
      $Qcountry = tep_db_query('select entry_country_id from ' . TABLE_ADDRESS_BOOK . ' where address_book_id = "' . $id . '"');
      $country = tep_db_fetch_array($Qcountry);
    return $country['entry_country_id'];
  }
  
  function tep_get_address_zone($id){
      $Qzone = tep_db_query('select entry_zone_id from ' . TABLE_ADDRESS_BOOK . ' where address_book_id = "' . $id . '"');
      $zone = tep_db_fetch_array($Qzone);
    return $zone['entry_zone_id'];
  }
////
// Return the tax description for a zone / class
// TABLES: tax_rates;
  function tep_get_tax_description($class_id, $country_id, $zone_id) {
    $tax_query = tep_db_query("select tax_description from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' order by tr.tax_priority");
    if (tep_db_num_rows($tax_query)) {
      $tax_description = '';
      while ($tax = tep_db_fetch_array($tax_query)) {
        $tax_description .= $tax['tax_description'] . ' + ';
      }
      $tax_description = substr($tax_description, 0, -3);

      return $tax_description;
    } else {
      return sysLanguage::get('TEXT_UNKNOWN_TAX_RATE');
    }
  }
////
// Checks to see if the currency code exists as a currency
// TABLES: currencies
  function tep_currency_exists($code) {
    $code = tep_db_prepare_input($code);

    $currency_code = tep_db_query("select currencies_id from " . TABLE_CURRENCIES . " where code = '" . tep_db_input($code) . "'");
    if (tep_db_num_rows($currency_code)) {
      return $code;
    } else {
      return false;
    }
  }
////
// Creates a pull-down list of countries
  function tep_get_country_list($name, $selected = '', $parameters = '') {
    $countries_array = array(array('id' => '', 'text' => sysLanguage::get('PULL_DOWN_DEFAULT')));
    $countries = tep_get_countries();
    
	$cIndex = 3;
    for ($i=0, $n=sizeof($countries); $i<$n; $i++) {
	  if ($countries[$i]['text'] == 'Canada'){
         $countries_array[2] = array('id' => $countries[$i]['id'], 'text' => $countries[$i]['text']);
	  }elseif ($countries[$i]['text'] == 'United States'){
         $countries_array[1] = array('id' => $countries[$i]['id'], 'text' => $countries[$i]['text']);
	  }else{
         $countries_array[$cIndex] = array('id' => $countries[$i]['id'], 'text' => $countries[$i]['text']);
		 $cIndex++;
	  }
    }

    return tep_draw_pull_down_menu($name, $countries_array, $selected, $parameters);
  }

  function newOrder_getZoneInfo($zone_name){
      $Qzone = tep_db_query('select * from ' . TABLE_ZONES . ' where zone_name = "' . $zone_name . '"');
      $zone = tep_db_fetch_array($Qzone);
    return array(
        'zone_id'   => $zone['zone_id'],
        'zone_code' => $zone['zone_code'],
        'zone_name' => $zone['zone_name']
    );
  }
      
  function newOrder_getCountryInfo($country_name){
      if (is_numeric($country_name)){
          $Qcountry = tep_db_query('select * from ' . TABLE_COUNTRIES . ' where countries_id = "' . $country_name . '"');
      }else{
          $Qcountry = tep_db_query('select * from ' . TABLE_COUNTRIES . ' where countries_name = "' . $country_name . '"');
      }
      $country = tep_db_fetch_array($Qcountry);
    return array(
        'country_id'         => $country['countries_id'],
        'country_name'       => $country['countries_name'],
        'country_iso_code_2' => $country['countries_iso_code_2'],
        'country_iso_code_3' => $country['countries_iso_code_3']
    );
  }

  function newOrder_getShippingQuote($module, $method){
    global $currencies, $cart, $order;
      $order = $_SESSION['order'];
      $cart = $_SESSION['order'];
      $mInfo = array($module, $method);
      if ($mInfo[0] == 'free'){
          $quotes['methods'][0] = array(
              'id'    => 'free',
              'title' => 'Free Shipping',
              'cost'  => 0.00,
              'days'  => 0
          );
      }else{
          if (!class_exists($mInfo[0])){
              include(DIR_FS_CATALOG_LANGUAGES . $_SESSION['language'] . '/modules/shipping/' . $mInfo[0] . '.php');
              include(DIR_FS_CATALOG_MODULES . 'shipping/' . $mInfo[0] . '.php');
          }
          $class = new $mInfo[0];
          $quotes = $class->quote($mInfo[1]);
      }
    return array(
        'id'    => $quotes['methods'][0]['id'],
        'title' => $quotes['methods'][0]['title'],
        'cost'  => $quotes['methods'][0]['cost'],
        'days'  => $quotes['methods'][0]['days']
    );
  }
  
  function newOrder_getOrdersProductsArrtibutes($ordersProductsID, $productsID){
      $attributes = array();
      $QordersAttributes = tep_db_query('select * from ' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . ' where orders_products_id = "' . $ordersProductsID . '"');
      if (tep_db_num_rows($QordersAttributes)){
          while($ordersAttributes = tep_db_fetch_array($QordersAttributes)){
              $Qoption = tep_db_query('select po.products_options_id from ' . TABLE_PRODUCTS_ATTRIBUTES . ' pa, ' . TABLE_PRODUCTS_OPTIONS . ' po, ' . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . ' po2p where pa.products_id = "' . $productsID . '" and pa.options_id = po.products_options_id and po2p.products_options_id = po.products_options_id and po.language_id = "' . $_SESSION['languages_id'] . '" and po.products_options_name = "' . $ordersAttributes['products_options'] . '"');
              if (tep_db_num_rows($Qoption)){
                  $option = tep_db_fetch_array($Qoption);
                  
                  $Qvalue = tep_db_query('select ov.products_options_values_id from ' . TABLE_PRODUCTS_ATTRIBUTES . ' pa, ' . TABLE_PRODUCTS_OPTIONS_VALUES . ' ov, ' . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . ' po2p where pa.products_id = "' . $productsID . '" and pa.options_values_id = ov.products_options_values_id and po2p.products_options_id = "' . $option['products_options_id'] . '" and ov.products_options_values_name = "' . $ordersAttributes['products_options_values'] . '"');
                  if (tep_db_num_rows($Qvalue)){
                      $value = tep_db_fetch_array($Qvalue);
                      $attributes[$option['products_options_id']] = $value['products_options_values_id'];
                  }
              }
          }
      }
    return $attributes;
  }
  
  function newOrder_getOrdersProductsReservation($ordersProductsID){
      $Qcheck = tep_db_query('select * from rental_bookings where orders_products_id = "' . $ordersProductsID . '"');
      if (tep_db_num_rows($Qcheck)){
          $check = tep_db_fetch_array($Qcheck);
          $quote = newOrder_getShippingQuote('reservation', $check['shipping_method']);
          $reservationInfo = array(
              'id'            => $check['rental_booking_id'],
              'start_date'    => $check['start_date'],
              'end_date'      => $check['end_date'],
              'insure'        => $check['insure'],
              'insure_per'    => $check['insure_per'],
              'quantity'      => $check['rental_qty'],
              'shipping'      => array(
                  'module' => 'reservation',
                  'method' => $check['shipping_method'],
                  'title'  => $quote['title'],
                  'cost'   => $quote['cost'],
                  'id'     => $quote['id'],
                  'days'   => $quote['days']
              )
          );
      }else{
          $reservationInfo = '';
      }
    return $reservationInfo;
  }
  
?>