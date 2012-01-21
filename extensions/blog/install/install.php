<?php
/*
	Blog Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class blogInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('blog');
	}
	
	public function install(){
		if (defined('EXTENSION_BLOG_ENABLED')) return;
		
		parent::install();
	}
	
	public function uninstall($remove = false){
		if (!defined('EXTENSION_BLOG_ENABLED')) return;
		
		parent::uninstall($remove);
	}
}

?>