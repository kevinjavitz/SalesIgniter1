<?php
	ob_start();
  if (isset($_GET['delete'])) {
?>
<div class="main"><b><?php echo sysLanguage::get('DELETE_ADDRESS_TITLE'); ?></b></div>
<div class="ui-widget ui-widget-content ui-corner-all" style="padding:.5em;">
 <table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
   <td class="main" width="50%" valign="top"><?php echo sysLanguage::get('DELETE_ADDRESS_DESCRIPTION'); ?></td>
   <td align="right" width="50%" valign="top"><table border="0" cellspacing="0" cellpadding="2">
    <tr>
     <td class="main" align="center" valign="top"><b><?php echo sysLanguage::get('SELECTED_ADDRESS'); ?></b><br><?php echo tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?></td>
     <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
     <td class="main" valign="top"><?php echo $addressBook->formatAddress($_GET['delete'], true); ?></td>
    </tr>
   </table></td>
  </tr>
 </table>
</div>
<?php
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null, 'account', 'address_book', 'SSL'))
	->draw();
	
	$pageButtons .= htmlBase::newElement('button')
	->usePreset('delete')
	->setHref(itw_app_link('action=deleteAddress&delete=' . $_GET['delete'] , 'account', 'address_book_process', 'SSL'))
	->draw();
  } else {
?>
<div class="ui-widget ui-widget-content ui-corner-all" style="padding:.5em;"><?php
	include('address_book_details.php');
?></div>
<?php
	if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
		$pageButtons = htmlBase::newElement('button')
		->usePreset('back')
		->setHref(itw_app_link(null, 'account', 'address_book', 'SSL'))
		->draw();
	
		$pageButtons .= htmlBase::newElement('button')
		->usePreset('save')
		->setType('submit')
		->draw();
    } else {
		$back_link = itw_app_link(null, 'account', 'address_book', 'SSL');
		
		$pageButtons = htmlBase::newElement('button')
		->usePreset('back')
		->setHref($back_link)
		->draw();
	
		$pageButtons .= tep_draw_hidden_field('action', 'process') . htmlBase::newElement('button')
		->usePreset('continue')
		->setType('submit')
		->draw();
    }
  }
  
  $pageContents = ob_get_contents();
  ob_end_clean();

	if (isset($_GET['edit'])){
		$pageTitle = sysLanguage::get('HEADING_TITLE_ADDRESS_BOOK_PROCESS_MODIFY_ENTRY');
	}elseif (isset($_GET['delete'])){
		$pageTitle = sysLanguage::get('HEADING_TITLE_ADDRESS_BOOK_PROCESS_DELETE_ENTRY');
	}else{
		$pageTitle = sysLanguage::get('HEADING_TITLE_ADDRESS_BOOK_PROCESS_ADD_ENTRY');
	}
	
	if (!isset($_GET['delete'])){
		$pageContent->set('pageForm', array(
			'name' => 'addressbook',
			'action' => itw_app_link('action=saveAddress' . (isset($_GET['edit']) ? '&edit=' . $_GET['edit'] : ''), 'account', 'address_book_process', 'SSL'),
			'method' => 'post'
		));
	}
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
?>