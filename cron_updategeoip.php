<?php
require('includes/application_top.php');
$multiStore = $appExtension->getExtension('multiStore');
if($multiStore !== false && $multiStore->isEnabled()=== true ){
	require(sysConfig::getDirFsCatalog() . 'includes/classes/ftp/base.php');
	$Ftp = new SystemFTP();
	$Ftp->connect();
	$Ftp->makeWritable(sysConfig::getDirFsCatalog(). 'extensions/multiStore/geoip/GeoIP.dat');
	$zip = new ZipArchive();
     $res = $zip->open(sysConfig::getDirFsCatalog(). 'extensions/multiStore/geoip/GeoIP.zip');
     if ($res === true) {
         $zip->extractTo(sysConfig::getDirFsCatalog(). 'extensions/multiStore/geoip/');
         $zip->close();
     }
}
require('includes/application_bottom.php');
?>