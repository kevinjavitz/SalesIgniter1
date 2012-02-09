<?php
$groupId = (int) $_GET['gID'];
$AdminGroups = Doctrine_Core::getTable('AdminGroups')->find($groupId);

$varExtra = unserialize($AdminGroups->extra_data);
$varExtra['buttonsMultistoreEnabled']['hasCreateInvoice'] = false;
$varExtra['buttonsMultistoreEnabled']['hasPayInvoice'] = false;

if(isset($_POST['buttonsMultistoreEnabled'])){
	foreach($_POST['buttonsMultistoreEnabled'] as $but){
		if($but == 'hasCreateInvoice'){
			$varExtra['buttonsMultistoreEnabled']['hasCreateInvoice'] = true;
		}
		if($but == 'hasPayInvoice'){
			$varExtra['buttonsMultistoreEnabled']['hasPayInvoice'] = true;
		}
	}
}

$AdminGroups->extra_data = serialize($varExtra);

$AdminGroups->save();