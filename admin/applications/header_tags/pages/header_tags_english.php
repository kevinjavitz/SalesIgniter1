<?php
/*
  $Id: header_tags_controller.php,v 1.0 2005/04/08 22:50:52 hpdl Exp $
  Originally Created by: Jack York - http://www.oscommerce-solution.com
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

require('includes/application_top.php');
  require('includes/functions/header_tags.php');
  $filename = DIR_FS_CATALOG. DIR_WS_LANGUAGES . Session::get('language') . '/header_tags.php';
  
  $formActive = false;
  
  /****************** READ IN FORM DATA ******************/
  $action = (isset($_POST['action']) ? $_POST['action'] : '');
  
  if (tep_not_null($action)) 
  {
      $main['title'] = $_POST['main_title'];  //read in the knowns
      $main['desc'] = $_POST['main_desc'];
      $main['keyword'] = $_POST['main_keyword'];

      $formActive = true;
      $args_new = array();
      $c = 0;
      $pageCount = TotalPages($filename);
      for ($t = 0, $c = 0; $t < $pageCount; ++$t, $c += 3) //read in the unknowns
      {
         $args_new['title'][$t] = $_POST[$c];
         $args_new['desc'][$t] = $_POST[$c+1];
         $args_new['keyword'][$t] = $_POST[$c+2];
        
         $boxID = sprintf("HTTA_%d", $t); 
         $args_new['HTTA'][$t] = $_POST[$boxID];
         $boxID = sprintf("HTDA_%d", $t); 
         $args_new['HTDA'][$t] = $_POST[$boxID];
         $boxID = sprintf("HTKA_%d", $t); 
         $args_new['HTKA'][$t] = $_POST[$boxID];
         $boxID = sprintf("HTCA_%d", $t); 
         $args_new['HTCA'][$t] = $_POST[$boxID];
      }   
  }

  /***************** READ IN DISK FILE ******************/
  $main_title = '';
  $main_desc = '';
  $main_key = '';
  $sections = array();      //used for unknown titles
  $args = array();          //used for unknown titles
  $ctr = 0;                 //used for unknown titles
  $findTitles = false;      //used for unknown titles
  $fp = file($filename);  

  for ($idx = 0; $idx < count($fp); ++$idx)
  { 
      if (strpos($fp[$idx], "define('HEAD_TITLE_TAG_ALL'") !== FALSE)
      {
//      echo 'SEND sysLanguage::get('TITLE') '.$main_title.' '. ' - '.$main['title'].' - '.$formActive.'<br>';
          $main_title = GetMainArgument($fp[$idx], $main['title'], $formActive);
      } 
      else if (strpos($fp[$idx], "define('HEAD_DESC_TAG_ALL'") !== FALSE)
      {
     // echo 'SEND DESC '.$main['desc']. ' '.$formActive.'<br>';
          $main_desc = GetMainArgument($fp[$idx], $main['desc'], $formActive);
      } 
      else if (strpos($fp[$idx], "define('HEAD_KEY_TAG_ALL'") !== FALSE)
      { 
          $main_key = GetMainArgument($fp[$idx], $main['keyword'], $formActive);
          $findTitles = true;  //enable next section            
      } 
      else if ($findTitles)
      {
          if (($pos = strpos($fp[$idx], '.php')) !== FALSE) //get the section titles
          {
              $sections['titles'][$ctr] = GetSectionName($fp[$idx]);   
              $ctr++; 
          }
          else                                   //get the rest of the items in this section
          {
              if (! IsComment($fp[$idx])) // && tep_not_null($fp[$idx]))
              {
                  $c = $ctr - 1;
                  if (IsTitleSwitch($fp[$idx]))
                  {
                     if ($formActive)
                     {
                       $fp[$idx] = ChangeSwitch($fp[$idx], $args_new['HTTA'][$c]);
                     }                      
                     $args['title_switch'][$c] = GetSwitchSetting($fp[$idx]);
                     $args['title_switch_name'][$c] = sprintf("HTTA_%d",$c);                     
                  }
                  else if (IsDescriptionSwitch($fp[$idx]))
                  {
                     if ($formActive)
                     {
                       $fp[$idx] = ChangeSwitch($fp[$idx], $args_new['HTDA'][$c]);
                     } 
                     $args['desc_switch'][$c] = GetSwitchSetting($fp[$idx]);
                     $args['desc_switch_name'][$c] = sprintf("HTDA_%d",$c);  
                  }
                  if (IsKeywordSwitch($fp[$idx]))
                  {
                     if ($formActive)
                     {
                       $fp[$idx] = ChangeSwitch($fp[$idx], $args_new['HTKA'][$c]);
                     }   
                     $args['keyword_switch'][$c] = GetSwitchSetting($fp[$idx]);
                     $args['keyword_switch_name'][$c] = sprintf("HTKA_%d",$c);
                  }
                  else if (IsCatSwitch($fp[$idx]))
                  {
                     if ($formActive)
                     {
                       $fp[$idx] = ChangeSwitch($fp[$idx], $args_new['HTCA'][$c]); 
                     }  
                     $args['cat_switch'][$c] = GetSwitchSetting($fp[$idx]);
                     $args['cat_switch_name'][$c] = sprintf("HTCA_%d",$c);
                  }
                  else if (IsTitleTag($fp[$idx]))
                  {
                     $args['title'][$c] = GetArgument($fp[$idx], $args_new['title'][$c], $formActive);
                  } 
                  else if (IsDescriptionTag($fp[$idx])) 
                  {
                     $args['desc'][$c] = GetArgument($fp[$idx], $args_new['desc'][$c], $formActive);                   
                  }
                  else if (IsKeywordTag($fp[$idx])) 
                  {
                    $args['keyword'][$c] = GetArgument($fp[$idx], $args_new['keyword'][$c], $formActive);
                  }                                   
              }
          }
      }
  }

  /***************** WRITE THE FILE ******************/
  if ($formActive)
  {      
     WriteHeaderTagsFile($filename, $fp);  
  }
  
  $pageName = basename($_SERVER['PHP_SELF']);
  $pageContent = substr($pageName, 0, strpos($pageName, '.'));
  require(DIR_WS_ADMIN_TEMPLATES . ADMIN_TEMPLATE_NAME . TEMPLATE_MAIN_PAGE);
  
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>