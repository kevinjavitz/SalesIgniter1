<?php
if (function_exists('apc_clear_cache')){
	apc_clear_cache('user');
}
$dir = sysConfig::getDirFsCatalog() . 'cache/';
foreach(glob($dir . '*javascript*.*') as $v){
	unlink($v);
}
foreach(glob($dir . '*stylesheet*.*') as $v){
	unlink($v);
}
?>