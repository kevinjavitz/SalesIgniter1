<?php
	$Qorder = Doctrine_Query::create()
		->select('customers_id')
		->from('Orders')
		->where('orders_id = ?', $_GET['oID'])
		->fetchOne();

?>
<?php
    if(!empty($Qorder->terms)){
		echo 'Order Date: ' . date('m/d/Y', strtotime($Qorder->date_purchased)) . '<br/>';
		echo $Qorder->terms;
	}
?>
