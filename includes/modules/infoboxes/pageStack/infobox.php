<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxPageStack extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('pageStack');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_PAGE_STACK_OUTPUT'));
	}

	public function show(){
		    global $messageStack;
		    $pageStackOutput = ($messageStack->size('pageStack') > 0 ? $messageStack->output('pageStack') : '');
			$this->setBoxContent($pageStackOutput);

			return $this->draw();

			return false;
	}
}
?>