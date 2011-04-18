<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxLanguages extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('languages');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_LANGUAGES'));
	}

	public function show(){
		global $request_type;

		$boxContent = '';
		foreach(sysLanguage::getLanguages() as $lInfo) {
			$boxContent .= ' <a href="' . itw_app_link(tep_get_all_get_params(array('language', 'currency')) . 'language=' . $lInfo['code']) . '">' . $lInfo['showName']('&nbsp;') . '</a><br>';
		}
		
		$this->setBoxContent($boxContent);

		return $this->draw();
	}
}
?>