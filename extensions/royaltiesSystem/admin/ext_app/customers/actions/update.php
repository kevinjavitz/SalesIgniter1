<?php
	Doctrine_Query::create()
	->update('Customers')
	->set('is_content_provider', '?', (int) (isset($_POST['isContentProvider'])))
	->where('customers_id = ?', (int)$userAccount->getCustomerId())
	->execute();
?>