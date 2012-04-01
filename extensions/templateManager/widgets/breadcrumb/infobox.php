<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxBreadcrumb extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('breadcrumb');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_BREADCRUMB'));
	}

	public function show(){
		    global $breadcrumb;

			$this->setBoxContent('<div class="breadcrumbTrail">'.$breadcrumb->trail(' &raquo; ').'</div>');

			return $this->draw();

			return false;
	}
}
?>