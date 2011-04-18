<?php
/*
	Blog Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

//if (!isset($_GET['post_id'])){
    $App->setAppPage('default');
//}


	$appContent = $App->getAppContentFile();

if ($App->getAppPage() == 'default'){
		//$javascriptFiles[] =  'admin/rental_wysiwyg/ckeditor.js';
		//$javascriptFiles[] = 'admin/rental_wysiwyg/adapters/jquery.js';
	}
?>