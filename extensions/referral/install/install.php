<?php
/*
	Related Products Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class referralInstall extends extensionInstaller {

	public function __construct(){
		parent::__construct('referral');
	}

	public function install(){
		global $currencies;
		if (sysConfig::exists('EXTENSION_REFFERAL_SYSTEM_ENABLED') === true) return;

		parent::install();

		$EmailTemplates = Doctrine_Core::getTable('EmailTemplates');
		$Template = $EmailTemplates->create();
		$Template->email_templates_name = 'referral_earned_create_account';
		$Template->email_templates_event = 'referral_earned_create_account';
		$Template->email_templates_attach = '';
		$Descriptions = $Template->EmailTemplatesDescription;
		$Variables = $Template->EmailTemplatesVariables;
		$emailTemplateVars = array('firstname','lastname','fullname','email_address', 'pointsEarned');
		foreach($emailTemplateVars as $varName){
			$Variable = new EmailTemplatesVariables();
			$Variable->event_variable = $varName;
			$Variable->is_conditional = '0';
			$Variables[] = $Variable;
		}
		$languages = sysLanguage::getLanguages();
		$pointsEarned = $currencies->format($pointsEarned);
		foreach($languages as $lInfo){
				$Descriptions[$lInfo['id']]->email_templates_subject = 'You have earned a referral reward';
				$Descriptions[$lInfo['id']]->email_templates_content = 'Congratulations {$fullName} has used your referral code and you have earned {$pointsEarned} in rewards points';
				$Descriptions[$lInfo['id']]->language_id = $lInfo['id'];
		}
		$Template->save();
	}

	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_REFFERAL_SYSTEM_ENABLED') === false) return;

		$EmailTemplates = Doctrine_Core::getTable('EmailTemplates');
		$Template = $EmailTemplates->findByEmailTemplatesEvent('referral_earned_create_account');
		$Template->delete();
		parent::uninstall($remove);
	}
}
?>