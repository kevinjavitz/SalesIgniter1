<?php
$productsId = $_GET['products_id'];
$_POST['purchase_type'] = 'membershipRental';
$_POST['queue_id'] = $_GET['customers_queue_id'];
/*$QRentedProducts = Doctrine_Query::create()
->from('RentedProducts')
->where('customers_id = ?', $userAccount->getCustomerId())
->andWhere('products_id = ?', $productsId)
->execute(array(), Doctrine_Core::HYDRATE_ARRAY);*/

$ShoppingCart->add($productsId);

EventManager::attachActionResponse(itw_app_link(null,'shoppingCart','default'), 'redirect');


?>