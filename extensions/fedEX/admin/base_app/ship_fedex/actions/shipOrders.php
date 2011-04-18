<?php

    $debug = 0;

    $order = $_GET['oID'];

    $lastshipment = sysConfig::get('EXTENSION_FED_EX_LASTSHIPMENT'); // Define last shipment time (ex: 17 would be 5pm on your server)

    $send_email_on_shipping = 1; // Set to 0 to disable, set to 1 to enable automatic email of tracking number

    $thermal_printing = (sysConfig::get('EXTENSION_FED_EX_PRINTING') == 'Thermal') ? 1 : 0; // set the printing type, thermal_printing = 0 for laser, thermal_printing = 1 for label printer

    $order = $_GET['oID'];



    if (isset($_POST['package_num'])) {

        $package_num = $_POST['package_num'];

    }



    $fedex_gateway = sysConfig::get('EXTENSION_FED_EX_SERVER');

    // array of characters we don't want in phone numbers

    $unwanted = array('(', ')', '-', '.', ' ', '/');



    $transaction_code = 21; // 21 is a ship request



    // get the country we're shipping from



    $country = tep_get_country_name(sysConfig::get('STORE_COUNTRY'));

    // abbreviate it for fedex (United States = US etc.)

    $senders_country = abbreviate_country($country);



    // get sender's fedex info from configuration table

    // (requires installation & configuration of FedEx RealTime Quotes)



    $fedexVars = array(

        10 => sysConfig::get('EXTENSION_FED_EX_ACCOUNT'), // 0

        498 => sysConfig::get('EXTENSION_FED_EX_METER'), // 1

        75 => sysConfig::get('EXTENSION_FED_EX_WEIGHT'), // 2

        4 => sysConfig::get('STORE_NAME'), // 3

        5 => sysConfig::get('EXTENSION_FED_EX_ADDRESS1'), // 4

        6 => sysConfig::get('EXTENSION_FED_EX_ADDRESS2'), // 5

        7 => sysConfig::get('EXTENSION_FED_EX_CITY'), // 6

        8 => sysConfig::get('EXTENSION_FED_EX_STATE'), // 7

        9 => sysConfig::get('EXTENSION_FED_EX_POSTAL'), // 8

        183 => sysConfig::get('EXTENSION_FED_EX_PHONE'), // 9

        68 => Session::get('currency'), // 10

    );



    // create new FedExDC object

    $fed = new FedExDC($fedexVars[10], $fedexVars[498]);



    // get all information from the order record

    $QOrders = Doctrine_Query::create()

            ->from('Orders o')

            ->leftJoin('o.OrdersProducts op')

            ->leftJoin('o.OrdersTotal ot')

            ->leftJoin('o.OrdersAddresses a')

            ->andWhere('o.orders_id = ?', $order)

            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);



    if (count($QOrders)) {

        $orderInfo['customers_telephone'] = $QOrders[0]['customers_telephone'];

        $orderInfo['customers_email_address'] = $QOrders[0]['customers_email_address'];

        foreach ($QOrders[0]['OrdersAddresses'] as $oaInfo) {

            if ($oaInfo['address_type'] == 'billing') {

                $orderInfo['customers_name'] = $oaInfo['entry_name'];

                $orderInfo['customers_company'] = $oaInfo['entry_company'];

                $orderInfo['customers_address'] = $oaInfo['entry_street_address'];

                $orderInfo['customers_city'] = $oaInfo['entry_city'];

                $orderInfo['customers_state'] = $oaInfo['entry_state'];

                $orderInfo['customers_country'] = $oaInfo['entry_country'];

                $orderInfo['customers_postcode'] = $oaInfo['entry_postcode'];

            } else if ($oaInfo['address_type'] == 'delivery') {

                $orderInfo['delivery_name'] = $oaInfo['entry_name'];

                $orderInfo['delivery_company'] = $oaInfo['entry_company'];

                $orderInfo['delivery_address'] = $oaInfo['entry_street_address'];

                $orderInfo['delivery_city'] = $oaInfo['entry_city'];

                $orderInfo['delivery_state'] = $oaInfo['entry_state'];

                $orderInfo['delivery_country'] = $oaInfo['entry_country'];

                $orderInfo['delivery_postcode'] = $oaInfo['entry_postcode'];

            }

        }



    // abbreviate the delivery state (function is in abbreviate.php)

        $orderInfo['delivery_company'] = $_POST['delivery_company'];

        $orderInfo['delivery_address'] = $_POST['delivery_address'];

        $orderInfo['delivery_state'] = $_POST['delivery_state'];

        $orderInfo['delivery_city'] = $_POST['delivery_city'];

        $orderInfo['delivery_postcode'] = $_POST['delivery_postcode'];

        $orderInfo['delivery_name'] = $_POST['delivery_name'];

        $orderInfo['delivery_phone'] = $_POST['delivery_phone'];



        $delivery_state = abbreviate_state($orderInfo['delivery_state']);



    // abbreviate the delivery country (function is in abbreviate.php)

        $delivery_country = abbreviate_country($orderInfo['delivery_country']);



    // get rid of dashes, parentheses and periods in customer's telephone number

        $delivery_phone = trim(str_replace($unwanted, '', $orderInfo['delivery_phone']));

        $date_array = explode('-', $_POST['pickup_date']);

        $corrected_date = $date_array[2] . $date_array[0] . $date_array[1];



    // determine whether the ship date is today or later

        if ($corrected_date == date('Ymd')) {

            $future = 'N'; // today

        }

        else {

            $future = 'Y'; // later date

        }



    // start the array for fedex

        $shipData = array(

            0 => $transaction_code// transaction code

            , 2399 => $_POST['signature_type']// signature type

            , 16 => $delivery_state// delivery state

            , 13 => $orderInfo['delivery_address']// delivery address

            , 1273 => $_POST['packaging_type']// packaging type (01 is customer packaging)

            , 1274 => $_POST['service_type']//

            , 18 => $delivery_phone// customer's phone number

            , 15 => $orderInfo['delivery_city']

            , 23 => $_POST['bill_type']// payment type (1 is bill to sender)

            , 117 => $senders_country// sender's country

            , 17 => $orderInfo['delivery_postcode']// postal code it's going to

            , 50 => $delivery_country// country it's going to

            , 11 => $orderInfo['delivery_company']// recipient's company name

            , 12 => name_case($orderInfo['delivery_name'])// recipient's contact name

            , 1333 => $_POST['dropoff_type']// drop off type (1 is regular pickup)

            , 1415 => $declare_value . '.00'// total order value, forced to 2 decimal places

            , 1368 => 2// label type (2 is standard)

            , 1369 => 1// printer type (1 is laser)

            , 1370 => (($thermal_printing) ? 7 : 5)// label media (5 is plain paper, 7 is 4x6 image)

            , 3002 => $_POST['package_invoice']// invoice number

            , 25 => $_POST['package_reference']// reference number

            , 3001 => $_POST['package_po']// purchase order number

            , 38 => $_POST['package_department']// department name

            , 24 => $corrected_date// ship date

            , 1119 => $future// future day shipment

            , 2975 => 'Y'// keep your fedex number off the label

            , 1266 => $_POST['saturday_delivery']// Saturday delivery

            , 1200 => $_POST['hold_at_location']// Hold At Fedex Location

            , 44 => $_POST['hal_address']// Hold At Location address

            , 46 => $_POST['hal_city']// Hold At Location city

            , 47 => $_POST['hal_state']// Hold At Location state

            , 48 => $_POST['hal_postcode']// Hold At Location postal code

            , 49 => $_POST['hal_phone']// Hold At Location phone number

            , 57 => $_POST['dim_height']// "your package" height dimension

            , 58 => $_POST['dim_width']// "your package" width dimension

            , 59 => $_POST['dim_length']// "your package length dimension

            , 3008 => $_POST['autopod']// Automatic Proof of Delivery

        );



    // if it's home delivery (90), add the "residential delivery flag" (440)

        if ($_POST['service_type'] == 90) {

            $shipData[440] = 'Y';

            $residential = 'Y';

        }

        else {

            $shipData[440] = 'N';

            $residential = 'N';

        }





    // if it's a Saturday delivery

        if ($_POST['service_type'] == 03 && $_POST['saturday_delivery'] == 1) {

            $shipData[1266] = 'Y';

        }

        if ($_POST['service_type'] == 01 && $_POST['saturday_delivery'] == 1) {

            $shipData[1266] = 'Y';

        }

        else {

            $shipData[1266] = 'N';

        }

    // if it's a Hold At Fedex Location delivery

        if ($_POST['hold_at_location'] == 1) {

            $shipData[1200] = 'Y';

        }

        else {

            $shipData[1200] = 'N';

        }



    // if AutoPOD, add AutoPOD flag

        if ($_POST['autopod'] == 1) {

            $shipData[3008] = 'Y';

        }

        else {

            $shipData[3008] = 'N';

        }



    // if "your package" type is selected, add the dimensions flag

        if ($_POST['packaging_type'] == 01) {

            $shipData[57] = $_POST['dim_height'];

            $shipData[58] = $_POST['dim_width'];

            $shipData[59] = $_POST['dim_length'];

        }





    // if it's an oversized shipment...

        if ($oversized) {

            $shipData[3124] = $_POST['oversized'];

        }



    ////

    // if there's no meter number in the database, ask for a new one

        if (!$fedexVars[498]) {

            $fed = new FedExDC($fedexVars[10]);



    // variables needed to subscribe

            $requestData = array(

                0 => 211, // 211 is the transaction code for a subscription request

                10 => $fedexVars[10], // account number

                4003 => $fedexVars[4], // contact name, using store name for now

                4008 => $fedexVars[5], // street address

                4011 => $fedexVars[7], // city

                4012 => $fedexVars[8], // state

                4013 => $fedexVars[9], // postal code

                4014 => $senders_country, // country

                4015 => $fedexVars[183] // phone number

            );



            $keyRequest = $fed->subscribe($requestData);

            if ($error = $fed->getError()) {

                echo '<pre>';

                print_r($requestData);

                echo '</pre>';

                echo '<pre>';

                print_r($keyRequest);

                echo '</pre>';

                die("ERROR: " . $error);

            }else {

                $configTable = Doctrine_Core::getTable('Configuration')->findOneByConfigurationKey('EXTENSION_FED_EX_METER');

                $configTable->configuration_value = $keyRequest[498];

                $configTable->save();                

                $fedexVars[498] = $keyRequest[498];

            }

        }

       $shipData = $shipData + $fedexVars;



    // determine shipment type (either ground or express)

    // (this is used in the call to fedexdc.php)

        if (($_POST['service_type'] == 92) or ($_POST['service_type'] == 90)) {

            $ship_type = 'ship_ground';

        }

        else {

            $ship_type = 'ship_express';

        }



    ////

    // request shipment(s) and post data to shipping manifest



        for ($i = 1; $_POST['package_num'] >= $i; $i++) {



            // data for shipping_manifest

            $manifest_data = array(

                'delivery_id' => '',

                'orders_id' => $order,

                'delivery_name' => name_case($orderInfo['customers_name']),

                'delivery_company' => '',

                'delivery_address_1' => $orderInfo['delivery_address'],

                'delivery_address_2' => '',

                'delivery_city' => $orderInfo['delivery_city'],

                'delivery_state' => $delivery_state,

                'delivery_postcode' => $orderInfo['delivery_postcode'],

                'delivery_phone' => $orderInfo['customers_telephone'],

                'package_weight' => $_POST['package_weight'],

                'package_value' => $declare_value,

                'oversized' => $oversized,

                'pickup_date' => $corrected_date,

                'saturday_delivery' => $saturday_delivery,

                'hold_at_location' => $hold_at_location,

                'hal_address' => $_POST['hal_address'],

                'hal_city' => $_POST['hal_city'],

                'hal_state' => $_POST['hal_state'],

                'hal_postcode' => $_POST['hal_postcode'],

                'hal_phone' => $_POST['hal_phone'],

                'dim_height' => $_POST['dim_height'],

                'dim_width' => $_POST['dim_width'],

                'dim_length' => $_POST['dim_length'],

                'shipping_type' => $_POST['service_type'],

                'residential' => $residential,

                'autopod' => $_POST['autopod'],

                'cod' => ''

            );



    // get the package weight/total weight and format it to one decimal place

            $total_weight = round($_POST['package_weight'], 1);

            $total_weight = sprintf("%01.1f", $total_weight);



    // deal with multiple packages



            if ($_POST['package_num'] > 1) {

                $shipData[116] = $_POST['package_num'];

                $shipData[1117] = $i;

                $manifest_data['multiple'] = $i;



                if ($i == 1) {

                    if ($debug) {

                        $shipData[1400] = $total_weight;

                        $package_weight = $_POST['package_' . $i . '_weight'];

                        $package_weight = sprintf("%01.1f", $package_weight);

                        $shipData[1401] = $package_weight;



                        echo SHIPMENT_REQUEST_DATA . $i . ':<br><pre>';

                        print_r($shipData);

                        echo '</pre>';

                        $manifest_data['tracking_num'] = 'master_trackNum';

                        echo MANIFEST_DATA . $i . ':<br><pre>';

                        print_r($manifest_data);

                        echo '</pre>';

                    }

                    else {

                        $shipData[1400] = $total_weight;

                        $package_weight = round($_POST['package_' . $i . '_weight'], 1);

                        $package_weight = sprintf("%01.1f", $package_weight);

                        $shipData[1401] = $package_weight;

                        $master_trackNum = tep_ship_request($shipData, $ship_type, $order);

                        $manifest_data['tracking_num'] = $master_trackNum;

                    }

                }

                else {

                    if ($debug) {

                        $shipData[1123] = 'master_trackNum';

                        $package_weight = $_POST['package_' . $i . '_weight'];

                        $package_weight = sprintf("%01.1f", $package_weight);

                        $shipData[1401] = $package_weight;

                        echo SHIPMENT_REQUEST_DATA . $i . ':<br><pre>';

                        print_r($shipData);

                        echo '</pre>';

                        $manifest_data['tracking_num'] = 'trackNum';

                        echo MANIFEST_DATA . $i . ':<br><pre>';

                        print_r($manifest_data);

                        echo '</pre>';

                    }

                    else {

                        $shipData[1123] = $master_trackNum;

                        $package_weight = round($_POST['package_' . $i . '_weight'], 1);

                        $package_weight = sprintf("%01.1f", $package_weight);

                        $shipData[1401] = $package_weight;

                        $trackNum = tep_ship_request($shipData, $ship_type, $order);

                        $manifest_data['tracking_num'] = $trackNum;

                    }

                }

            }

                // for single package shipments

            elseif ($_POST['package_num'] == 1) {

                if ($debug) {

                    $shipData[1401] = $total_weight;

                    echo SHIPMENT_REQUEST_DATA . $i . ':<br><pre>';

                    print_r($shipData);

                    echo '</pre>';

                    $manifest_data['tracking_num'] = 'master_trackNum';

                    echo MANIFEST_DATA . $i . ':<br><pre>';

                    print_r($manifest_data);

                    echo '</pre>';

                }

                else {

                    $shipData[1401] = $total_weight;

                    $master_trackNum = tep_ship_request($shipData, $ship_type, $order);

                    $manifest_data['tracking_num'] = $master_trackNum;

                }

            }

            if ($debug == 0) {

                //$shipManifest = Doctrine_Core::getTable('ShippingManifest')->getRecordInstance();

                //$shipManifest->insert($manifest_data);                

                tep_db_perform(TABLE_SHIPPING_MANIFEST, $manifest_data);

            }

        }



        if ($debug) {

            die('Debugging');

        }

    // if there's a master tracking number, keep it with the order

        if ($master_trackNum) {
            $trackNum = $master_trackNum;
        }



    // store the tracking number

        tep_db_query("update " . TABLE_ORDERS . " set fedex_track_num='" . $trackNum . "' where orders_id = " . $order . "");

    // add comment to order history

        $fedex_comments = sysLanguage::get('ORDER_HISTORY_DELIVERED') . $trackNum;

    // ...mark the order record "delivered"...

        $update_status = array('orders_status' => 3);

        tep_db_perform(TABLE_ORDERS, $update_status, 'update', "orders_id = '" . $order . "'");



        if ($send_email_on_shipping) {

            $customer_notified = '1';

        }

        else {

            $customer_notified = '0';

        }



        tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . $order . "', '3', now(), '" . $customer_notified . "', '" . $fedex_comments . "')");



    // send email automatically on shipping

        if ($send_email_on_shipping) {



            if (tep_not_null($trackNum)) {

                $email_notify_tracking = sprintf(sysLanguage::get('EMAIL_TEXT_TRACKING_NUMBER')) . "\n" . 'http://www.fedex.com/Tracking?action=track&tracknumbers=' . nl2br(tep_output_string_protected($trackNum)) . "\n\n";

            }

            $email_txt = sysConfig::get('STORE_NAME') . "<br>" . sysLanguage::get('EMAIL_SEPARATOR') . "<br>" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $order . "<br><br>" . EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $order, 'SSL') . "<br>" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($QOrders[0]['date_purchased']) . "<br><br>" . "<br>" . $email_notify_tracking . "<br><br>" . sprintf(sysLanguage::get('EMAIL_TEXT_STATUS_UPDATE'), 'Shipped');

            tep_mail($orderInfo['customers_name'], $orderInfo['customers_email_address'], sysLanguage::get('EMAIL_TEXT_SUBJECT'), $email_txt, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

        }



        $ship_type_query = tep_db_query("select shipping_type from " . TABLE_SHIPPING_MANIFEST . " where orders_id = '" . $order . "'");

        $ship_type = tep_db_fetch_array($ship_type_query);

        if ($service_type < 89) {

            $delete_manifest_query = tep_db_query("delete from " . TABLE_SHIPPING_MANIFEST . " where orders_id = '" . $order . "'");

        }



        if (sysConfig::get('EXTENSION_FED_EX_PRINTING') == 'Thermal'){

            $link = itw_app_link('action=fedex_popup_thermal&num=' . $trackNum . '&oID=' . $order . '&multiple=' . $shipData[1117],'ship_fedex','default');

        }else{

             $link = itw_app_link('action=fedex_popup_laser&num=' . $trackNum . '&oID=' . $order . '&multiple=' . $shipData[1117],'ship_fedex','default');

        }

        EventManager::attachActionResponse($link, 'redirect');        



    }else{

        EventManager::attachActionResponse(itw_app_link(null,'orders','default'), 'redirect'); 

    }

?>