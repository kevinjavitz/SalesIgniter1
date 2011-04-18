<?php

if ($_GET['action']=='confirmrelease' && isset($_GET['gid'])) {
    $gv_query=tep_db_query("select release_flag from " . TABLE_COUPON_GV_QUEUE . " where unique_id='".$_GET['gid']."'");
    $gv_result=tep_db_fetch_array($gv_query);
    if ($gv_result['release_flag']=='N') {
      $gv_query=tep_db_query("select customer_id, amount from " . TABLE_COUPON_GV_QUEUE ." where unique_id='".$_GET['gid']."'");
      if ($gv_resulta=tep_db_fetch_array($gv_query)) {
      $gv_amount = $gv_resulta['amount'];
      //Let's build a message object using the email class
      $mail_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . $gv_resulta['customer_id'] . "'");
      $mail = tep_db_fetch_array($mail_query);
      $message = sysLanguage::get('TEXT_REDEEM_COUPON_MESSAGE_HEADER');
      $message .= sprintf(sysLanguage::get('TEXT_REDEEM_COUPON_MESSAGE_AMOUNT'), $currencies->format($gv_amount));
      $message .= sysLanguage::get('TEXT_REDEEM_COUPON_MESSAGE_BODY');
      $message .= sysLanguage::get('TEXT_REDEEM_COUPON_MESSAGE_FOOTER');
      $mimemessage = new email(array('X-Mailer: osCommerce bulk mailer'));
      // add the message to the object
      $mimemessage->add_text($message);
      $mimemessage->build_message();

      $mimemessage->send($mail['customers_firstname'] . ' ' . $mail['customers_lastname'], $mail['customers_email_address'], '', EMAIL_FROM, sysLanguage::get('TEXT_REDEEM_COUPON_SUBJECT') );
      $gv_amount=$gv_resulta['amount'];
      $gv_query=tep_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id='".$gv_resulta['customer_id']."'");
      $customer_gv=false;
      $total_gv_amount=0;
      if ($gv_result=tep_db_fetch_array($gv_query)) {
        $total_gv_amount=$gv_result['amount'];
        $customer_gv=true;
      }
      $total_gv_amount=$total_gv_amount+$gv_amount;
      if ($customer_gv) {
        $gv_update=tep_db_query("update " . TABLE_COUPON_GV_CUSTOMER . " set amount='".$total_gv_amount."' where customer_id='".$gv_resulta['customer_id']."'");
      } else {
        $gv_insert=tep_db_query("insert into " .TABLE_COUPON_GV_CUSTOMER . " (customer_id, amount) values ('".$gv_resulta['customer_id']."','".$total_gv_amount."')");
      }
        $gv_update=tep_db_query("update " . TABLE_COUPON_GV_QUEUE . " set release_flag='Y' where unique_id='".$_GET['gid']."'");
      }
    }
  }
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE'); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_CUSTOMERS'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('TABLE_HEADING_ORDERS_ID'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo sysLanguage::get('TABLE_HEADING_VOUCHER_VALUE'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo sysLanguage::get('TABLE_HEADING_DATE_PURCHASED'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo sysLanguage::get('TABLE_HEADING_ACTION'); ?>&nbsp;</td>
              </tr>
<?php
  $gv_query_raw = "select c.customers_firstname, c.customers_lastname, gv.unique_id, gv.date_created, gv.amount, gv.order_id from " . TABLE_CUSTOMERS . " c, " . TABLE_COUPON_GV_QUEUE . " gv where (gv.customer_id = c.customers_id and gv.release_flag = 'N')";
  $gv_query = tep_db_query($gv_query_raw);
  $gv_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $gv_query_raw, $gv_query_numrows);

  while ($gv_list = tep_db_fetch_array($gv_query)) {
    if (((!$_GET['gid']) || (@$_GET['gid'] == $gv_list['unique_id'])) && (!$gInfo)) {
      $gInfo = new objectInfo($gv_list);
    }
    if ( (is_object($gInfo)) && ($gv_list['unique_id'] == $gInfo->unique_id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . itw_app_link(tep_get_all_get_params(array('gid', 'action')) . 'gid=' . $gInfo->unique_id . '&action=edit','coupons','gv_queue') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . itw_app_link(tep_get_all_get_params(array('gid', 'action')) . 'gid=' . $gv_list['unique_id'],'coupons','gv_queue') . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $gv_list['customers_firstname'] . ' ' . $gv_list['customers_lastname']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $gv_list['order_id']; ?></td>
                <td class="dataTableContent" align="right"><?php echo $currencies->format($gv_list['amount']); ?></td>
                <td class="dataTableContent" align="right"><?php echo tep_datetime_short($gv_list['date_created']); ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($gInfo)) && ($gv_list['unique_id'] == $gInfo->unique_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' .itw_app_link('page=' . $_GET['page'] . '&gid=' . $gv_list['unique_id'],'coupons','gv_queue') . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $gv_split->display_count($gv_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], sysLanguage::get('TEXT_DISPLAY_NUMBER_OF_GIFT_VOUCHERS')); ?></td>
                    <td class="smallText" align="right"><?php echo $gv_split->display_links($gv_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'release':
      $heading[] = array('text' => '[' . $gInfo->unique_id . '] ' . tep_datetime_short($gInfo->date_created) . ' ' . $currencies->format($gInfo->amount));

      $contents[] = array('align' => 'center', 'text' => htmlBase::newElement('button')->setHref(itw_app_link('action=confirmrelease&gid='.$gInfo->unique_id,'coupons','gv_queue'))->setText('Confirm')->draw() . htmlBase::newElement('button')->setHref(itw_app_link('action=cancel&gid=' . $gInfo->unique_id,'coupons','gv_queue'))->setText('Cancel')->draw());
      break;
    default:
      $heading[] = array('text' => '[' . $gInfo->unique_id . '] ' . tep_datetime_short($gInfo->date_created) . ' ' . $currencies->format($gInfo->amount));

      $contents[] = array('align' => 'center','text' => htmlBase::newElement('button')->setHref(itw_app_link('action=release&gid='.$gInfo->unique_id,'coupons','gv_queue'))->setText('Release')->draw());
      break;
   }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table>