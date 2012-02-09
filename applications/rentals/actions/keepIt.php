<?php
$productsId = $_GET['products_id'];
$_POST['queue_id'] = $_GET['customers_queue_id'];
$ShoppingCart->addProduct($productsId,'rental',1);

EventManager::attachActionResponse(itw_app_link(null,'shoppingCart','default'), 'redirect');


?>