<?php
/*
	Categories Pages Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_categoriesPages extends ExtensionBase {
	
	public function __construct(){
		parent::__construct('categoriesPages');
	}
	
	public function init(){
		global $App, $appExtension, $Template;
		if ($this->enabled === false) return;
		
	}



}

?>