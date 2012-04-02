<?php
/*
	Manage QuickBooks Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2012 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_manageQuickBooks extends ExtensionBase {
	
	public function __construct(){
		parent::__construct('manageQuickBooks');
	}
	
	public function init(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvent('BoxDataManagerAddLink', null, $this);
	}
	
	public function BoxDataManagerAddLink(&$contents){
		$contents[] = array(
			'link'       => itw_app_link('appExt=manageQuickBooks','manageQuickBooks','default','SSL'),
			'text' => 'Manage QuickBooks'
		);
	}
	

}
?>