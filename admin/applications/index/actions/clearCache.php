<?php
if (function_exists('apc_clear_cache')){
	apc_clear_cache('user');
}
$dir = sysConfig::getDirFsCatalog() . 'cache/admin/javascript/';
foreach(glob($dir . '*javascript*.*') as $v){
	unlink($v);
}
$dir = sysConfig::getDirFsCatalog() . 'cache/catalog/javascript/';
foreach(glob($dir . '*javascript*.*') as $v){
	unlink($v);
}

$dir = sysConfig::getDirFsCatalog() . 'cache/catalog/stylesheet/';
foreach(glob($dir . '*stylesheet*.*') as $v){
	unlink($v);
}
$dir = sysConfig::getDirFsCatalog() . 'cache/admin/stylesheet/';
foreach(glob($dir . '*stylesheet*.*') as $v){
	unlink($v);
}

?>