<?php
	apc_clear_cache('user');
	$dir = sysConfig::getDirFsCatalog().'cache/';
	foreach(glob($dir.'*javascript*.*') as $v){
		unlink($v);
	}
	foreach(glob($dir.'*stylesheet*.*') as $v){
		unlink($v);
	}
?>