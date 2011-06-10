<?php
	ob_start();
?>
<div class="main"><b><?php echo sysLanguage::get('PRIMARY_ADDRESS_TITLE'); ?></b></div>
<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">
 <table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
   <td class="main" width="50%" valign="top"><?php echo sysLanguage::get('PRIMARY_ADDRESS_DESCRIPTION'); ?></td>
   <td align="right" width="50%" valign="top"><table border="0" cellspacing="0" cellpadding="2">
    <tr>
     <td class="main" align="center" valign="top"><b><?php echo sysLanguage::get('PRIMARY_ADDRESS_TITLE'); ?></b><br><?php echo tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?></td>
     <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
     <td class="main" valign="top"><?php echo $addressBook->formatAddress($addressBook->getDefaultAddressId(), true); ?></td>
    </tr>
   </table></td>
  </tr>
 </table>
</div>
<div class="main" style="margin-top:1em;"><b><?php echo sysLanguage::get('ADDRESS_BOOK_TITLE'); ?></b></div>
<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">
<?php
  foreach($addressBook->addresses as $aID => $address){
      if (is_numeric($aID)){
?>
<div class="main">
 <b><?php echo $address['entry_firstname'] . ' ' . $address['entry_lastname']; ?></b>
 <?php if ($aID == $addressBook->getDefaultAddressId()) echo '&nbsp;<small><i>' . sysLanguage::get('PRIMARY_ADDRESS') . '</i></small>'; ?>
 <?php if ($aID == $addressBook->getDeliveryDefaultAddressId()) echo '&nbsp;<small><i>' . sysLanguage::get('PRIMARY_SHIPPING_ADDRESS') . '</i></small>'; ?>
 <div style="position:relative;float:right;"><?php
  $editButton = htmlBase::newElement('button')->usePreset('edit')
  ->setHref(itw_app_link('edit=' . $aID, 'account', 'address_book_process', 'SSL'));
  $deleteButton = htmlBase::newElement('button')->usePreset('delete')
  ->setHref(itw_app_link('delete=' . $aID, 'account', 'address_book_process', 'SSL'));
  echo $editButton->draw() . $deleteButton->draw();
 ?></div>
</div>
<p style="margin:0;padding:0;margin-left:1em;"><?php echo $addressBook->formatAddress($aID, true); ?></p>
<br />
<?php
      }
  }
?>
</div>
<div class="smallText"><?php echo sprintf(sysLanguage::get('TEXT_MAXIMUM_ENTRIES'), sysConfig::get('MAX_ADDRESS_BOOK_ENTRIES')); ?></div>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
    ->setHref(itw_app_link(null, 'account', 'default', 'SSL'))
    ->draw();
    if (sizeof($addressBook->addresses) < sysConfig::get('MAX_ADDRESS_BOOK_ENTRIES')) {
    	$pageButtons .= htmlBase::newElement('button')
    	->usePreset('install')
    	->setText(sysLanguage::get('TEXT_BUTTON_ADD_ADDRESS'))
    	->setHref(itw_app_link(null, 'account', 'address_book_process', 'SSL'))
    	->draw();
    }
    
	$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE_ADDRESS_BOOK'));
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
