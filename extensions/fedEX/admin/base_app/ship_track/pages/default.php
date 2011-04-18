    <?php

    $debug = 0; // 1 for yes, 0 for no
    $order = $_GET['oID'];
    if ($order){
        $QOrder = Doctrine_Core::getTable('Orders')->findOneByOrdersId($order);
        $tracking_number = $QOrder->fedex_track_num;

        $fed = new FedExDC();
        $track_Ret = $fed->ref_track(array(1537 => $tracking_number));

        // debug (prints array of all data returned)

        if ($debug) {
            echo '<pre>';

            if ($error = $fed->getError()) {
                echo "ERROR :" . $error;
            } else {
                echo $fed->debug_str . "\n<BR>";
                print_r($track_Ret);
                echo "\n\n";
                for ($i = 1; $i <= $track_Ret[1584]; $i++) {
                    echo sysLanguage::get('PACKAGE_DELIVERED_ON') . $track_Ret['1720-' . $i];
                    echo '\n' . sysLanguage::get('PACKAGE_SIGNED_BY') . $track_Ret['1706-' . $i];
                }
            }
            echo '</pre>';
        }

        ?>
        <table>
              <tr>
                <td><table width="80%" border="0" cellspacing="0" cellpadding="2">
                  <tr>
                      <td><?php echo tep_draw_separator('pixel_trans.gif', 1, 15); ?></td>
                  </tr>
                  <tr>
                    <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
                      <tr>
        <?

        if ($error = $fed->getError()) {
            if (strstr($error, '500139')) {
                echo sysLanguage::get('PACKAGE_NOT_IN_SYSTEM') . '<br/>';
            }
            if (strstr($error, '6070')) {
                echo sysLanguage::get('INVALID_TRACKING_NUM') . '<br/>';
            }
            else {
                echo sysLanguage::get('PACKAGE_ERROR') . $error;
            }
        }
        else {
            for ($i = 1; $i <= $track_Ret[1584]; $i++) {
                // list destination
                $dest_city = $track_Ret['15-' . $i];
                $dest_state = $track_Ret['16-' . $i];
                $dest_zip = $track_Ret['17-' . $i];
                $signed_by = $track_Ret['1706-' . $i];
                $delivery_date = $track_Ret['1720-' . $i];
                $delivery_time = $track_Ret['1707-' . $i];

                if ($signed_by){
                    $delivery_date = strtotime($delivery_date);
                }
                $delivery_date = date("F j, Y", $delivery_date);
                $hour = substr($delivery_time, 0, 2);
                $minute = substr($delivery_time, 2, 2);
                if ($hour >= 12) {
                    $time_mod = 'pm';
                    if ($hour > 12) {
                        $hour = ($hour - 12);
                    }
                }else {
                    $time_mod = 'am';
                }

                if (!$error) {
                    echo '<td class="main"><b>' . sysLanguage::get('PACKAGE_DESTINATION') . '</b></td></tr><tr><td class="main"> ' . $dest_city . ', ' . $dest_state . ' ' . $dest_zip . '</td></tr>';
                    ?>
                        <tr>
                            <td><?php echo tep_draw_separator('pixel_trans.gif', 1, 15); ?></td>
                        </tr>
                    <?
                    echo '<tr><td class="main"><b>' . sysLanguage::get('PACKAGE_STATUS') . '</b></td></tr>';
                    if ($signed_by) {

                        // if left without signature, let them know
                        // (add more as they appear)
                        if (strstr($signed_by, 'F.RONTDOOR')) {
                            $signed_by = '<tr><td class="main">' . sysLanguage::get('DELIVERED_FRONTDOOR') . '</td></tr>';
                        }
                        if (strstr($signed_by, 'S.IDEDOOR')) {
                            $signed_by = '<tr><td class="main">' . sysLanguage::get('DELIVERED_SIDEDOOR') . '</td></tr>';
                        }
                        if (strstr($signed_by, 'G.ARAGE')) {
                            $signed_by = '<tr><td class="main">' . sysLanguage::get('DELIVERED_GARAGE') . '</td></tr>';
                        }
                        if (strstr($signed_by, 'B.ACKDOOR')) {
                            $signed_by = '<tr><td class="main">' . sysLanguage::get('DELIVERED_BACKDOOR') . '</td></tr>';
                        }
                        echo '<tr><td class="main">' . PACKAGE_DELIVERED_ON . $delivery_date . sysLanguage::get('PACKAGE_DELIVERED_AT') . $hour . ':' . $minute . ' ' . $time_mod . '</td></tr>';

                        ?>
                            <tr>
                                <td><?php echo tep_draw_separator('pixel_trans.gif', 1, 15); ?></td>
                            </tr>
                        <?

                        echo '<tr><td class="main"><b>' . sysLanguage::get('PACKAGE_SIGNED_BY') . '</b></td></tr><tr><td class="main">' . $signed_by . '</td></tr>';
                    }
                    else {
                        $status_note = $track_Ret['1159-' . $i . '-1'];
                        $status_city = $track_Ret['1160-' . $i . '-1'];
                        $status_state = $track_Ret['1161-' . $i . '-1'];
                        echo '<tr><td class="main"><b>' . sysLanguage::get('PACKAGE_IN_TRANSIT') . '</td></tr>';
                        echo '<tr><td class="main">' . $status_note . ': ' . $status_city . ', ' . $status_state . '</td></tr>';
                    }
                }
            }
        }
        ?>
          </table>
        <?php
    }else{
        $link = itw_app_link(null,'orders','default');
        echo '<script type="text/javascript">$(document).ready(function (){window.location.href="'.$link.'";});</script>';

    }
    echo '<br/><a href="'. itw_app_link(null,'orders','default').'">Back to Orders</a>';

 ?>