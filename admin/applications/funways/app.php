<?php
require(sysConfig::getDirFsAdmin() . 'includes/classes/upload.php');
require(sysConfig::getDirFsAdmin() . 'includes/classes/table_block.php');
require(sysConfig::getDirFsAdmin() . 'includes/classes/box.php');

require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
$currencies = new currencies();
$appContent = $App->getAppContentFile();
?>