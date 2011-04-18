<?php
/*
  $Id: admin_account.php,v 1.29 2002/03/17 17:52:23 harley_vb Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  $current_boxes = DIR_FS_ADMIN . DIR_WS_BOXES;
  
  $pageName = basename($_SERVER['PHP_SELF']);
  $pageContent = substr($pageName, 0, strpos($pageName, '.'));
  require(DIR_WS_ADMIN_TEMPLATES . ADMIN_TEMPLATE_NAME . TEMPLATE_MAIN_PAGE);
  
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>