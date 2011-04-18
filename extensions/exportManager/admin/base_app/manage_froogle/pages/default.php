<?php
/*
	Export Manager Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
	  <?php
	        echo "The script to call or add to cron is: ". itw_catalog_app_link('appExt=exportManager&action=exportFroogle','do_export','default','SSL');

	  ?>
  </div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td align="right" class="smallText"><?php

    $doExportNowButton = htmlBase::newElement('button')->usePreset('install')->setText(sysLanguage::get('TEXT_BUTTON_DO_EXPORT_NOW'))
   	->setHref(itw_app_link('appExt=exportManager&action=exportFroogle','manage_froogle','default','SSL'));

   	echo $doExportNowButton->draw();
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php

 ?></div>