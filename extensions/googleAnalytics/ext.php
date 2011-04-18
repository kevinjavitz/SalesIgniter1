<?php
/*
	Google Analytics Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_googleAnalytics extends ExtensionBase {

	public function __construct(){
		parent::__construct('googleAnalytics');
	}
	
	public function init(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'PageLayoutFooterAfterDraw',
			'PageLayoutHeaderCustomMeta'

		), null, $this);
	}

	public function PageLayoutFooterAfterDraw(){
		return sysConfig::get('EXTENSION_GOOGLE_ANALYTICS_TRACKING_GENERAL');
	}

	public function PageLayoutHeaderCustomMeta(){
		return '<meta name="google-site-verification" content="'.sysConfig::get('EXTENSION_GOOGLE_ANALYTICS_META_VERIFICATION').'">';
	}
}
?>