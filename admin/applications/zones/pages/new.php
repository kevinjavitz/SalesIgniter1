<?php
	$GoogleZones = Doctrine_Core::getTable('GoogleZones');
	if (isset($_GET['zID']) && empty($_POST)){
		$GoogleZones = $GoogleZones->findOneByGoogleZonesId((int)$_GET['zID']);		
	}else{
		$GoogleZones = $GoogleZones->getRecord();
	}

?>
<form name="new_zone_center" action="<?php echo itw_app_link(tep_get_all_get_params(array('action')) . 'action=save');?>" method="post" enctype="multipart/form-data">
<div class="pageHeading"><?php
 echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />

<div id="tab_container">
 <ul>
  <li class="ui-tabs-nav-item"><a href="#page-2"><span><?php echo sysLanguage::get('TAB_DESCRIPTION');?></span></a></li>
 </ul>

 <div id="page-2"><?php include(sysConfig::getDirFsAdmin() . 'applications/zones/pages_tabs/tab_description.php');?></div>

</div>
<br />
<div style="text-align:right"><?php
   $saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
   $cancelButton = htmlBase::newElement('button')->usePreset('cancel')
   ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));

   echo $saveButton->draw() . $cancelButton->draw();
?></div>
</form>
