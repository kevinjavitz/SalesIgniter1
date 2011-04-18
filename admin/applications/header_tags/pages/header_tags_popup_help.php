<?php
/*
  $Id: header_tags_popup_help.php,v 1.0 2005/09/22 13:45:11 devosc Exp $
  produced by Jack_mcs
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
 
  require("includes/application_top.php");
  
  $pageName = basename($_SERVER['PHP_SELF']);
  $pageContent = substr($pageName, 0, strpos($pageName, '.'));
  require(DIR_WS_ADMIN_TEMPLATES . ADMIN_TEMPLATE_NAME . TEMPLATE_MAIN_PAGE);
  
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>?>