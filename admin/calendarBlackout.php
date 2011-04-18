<?php
/*
  $Id: configuration.php,v 1.43 2003/06/29 22:50:51 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

 require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'save':
          if (isset($_POST['blackoutDays'])){
              $days = implode(',', $_POST['blackoutDays']);
          }else{
              $days = '';
          }
          tep_db_query('update ' . TABLE_CONFIGURATION . ' set configuration_value = "' . $days . '" where configuration_key = "CALENDAR_DISABLED_DAYS"');
          
          //echo '<pre>';print_r($_POST);echo '</pre>';exit;
          tep_db_query('truncate table blackout_dates');
          $fromDates = $_POST['from'];
          foreach($fromDates as $key => $fromVal){
//              echo 'FROM::' . $fromVal . '<br>TO::' . $_POST['to'][$key] . '<br><br>';
              if (tep_not_null($fromVal)){
                  tep_db_query('insert into blackout_dates (date_from, date_to, repeats) values ("' . $fromVal . '", "' . $_POST['to'][$key] . '", "' . (isset($_POST['repeats'][$key]) ? '1' : '0') . '")');
              }
          }
//          exit;
        tep_redirect(tep_href_link('calendarBlackout.php'));
        break;
    }
  }
  
  $pageName = basename($_SERVER['PHP_SELF']);
  $pageContent = substr($pageName, 0, strpos($pageName, '.'));
  require(DIR_WS_ADMIN_TEMPLATES . ADMIN_TEMPLATE_NAME . TEMPLATE_MAIN_PAGE);
  
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>