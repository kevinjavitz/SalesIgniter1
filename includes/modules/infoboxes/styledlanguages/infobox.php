<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxStyledLanguages extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('styledlanguages');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_LANGUAGES'));
	}

	public function show(){
		global $request_type;

		$boxContent = '<ul>';
		foreach(sysLanguage::getLanguages() as $lInfo) {
			$boxContent .= '
			    <li class="lang-'.$lInfo['code'].'">
				<a href="' . itw_app_link(tep_get_all_get_params(array('language', 'currency')) . 'language=' . $lInfo['code']) . '"><span>' 
				. $lInfo['name_real'] . 
				'</span></a>
			    </li>';
		}		
		$boxContent .= '</ul>';
		$this->setBoxContent($boxContent);

		return $this->draw();
	}
}
?>