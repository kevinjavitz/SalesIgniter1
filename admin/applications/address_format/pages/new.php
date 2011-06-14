	<?php
	$AddressFormat = Doctrine_Core::getTable('AddressFormat');
	if (isset($_GET['fID']) && empty($_POST)){
		$AddressFormat = $AddressFormat->find((int)$_GET['fID']);
	}else{
		$AddressFormat = $AddressFormat->getRecord();
	}

	$AddressBook = Doctrine_Core::getTable('AddressBook');
	$addressBookColumns = $AddressBook->getColumns();
	$columns = '';
	$myColumn = 'country';
	$columns .= '$'. $myColumn. '<br/>';

	$myColumn = 'abbrstate';
	$columns .= '$'. $myColumn. '<br/>';

	foreach ($addressBookColumns as $column =>$value){

		if(strpos($column,'_id') === false){
			$myColumn = str_replace('entry_','', $column);
			$columns .= '$'. $myColumn. '<br/>';
		}


	}


?>
<form name="new_adress_format" action="<?php echo itw_app_link(tep_get_all_get_params(array('action')) . 'action=save');?>" method="post" enctype="multipart/form-data">
<div class="pageHeading"><?php
 echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />

<div id="tab_container">
 <ul>
  <li class="ui-tabs-nav-item"><a href="#page-2"><span><?php echo sysLanguage::get('TAB_DESCRIPTION');?></span></a></li>
 </ul>

 <div id="page-2"><?php include(sysConfig::getDirFsAdmin() . 'applications/address_format/pages_tabs/tab_description.php');?></div>

</div>
<br />
<div style="text-align:right"><?php
   $saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
   $cancelButton = htmlBase::newElement('button')->usePreset('cancel')
   ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));

   echo $saveButton->draw() . $cancelButton->draw();
?></div>
</form>