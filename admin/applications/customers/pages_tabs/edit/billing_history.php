<?php
  $customers_query_raw = "select o.orders_id, o.date_purchased, o.last_modified, o.currency, o.currency_value,md.name, ot.text as order_total, o.is_rental from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id) and (ot.module_type = 'ot_total' or ot.module_type = 'total') inner join " . TABLE_CUSTOMERS . " c on o.customers_id = c.customers_id left join customers_membership cm on cm.customers_id = c.customers_id inner join " . TABLE_MEMBER . " m on m.plan_id=cm.plan_id left join membership_plan_description md on m.plan_id=md.plan_id where md.language_id=".Session::get('languages_id')." and o.customers_id = '" . $cID . "' and o.is_rental=1 order by o.date_purchased desc";
  $customers_query = tep_db_query($customers_query_raw);
  $templateParsed = array();
  if (tep_db_num_rows($customers_query) < 1){
      $templateParsed[] = '<tr>
       <td colspan="4" class="messageStackError">' . sysLanguage::get('TEXT_INFO_NO_BILLING_HISTORY') . '</td>
      </tr>';
  } else {
      $template = '<tr class="dataTableRow">
       <td class="smallText" align="left">%s</td>
       <td class="smallText" align="left">%s</td>
       <td class="smallText" align="left">%s</td>
       <td class="smallText" align="left">%s</td>
      </tr>';
      while ($customers = tep_db_fetch_array($customers_query)){
          $templateParsed[] = sprintf($template, 
              $customers['orders_id'], 
              date('Y-m-d', strtotime($customers['date_purchased'])), 
              $customers['name'],
              strip_tags($customers['order_total'])
          );
      }
  }
?>
 <table border="0" width="95%" cellspacing="0" cellpadding="2">
  <tr class="dataTableHeadingRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
   <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_ORDER_ID');?></td>
   <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_BILLED_DATE');?></td>
   <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_PACKAGE_NAME');?></td>
   <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_AMOUNT');?></td>
  </tr>
  <?php echo implode("\n", $templateParsed);?>
 </table>