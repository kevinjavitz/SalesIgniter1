<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<?php require(DIR_WS_INCLUDES . 'meta_tags.php'); ?>
<title><?php echo META_TAG_TITLE; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<meta name="keywords" content="<?php echo META_TAG_KEYWORDS; ?>">
<meta name="description" content="<?php echo META_TAG_DESCRIPTION; ?>">
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="<?php echo (bts_select('stylesheet','stylesheet.css')); // BTSv1.5 ?>">
<link rel="stylesheet" type="text/css" href="<?php echo (bts_select('stylesheet','stylesheet-new.css')); // BTSv1.5 ?>">
<link rel="stylesheet" type="text/css" href="<?php echo (bts_select('stylesheet','print.css')); // BTSv1.5 ?>" media="print">
<script type="text/javascript" language="javascript" src="<?echo DIR_WS_JAVASCRIPT;?>behavior.js"></script>
<script type="text/javascript" language="javascript" src="<?echo DIR_WS_JAVASCRIPT;?>rating.js"></script>
<script type="text/javascript" language="javascript" src="ext/jQuery/jQuery.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo (bts_select('stylesheet','rating.css'));?>" />
<?php if (isset($javascript) && file_exists(DIR_WS_JAVASCRIPT . basename($javascript))) { require(DIR_WS_JAVASCRIPT . basename($javascript)); } ?>
</head>
<body>
<!-- warnings //-->
<?php require(DIR_WS_INCLUDES . 'warnings.php'); ?>
<!-- warning_eof //-->
<?php
// include i.e. template switcher in every template
if(bts_select('common', 'common_top.php')) include (bts_select('common', 'common_top.php')); // BTSv1.5
?>
<!-- header //-->
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr class="header">
    <td valign="middle"><?php echo '<a href="' . itw_app_link(null, 'index', 'default') . '">' . tep_image(DIR_WS_IMAGES . 'oscommerce.gif', 'osCommerce') . '</a>'; ?></td>
    <td align="right" valign="bottom"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_account.gif', HEADER_TITLE_MY_ACCOUNT) . '</a>&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_SHOPPING_CART) . '">' . tep_image(DIR_WS_IMAGES . 'header_cart.gif', HEADER_TITLE_CART_CONTENTS) . '</a>&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_checkout.gif', HEADER_TITLE_CHECKOUT) . '</a>'; ?>&nbsp;&nbsp;</td>
  </tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="1">
  <tr class="headerNavigation">
    <td class="headerNavigation">vxvxcvcvcxvcxvxc&nbsp;&nbsp;<?php echo $breadcrumb->trail(' &raquo; '); ?></td>
    <td align="right" class="headerNavigation"><a href="<?php echo tep_href_link(FILENAME_RENTAL_TOP); ?>" class="headerNavigation"><?php echo sysLanguage::get('HEADER_TITLE_RENTAL_TOP'); ?></a> &nbsp;|&nbsp; <?php if (tep_session_is_registered('customer_id')) { ?><a href="<?php echo itw_app_link(null, 'account', 'logoff'); ?>" class="headerNavigation"><?php echo sysLanguage::get('HEADER_TITLE_LOGOFF'); ?></a> &nbsp;|&nbsp; <?php } ?><a href="<?php echo tep_href_link(FILENAME_ACCOUNT, '', 'SSL'); ?>" class="headerNavigation"><?php echo sysLanguage::get('HEADER_TITLE_MY_ACCOUNT'); ?></a> &nbsp;|&nbsp; <a href="<?php echo tep_href_link(FILENAME_RENTAL_QUEUE); ?>" class="headerNavigation"><?php echo sysLanguage::get('HEADER_TITLE_QUEUE_CONTENTS'); ?></a> &nbsp;|&nbsp; <a href="<?php echo tep_href_link(FILENAME_SHOPPING_CART); ?>" class="headerNavigation"><?php echo sysLanguage::get('HEADER_TITLE_CART_CONTENTS'); ?></a> &nbsp;|&nbsp; <a href="<?php echo tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'); ?>" class="headerNavigation"><?php echo sysLanguage::get('HEADER_TITLE_CHECKOUT'); ?></a> &nbsp;&nbsp;</td>
  </tr>
</table>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- column_left //-->
<?php require(bts_select('column', 'column_left.php')); // BTSv1.5 ?>
<!-- column_left eof //-->
    </table></td>
    <td width="100%" valign="top"><!-- content //--><?php
    
     require (bts_select ('content')); // BTSv1.5 
     
     ?><!-- content_eof //--></td>    
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- column_right //-->
<?php require(bts_select('column', 'column_right.php')); // BTSv1.5 ?>
<!-- column_right eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'counter.php'); ?>
<table border="0" width="100%" cellspacing="0" cellpadding="1">
  <tr class="footer">
    <td class="footer">&nbsp;&nbsp;<?php echo strftime(sysLanguage::getDateFormat('long')); ?>&nbsp;&nbsp;</td>
    <td align="right" class="footer">&nbsp;&nbsp;<?php echo $counter_now . ' ' . FOOTER_TEXT_REQUESTS_SINCE . ' ' . $counter_startdate_formatted; ?>&nbsp;&nbsp;</td>
  </tr>
</table>
<br>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" class="smallText">
<?php
/*
  The following copyright announcement can only be
  appropriately modified or removed if the layout of
  the site theme has been modified to distinguish
  itself from the default osCommerce-copyrighted
  theme.

  For more information please read the following
  Frequently Asked Questions entry on the osCommerce
  support site:

  http://www.oscommerce.com/community.php/faq,26/q,50

  Please leave this comment intact together with the
  following copyright announcement.
*/

  echo sysLanguage::get('FOOTER_TEXT_BODY')
?>
    </td>
  </tr>
</table>
<!-- footer_eof //-->
<br>
</body>
</html>
