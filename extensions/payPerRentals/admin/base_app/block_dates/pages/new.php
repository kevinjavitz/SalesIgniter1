<?php
	$Period = Doctrine_Core::getTable('PayPerRentalBlockedDates');
	if (isset($_GET['pID']) && empty($_POST)){
		$Period = $Period->find((int)$_GET['pID']);
		//$Inventory->refresh(true);
	}else{
		$Period = $Period->getRecord();
	}

?>
<form name="new_period" action="<?php echo itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) . 'action=save');?>" method="post" enctype="multipart/form-data">
<div class="pageHeading"><?php
 echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />

<div id="tab_container">
 <ul>
  <li class="ui-tabs-nav-item"><a href="#page-2"><span><?php echo sysLanguage::get('TAB_DESCRIPTION');?></span></a></li>
 </ul>

 <div id="page-2"><?php include(sysConfig::getDirFsCatalog() . 'extensions/payPerRentals/admin/base_app/block_dates/pages_tabs/tab_description.php');?></div>

</div>
<br />
<div style="text-align:right"><?php
   $saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
   $cancelButton = htmlBase::newElement('button')->usePreset('cancel')
   ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));

   echo $saveButton->draw() . $cancelButton->draw();
?></div>
</form>