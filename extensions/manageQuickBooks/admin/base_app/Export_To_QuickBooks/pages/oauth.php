<?php
/*
	Manage QuickBooks Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2012 I.T. Web Experts

	This script and its source is not redistributable
*/
    

require_once sysConfig::getDirFsCatalog() . 'extensions/manageQuickBooks/QuickBooks.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$token = sysConfig::get('EXTENSION_MANAGE_QUICKBOOKS_TOKEN');
$oauth_consumer_key= sysConfig::get('EXTENSION_MANAGE_QUICKBOOKS_KEY');
$oauth_consumer_secret= sysConfig::get('EXTENSION_MANAGE_QUICKBOOKS_SECRET');

$domain = sysConfig::get('HTTP_DOMAIN_NAME');
//change to ssl
$this_url =  'http://' . $domain .  '/admin/manageQuickBooks/Export_To_QuickBooks/oauth.php';
$that_url =  'http://' . $domain .  '/admin/manageQuickBooks/Export_To_QuickBooks/exportToQB.php';

$dsn=$connString;
//$encryption_key='35271962697020860610';
$encryption_key= sysConfig::get('EXTENSION_MANAGE_QUICKBOOKS_ENCRYPT');

$the_username = Session::get('login_firstname') . Session::get('login_id');
$the_tenant = 12345;

if (!QuickBooks_Utilities::initialized($dsn))
{
	// Initialize creates the neccessary database schema for queueing up requests and logging
	QuickBooks_Utilities::initialize($dsn);
}
$IntuitAnywhere = new QuickBooks_IPP_IntuitAnywhere($dsn, $encryption_key, $oauth_consumer_key, $oauth_consumer_secret, $this_url, $that_url);

echo "SalesIgniter is now exporting to QuickBooks - Please wait..\n";
echo "You will receive an email will the process completes.";

// Try to handle the OAuth request 
if ($IntuitAnywhere->handle($the_username, $the_tenant))

{
   ; // The user has been connected, and will be redirected to $that_url automatically. 

}
else
{
	// If this happens, something went wrong with the OAuth handshake
	die('Oh no, something bad happened: ' . $IntuitAnywhere->errorNumber() . ': ' . $IntuitAnywhere->errorMessage());
}


?>
