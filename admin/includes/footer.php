<?php
/*
  $Id: footer.php,v 1.12 2003/02/17 16:54:12 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
   if ($ExceptionManager->size() > 0){
       echo '<br /><div style="width:98%;margin-right:auto;margin-left:auto;">' . $ExceptionManager->output() . '</div>';
   }
  if ($messageStack->size('footerStack') > 0){
      echo '<br><div style="width:98%;margin-right:auto;margin-left:auto;">' . $messageStack->output('footerStack') . '</div>';
  }
    
/*
 * @Todo: Remove this when we have done enough to not be required to keep it
 
 
  The following copyright announcement is in compliance
  to section 2c of the GNU General Public License, and
  thus can not be removed, or can only be modified
  appropriately.

  For more information please read the following
  Frequently Asked Questions entry on the osCommerce
  support site:

  http://www.oscommerce.com/community.php/faq,26/q,50

  Please leave this comment intact together with the
  following copyright announcement.
*/
?>