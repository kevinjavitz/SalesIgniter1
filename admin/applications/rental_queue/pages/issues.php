<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_ISSUES'); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_ISSUE_ID'); ?></td>
                <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_RENTED_PRODUCT'); ?></td>
                <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_REPORTED_DATE'); ?></td>
                <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_CUSTOMER_NAME'); ?></td>
                <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_STATUS'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo sysLanguage::get('TABLE_HEADING_ACTION'); ?>&nbsp;</td>
              </tr>
<?php
  $rentalissues_query_raw = "select issue_id,products_name from " . TABLE_RENTAL_ISSUES . " ORDER BY issue_id desc";
  $rentalissues_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $rentalissues_query_raw, $rentalissues_query_numrows);
  $rentalissues_query_raw = "select i.issue_id, i.products_name, date_format(i.reported_date,'%m/%d/%Y') as formatted_date, i.status, c.customers_firstname, c.customers_lastname, c.customers_id, problem, feedback FROM " . TABLE_RENTAL_ISSUES . " i , " . TABLE_CUSTOMERS . " c  WHERE c.customers_id = i.customers_id order by i.issue_id desc ";
  $rentalissues_query = tep_db_query($rentalissues_query_raw);
  while ($rental_issues = tep_db_fetch_array($rentalissues_query)) {
    if ((!isset($_GET['tID']) || (isset($_GET['tID']) && ($_GET['tID'] == $rental_issues['issue_id']))) && !isset($tcInfo) && (substr($action, 0, 3) != 'new')) {
      $tcInfo = new objectInfo($rental_issues);
    }

    if (isset($tcInfo) && is_object($tcInfo) && ($rental_issues['issue_id'] == $tcInfo->issue_id)) {
      echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . itw_app_link('page=' . $_GET['page'] . '&tID=' . $rental_issues['issue_id'] . '&action=edit', 'rental_queue', 'issues') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . itw_app_link('page=' . $_GET['page'] . '&tID=' . $rental_issues['issue_id'], 'rental_queue', 'issues') . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $rental_issues['issue_id']; ?></td>
                <td class="dataTableContent"><?php echo $rental_issues['products_name']; ?></td>
                <td class="dataTableContent"><?php echo $rental_issues['formatted_date']; ?></td>
                <td class="dataTableContent"><?php echo $rental_issues['customers_firstname'] . ' '  . $rental_issues['customers_lastname']; ?></td>
                <td class="dataTableContent">
                	<?php

                	if($rental_issues['status']=='P'){
                		echo 'Pending';
                	}
                	if($rental_issues['status']=='O'){
                		echo 'Open';
                	}
                	if($rental_issues['status']=='C'){
                		echo 'Closed';
                	}
                	?></td>
                <td class="dataTableContent" align="right"><?php if (isset($tcInfo) && is_object($tcInfo) && ($rental_issues['issue_id'] == $tcInfo->issue_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . itw_app_link('page=' . $_GET['page'] . '&tID=' . $rental_issues['issue_id'], 'rental_queue', 'issues') . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $rentalissues_split->display_count($rentalissues_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], sysLanguage::get('TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS')); ?></td>
                    <td class="smallText" align="right"><?php echo $rentalissues_split->display_links($rentalissues_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
<?php
  if (empty($action)) {
  }
?>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'edit':
      $rental_issues_query = "select i.issue_id, i.products_name, date_format(i.reported_date,'%m/%d/%Y') as formatted_date, i.status, c.customers_firstname, c.customers_lastname, c.customers_id, problem, feedback FROM " . TABLE_RENTAL_ISSUES . " i , " . TABLE_CUSTOMERS . " c  WHERE c.customers_id = i.customers_id AND issue_id = '" . $_GET['tID'] . "' order by i.issue_id desc ";


      $issues = tep_db_fetch_array(tep_db_query($rental_issues_query));
      $heading[] = array('text' => '<b>' . sysLanguage::get('TEXT_INFO_HEADING_EDIT_RENTAL_ISSUE') . '</b>');

      $contents = array('form' => '<form name="rental_issues" action="' . itw_app_link('page=' . $_GET['page'] . '&tID=' . $issues['issue_id'] . '&action=saveIssue', 'rental_queue', 'issues') . '" method="post">');
      $contents[] = array('text' => sysLanguage::get('TEXT_INFO_EDIT_INTRO'));
      $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_INFO_PRODUCT_NAME') . '<br><b>' . $issues['products_name'].'</b>');
      $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_INFO_PROBLEM') . '<br><b>' . $issues['problem'].'</b>');
      $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_INFO_DATE_REPORTED'). '<br><b>' . $issues['formatted_date'].'</b>');
      $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_INFO_FEEDBACK') . '<br>' . tep_draw_textarea_field('feedback','',30,5,$issues['feedback']));
      $contents[] = array('align' => 'center', 'text' => '<br>'.tep_draw_hidden_field('customers_id',$issues['customers_id'])  . htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw() . '&nbsp;' . htmlBase::newElement('button')->usePreset('cancel')->setHref(itw_app_link('page=' . $_GET['page'] . '&tID=' . $tcInfo->issue_id, 'rental_queue', 'issues'))->draw());
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . $tcInfo->products_name . '</b>');

      $contents = array('form' => '<form name="deleteIssue" action="' . itw_app_link('page=' . $_GET['page'] . '&tID=' . $tcInfo->issue_id . '&action=deleteIssueConfirm', 'rental_queue', 'issues') . '" method="post">');
      $contents[] = array('text' => sysLanguage::get('TEXT_INFO_DELETE_INTRO'));
      $contents[] = array('align' => 'center', 'text' => '<br>' . htmlBase::newElement('button')->usePreset('delete')->setType('submit')->draw() . '&nbsp;' . htmlBase::newElement('button')->usePreset('cancel')->setHref(itw_app_link('page=' . $_GET['page'] . '&tID=' . $tcInfo->issue_id, 'rental_queue', 'issues'))->draw());
      break;
    default:
      if (isset($tcInfo) && is_object($tcInfo)) {
        $heading[] = array('text' => '<b>' . $tcInfo->products_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => htmlBase::newElement('button')->usePreset('edit')->setHref(itw_app_link('page=' . $_GET['page'] . '&tID=' . $tcInfo->issue_id . '&action=edit', 'rental_queue', 'issues'))->draw() . ' ' . htmlBase::newElement('button')->usePreset('delete')->setHref(itw_app_link('page=' . $_GET['page'] . '&tID=' . $tcInfo->issue_id . '&action=delete', 'rental_queue', 'issues'))->draw());
        $contents[] = array('text' => '<br><b>' . sysLanguage::get('TEXT_INFO_PROBLEM') . '</b> ' . $tcInfo->problem);
        $contents[] = array('text' => '<b>' . sysLanguage::get('TEXT_INFO_FEEDBACK') . '</b> ' . $tcInfo->feedback);

      }
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