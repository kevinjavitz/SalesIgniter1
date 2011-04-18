<?php
/*
	Waiting List Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class waitingListInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('waitingList');
	}
	
	public function install(){
		if (sysConfig::exists('EXTENSION_WAITINGLIST_ENABLED') === true) return;
		
		parent::install();
		
		$Email = new EmailTemplates();
		$Email->email_templates_name = '(Site) Waiting List Notify';
		$Email->email_templates_event = 'waiting_list_notify';
		$Email->EmailTemplatesVariables[0]->event_variable = 'fullName';
		$Email->EmailTemplatesVariables[0]->is_conditional = '0';
		$Email->EmailTemplatesVariables[1]->event_variable = 'productName';
		$Email->EmailTemplatesVariables[1]->is_conditional = '0';
		$Email->EmailTemplatesVariables[2]->event_variable = 'productInfoLink';
		$Email->EmailTemplatesVariables[2]->is_conditional = '0';
		foreach(sysLanguage::getLanguages() as $lInfo){
			$Email->EmailTemplatesDescription[$lInfo['id']]->language_id = $lInfo['id'];
			$Email->EmailTemplatesDescription[$lInfo['id']]->email_templates_subject = 'Requested Product Is In Stock';
			$Email->EmailTemplatesDescription[$lInfo['id']]->email_templates_content = 'Dear {$fullName},' . "\n\r" . 
				'The product you requested to be notified about ( {$productName} ) is now in stock, click below to go to the products information page.' . "\n\r" . 
				'<a href="{$productInfoLink}">{$productName}</a>';
		}
		$Email->save();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_WAITINGLIST_ENABLED') === false) return;
		
		parent::uninstall($remove);
		
		Doctrine_Query::create()
		->delete('EmailTemplates')
		->where('email_templates_event = ?', 'waiting_list_notify')
		->execute();
	}
}
?>