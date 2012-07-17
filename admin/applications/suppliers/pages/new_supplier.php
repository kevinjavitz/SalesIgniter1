<?php
	$Suppliers = Doctrine_Core::getTable('Suppliers');
	if (isset($_GET['sID']) && empty($_POST)){
		$Supplier = $Suppliers->find((int) $_GET['sID']);
	}else{
		$Supplier = $Suppliers->getRecord();
    }

	$languages = tep_get_languages();
	
	$ajaxSaveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save')->addClass('ajaxSave')->setText(sysLanguage::get('TEXT_BUTTON_AJAX_SAVE'));
	$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save')->setText(sysLanguage::get('TEXT_BUTTON_SAVE'));
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel');
    $cancelButton->setHref(itw_app_link((isset($_GET['sID']) ? 'sID=' . $_GET['sID'] : ''), null, 'default'));
?>
<script language="javascript">
    <?php
            if($Supplier['suppliers_id'])
                echo 'var supplierID = ' . $Supplier['suppliers_id'].';';
            else
                echo 'var supplierID = 0;';
    ?>
</script>
 <form name="new_supplier" action="<?php echo itw_app_link(tep_get_all_get_params(array('action','sID')) . 'action=saveSupplier' . ((int)$Supplier['suppliers_id'] > 0 ? '&sID=' . $Supplier['suppliers_id'] : ''));?>" method="post" enctype="multipart/form-data">
 <div style="position:relative;text-align:right;"><?php
 	echo $ajaxSaveButton->draw() . $saveButton->draw() . $cancelButton->draw();
 	echo '<div class="pageHeading" style="position:absolute;left:0;top:.5em;">' . (isset($_GET['sID']) ? 'Edit Supplier' : 'New Supplier') . '</div>';
 ?></div>
 <br />
 <?php if (!isset($_GET['sID'])){ ?>
 <div class="ui-widget ui-widget-content ui-corner-all ui-state-warning newProductMessage" style="padding:.3em;font-weight:bold;">You are entering a new Supplier.</div>
 <br />
 <?php } ?>
 <div id="tab_container">
  <ul>
   <li class="ui-tabs-nav-item"><a href="#page-1"><span><?php echo sysLanguage::get('TAB_GENERAL');?></span></a></li>
  </ul>

  <div id="page-1"><?php include(sysConfig::get('DIR_WS_APP') . 'suppliers/pages_tabs/tab_general.php');?></div>

<?php
	/*$contents = EventManager::notifyWithReturn('NewProductTabBody', &$Supplier);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}*/
?>
 </div>
 <div style="position:relative;text-align:right;margin-top:.5em;margin-left:250px;"><?php
	echo $ajaxSaveButton->draw() . $saveButton->draw() . $cancelButton->draw();
 ?><div class="smallText" style="text-align:left;width:315px;position:absolute;right:.5em;top:3em;">*Image upload fields do not work with ajax save<br>So you'll need to use the normal save button for uploads</div></div>
</form>