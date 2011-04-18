<?php
/*
  $Id: header_tags_fill_tags.php,v 1.0 2005/08/25
  Originally Created by: Jack York - http://www.oscommerce-solution.com
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
 
  require('includes/application_top.php'); 
 
  /****************** READ IN FORM DATA ******************/
  $categories_fill = $_POST['group1'];
  $manufacturers_fill = $_POST['group2'];
  $products_fill = $_POST['group3'];
  $productsMetaDesc = $_POST['group4'];
  $productsMetaDescLength = $_POST['fillMetaDescrlength'];
 
  $checkedCats = array();
  $checkedManuf = array();
  $checkedProds = array();
  $checkedMetaDesc = array();
  
  $languages = tep_get_languages();
  $languages_array = array();
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $languages_array[] = array('id' => $languages[$i]['id'], // $i + 1, 
                               'text' => $languages[$i]['name']);
  }
  $langID = Session::get('languages_id'); 
  $updateDP = false;
  $updateTextCat = '';
  $updateTextManuf = '';
  $updateTextProd = '';
    
  /****************** FILL THE CATEGORIES ******************/
   
  if (isset($categories_fill))
  {
    $langID = $_POST['fill_language'];
    
    if ($categories_fill == 'none') 
    {
       $checkedCats['none'] = 'Checked';
    }
    else
    { 
      $categories_tags_query = tep_db_query("select categories_name, categories_id, categories_htc_title_tag, categories_htc_desc_tag, categories_htc_keywords_tag, language_id from  " . TABLE_CATEGORIES_DESCRIPTION . " where language_id = '" . $langID . "'");
      while ($categories_tags = tep_db_fetch_array($categories_tags_query))
      {
        $updateDP = false;
        
        if ($categories_fill == 'empty')
        {
           if (! tep_not_null($categories_tags['categories_htc_title_tag']))
           {
             $updateDB = true;
             $updateTextCat = TEXT_INFO_CATEGORY_FILLED;
           }  
           $checkedCats['empty'] = 'Checked';
        }
        else if ($categories_fill == 'full')
        {
           $updateDB = true;
           $updateTextCat = TEXT_INFO_ALL_CATEGORY_FILLED;
           $checkedCats['full'] = 'Checked';
        }
        else      //assume clear all
        {
           tep_db_query("update " . TABLE_CATEGORIES_DESCRIPTION . " set categories_htc_title_tag='', categories_htc_desc_tag = '', categories_htc_keywords_tag = '' where categories_id = '" . $categories_tags['categories_id']."' and language_id  = '" . $langID . "'");
           $updateTextCat = 'All Category tags have been cleared.';
           $checkedCats['clear'] = 'Checked';
        }      
             
        if ($updateDB)
          tep_db_query("update " . TABLE_CATEGORIES_DESCRIPTION . " set categories_htc_title_tag='".addslashes($categories_tags['categories_name'])."', categories_htc_desc_tag = '". addslashes($categories_tags['categories_name'])."', categories_htc_keywords_tag = '". addslashes($categories_tags['categories_name']) . "' where categories_id = '" . $categories_tags['categories_id']."' and language_id  = '" . $langID . "'");
      }
    }
  }
  else
    $checkedCats['none'] = 'Checked';
   
  /****************** FILL THE MANUFACTURERS ******************/
   
  if (isset($manufacturers_fill))
  {
    $langID = $_POST['fill_language'];
    
    if ($manufacturers_fill == 'none') 
    {
       $checkedManuf['none'] = 'Checked';
    }
    else
    { 
      $manufacturers_tags_query = tep_db_query("select m.manufacturers_name, m.manufacturers_id, mi.languages_id, mi.manufacturers_htc_title_tag, mi.manufacturers_htc_desc_tag, mi.manufacturers_htc_keywords_tag from " . TABLE_MANUFACTURERS . " m, " . TABLE_MANUFACTURERS_INFO . " mi where mi.languages_id = '" . $langID . "'");
      while ($manufacturers_tags = tep_db_fetch_array($manufacturers_tags_query))
      {
        $updateDP = false;
        
        if ($manufacturers_fill == 'empty')
        {
           if (! tep_not_null($manufacturers_tags['manufacturers_htc_title_tag']))
           {
             $updateDB = true;
             $updateTextManuf = TEXT_INFO_MANUFACTURERS_FILLED;
           }  
           $checkedManuf['empty'] = 'Checked';
        }
        else if ($manufacturers_fill == 'full')
        {
           $updateDB = true;
           $updateTextManuf = TEXT_INFO_ALL_MANUFACTURERS_FILLED;
           $checkedManuf['full'] = 'Checked';
        }
        else      //assume clear all
        {
           tep_db_query("update " . TABLE_MANUFACTURERS_INFO . " set manufacturers_htc_title_tag='', manufacturers_htc_desc_tag = '', manufacturers_htc_keywords_tag = '' where manufacturers_id = '" . $manufacturers_tags['manufacturers_id']."' and languages_id  = '" . $langID . "'");
           $updateTextManuf = TEXT_INFO_ALL_MANUFACTURERS_CLEARED;
           $checkedManuf['clear'] = 'Checked';
        }      
             
        if ($updateDB)
          tep_db_query("update " . TABLE_MANUFACTURERS_INFO . " set manufacturers_htc_title_tag='".addslashes($manufacturers_tags['manufacturers_name'])."', manufacturers_htc_desc_tag = '". addslashes($manufacturers_tags['manufacturers_name'])."', manufacturers_htc_keywords_tag = '". addslashes($manufacturers_tags['manufacturers_name']) . "' where manufacturers_id = '" . $manufacturers_tags['manufacturers_id']."' and languages_id  = '" . $langID . "'");
      }
    }
  }
  else
    $checkedManuf['none'] = 'Checked';
       
  /****************** FILL THE PRODUCTS ******************/  
  
  if (isset($products_fill))
  {
    $langID = $_POST['fill_language'];
    
    if ($products_fill == 'none') 
    {
       $checkedProds['none'] = 'Checked';
    }
    else
    { 
      $products_tags_query = tep_db_query("select products_name, products_description, products_id, products_head_title_tag, products_head_desc_tag, products_head_keywords_tag, language_id from " . TABLE_PRODUCTS_DESCRIPTION . " where language_id = '" . $langID . "'");
      while ($products_tags = tep_db_fetch_array($products_tags_query))
      {
        $updateDP = false;
        
        if ($products_fill == 'empty')
        {
          if (! tep_not_null($products_tags['products_head_title_tag']))
          {
            $updateDB = true;
            $updateTextProd = TEXT_INFO_PRODUCTS_FILLED;
          }  
          $checkedProds['empty'] = 'Checked';
        }
        else if ($products_fill == 'full')
        {
          $updateDB = true;
          $updateTextProd = TEXT_INFO_ALL_PRODUCTS_FILLED;
          $checkedProds['full'] = 'Checked';
        }
        else      //assume clear all
        {
          tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_head_title_tag='', products_head_desc_tag = '', products_head_keywords_tag =  '' where products_id = '" . $products_tags['products_id'] . "' and language_id='". $langID ."'");
          $updateTextProd = TEXT_INFO_ALL_PRODUCTS_CLEARED;
          $checkedProds['clear'] = 'Checked';
        }
               
        if ($updateDB)
        {
          if ($productsMetaDesc == 'fillMetaDesc_yes')          //fill the description with all or part of the 
          {                                                     //product description
            if (! empty($products_tags['products_description']))
            {
              if (isset($productsMetaDescLength) && (int)$productsMetaDescLength > 3 && (int)$productsMetaDescLength < strlen($products_tags['products_description']))
                $desc = substr($products_tags['products_description'], 0, (int)$productsMetaDescLength);
              else if ((int)$productsMetaDescLength <= 3)       //length not entered or too small    
                $desc = $products_tags['products_description']; //so use the whole description
            }   
            else
              $desc = $products_tags['products_name'];  

            $checkedMetaDesc['no'] = '';
            $checkedMetaDesc['yes'] = 'Checked';
          }  
          else
          {        
            $desc = $products_tags['products_name'];           
            $checkedMetaDesc['no'] = 'Checked';
            $checkedMetaDesc['yes'] = '';
          }  

          tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_head_title_tag='".addslashes($products_tags['products_name'])."', products_head_desc_tag = '". addslashes(strip_tags($desc))."', products_head_keywords_tag =  '" . addslashes($products_tags['products_name']) . "' where products_id = '" . $products_tags['products_id'] . "' and language_id='". $langID ."'");
        } 
      }  
    }
  }
  else
  { 
    $checkedProds['none'] = 'Checked';
    $checkedMetaDesc['no'] = 'Checked';
    $checkedMetaDesc['yes'] = '';
  }
  
  $pageName = basename($_SERVER['PHP_SELF']);
  $pageContent = substr($pageName, 0, strpos($pageName, '.'));
  require(DIR_WS_ADMIN_TEMPLATES . ADMIN_TEMPLATE_NAME . TEMPLATE_MAIN_PAGE);
  
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>