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

    if ($order) {
        $transaction_code = "023";
        $fedexVars = array(
            10 => sysConfig::get('EXTENSION_FED_EX_ACCOUNT'),
            498 => sysConfig::get('EXTENSION_FED_EX_METER')
        );


        //$fedex_gateway = sysConfig::get('EXTENSION_FED_EX_SERVER');
        // create new FedExDC object
        $fed = new FedExDC($fedexVars[10], $fedexVars[498]);

        // get the tracking number from the order record
        $fedex_tracking_query = tep_db_query("select fedex_track_num from " . TABLE_ORDERS . " where orders_id = '" . $order . "'");
        $r = tep_db_fetch_array($fedex_tracking_query);
        $fedex_tracking = $r['fedex_track_num'];

        // get the shipment type from the shipping manifest
        $ship_type_query = tep_db_query("select shipping_type from " . TABLE_SHIPPING_MANIFEST . " where orders_id = '" . $order . "'");
        $ship_type = tep_db_fetch_array($ship_type_query);
        if (($ship_type['shipping_type'] == 90) or ($ship_type['shipping_type'] == 92)) {
            $ship_type = 'FDXG';
        }
        else {
            $ship_type = 'FDXE';
        }

    // simple array with transaction code, tracking number, carrier code
        $cancelData = array(
            0 => $transaction_code,
            1 => ORDER_NUMBER . $order, // order number, optional
            29 => $fedex_tracking,
            3025 => $ship_type
        );

        $cancelData = $fedexVars + $cancelData;
        $delete_manifest_query = tep_db_query("delete from " . TABLE_SHIPPING_MANIFEST . " where orders_id = '" . $order . "'");
        // cancel the shipment
        $cancelRet = $fed->cancel_ground($cancelData);
        if ($error = $fed->getError()) {
            $messageStack->addSession('pageStack','Shipping has not been cancelled. Error: '. $error,'error');
        }

        $delete_trackNum = array('fedex_track_num' => '');

        tep_db_perform(TABLE_ORDERS, $delete_trackNum, 'update', "orders_id = '" . $order . "'");
    
        $update_status = array('orders_status' => 2);
        tep_db_perform(TABLE_ORDERS, $update_status, 'update', "orders_id = '" . $order . "'");
        $fedex_comments = sysLanguage::get('ORDER_HISTORY_CANCELLED') . $trackNum;
        tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . $order . "', 6, now(), '', '" . $fedex_comments . "')");
        tep_db_query("delete from " . TABLE_SHIPPING_MANIFEST . " where orders_id = '" . $order . "'");
        $messageStack->addSession('pageStack','Shipping has been cancelled!','success');
    }
     EventManager::attachActionResponse(itw_app_link(null,'orders','default'), 'redirect');

?>