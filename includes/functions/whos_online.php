<?php
/*
 $Id: whos_online.php,v 2.00 2006/02/14 15:48:55 harley_vb Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  function request_uri(){
      if (isset($_SERVER['REQUEST_URI'])){
          $uri = $_SERVER['REQUEST_URI'];
      }else{ 
          if (isset($_SERVER['argv'])){
              $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
          }else{
              $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
          }
      }
    return $uri;
  }
  
  function tep_update_whos_online() {
    // WOL 1.6 - Need access to spider_flag and user_agent and moved some assignments up here from below
    global $spider_flag, $user_agent, $userAccount;
      
      $wo_ip_address = tep_get_ip_address();
      $wo_last_page_url = request_uri();
      $current_time = time();
      $xx_mins_ago = ($current_time - 900);
      $wo_session_id = Session::getSessionId();
      $user_agent = getenv('HTTP_USER_AGENT');
      $wo_user_agent = $user_agent;
    // WOL 1.6 EOF
    
      if ($userAccount->getCustomerId() > 0){
          $wo_customer_id = $userAccount->getCustomerId();
          $wo_full_name = $userAccount->getFullName();
      }else{
          if ($spider_flag || strpos($user_agent, "Googlebot") > 0){
              // Bots are customerID = -1
              $wo_customer_id = -1;
            
              // The Bots name is extracted from the User Agent in the WOE Admin screen
              $wo_full_name = $user_agent;
            
              // Session IDs are the WOE primary key.  If a Bot doesn't have a session (normally shouldn't),
              //   use the IP Address as unique identifier, otherwise, use the session ID
              if ($wo_session_id == ""){
                  $wo_session_id = $wo_ip_address;
              }
          }else{
              // Must be a Guest
              $wo_full_name = 'Guest';
              $wo_customer_id = 0;
          }
          // WOL 1.6 EOF
      }
    
      // remove entries that have expired
	  Doctrine_Query::create()
		  ->delete('WhosOnline')
		  ->where('time_last_click < ?', $xx_mins_ago)
		  ->execute();

	  $WhosOnline = Doctrine_Core::getTable('WhosOnline');
	  $Entry = $WhosOnline->findBySessionId($wo_session_id);
	  if (!$Entry){
		  $Entry = $WhosOnline->create();
		  $Entry->session_id = $wo_session_id;
	  }
	  $Entry->customer_id = (int)$wo_customer_id;
	  $Entry->full_name = $wo_full_name;
	  $Entry->ip_address = $wo_ip_address;
	  $Entry->time_entry = $current_time;
	  $Entry->time_last_click = $current_time;
	  $Entry->last_page_url = $wo_last_page_url;
	  $Entry->http_referer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
	  $Entry->user_agent = $user_agent;
	  $Entry->save();
  }
?>
