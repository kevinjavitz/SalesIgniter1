<?php
	$Groups = Doctrine_Query::create()
	->select('products_options_groups_id, products_options_groups_name')
	->from('ProductsOptionsGroups')
	->orderBy('products_options_groups_name');
	
	$Groups = $Groups->execute()->toArray();
	if ($Groups){
		$selectBox = htmlBase::newElement('selectbox')->setName('option_group');
		$selectBox->addOption('', 'Please Select');
		foreach($Groups as $Group){
			$selectBox->addOption($Group['products_options_groups_id'], $Group['products_options_groups_name']);
		}
	}
	
	EventManager::attachActionResponse('This will remove you current group and replace it with the selected one.<br /><br />' . (isset($selectBox)?$selectBox->draw():'<b>No attribute Group created</b>') . '<br /><br /><small>*Changes are not saved until the product is saved</small>', 'html');
?>