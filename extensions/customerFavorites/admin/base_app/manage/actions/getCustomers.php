<?php
    if(isset($_GET['cName'])){
		$cName = $_GET['cName'];
	}else{
	 	$cName = '';
	}
	$alreadyIn = array();
	if(isset($_POST['selectedCustomer']) && is_array($_POST['selectedCustomer'])){
		$alreadyIn = $_POST['selectedCustomer'];
	}
	//print_r($alreadyIn);
	$htmlCustomer = '<ul>';
	$QCustomers = Doctrine_Query::create()
	->from('Customers c')
	->where('(c.customers_lastname like "%'.$cName.'%" OR c.customers_firstname like "%'.$cName.'%" OR c.customers_email_address like "%'.$cName.'%")')
	->andWhereNotIn('c.customers_id', $alreadyIn)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);


	foreach($QCustomers as $iCustomer){
		$htmlCheckbox = htmlBase::newElement('checkbox')
		->setName('searchCustomer[]')
		->addClass('searchCustomer')
		->setValue($iCustomer['customers_id']);
		$htmlCustomer .= '<li>'.$htmlCheckbox->draw().' '.$iCustomer['customers_firstname'].' '.$iCustomer['customers_lastname'].'('.$iCustomer['customers_email_address'].')'.'</li>';
	}
	$htmlCustomer .= '</ul>';

	EventManager::attachActionResponse(array(
		'success' => true,
		'customers' => $htmlCustomer
	), 'json');
?>