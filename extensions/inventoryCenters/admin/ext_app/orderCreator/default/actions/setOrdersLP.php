<?php
$Editor->setData('inventory_center_lp', $_GET['id']);
$selectedInventory = '';
if(sysConfig::get('EXTENSION_INVENTORY_CENTERS_USE_LP') == 'True'){
	$QLP = Doctrine_Query::create()
	->from('InventoryCentersLaunchPoints')
	->where('lp_name = ?', $_GET['id'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if(isset($QLP[0])){
		$selectedInventory = $QLP[0]['inventory_center_id'];
		$Editor->setData('inventory_center_id', $selectedInventory);
	}
}
EventManager::attachActionResponse(array(
		'success' => true,
		'selectedInventory' => $selectedInventory
	), 'json');
