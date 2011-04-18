<?php
  $rentalissues_query = tep_db_query('
      select 
          i.issue_id, i.products_name, date_format(i.reported_date,"%m/%d/%Y") as formatted_date, i.status, problem, feedback  
      from 
          ' . TABLE_RENTAL_ISSUES . ' i 
      where 
          i.customers_id = "' . (int)$Customer->customers_id . '" 
      order by 
          i.issue_id desc
  ');

  $template = '<tr %s class="%s" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'%s\'">
   <td class="dataTableContent">%s</td>
   <td class="dataTableContent">%s</td>
   <td class="dataTableContent">%s</td>
   <td class="dataTableContent">%s</td>
   <td class="dataTableContent">%s</td>
   <td class="dataTableContent" align="right">%s</td>
  </tr>';
  $templateParsed = array();
  while ($rental_issues = tep_db_fetch_array($rentalissues_query)) {
      if ((!isset($_GET['tID']) || (isset($_GET['tID']) && ($_GET['tID'] == $rental_issues['issue_id']))) && !isset($tcInfo) && (substr($action, 0, 3) != 'new')) {
          $tcInfo = new objectInfo($rental_issues);
      }

      if (isset($tcInfo) && is_object($tcInfo) && ($rental_issues['issue_id'] == $tcInfo->issue_id)){
          $id = 'id="defaultSelected"';
          $class = 'dataTableRowSelected';
          $onclick = itw_app_link('&tID=' . $rental_issues['issue_id'] . '&action=edit','rental_queue','issues');
      } else {
          $id = '';
          $class = 'dataTableRow';
          $onclick = itw_app_link('&tID=' . $rental_issues['issue_id'],'rental_queue','issues');
      }
      
      $status = 'Unknown';
      if ($rental_issues['status'] == 'P') $status = 'Pending';
      if ($rental_issues['status'] == 'O') $status = 'Open';
      if ($rental_issues['status'] == 'C') $status = 'Closed';
      
      if (isset($tcInfo) && is_object($tcInfo) && ($rental_issues['issue_id'] == $tcInfo->issue_id)) {
          $rowButton = tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif');
      } else {
          $rowButton = '<a href="' . itw_app_link('&tID=' . $rental_issues['issue_id'],'rental_queue','issues') . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
      }
      $customerTable = Doctrine_Core::getTable('Customers')->find((int)$Customer->customers_id);
      $templateParsed[] = sprintf($template, 
          $id, 
          $class, 
          $onclick,
          $rental_issues['issue_id'],
          $rental_issues['products_name'],
          $rental_issues['formatted_date'],
          $customerTable->customers_firstname . ' '  . $customerTable->customers_lastname,
          $status,
          $rowButton
      );
  }
?>
 <table border="0" width="95%" cellspacing="0" cellpadding="2">
  <tr class="dataTableHeadingRow">
   <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_ISSUE_ID'); ?></td>
   <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_RENTED_PRODUCT'); ?></td>
   <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_REPORTED_DATE'); ?></td>
   <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_CUSTOMER_NAME'); ?></td>
   <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_STATUS'); ?></td>
   <td class="dataTableHeadingContent" align="right"><?php echo sysLanguage::get('TABLE_HEADING_ACTION'); ?>&nbsp;</td>
  </tr>
  <?php echo implode("\n", $templateParsed);?>
 </table>