<?php
apc_clear_cache('opcode');
$dir = sysConfig::getDirFsCatalog().'cache/';
foreach(glob($dir.'*.*') as $v){
	unlink($v);
}
?>