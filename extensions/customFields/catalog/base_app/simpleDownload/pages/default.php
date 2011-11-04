<?php
	if (!file_exists(sysConfig::getFirFsCatalog() . 'images/' . basename($_GET['filename']))){
		echo 'File does not exist.';
		itwExit();
	}


	header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
	header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-Type: Application/octet-stream");
	header("Content-disposition: attachment; filename=images/" . $_GET['filename']);

	readfile(sysConfig::getFirFsCatalog() . 'images/' .$_GET['filename']);
?>