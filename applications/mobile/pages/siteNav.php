<?php
$ListView = htmlBase::newElement('list')
	->attr('data-role', 'listview')
	->attr('data-theme', 'c');

$ListView->addItem('', '<a href="' . itw_app_link(null, 'index', 'default') . '">Home</a>');
if ($userAccount->isLoggedIn() === true){
	$ListView->addItem('', '<a href="' . itw_app_link(null, 'account', 'default') . '">My Account</a>');
}else{
	$ListView->addItem('', '<a href="' . itw_app_link(null, 'mobile', 'createAccount') . '">Create Account</a>');
}
$ListView->addItem('', '<a href="' . itw_app_link(null, 'mobile', 'shoppingCart') . '">Shopping Cart</a>');
if ($userAccount->isLoggedIn() === true){
	$ListView->addItem('', '<a href="' . itw_app_link(null, 'account', 'logoff') . '">Logoff</a>');
}else{
	$ListView->addItem('', '<a href="' . itw_app_link(null, 'mobile', 'login') . '">Login</a>');
}

$pageContent->set('pageTitle', 'Site Navigation');
$pageContent->set('pageContent', $ListView->draw());
