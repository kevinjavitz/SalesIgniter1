<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxPageTitle extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('pageTitle');
	}

	public function showLayoutPreview($WidgetSettings) {
		return 'Demo Page Title';
	}

	public function show(){
		global $Template, $pageContent;
		$this->setBoxContent('<h1 class="headingTitle">' . $pageContent->getVar('pageTitle') . '</h1>');
		return $this->draw();
	}
}
?>