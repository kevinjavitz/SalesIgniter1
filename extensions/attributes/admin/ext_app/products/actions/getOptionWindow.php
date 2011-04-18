<?php
	$Options = Doctrine_Query::create()
	->select('o.products_options_id, od.products_options_name')
	->from('ProductsOptions o')
	->leftJoin('o.ProductsOptionsDescription od')
	->orderBy('od.products_options_name');
	
	$Options = $Options->execute()->toArray();
	if ($Options){
		$selectBox = htmlBase::newElement('selectbox')->setName('option');
		$selectBox->addOption('', 'Please Select');
		foreach($Options as $Option){
			$selectBox->addOption($Option['products_options_id'], $Option['ProductsOptionsDescription'][Session::get('languages_id')]['products_options_name']);
		}
	}
	
	EventManager::attachActionResponse('Select an option below to add it.<br /><br />' . $selectBox->draw() . '<br /><br /><small>*Changes are not saved until the product is saved</small>', 'html');
?>