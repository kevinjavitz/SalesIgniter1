<?php
   require('includes/application_top.php');

function add_extra_fields($table, $column, $column_attr = 'VARCHAR(255) NULL'){

	$db=sysConfig::get('DB_DATABASE');
	$link = mysql_connect(sysConfig::get('DB_SERVER'), sysConfig::get('DB_SERVER_USERNAME'), sysConfig::get('DB_SERVER_PASSWORD'));
	if (! $link){
		die(mysql_error());
	}
	mysql_select_db($db , $link) or die("Select Error: ".mysql_error());

	$exists = false;
	$columns = mysql_query("show columns from $table");
	while($c = mysql_fetch_assoc($columns)){
		if($c['Field'] == $column){
			$exists = true;
			break;
		}
	}

	if(!$exists){
		mysql_query("ALTER TABLE `$table` ADD `$column`  $column_attr") or die("An error occured when running \n ALTER TABLE `$table` ADD `$column`  $column_attr \n" . mysql_error());
	}

}



add_extra_fields('admin','admin_override_password',"VARCHAR( 40 ) NOT NULL DEFAULT  ''");
add_extra_fields('admin','admins_stores'," text NOT NULL");
add_extra_fields('admin','admins_main_store',"int(11) NOT NULL");
add_extra_fields('admin','admin_simple_admin',"int(1) NOT NULL default '0'");
add_extra_fields('admin','admin_favs_id',"int(11) NOT NULL");

mkdir(sysConfig::getDirFsCatalog().'temp/pdf');
mkdir(sysConfig::getDirFsCatalog().'cache');

require(sysConfig::getDirFsCatalog() . 'includes/classes/ftp/base.php');
$Ftp = new SystemFTP();
$Ftp->connect();
$Ftp->makeWritable(sysConfig::getDirFsCatalog().'images');
$Ftp->makeWritable(sysConfig::getDirFsCatalog().'cache');
$Ftp->makeWritable(sysConfig::getDirFsCatalog().'temp');
$Ftp->makeWritable(sysConfig::getDirFsCatalog().'temp/pdf');
$Ftp->makeWritable(sysConfig::getDirFsCatalog().'extensions/imageRot/images');
$Ftp->makeWritable(sysConfig::getDirFsCatalog().'extensions/pdfPrinter/images');
$Ftp->makeWritable(sysConfig::getDirFsCatalog().'templates');


require(sysConfig::getDirFsCatalog() . 'includes/classes/fileSystemBrowser.php');
$templates = new fileSystemBrowser(sysConfig::getDirFsCatalog()  . 'templates/');
$directories = $templates->getDirectories();

foreach($directories as $dirInfo){
	$Ftp->makeWritable(sysConfig::getDirFsCatalog().'templates/'.$dirInfo['basename'].'/images');
}

$iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator(sysConfig::getDirFsCatalog().'includes/languages'),
	RecursiveIteratorIterator::SELF_FIRST);

$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);

foreach($iterator as $file) {
	if(($file->isDir() || $file->isFile())) {
		$Ftp->makeWritable($file->getRealpath());
	}
}

$Ftp->disconnect();

/*
add_extra_fields('stores','stores_street_address'," text NOT NULL");
add_extra_fields('stores','stores_postcode'," text NOT NULL");
add_extra_fields('stores','stores_reg_number'," text NOT NULL");
add_extra_fields('stores','stores_vat_number'," text NOT NULL");
//add_extra_fields('stores','stores_street_address'," text NOT NULL");


$sqlData = file('ext/update/ses2.sql');
foreach($sqlData as $i => $line){
	if (strlen(trim($line)) > 0 && trim($line) != ''){
		try{
			$conn->exec($line);
		}catch(Exception $exception){
			error_log($exception);
		}
	}
}           */

require('includes/application_bottom.php');
?>