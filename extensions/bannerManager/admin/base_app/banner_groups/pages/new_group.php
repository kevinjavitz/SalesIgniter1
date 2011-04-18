<?php
	$Groups = Doctrine_Core::getTable('BannerManagerGroups');
	if (isset($_GET['gID']) && empty($_POST)){
		$Group = $Groups->findOneByBannerGroupId((int)$_GET['gID']);
		$Group->refresh(true);
	}else{
		$Group = $Groups->getRecord();
	}

?>
<form name="new_group" action="<?php echo itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) . 'action=saveGroup');?>" method="post" enctype="multipart/form-data">
<div class="pageHeading"><?php
 echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />

<div id="tab_container">
 <ul>
  <li class="ui-tabs-nav-item"><a href="#page-2"><span><?php echo sysLanguage::get('TAB_DESCRIPTION');?></span></a></li>
<?php
	$contents = EventManager::notifyWithReturn('NewGroupTabHeader');
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
?>
 </ul>

 <div id="page-2"><?php include(sysConfig::getDirFsCatalog() . 'extensions/bannerManager/admin/base_app/banner_groups/pages_tabs/tab_description.php');?></div>
<?php
	$contents = EventManager::notifyWithReturn('NewGroupTabBody', &$Group);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
?>
</div>
<br />
<div style="text-align:right"><?php
   $saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
   $cancelButton = htmlBase::newElement('button')->usePreset('cancel')
   ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));

   echo $saveButton->draw() . $cancelButton->draw();
?></div>
</form>