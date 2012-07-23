<?php
/*
	Manage QuickBooks Extension Version 1  
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2012 I.T. Web Experts

	This script and its source is not redistributable
*/

?>
<!-- might take this out of body? -->
<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
<script>intuit.ipp.anywhere.setup({
    menuProxy: '<?php echo 'http://' . sysConfig::get('HTTP_DOMAIN_NAME') . '/admin/manageQuickBooks/Export_To_QuickBooks/menu.php'; ?>',
    grantUrl: '<?php echo 'http://' . sysConfig::get('HTTP_DOMAIN_NAME') .  '/admin/manageQuickBooks/Export_To_QuickBooks/oauth.php'; ?>'
});</script>


 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
	  <?php
	     echo sysLanguage::get('QB_WARNING') . "<br>";
	  ?>
  </div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td align="right" class="smallText"><?php

   /* $doExportNowButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_DO_EXPORT_NOW'))
       ->setTooltip(sysLanguage::get('TEXT_BUTTON_DO_EXPORT_NOW'))
   	->setHref(itw_app_link('appExt=manageQuickBooks&action=exportToQuickBooks','Export_To_QuickBooks','default','SSL'));

   	echo $doExportNowButton->draw();*/
       
    ?>&nbsp;</td><ipp:connectToIntuit></ipp:connectToIntuit>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php

 ?></div>