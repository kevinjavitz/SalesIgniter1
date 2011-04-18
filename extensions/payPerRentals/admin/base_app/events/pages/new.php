<?php
	$Event = Doctrine_Core::getTable('PayPerRentalEvents');
	if (isset($_GET['eID']) && empty($_POST)){
		$Event = $Event->findOneByEventsId((int)$_GET['eID']);
		//$Inventory->refresh(true);
	}else{
		$Event = $Event->getRecord();
	}

?>
<form name="new_event" action="<?php echo itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) . 'action=save');?>" method="post" enctype="multipart/form-data">
<div class="pageHeading"><?php
 echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />

<div id="tab_container">
 <ul>
  <li class="ui-tabs-nav-item"><a href="#page-2"><span><?php echo sysLanguage::get('TAB_DESCRIPTION');?></span></a></li>
 </ul>

 <div id="page-2"><?php include(sysConfig::getDirFsCatalog() . 'extensions/payPerRentals/admin/base_app/events/pages_tabs/tab_description.php');?></div>

</div>
<br />
<div style="text-align:right"><?php
   $saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
   $cancelButton = htmlBase::newElement('button')->usePreset('cancel')
   ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));

   echo $saveButton->draw() . $cancelButton->draw();
?></div>
</form>