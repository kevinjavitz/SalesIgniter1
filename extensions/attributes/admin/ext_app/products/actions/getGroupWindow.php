<?php
	$Groups = Doctrine_Query::create()
	->select('products_options_groups_id, products_options_groups_name')
	->from('ProductsOptionsGroups')
	->orderBy('products_options_groups_name');
	
	$Groups = $Groups->execute()->toArray();
	if ($Groups){
		$selectBox = htmlBase::newElement('selectbox')->setName('option_group');
		$selectBox->addOption('', 'No Group');
		foreach($Groups as $Group){
			$selectBox->addOption($Group['products_options_groups_id'], $Group['products_options_groups_name']);
		}
		$html = 'This will remove you current group and replace it with the selected one.<br /><br />' .$selectBox->draw(). '<br /><br /><small>*Changes are not saved until the product is saved</small>';
	}else{
		$html = 'Note: You have not yet created any attribute groups. You can do this <a href="'.itw_app_link('appExt=attributes','manage','default').'">here under product attributes</a>';
	}
	
	EventManager::attachActionResponse( $html, 'html');
?>