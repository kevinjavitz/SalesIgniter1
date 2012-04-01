<?php
if(isset($_POST['selectTaxClass']) && $_POST['selectTaxClass'] > -1){
	$QProducts = Doctrine_Query::create()
	->update('Products')
	->set('products_tax_class_id','?', $_POST['selectTaxClass'])
	->execute();
	$messageStack->addSession('pageStack','Taxes Modified for all products');
}

EventManager::attachActionResponse(itw_app_link(null, 'tools', 'setTaxable'), 'redirect');

?>