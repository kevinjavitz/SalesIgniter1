<?php
	chdir('../../../../');
	require('includes/application_top.php');
	Session::set('minfee', $_GET['minfeeVal']);
	tep_redirect(itw_app_link(null,'checkout','default','SSL'));
	require('includes/application_bottom.php');

?>