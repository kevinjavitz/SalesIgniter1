<?php
/*
  $Id: database.php,v 1.21 2003/06/09 21:21:59 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  function tep_db_connect() {
  }

  function tep_db_close() {
    return dataAccess::disconnect();
  }

  function tep_db_error($query, $errno, $error) {
    echo '<table cellpadding="3" cellspacing="0" border="0">' . 
     '<tr>' . 
      '<td class="main" style="white-space:nowrap;">' . $errno . '</td>' . 
     '</tr>' . 
     '<tr>' . 
      '<td class="main" style="white-space:nowrap;" valign="top"><b><u>Query Used</u></b></td>' . 
     '</tr>' . 
     '<tr>' . 
      '<td class="main">' . $query . '</td>' . 
     '</tr>' . 
     '<tr>' . 
      '<td class="main" style="white-space:nowrap;" valign="top"><b><u>Server Message</u></b></td>' . 
     '</tr>' . 
     '<tr>' . 
      '<td class="main">' . $error . '</td>' . 
     '</tr>' . 
    '</table>';
  }

  function tep_db_query($query, $link = 'db_link') {
    global $messageStack;
      $return = dataAccess::runQuery($query);
          if (isset($return['errMsg']) && !empty($return['errMsg'])){
              $errMsg = '<table cellpadding="3" cellspacing="0" border="0">' . 
                         '<tr>' . 
                          '<td class="main" style="white-space:nowrap;">' . $return['errMsg'] . '</td>' . 
                         '</tr>' . 
                         '<tr>' . 
                          '<td class="main" style="white-space:nowrap;" valign="top"><b><u>Query Used</u></b></td>' . 
                         '</tr>' . 
                         '<tr>' . 
                          '<td class="main">' . $query . '</td>' . 
                         '</tr>' . 
                         '<tr>' . 
                          '<td class="main" style="white-space:nowrap;" valign="top"><b><u>Server Message</u></b></td>' . 
                         '</tr>' . 
                         '<tr>' . 
                          '<td class="main">' . $return['serverErrMsg'] . '</td>' . 
                         '</tr>' . 
                        '</table>';
              if (is_object($messageStack)){
                  $messageStack->addSession('footerStack', $errMsg, 'error');
              }
          }
    return $return['queryResource'];
  }

  function tep_db_perform($table, $data, $action = 'insert', $parameters = '', $link = 'db_link') {
    reset($data);
    if ($action == 'insert') {
      $query = 'insert into ' . $table . ' (';
      while (list($columns, ) = each($data)) {
        $query .= $columns . ', ';
      }
      $query = substr($query, 0, -2) . ') values (';
      reset($data);
      while (list(, $value) = each($data)) {
        
        if (substr($value, 0, 7) == 'encode('){
            $query .= $value;
        } else{
            switch ((string)$value) {
                case 'now()':
                    $query .= 'now(), ';
                break;
                case 'null':
                    $query .= 'null, ';
                break;
                default:
                    $query .= '\'' . tep_db_input($value) . '\', ';
                break;
            }
        }
      }
      $query = substr($query, 0, -2) . ')';
    } elseif ($action == 'update') {
      $query = 'update ' . $table . ' set ';
      while (list($columns, $value) = each($data)) {
        if (substr($value, 0, 7) == 'encode('){
            $query .= $columns . ' = ' . $value . ', ';
        } else{
            switch ((string)$value) {
                case 'now()':
                    $query .= $columns . ' = now(), ';
                break;
                case 'null':
                    $query .= $columns .= ' = null, ';
                break;
                default:
                    $query .= $columns . ' = \'' . tep_db_input($value) . '\', ';
                break;
            }
        }
      }
      $query = substr($query, 0, -2) . ' where ' . $parameters;
    }

    return dataAccess::runQuery($query);
  }

  function tep_db_fetch_array($db_query) {
      $return = dataAccess::fetchArray($db_query);
    return $return['fetchResource'];
  }

  function tep_db_num_rows($db_query) {
      $return = dataAccess::numberOfRows($db_query);
    return $return['numberOfRows'];
  }

  function tep_db_data_seek($db_query, $row_number) {
    return mysql_data_seek($db_query, $row_number);
  }

  function tep_db_insert_id() {
    return dataAccess::insertId();
  }

  function tep_db_free_result($db_query) {
    return dataAccess::freeResult($db_query);
  }

  function tep_db_fetch_fields($db_query) {
    return mysql_fetch_field($db_query);
  }

  function tep_db_output($string) {
    return dataAccess::cleanOutput($db_query);
  }

function tep_db_input($string, $link = 'db_link') {
    return dataAccess::cleanInput($string);
}

  function tep_db_prepare_input($string) {
    return dataAccess::cleanInput($string);
  }
?>
