<?php
/*
	Info Pages Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_exportManager extends ExtensionBase {
	
	public function __construct(){
		parent::__construct('exportManager');
	}
	
	public function init(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvent('BoxDataManagerAddLink', null, $this);
	}
	
	public function BoxDataManagerAddLink(&$contents){
		$contents[] = array(
			'link'       => itw_app_link('appExt=exportManager','manage_froogle','default','SSL'),
			'text' => 'Manage Froogle Feed'
		);
	}
	

}
?>